<?php
/**
 * Event observers for local_qrenrol.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\user_created',
        'callback'  => '\local_qrenrol\observer::user_created',
        'internal'  => false,
        'priority'  => 200,
    ],
    [
        'eventname' => '\core\event\user_loggedin',
        'callback'  => '\local_qrenrol\observer::user_loggedin',
        'internal'  => false,
        'priority'  => 200,
    ],
];