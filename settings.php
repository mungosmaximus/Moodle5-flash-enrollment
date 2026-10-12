<?php
/**
 * Admin settings for local_qrenrol.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    // Registrujemo eksternu admin stranicu.
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_qrenrol_manage',
        get_string('pluginname', 'local_qrenrol'),
        new moodle_url('/local/qrenrol/index.php'),
        'local/qrenrol:manage'
    ));

    // Opciona podešavanja.
    $settings = new admin_settingpage(
        'local_qrenrol_settings',
        get_string('settings', 'local_qrenrol')
    );

    $settings->add(new admin_setting_configtext(
        'local_qrenrol/default_expiry_days',
        get_string('setting_default_expiry', 'local_qrenrol'),
        get_string('setting_default_expiry_desc', 'local_qrenrol'),
        30,
        PARAM_INT
    ));

    $ADMIN->add('localplugins', $settings);
}