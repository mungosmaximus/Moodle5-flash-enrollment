<?php
/**
 * Token manager for local_qrenrol.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_qrenrol;

defined('MOODLE_INTERNAL') || die();

/**
 * Centralna klasa za rad sa tokenima za upis.
 */
class token_manager {

    const STATUS_ACTIVE   = 'active';
    const STATUS_EXPIRED  = 'expired';
    const STATUS_DISABLED = 'disabled';

    /**
     * Generiše jedinstveni token koji ne postoji u bazi.
     *
     * @return string
     */
    public static function generate_unique_token(): string {
        global $DB;

        do {
            $token = bin2hex(random_bytes(16));
        } while ($DB->record_exists('local_qrenrol_tokens', ['token' => $token]));

        return $token;
    }

    /**
     * Vraća zapis tokena ili null.
     *
     * @param string $token
     * @return \stdClass|null
     */
    public static function get_token(string $token): ?\stdClass {
        global $DB;

        if (empty($token)) {
            return null;
        }

        $record = $DB->get_record('local_qrenrol_tokens', ['token' => $token]);
        return $record ?: null;
    }

    /**
     * Vraća status linka.
     *
     * @param \stdClass $record
     * @return string
     */
    public static function get_status(\stdClass $record): string {
        if (!empty($record->disabled)) {
            return self::STATUS_DISABLED;
        }

        if ($record->expiresat > 0 && $record->expiresat < time()) {
            return self::STATUS_EXPIRED;
        }

        return self::STATUS_ACTIVE;
    }

    /**
     * Proverava da li je link upotrebljiv.
     *
     * @param \stdClass $record
     * @return bool
     */
    public static function is_usable(\stdClass $record): bool {
        return self::get_status($record) === self::STATUS_ACTIVE;
    }

    /**
     * Vraća HTML badge za status.
     *
     * @param string $status
     * @return string
     */
    public static function get_status_badge(string $status): string {
        $map = [
            self::STATUS_ACTIVE   => ['label' => 'status_active',   'class' => 'badge badge-success'],
            self::STATUS_EXPIRED  => ['label' => 'status_expired',  'class' => 'badge badge-secondary'],
            self::STATUS_DISABLED => ['label' => 'status_disabled', 'class' => 'badge badge-danger'],
        ];

        $info = $map[$status] ?? $map[self::STATUS_EXPIRED];

        return \html_writer::tag(
            'span',
            get_string($info['label'], 'local_qrenrol'),
            ['class' => $info['class']]
        );
    }

    /**
     * Upisuje korisnika na kurs ako već nije upisan.
     *
     * @param int $userid
     * @param int $courseid
     * @return bool
     */
    public static function enrol_user_to_course(int $userid, int $courseid): bool {
        global $DB;

        $course = $DB->get_record('course', ['id' => $courseid]);
        if (!$course) {
            return false;
        }

        $context = \context_course::instance($courseid);

        if (is_enrolled($context, $userid, '', true)) {
            return true;
        }

        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        if (!$studentroleid) {
            return false;
        }

        $manualinstance = self::get_manual_enrol_instance($courseid);
        if (!$manualinstance) {
            return false;
        }

        $enrol = enrol_get_plugin('manual');
        if (!$enrol) {
            return false;
        }

        try {
            $enrol->enrol_user($manualinstance, $userid, $studentroleid);
        } catch (\Exception $e) {
            debugging('local_qrenrol: enrolment failed — ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        return true;
    }

    /**
     * Vraća manual enrolment instancu (kreira je ako ne postoji).
     *
     * @param int $courseid
     * @return \stdClass|null
     */
    protected static function get_manual_enrol_instance(int $courseid): ?\stdClass {
        global $DB;

        $instances = enrol_get_instances($courseid, true);
        foreach ($instances as $instance) {
            if ($instance->enrol === 'manual') {
                return $instance;
            }
        }

        $enrol = enrol_get_plugin('manual');
        if (!$enrol) {
            return null;
        }

        $course = $DB->get_record('course', ['id' => $courseid]);
        if (!$course) {
            return null;
        }

        $enrol->add_default_instance($course);

        $instances = enrol_get_instances($courseid, true);
        foreach ($instances as $instance) {
            if ($instance->enrol === 'manual') {
                return $instance;
            }
        }

        return null;
    }

    /**
     * Vraća URL za pristup entry.php sa datim tokenom.
     *
     * @param string $token
     * @return \moodle_url
     */
    public static function get_entry_url(string $token): \moodle_url {
        return new \moodle_url('/local/qrenrol/entry.php', ['token' => $token]);
    }

    /**
     * Ručno isključuje link pre njegovog isteka.
     *
     * @param int $id
     * @return bool
     */
    public static function disable_token(int $id): bool {
        global $DB;

        $record = $DB->get_record('local_qrenrol_tokens', ['id' => $id]);
        if (!$record) {
            return false;
        }

        $record->disabled  = 1;
        $record->expiresat = time();

        return $DB->update_record('local_qrenrol_tokens', $record);
    }

    /**
     * Briše tokene koji su istekli pre više od zadatog broja dana.
     *
     * @param int $days
     * @param bool $keepdisabled
     * @return int
     */
    public static function cleanup_expired(int $days = 90, bool $keepdisabled = true): int {
        global $DB;

        $cutoff = time() - ($days * DAYSECS);

        $where = 'expiresat > 0 AND expiresat < :cutoff';
        $params = ['cutoff' => $cutoff];

        if ($keepdisabled) {
            $where .= ' AND disabled = 0';
        }

        $records = $DB->get_records_select(
            'local_qrenrol_tokens',
            $where,
            $params,
            '',
            'id'
        );

        if (empty($records)) {
            return 0;
        }

        $ids = array_keys($records);
        $DB->delete_records_list('local_qrenrol_tokens', 'id', $ids);

        return count($ids);
    }
}