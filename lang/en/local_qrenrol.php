<?php
/**
 * English language strings for local_qrenrol.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// --- Plugin metadata ---
$string['pluginname'] = 'QR enrolment links';

// --- Admin page: index.php ---
$string['manage_links']            = 'Manage enrolment links';
$string['create_new_link']         = 'Create a new link';
$string['existing_links']          = 'Existing links';
$string['no_links_yet']            = 'There are no links created yet.';

// --- Form: generate_form.php ---
$string['form_name']               = 'Link name';
$string['form_name_help']          = 'A descriptive name for this link (e.g. "Group A — June 2026").';
$string['form_course']             = 'Course';
$string['form_course_help']        = 'The course to which users will be enrolled.';
$string['form_expiresat']          = 'Link valid until';
$string['form_expiresat_help']     = 'After this date, the link will show an expiration message. Leave empty if the link should never expire.';
$string['form_expiresat_past']     = 'The expiration date must be in the future.';
$string['form_submit']             = 'Create link';

// --- Table columns ---
$string['table_name']              = 'Name';
$string['table_course']            = 'Course';
$string['table_created']           = 'Created';
$string['table_expires']           = 'Expires';
$string['table_status']            = 'Status';
$string['table_actions']           = 'Actions';
$string['never']                   = 'Never';

// --- Status labels ---
$string['status_active']           = 'Active';
$string['status_expired']          = 'Expired';
$string['status_disabled']         = 'Disabled';

// --- Actions ---
$string['copy_link']               = 'Copy link';
$string['copied']                  = 'Copied!';
$string['disable_link']            = 'Disable';

// --- Success / error messages ---
$string['link_created_success']    = 'The link has been created successfully.';
$string['link_disabled_success']   = 'The link has been disabled.';

// --- Disable confirmation modal ---
$string['disable_modal_title']     = 'Disable link';
$string['disable_modal_question']  = 'Are you sure you want to disable this link before its expiration date?';
$string['disable_confirm']         = 'Disable';
$string['cancel']                  = 'Cancel';

// --- entry.php ---
$string['invalidtoken_title']      = 'Invalid link';
$string['invalidtoken_message']    = 'The link you followed is not valid. Please contact your instructor.';
$string['enrolmentpage_title']     = 'Course enrolment';
$string['enrolmentpage_heading']   = 'Enrol to course';
$string['linkexpired_message']     = 'This enrolment link expired on {$a}. Please contact your instructor for a new link.';
$string['youareenrollingin']       = 'You are about to enrol in:';
$string['linkvaliduntil']          = 'This link is valid until {$a}.';

$string['chooseaction_intro']       = 'To continue, please choose one of the options below:';
$string['chooseaction_explanation'] = 'Choose <strong>Log in</strong> if you already have an account on this portal. Choose <strong>Create a new account</strong> if you do not have an account yet and need to open one.';

$string['registernewaccount']      = 'Create a new account';
$string['login_existingaccount']   = 'Log in';

$string['alreadyenrolled_redirect']  = 'You are already enrolled in this course.';
$string['enrolmentsuccess_redirect'] = 'You have been enrolled in {$a}.';
$string['enrolmentfailed_message']   = 'Enrolment could not be completed. Please contact your instructor.';

// --- Settings ---
$string['settings']                    = 'QR enrolment links settings';
$string['setting_default_expiry']      = 'Default link validity (days)';
$string['setting_default_expiry_desc'] = 'Default number of days a new link should remain valid. Leave empty or set to 0 to never expire by default.';

// --- Capabilities ---
$string['qrenrol:manage'] = 'Manage enrolment links';
$string['qrenrol:view']   = 'View enrolment links';