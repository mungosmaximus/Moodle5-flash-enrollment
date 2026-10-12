<?php
/**
 * Entry page for tokenized enrolment links.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/enrollib.php');

use local_qrenrol\token_manager;

$token = required_param('token', PARAM_ALPHANUM);

$record = token_manager::get_token($token);

// 1. Token ne postoji.
if (!$record) {
    $PAGE->set_url('/local/qrenrol/entry.php', ['token' => $token]);
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('login');
    $PAGE->set_title(get_string('invalidtoken_title', 'local_qrenrol'));

    echo $OUTPUT->header();
    echo $OUTPUT->notification(
        get_string('invalidtoken_message', 'local_qrenrol'),
        \core\output\notification::NOTIFY_ERROR
    );
    echo $OUTPUT->footer();
    exit;
}

// 2. Učitaj kurs.
$course = $DB->get_record('course', ['id' => $record->courseid], '*', MUST_EXIST);
$coursecontext = context_course::instance($course->id);

// 3. Postavke stranice.
$PAGE->set_url('/local/qrenrol/entry.php', ['token' => $token]);
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('login');
$PAGE->set_title(get_string('enrolmentpage_title', 'local_qrenrol'));
$PAGE->set_heading(get_string('enrolmentpage_heading', 'local_qrenrol'));

// 4. Provera isteka / isključenja.
if (!token_manager::is_usable($record)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(
        get_string('linkexpired_message', 'local_qrenrol', userdate($record->expiresat)),
        \core\output\notification::NOTIFY_ERROR
    );
    echo $OUTPUT->footer();
    exit;
}

// 5. Korisnik je već prijavljen — upiši i preusmeri.
if (isloggedin() && !isguestuser()) {
    global $USER;

    if (token_manager::enrol_user_to_course($USER->id, $course->id)) {
        redirect(
            new moodle_url('/course/view.php', ['id' => $course->id]),
            get_string('enrolmentsuccess_redirect', 'local_qrenrol', format_string($course->fullname)),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }

    echo $OUTPUT->header();
    echo $OUTPUT->notification(
        get_string('enrolmentfailed_message', 'local_qrenrol'),
        \core\output\notification::NOTIFY_ERROR
    );
    echo $OUTPUT->footer();
    exit;
}

// 6. Sačuvaj token u sesiju za observer-e.
if (isset($SESSION)) {
    $SESSION->local_qrenrol_token = $token;
}

// 7. Prikaz stranice.
echo $OUTPUT->header();

echo html_writer::start_div('local-qrenrol-entry card p-4 my-4');

echo html_writer::tag('h3', get_string('youareenrollingin', 'local_qrenrol'), ['class' => 'mb-3']);
echo html_writer::tag('p', format_string($course->fullname), ['class' => 'h4 text-primary mb-4']);

if (!empty($course->summary)) {
    echo html_writer::tag(
        'div',
        format_text($course->summary, $course->summaryformat, ['context' => $coursecontext]),
        ['class' => 'course-summary mb-4']
    );
}

// Poruka sa uputstvom.
echo html_writer::start_div('local-qrenrol-instructions mb-4');
echo html_writer::tag('p', get_string('chooseaction_intro', 'local_qrenrol'),
    ['class' => 'font-weight-bold mb-2']);
echo html_writer::tag('p', get_string('chooseaction_explanation', 'local_qrenrol'),
    ['class' => 'text-muted mb-0']);
echo html_writer::end_div();

// Dva dugmeta.
echo html_writer::start_div('d-flex gap-3 flex-wrap mb-3');

$signupurl = new moodle_url('/login/signup.php');
$loginurl  = new moodle_url('/login/index.php');

echo html_writer::link(
    $loginurl,
    get_string('login_existingaccount', 'local_qrenrol'),
    ['class' => 'btn btn-primary btn-lg']
);

echo html_writer::link(
    $signupurl,
    get_string('registernewaccount', 'local_qrenrol'),
    ['class' => 'btn btn-outline-secondary btn-lg']
);

echo html_writer::end_div();

// Poruka o roku važenja.
if ($record->expiresat > 0) {
    echo html_writer::tag(
        'p',
        get_string('linkvaliduntil', 'local_qrenrol', userdate($record->expiresat)),
        ['class' => 'text-muted small mb-0']
    );
}

echo html_writer::end_div();

echo $OUTPUT->footer();