<?php
/**
 * Admin page for managing enrolment links.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_qrenrol\form\generate_form;
use local_qrenrol\token_manager;

$action  = optional_param('action', '', PARAM_ALPHA);
$id      = optional_param('id', 0, PARAM_INT);
$page    = optional_param('page', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$perpage = 10;

admin_externalpage_setup('local_qrenrol_manage');

$url = new moodle_url('/local/qrenrol/index.php');
$PAGE->set_url($url);
$PAGE->set_title(get_string('manage_links', 'local_qrenrol'));
$PAGE->set_heading(get_string('manage_links', 'local_qrenrol'));

$context = context_system::instance();
require_capability('local/qrenrol:manage', $context);

// --- Obrada akcije "Isključi" ---
if ($action === 'disable' && $id && $confirm && confirm_sesskey()) {
    if (token_manager::disable_token($id)) {
        redirect(
            $url,
            get_string('link_disabled_success', 'local_qrenrol'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
    redirect(
        $url,
        get_string('enrolmentfailed_message', 'local_qrenrol'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

// --- Obrada forme za kreiranje ---
$form = new generate_form();

if ($data = $form->get_data()) {
    $record = new stdClass();
    $record->token       = token_manager::generate_unique_token();
    $record->name        = $data->name;
    $record->courseid    = $data->courseid;
    $record->timecreated = time();
    $record->expiresat   = !empty($data->expiresat) ? $data->expiresat : 0;
    $record->createdby   = $USER->id;
    $record->disabled    = 0;

    $DB->insert_record('local_qrenrol_tokens', $record);

    redirect(
        $url,
        get_string('link_created_success', 'local_qrenrol'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// --- Prikaz stranice ---
echo $OUTPUT->header();

// Prikupi stringove unapred (izbegavamo pozive u petlji).
$strcopy    = get_string('copy_link', 'local_qrenrol');
$strcopied  = get_string('copied', 'local_qrenrol');
$strdisable = get_string('disable_link', 'local_qrenrol');
$strnever   = get_string('never', 'local_qrenrol');
$strdatefmt = get_string('strftimedatetimeshort', 'langconfig');

echo html_writer::start_div('local-qrenrol-admin');

// === Sekcija 1: Forma za kreiranje ===
echo html_writer::start_div('card mb-4');
echo html_writer::start_div('card-body');
echo html_writer::tag('h3', get_string('create_new_link', 'local_qrenrol'),
    ['class' => 'card-title mb-3']);
$form->display();
echo html_writer::end_div();
echo html_writer::end_div();

// === Sekcija 2: Tabela ===
echo html_writer::tag('h3', get_string('existing_links', 'local_qrenrol'), ['class' => 'mb-3']);

$totalcount = $DB->count_records('local_qrenrol_tokens');

if ($totalcount === 0) {
    echo $OUTPUT->notification(
        get_string('no_links_yet', 'local_qrenrol'),
        \core\output\notification::NOTIFY_INFO
    );
} else {
    $records = $DB->get_records(
        'local_qrenrol_tokens',
        null,
        'timecreated DESC',
        '*',
        $page * $perpage,
        $perpage
    );

    // Batch učitavanje kurseva — izbegavamo N+1 upit.
    $courseids = array_unique(array_column($records, 'courseid'));
    $courses = !empty($courseids)
        ? $DB->get_records_list('course', 'id', $courseids, '', 'id, fullname')
        : [];

    $table = new html_table();
    $table->head = [
        get_string('table_name', 'local_qrenrol'),
        get_string('table_course', 'local_qrenrol'),
        get_string('table_created', 'local_qrenrol'),
        get_string('table_expires', 'local_qrenrol'),
        get_string('table_status', 'local_qrenrol'),
        get_string('table_actions', 'local_qrenrol'),
    ];
    $table->attributes['class'] = 'table table-striped table-hover generaltable';
    $table->data = [];

    foreach ($records as $record) {
        $course = $courses[$record->courseid] ?? null;

        $status      = token_manager::get_status($record);
        $statusbadge = token_manager::get_status_badge($status);
        $shorttoken  = substr($record->token, 0, 6);
        $linkurl     = token_manager::get_entry_url($record->token);

        // Akcije.
        $actions = [];

        $actions[] = html_writer::tag('button',
            $strcopy,
            [
                'class'   => 'btn btn-sm btn-outline-secondary',
                'data-url'=> $linkurl->out(false),
                'onclick' => "navigator.clipboard.writeText(this.dataset.url); this.innerText='"
                    . $strcopied . "';",
                'type'    => 'button',
            ]
        );

        if ($status === token_manager::STATUS_ACTIVE) {
            $disableurl = new moodle_url('/local/qrenrol/index.php', [
                'action'  => 'disable',
                'id'      => $record->id,
                'confirm' => 1,
                'sesskey' => sesskey(),
            ]);

            $actions[] = html_writer::link('#', $strdisable, [
                'class'         => 'btn btn-sm btn-outline-danger',
                'data-bs-toggle'=> 'modal',
                'data-bs-target'=> '#disableModal',
                'data-url'      => $disableurl->out(false),
                'data-name'     => format_string($record->name),
            ]);
        }

        // Kolona 1: Naziv + kratak token.
        $namecell = html_writer::tag('div', format_string($record->name),
            ['class' => 'font-weight-bold']);
        $namecell .= html_writer::tag('div',
            html_writer::tag('code', $shorttoken, ['class' => 'small text-muted']),
            ['class' => 'mt-1']
        );

        // Kolona 2: Kurs.
        $coursecell = $course
            ? format_string($course->fullname)
            : html_writer::tag('span', '-', ['class' => 'text-muted']);

        $table->data[] = [
            $namecell,
            $coursecell,
            userdate($record->timecreated, $strdatefmt),
            $record->expiresat > 0 ? userdate($record->expiresat, $strdatefmt) : $strnever,
            $statusbadge,
            implode(' ', $actions),
        ];
    }

    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $url);
}

echo html_writer::end_div();
?>

<div class="modal fade" id="disableModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?php echo get_string('disable_modal_title', 'local_qrenrol'); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><?php echo get_string('disable_modal_question', 'local_qrenrol'); ?></p>
        <p class="font-weight-bold" id="disableModalName"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <?php echo get_string('cancel', 'local_qrenrol'); ?>
        </button>
        <a href="#" class="btn btn-danger" id="disableModalConfirm">
          <?php echo get_string('disable_confirm', 'local_qrenrol'); ?>
        </a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('disableModal');
    if (!modal) return;

    modal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        if (!button) return;
        var url  = button.getAttribute('data-url');
        var name = button.getAttribute('data-name');

        document.getElementById('disableModalName').textContent = name;
        document.getElementById('disableModalConfirm').setAttribute('href', url);
    });
});
</script>

<?php
echo $OUTPUT->footer();