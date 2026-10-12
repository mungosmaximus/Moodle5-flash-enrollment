<?php
/**
 * Serbian language strings for local_qrenrol.
 *
 * @package    local_qrenrol
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// --- Metapodaci dodatka ---
$string['pluginname'] = 'QR linkovi za upis';

// --- Admin stranica: index.php ---
$string['manage_links']            = 'Upravljanje linkovima za upis';
$string['create_new_link']         = 'Kreiraj novi link';
$string['existing_links']          = 'Postojeći linkovi';
$string['no_links_yet']            = 'Još uvek nema kreiranih linkova.';

// --- Forma: generate_form.php ---
$string['form_name']               = 'Naziv linka';
$string['form_name_help']          = 'Opisni naziv za ovaj link (npr. „Grupa A — jun 2026").';
$string['form_course']             = 'Kurs';
$string['form_course_help']        = 'Kurs na koji će korisnici biti upisani.';
$string['form_expiresat']          = 'Link važi do';
$string['form_expiresat_help']     = 'Nakon ovog datuma, link će prikazivati poruku o isteku. Ostavite prazno ako link nikada ne treba da istekne.';
$string['form_expiresat_past']     = 'Datum isteka mora biti u budućnosti.';
$string['form_submit']             = 'Kreiraj link';

// --- Kolone tabele ---
$string['table_name']              = 'Naziv';
$string['table_course']            = 'Kurs';
$string['table_created']           = 'Kreiran';
$string['table_expires']           = 'Ističe';
$string['table_status']            = 'Status';
$string['table_actions']           = 'Akcije';
$string['never']                   = 'Nikada';

// --- Oznake statusa ---
$string['status_active']           = 'Aktivan';
$string['status_expired']          = 'Istekao';
$string['status_disabled']         = 'Isključen';

// --- Akcije ---
$string['copy_link']               = 'Kopiraj link';
$string['copied']                  = 'Kopirano!';
$string['disable_link']            = 'Isključi';

// --- Poruke ---
$string['link_created_success']    = 'Link je uspešno kreiran.';
$string['link_disabled_success']   = 'Link je isključen.';

// --- Modal ---
$string['disable_modal_title']     = 'Isključi link';
$string['disable_modal_question']  = 'Da li ste sigurni da želite da isključite ovaj link pre njegovog isticanja?';
$string['disable_confirm']         = 'Isključi';
$string['cancel']                  = 'Odustani';

// --- entry.php ---
$string['invalidtoken_title']      = 'Nevažeći link';
$string['invalidtoken_message']    = 'Link koji ste pratili nije važeći. Molimo obratite se predavaču.';
$string['enrolmentpage_title']     = 'Upis na kurs';
$string['enrolmentpage_heading']   = 'Upis na kurs';
$string['linkexpired_message']     = 'Ovaj link za upis je istekao {$a}. Molimo obratite se predavaču za novi link.';
$string['youareenrollingin']       = 'Upravo se upisujete na:';
$string['linkvaliduntil']          = 'Ovaj link važi do {$a}.';

$string['chooseaction_intro']       = 'Da biste nastavili, izaberite jednu od opcija ispod:';
$string['chooseaction_explanation'] = 'Izaberite <strong>Prijavi se</strong> ako već imate nalog na portalu. Izaberite <strong>Otvori novi nalog</strong> ako još uvek nemate nalog i potrebno je da ga kreirate.';

$string['registernewaccount']      = 'Otvori novi nalog';
$string['login_existingaccount']   = 'Prijavi se';

$string['alreadyenrolled_redirect']  = 'Već ste upisani na ovaj kurs.';
$string['enrolmentsuccess_redirect'] = 'Uspešno ste upisani na {$a}.';
$string['enrolmentfailed_message']   = 'Upis nije mogao biti završen. Molimo obratite se predavaču.';

// --- Podešavanja ---
$string['settings']                    = 'Podešavanja QR linkova za upis';
$string['setting_default_expiry']      = 'Podrazumevano trajanje linka (dana)';
$string['setting_default_expiry_desc'] = 'Podrazumevani broj dana koliko novi link treba da bude važeći. Ostavite prazno ili postavite na 0 da link podrazumevano nikada ne ističe.';

// --- Dozvole ---
$string['qrenrol:manage'] = 'Upravljanje linkovima za upis';
$string['qrenrol:view']   = 'Pregled linkova za upis';