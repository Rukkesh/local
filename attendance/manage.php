<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_attendance
 * @author      Pragathiswaran Ramyasri Rukkesh 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once('lib.php');

$PAGE->set_url(new moodle_url('/local/attendance/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Attendance');

echo $OUTPUT->header();

$local_attendance = new local_attendance();
$templatecontext = $local_attendance->getUseractivity();
echo $OUTPUT->render_from_template('local_attendance/manage', $templatecontext);

echo $OUTPUT->footer();