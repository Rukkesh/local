<?php

defined('MOODLE_INTERNAL') || die;

class local_attendance_observer {
    /**
     * Handle the user_loggedin event.
     *
     * @param core\event\user_loggedin $event
     */
    public static function user_loggedin(core\event\user_loggedin $event) {
        $userid = $event->userid;

        // Your logic to calculate time spent (use the previous script as a guide)
        // ...

        // Output the result or store it as needed
        echo "Total time spent by user with ID $userid since their last login: minutes";
    }
}
