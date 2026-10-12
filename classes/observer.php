<?php
/**
 * Event observers for local_qrenrol.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_qrenrol;

defined('MOODLE_INTERNAL') || die();

/**
 * Observer klasa za obradu događaja vezanih za upis putem tokena.
 */
class observer {

    /**
     * Reaguje na kreiranje novog korisničkog naloga.
     *
     * @param \core\event\user_created $event
     */
    public static function user_created(\core\event\user_created $event): void {
        global $SESSION;

        if (empty($SESSION->local_qrenrol_token)) {
            return;
        }

        $token = $SESSION->local_qrenrol_token;
        unset($SESSION->local_qrenrol_token);

        $record = token_manager::get_token($token);
        if (!$record) {
            return;
        }

        if (!token_manager::is_usable($record)) {
            return;
        }

        $userid   = $event->objectid;
        $courseid = $record->courseid;

        token_manager::enrol_user_to_course($userid, $courseid);

        $SESSION->local_qrenrol_pendingcourse = $courseid;
    }

    /**
     * Reaguje na prijavu korisnika na sistem.
     *
     * @param \core\event\user_loggedin $event
     */
    public static function user_loggedin(\core\event\user_loggedin $event): void {
        global $SESSION;

        $userid = $event->objectid;

        // Scenario A: korisnik je otvorio entry.php i ima token u sesiji.
        if (!empty($SESSION->local_qrenrol_token)) {
            $token = $SESSION->local_qrenrol_token;
            unset($SESSION->local_qrenrol_token);

            $record = token_manager::get_token($token);
            if ($record && token_manager::is_usable($record)) {
                token_manager::enrol_user_to_course($userid, $record->courseid);
                $SESSION->local_qrenrol_pendingcourse = $record->courseid;
            }
            return;
        }

        // Scenario B: korisnik je završio registraciju i automatski je prijavljen.
        if (!empty($SESSION->local_qrenrol_pendingcourse)) {
            $courseid = $SESSION->local_qrenrol_pendingcourse;
            unset($SESSION->local_qrenrol_pendingcourse);

            $context = \context_course::instance($courseid);
            if (is_enrolled($context, $userid, '', true)) {
                $course = get_course($courseid);
                redirect(
                    new \moodle_url('/course/view.php', ['id' => $courseid]),
                    get_string('enrolmentsuccess_redirect', 'local_qrenrol',
                        format_string($course->fullname)),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            }
        }
    }
}