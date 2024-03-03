<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * class and fucntions
 *
 * @package   local_attendance
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


 class local_attendance{
    public function getLogin(){
        global $DB;
        $sql = 'SELECT l.id AS "Log_event_id",
            l.timecreated AS "Timestamp",
            DATE_FORMAT(FROM_UNIXTIME(l.timecreated),"%Y-%m-%d %H:%i:%s") AS "Time_UTC",
            DATE_FORMAT(FROM_UNIXTIME(l.timecreated),"%Y-%m-%d") AS "date",
            l.action,
            u.username,
            u.id,
            l.origin,
            l.ip
            FROM mdl_logstore_standard_log l
            JOIN mdl_user u ON u.id = l.userid
            WHERE l.action IN ("loggedin")
            AND l.userid != :exclude_userid
            ORDER BY l.timecreated ' ;

        $param = ['exclude_userid' => 2];

        $loginData = $DB->get_records_sql($sql, $param);

        return $loginData;
    }

    public function getLogout(){
        global $DB;

        $sql = 'SELECT l.id AS "Log_event_id",
                l.timecreated AS "Timestamp",
                DATE_FORMAT(FROM_UNIXTIME(l.timecreated),"%Y-%m-%d %H:%i:%s") AS "Time_UTC",
                DATE_FORMAT(FROM_UNIXTIME(l.timecreated),"%Y-%m-%d") AS "date",
                l.action,
                u.username,
                l.origin,
                l.ip
                FROM mdl_logstore_standard_log l
                JOIN mdl_user u ON u.id = l.userid
                WHERE l.action IN ("loggedout")
                AND l.userid != :exclude_userid
                ORDER BY l.timecreated';

        $param = ['exclude_userid' => 2];

        $logoutData = $DB->get_records_sql($sql, $param);
            
        return $logoutData;

    }

    public function getUseractivity(){
        $loginData = $this->getLogin();
        $logoutData = $this->getLogout();
        $values = array();

        foreach ($loginData as $key => $value1) {
            $values[] = array(
                'username' => $value1->username,
                'userid' => $value1->id,
                'date' => $value1->date,
                'loggedin' => $value1->time_utc,
            );
        }

        $values1 = array();
        foreach ($logoutData as $key => $value2) {
            $values1[] = array(
                'loggedout' => $value2->time_utc,
            );
        }

        $result = array();
        for ($i = 0; $i < count($values); $i++) {
            $time1 = $values[$i]['loggedin'];
            $time2 = $values1[$i]['loggedout'];

            // Create DateTime objects from the time strings
            $datetime1 = new DateTime($time1);
            $datetime2 = new DateTime($time2);

            // Calculate the difference using date_diff()
            $interval = date_diff($datetime1, $datetime2);

            $result[] = array(
                'userid'=> $values[$i]['userid'],
                'username' => $values[$i]['username'],
                'date' => $values[$i]['date'],
                'loggedin' => (new DateTime($values[$i]['loggedin']))->format('H:i:s'),
                'loggedout' => (new DateTime($values1[$i]['loggedout']))->format('H:i:s'),
                'difference' => $interval->format('%H:%I:%S'),
            );
            
        }

        return array(
        'result'=> $result
        );
    }

 }
function local_attendance_before_footer() {
    // example();
}
