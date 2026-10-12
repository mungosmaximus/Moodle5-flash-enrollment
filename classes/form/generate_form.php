<?php
/**
 * Form for generating a new enrolment link.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_qrenrol\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Forma za kreiranje novog linka za upis.
 */
class generate_form extends \moodleform {

    protected function definition() {
        $mform = $this->_form;

        // 1. Naziv linka.
        $mform->addElement('text', 'name', get_string('form_name', 'local_qrenrol'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'form_name', 'local_qrenrol');

        // 2. Kurs.
        $courseoptions = self::get_course_options();

        $mform->addElement('autocomplete', 'courseid',
            get_string('form_course', 'local_qrenrol'),
            $courseoptions,
            ['multiple' => false]
        );
        $mform->setType('courseid', PARAM_INT);
        $mform->addRule('courseid', null, 'required', null, 'client');
        $mform->addHelpButton('courseid', 'form_course', 'local_qrenrol');

        // 3. Datum isteka.
        $mform->addElement('date_selector', 'expiresat',
            get_string('form_expiresat', 'local_qrenrol'),
            ['optional' => true]
        );
        $mform->addHelpButton('expiresat', 'form_expiresat', 'local_qrenrol');

        // Dugmad.
        $this->add_action_buttons(false, get_string('form_submit', 'local_qrenrol'));
    }

    /**
     * Vraća opcije kurseva za autocomplete element.
     *
     * @return array
     */
    protected static function get_course_options(): array {
        $courses = \core_course_category::get(0)->get_courses([
            'recursive' => true,
            'sort'      => ['fullname' => 'ASC'],
        ]);

        $options = [];
        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            $options[$course->id] = format_string($course->fullname);
        }

        return $options;
    }

    /**
     * Validacija forme.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['expiresat']) && $data['expiresat'] < time()) {
            $errors['expiresat'] = get_string('form_expiresat_past', 'local_qrenrol');
        }

        return $errors;
    }
}