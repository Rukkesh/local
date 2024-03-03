<?php
class local_attendance {
    public function getUserCourseActivity() {
        global $DB;

        $param = ['exclude_userid' => 2];

        $sql = 'SELECT l.id AS log_event_id,
                l.timecreated AS timestamp,
                DATE_FORMAT(FROM_UNIXTIME(l.timecreated), \'%%d-%%m-%%y %%H:%%m:%%s\') AS time_utc,
                DATE_FORMAT(FROM_UNIXTIME(l.timecreated), \'%%d-%%m-%%y\') AS date,
                l.action,
                u.username,
                u.id AS userid,
                c.id AS courseid,
                c.fullname AS course_name,
                l.origin,
                l.ip
            FROM mdl_logstore_standard_log l
            JOIN mdl_user u ON u.id = l.userid
            JOIN mdl_course c ON c.id = l.courseid
            WHERE l.courseid != 0 AND l.userid != :exclude_userid
            ORDER BY u.id, l.courseid, l.timecreated';

        $activityData = $DB->get_records_sql($sql, $param);

        $courseActivities = [];
        $lastActivityTime = [];

        foreach ($activityData as $activity) {
            $userId = $activity->userid;
            $courseId = $activity->courseid; 
            $username = $activity->username;
            $date = $activity->date; 
            $timestamp = $activity->timestamp;

            
            if (!isset($courseActivities[$userId][$courseId])) {
                $courseActivities[$userId][$courseId] = [
                    'username' => $username,
                    'course_name' => $activity->course_name,
                    'access_count' => 0,
                    'total_time_spent' => 0, 
                    'date' => $date 
                ];
                $lastActivityTime[$userId][$courseId] = $timestamp;
            }
            $courseActivities[$userId][$courseId]['access_count']++;
            if (isset($lastActivityTime[$userId][$courseId])) {
                $timeDiff = $timestamp - $lastActivityTime[$userId][$courseId];
                $maxIdleTime = 3600;
                if ($timeDiff < $maxIdleTime) 
                {
                    $courseActivities[$userId][$courseId]['total_time_spent'] += $timeDiff;
                }
            }
            $lastActivityTime[$userId][$courseId] = $timestamp;
        }
        foreach ($courseActivities as $userId => $courses) {
            foreach ($courses as $courseId => $summary) {
                $totalSeconds = $summary['total_time_spent'];
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds / 60) % 60);
                $seconds = $totalSeconds % 60;
                $courseActivities[$userId][$courseId]['formatted_time_spent'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
            }
        }

        return $courseActivities;
    }
}
