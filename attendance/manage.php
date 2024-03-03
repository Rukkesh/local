<?php
require_once(__DIR__.'/../../config.php'); // Adjust the path as necessary to point to Moodle's config.php
require_once($CFG->dirroot.'/local/attendance/lib.php'); // Adjust the path to your local plugin's library file

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context); // Adjust required capability as necessary

$PAGE->set_url('/local/attendance/manage.php'); // Adjust URL as necessary
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_attendance'));
//$PAGE->set_heading(get_string('activityreport', 'local_attendance'));

echo $OUTPUT->header();
//echo $OUTPUT->heading(get_string('activityreport', 'local_attendance'));

$attendance = new local_attendance();
$activitySummary = $attendance->getUserCourseActivity();

echo '<h3>User Activity</h3>';
echo '<table border="1" style="width:100%">';
echo '<tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Course Name</th>
        <th>Date</th>
        <th>Access Count</th>
        <th>Total Time Spent (H:M:S)</th>
      </tr>';

foreach ($activitySummary as $userId => $courses) {
    foreach ($courses as $courseId => $data) {
        echo '<tr>';
        echo '<td>'.htmlspecialchars($userId).'</td>';
        echo '<td>'.htmlspecialchars($data['username']).'</td>'; 
        echo '<td>'.htmlspecialchars($data['course_name']).'</td>';
        echo '<td>'.htmlspecialchars($data['date']).'</td>';
        echo '<td>'.htmlspecialchars($data['access_count']).'</td>';
        echo '<td>'.htmlspecialchars($data['formatted_time_spent']).'</td>';
        echo '</tr>';
    }
}

echo '</table>';

echo $OUTPUT->footer();

