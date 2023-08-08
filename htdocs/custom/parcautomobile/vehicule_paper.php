<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *   	\file       vehicule_card.php
 *		\ingroup    parcautomobile
 *		\brief      Page to create/edit/view vehicule
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');				// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK', '1');				// Do not check CSRF attack (test on referer + on token).
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL', '1');				// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK', '1');				// Do not check style html tag into posted data
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU', '1');				// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML', '1');				// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX', '1');       	  	// Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                  define("NOLOGIN", '1');					// If this page is public (can be called outside logged session). This include the NOIPCHECK too.
//if (! defined('NOIPCHECK'))                define('NOIPCHECK', '1');					// Do not check IP defined into conf $powererp_main_restrict_ip
//if (! defined("MAIN_LANG_DEFAULT"))        define('MAIN_LANG_DEFAULT', 'auto');					// Force lang to a particular value
//if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE', 'aloginmodule');	// Force authentication handler
//if (! defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN', 1);		// The main.inc.php does not make a redirect if not logged, instead show simple error message
//if (! defined("FORCECSP"))                 define('FORCECSP', 'none');				// Disable all Content Security Policies
//if (! defined('CSRFCHECK_WITH_TOKEN'))     define('CSRFCHECK_WITH_TOKEN', '1');		// Force use of CSRF protection with tokens even for GET
//if (! defined('NOBROWSERNOTIF'))     		 define('NOBROWSERNOTIF', '1');				// Disable browser notification
//if (! defined('NOSESSION'))     		     define('NOSESSION', '1');				    // Disable session

// Load PowerERP environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
    $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
    $i--;
    $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
    $res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
    $res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
    $res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}
if (!$res) {
    die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formprojet.class.php';
dol_include_once('/parcautomobile/class/vehicule.class.php');
dol_include_once('/parcautomobile/lib/parcautomobile_vehicule.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("parcautomobile@parcautomobile", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'vehiculecard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
$lineid   = GETPOST('lineid', 'int');

// Initialize technical objects
$object = new Vehicule($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->parcautomobile->dir_output . '/temp/massgeneration/' . $user->id;
$hookmanager->initHooks(array('vehiculecard', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

// Initialize array of search criterias
$search_all = GETPOST("search_all", 'alpha');
$search = array();
foreach ($object->fields as $key => $val) {
    if (GETPOST('search_' . $key, 'alpha')) {
        $search[$key] = GETPOST('search_' . $key, 'alpha');
    }
}

if (empty($action) && empty($id) && empty($ref)) {
    $action = 'view';
}

// Load object
include DOL_DOCUMENT_ROOT . '/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

// There is several ways to check permission.
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
$enablepermissioncheck = 0;
if ($enablepermissioncheck) {
    $permissiontoread = $user->rights->parcautomobile->vehicule->read;
    $permissiontoadd = $user->rights->parcautomobile->vehicule->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
    $permissiontodelete = $user->rights->parcautomobile->vehicule->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
    $permissionnote = $user->rights->parcautomobile->vehicule->write; // Used by the include of actions_setnotes.inc.php
    $permissiondellink = $user->rights->parcautomobile->vehicule->write; // Used by the include of actions_dellink.inc.php
} else {
    $permissiontoread = 1;
    $permissiontoadd = 1; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
    $permissiontodelete = 1;
    $permissionnote = 1;
    $permissiondellink = 1;
}

$upload_dir = $conf->parcautomobile->multidir_output[isset($object->entity) ? $object->entity : 1] . '/vehicule';

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (isset($object->status) && ($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->element, $object->id, $object->table_element, '', 'fk_soc', 'rowid', $isdraft);
if (empty($conf->parcautomobile->enabled)) accessforbidden();
if (!$permissiontoread) accessforbidden();


/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
    setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
    $error = 0;

    $backurlforlist = dol_buildpath('/parcautomobile/vehicule_list.php', 1);

    if (empty($backtopage) || ($cancel && empty($id))) {
        if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
            if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
                $backtopage = $backurlforlist;
            } else {
                $backtopage = dol_buildpath('/parcautomobile/vehicule_card.php', 1) . '?id=' . ((!empty($id) && $id > 0) ? $id : '__ID__');
            }
        }
    }

    $triggermodname = 'PARCAUTOMOBILE_VEHICULE_MODIFY'; // Name of trigger action code to execute when we modify record

    // Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
    include DOL_DOCUMENT_ROOT . '/core/actions_addupdatedelete.inc.php';

    // Actions when linking object each other
    include DOL_DOCUMENT_ROOT . '/core/actions_dellink.inc.php';

    // Actions when printing a doc from card
    include DOL_DOCUMENT_ROOT . '/core/actions_printing.inc.php';

    // Action to move up and down lines of object
    //include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';

    // Action to build doc
    include DOL_DOCUMENT_ROOT . '/core/actions_builddoc.inc.php';

    if ($action == 'set_thirdparty' && $permissiontoadd) {
        $object->setValueFrom('fk_soc', GETPOST('fk_soc', 'int'), '', '', 'date', '', $user, $triggermodname);
    }
    if ($action == 'classin' && $permissiontoadd) {
        $object->setProject(GETPOST('projectid', 'int'));
    }

    // Actions to send emails
    $triggersendname = 'PARCAUTOMOBILE_VEHICULE_SENTBYMAIL';
    $autocopy = 'MAIN_MAIL_AUTOCOPY_VEHICULE_TO';
    $trackid = 'vehicule' . $object->id;
    include DOL_DOCUMENT_ROOT . '/core/actions_sendmails.inc.php';
}




/*
 * View
 *
 * Put here all code to build page
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);

$title = $langs->trans("Vehicule");
$help_url = '';
llxHeader('', $title, $help_url);


/* custom */

//**********************     ACTION HEADER

$vehicule = new Vehicule($db);
$vehicule_id = $_GET['id'];
$vehicule_info = $vehicule->fetchInfoVehicules($vehicule_id);
$dateOfToday = new DateTime();


if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add_carte_grise') {
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "parcautomobile_cartegrise (id_vehicule, immatriculation, poids, titulaire, charge_max, energie, place, taxe, vin, date_limite, date_immatriculation) VALUES ('" . $vehicule_id . "','" . $_POST['immatriculation'] . "','" . $_POST['poids'] . "','" . $_POST['titulaire'] . "','" . $_POST['charge'] . "','" . $_POST['energie'] . "','" . $_POST['place'] . "','" . $_POST['taxe'] . "','" . $_POST['vin'] . "','" . $_POST['date_limite'] . "','" . $_POST['date_immatriculation'] . "')";
        $resql = $db->query($sql);
        echo "<script type='text/javascript'>document.location.replace('vehicule_paper.php?id=$vehicule_id');</script>";
    }
    if ($_GET['action'] == 'prol_carte_grise') {
        $sql = "UPDATE " . MAIN_DB_PREFIX . "parcautomobile_cartegrise SET date_limite = '" . $_POST['prol-date-carte-grise'] . "' WHERE id_vehicule = " . $vehicule_id;
        $resql = $db->query($sql);
        echo "<script type='text/javascript'>document.location.replace('vehicule_paper.php?id=$vehicule_id');</script>";
    }
    if ($_GET['action'] == 'add_assurance') {
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "parcautomobile_assurance (id_vehicule, ref, company, category, date_souscription, date_limite ) VALUES ('" . $vehicule_id . "','" . $_POST['libelle'] . "','" . $_POST['company'] . "','" . $_POST['category'] . "','" . $_POST['date_souscription'] . "','" . $_POST['date_limite'] . "')";
        $resql = $db->query($sql);
        echo "<script type='text/javascript'>document.location.replace('vehicule_paper.php?id=$vehicule_id');</script>";
    }
    if ($_GET['action'] == 'add_vignette') {
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "parcautomobile_vignette (id_vehicule, ref, company, year) VALUES ('" . $vehicule_id . "','" . $_POST['libelle'] . "','" . $_POST['company'] . "','" . $_POST['year'] . "')";
        $resql = $db->query($sql);
        echo "<script type='text/javascript'>document.location.replace('vehicule_paper.php?id=$vehicule_id');</script>";
    }
    if ($_GET['action'] == 'add_visite') {
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "parcautomobile_visitetechnique (id_vehicule, ref, company, rapport, date_evaluation, date_limite ) VALUES ('" . $vehicule_id . "','" . $_POST['libelle'] . "','" . $_POST['company'] . "','" . $_POST['rapport'] . "','" . $_POST['date_evaluation'] . "','" . $_POST['date_limite'] . "')";
        $resql = $db->query($sql);
        echo "<script type='text/javascript'>document.location.replace('vehicule_paper.php?id=$vehicule_id');</script>";
    }
    if ($_GET['action'] == 'add_entretien') {
        $sql = "INSERT INTO " . MAIN_DB_PREFIX . "parcautomobile_cartedisque (id_vehicule, type, etat, rapport, status ) VALUES ('" . $vehicule_id . "','" . $_POST['type'] . "','" . $_POST['etat'] . "','" . $_POST['rapport'] . "', 'visible')";
        $resql = $db->query($sql);
        var_dump($resql);
        if (($_POST['etat'] == '1') or ($_POST['etat'] == '4')) {
            $sql2 = "UPDATE " . MAIN_DB_PREFIX . "parcautomobile_vehicule SET etat = '" . $_POST['etat'] . "', extincteur = '" . $_POST['extincteur'] . "', disponibilite = '1' WHERE rowid = " . $vehicule_id;
        } else {
            $sql2 = "UPDATE " . MAIN_DB_PREFIX . "parcautomobile_vehicule SET etat = '" . $_POST['etat'] . "', extincteur = '" . $_POST['extincteur'] . "' WHERE rowid = " . $vehicule_id;
        }
        $resql2 = $db->query($sql2);
        echo "<script type='text/javascript'>document.location.replace('vehicule_paper.php?id=$vehicule_id');</script>";
    }
}

function setEtat($i)
{
    if ($i == 0) {
        return 'Neuf';
    }
    if ($i == 1) {
        return 'Reparation';
    }
    if ($i == 2) {
        return 'Endommage';
    }
    if ($i == 3) {
        return 'Bon etat';
    }
    if ($i == 4) {
        return 'Detruite';
    }
}


//**********************    ACTION HEADER


?>

<h2>Papier du vehicule</h2>

<ul class="custom-nav">
    <li id="grise" class="custom-nav-active"> Carte grise</li>
    <li id="assurance"> Assurance</li>
    <li id="vignette"> Vignette</li>
    <li id="visite"> Visite technique</li>
    <li id="disque"> Carte de disque</li>
    <li id="license_transport"> License de transport</li>
</ul>

<div class="paper-container">
    <div id="grise-container" class="show-container">
        <?php
        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'parcautomobile_cartegrise WHERE id_vehicule = ' . $vehicule_id;
        $resql = $db->query($sql);
        if ($db->num_rows($resql) == 0) {
            print '<p>Ce vehicule ne possede pas de carte grise</p><br>';
            print '<button class="like-btn" id="btn-show-form-carte-grise">Creer une carte grise</button>';
        } else {
            $vehicule_cartegrise = $vehicule->fetchCarteGrise($vehicule_id);
            $date1 = new DateTime($vehicule_cartegrise->date_limite);
            $date2 = new DateTime($vehicule_cartegrise->date_immatriculation);
            print '<div>
                <h2>Informations de la carte grise </h2>';
            print '
                <p><label>Numero immatriculation</label> : ' . $vehicule_cartegrise->immatriculation . '</p>
                <p><label>Numero VIN</label> : ' . $vehicule_cartegrise->vin . '</p>
                <p><label>Informations du titulaire</label> : ' . $vehicule_cartegrise->titulaire . '</p>
                <p><label>Montant des taxes de la carte</label> : ' . $vehicule_cartegrise->taxe . ' FCFA</p>
                <p><label>Poids du vehicule</label> : ' . $vehicule_cartegrise->poids . ' tonnes</p>
                <p><label>Charge maximale</label> : ' . $vehicule_cartegrise->charge_max . ' tonnes</p>
                <p><label>Energie / Puissance</label> : ' . $vehicule_cartegrise->energie . ' Joule</p>
                <p><label>Nombre de place</label> : ' . $vehicule_cartegrise->place . '</p>
                <p><label>Date limite de validite</label> : ' . $date1->format('d M Y') . '</p>
                <p><label>Date immatriculation</label> : ' . $date2->format('d M Y') . '</p>
                
            ';
            print '</div>';

            if ($vehicule_cartegrise->date_limite < date('Y-m-d h:i:s')) {
                print ' <div><br>
                            <span class="notif-error">La date limite de cette carte grise est depassee !!</span><br><br>
                            <br>
                            <form action="vehicule_paper.php?id=' . $vehicule_id . '&action=prol_carte_grise" method="POST">
                                <label for="new_date_limit">Nouvelle date limite</label>
                                <input type="date" name="prol-date-carte-grise" id="new_date_limit" required><br><br>
                                <button class="like-btn">Prolonger</button>
                            </form>
                        </div>
                    ';
            }
        }


        ?>
        <form action="<?= 'vehicule_paper.php?id=' . $vehicule_id . '&action=add_carte_grise'  ?>" method="POST" id="form-carte-grise" style="display:none;" class="st-form">
            <h2>Creer une carte grise</h2>
            <label for="immatriculation">Immatriculation</label>
            <input type="text" name="immatriculation" id="immatriculation" value="<?= $vehicule_info->immatriculation ?>" required><br>
            <label for="poids">Poids du vehicule (tonne)</label>
            <input type="text" name="poids" id="poids" required><br>
            <label for="titulaire">Informations du titulaire</label>
            <input type="text" name="titulaire" id="titulaire" required><br>
            <label for="charge">Charge maximale (tonne)</label>
            <input type="text" name="charge" id="charge" required><br>
            <label for="energie">Energie & puissance (Joule)</label>
            <input type="text" name="energie" id="energie" required><br>
            <label for="place">Nombre de places</label>
            <input type="text" name="place" id="place" required><br>
            <label for="taxe">Montant des taxes (FCFA)</label>
            <input type="text" name="taxe" id="taxe" required><br>
            <label for="vin">Numero de VIN</label>
            <input type="text" name="vin" id="vin" required><br>
            <label for="date_limite">Date limite</label>
            <input type="date" name="date_limite" id="date_limite" required><br>
            <label for="date_immatriculation">Date d'immatriculation</label>
            <input type="date" name="date_immatriculation" id="date_immatriculation" required><br><br>
            <button type="submit" class="like-btn">Creer</button>
        </form>
    </div>
    <div id="assurance-container" style="display: none;">
        <div class="st-fiche-1">
            <h2>Liste des assurances</h2>
            <?php
            $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'parcautomobile_assurance WHERE id_vehicule = ' . $vehicule_id;
            $resql = $db->query($sql);
            if ($db->num_rows($resql) == 0) {
                print '<p>Ce vehicule ne possede pas une assurance</p><br>';
                print '
                    <form action="vehicule_paper.php?id=' . $vehicule_id . '&action=add_assurance" method="POST" class="st-form">
                        <h2>Souscrire une assurance</h2>
                        <label for="libelle">Libelle</label>
                        <input type="text" name="libelle" id="libelle" required><br>
                        <label for="company">Entreprise souscrite</label>
                        <input type="text" name="company" id="company" required><br>
                        <label for="category">Categorie assurance</label>
                        <select type="text" name="category" id="category" required>
                            <option value="Assurance tier">Assurance tier</option>
                            <option value="Assurance intermediaire">Assurance intermediaire</option>
                            <option value="Assurance tous risques">Assurance tout risques</option>
                        </select><br>
                        <label for="date-souscription">Date souscription</label>
                        <input type="date" name="date_souscription" id="date-souscription" required><br>
                        <label for="date_limite">Date limite</label>
                        <input type="date" name="date_limite" id="date_limite" required><br><br>
                        <button type="submit" class="like-btn">Souscrire</button>
                    </form>
                    ';
            } else {
                print '
                        <table>
                            <thead>
                                <tr>
                                    <th>Ref</th>
                                    <th>Categorie</th>
                                    <th>Entreprise</th>
                                    <th>Date de subscription</th>
                                    <th>Date expiration</th>
                                </tr>
                            </thead>
                            <tbody>';
                $date_max_ass = new DateTime('2000-01-01 00:00:00.000000');
                while ($item = $db->fetch_object($resql)) {
                    $date3 = new DateTime($item->date_souscription);
                    $date4 = new DateTime($item->date_limite);
                    if ($date4 > $date_max_ass) {
                        $date_max_ass = $date4;
                    }
                    print '
                            <tr>
                                <td>' . $item->ref . '</td>
                                <td>' . $item->category . '</td>
                                <td>' . $item->company . '</td>
                                <td>' . $date3->format('d M Y') . '</td>
                                <td>' . $date4->format('d M Y') . '</td>
                            </tr>
                        
                        ';
                }
                print  '</tbody>
                        </table>
                    ';
                if ($date_max_ass < $dateOfToday) {
                    print '<br><br><span class="notif-error">La date limite de lassurance active est depassee !!</span><br><br>';
                    print '
                        <form action="vehicule_paper.php?id=' . $vehicule_id . '&action=add_assurance" method="POST" class="st-form">
                            <h2>Souscrire une assurance</h2>
                            <label for="libelle">Libelle</label>
                            <input type="text" name="libelle" id="libelle" required><br>
                            <label for="company">Entreprise souscrite</label>
                            <input type="text" name="company" id="company" required><br>
                            <label for="category">Categorie assurance</label>
                            <select type="text" name="category" id="category" required>
                                <option value="Assurance tier">Assurance tier</option>
                                <option value="Assurance intermediaire">Assurance intermediaire</option>
                                <option value="Assurance tous risques">Assurance tout risques</option>
                            </select><br>
                            <label for="date-souscription">Date souscription</label>
                            <input type="date" name="date_souscription" id="date-souscription" required><br>
                            <label for="date_limite">Date limite</label>
                            <input type="date" name="date_limite" id="date_limite" required><br><br>
                            <button type="submit" class="like-btn">Souscrire</button>
                        </form>
                    ';
                }
            }
            ?>
        </div>
    </div>
    <div id="vignette-container" style="display: none;">
        <div class="st-fiche-1">
            <h2>Liste des vignettes</h2>
            <?php
            $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'parcautomobile_vignette WHERE id_vehicule = ' . $vehicule_id;
            $resql = $db->query($sql);
            if ($db->num_rows($resql) == 0) {
                print '<p>Ce vehicule ne possede pas de vignette</p><br>';
                print '
                        <form action="vehicule_paper.php?id=' . $vehicule_id . '&action=add_vignette" method="POST" class="st-form">
                            <h2>Acheter une Vignette</h2>
                            <label for="libelle">Libelle</label>
                            <input type="text" name="libelle" id="libelle" required><br>
                            <label for="company">Entreprise souscrite</label>
                            <input type="text" name="company" id="company" required><br>
                            <label for="year">Annee de validite</label>
                            <select for="year" name="year" required>
                                <option value="2015">2015</option>
                                <option value="2016">2016</option>
                                <option value="2017">2017</option>
                                <option value="2018">2018</option>
                                <option value="2019">2019</option>
                                <option value="2020">2020</option>
                                <option value="2021">2021</option>
                                <option value="2022">2022</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                            <select><br><br>
                            <button type="submit" class="like-btn">Souscrire</button>
                        </form>
                        ';
            } else {
                print '
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ref</th>
                                        <th>Entreprise</th>
                                        <th>Annee de validite</th>
                                    </tr>
                                </thead>
                                <tbody>';
                $date_max_vign = '2000';
                while ($item = $db->fetch_object($resql)) {
                    if ($item->year > $date_max_vign) {
                        $date_max_vign = $item->year;
                    }
                    print '
                                <tr>
                                    <td>' . $item->ref . '</td>
                                    <td>' . $item->company . '</td>
                                    <td>' . $item->year . '</td>
                                </tr>
                            
                            ';
                }
                print  '</tbody>
                            </table>
                        ';
                if ($date_max_vign < date('Y')) {
                    print '<br><br><span class="notif-error">Le vehicule a besoin une nouvelle vignette !!</span><br><br>';
                    print '
                            <form action="vehicule_paper.php?id=' . $vehicule_id . '&action=add_vignette" method="POST" class="st-form">
                                <h2>Souscrire une Vignette</h2>
                                <label for="libelle">Libelle</label>
                                <input type="text" name="libelle" id="libelle" required><br>
                                <label for="company">Entreprise souscrite</label>
                                <input type="text" name="company" id="company" required><br>
                                <label for="year">Annee de validite</label>
                                <select for="year" name="year" required>
                                    <option value="2015">2015</option>
                                    <option value="2016">2016</option>
                                    <option value="2017">2017</option>
                                    <option value="2018">2018</option>
                                    <option value="2019">2019</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                <select><br><br>
                                <button type="submit" class="like-btn">Souscrire</button>
                            </form>
                        ';
                }
            }
            ?>
        </div>
    </div>
    <div id="visite-container" style="display: none;">
        <div class="st-fiche-1">
            <h2>Liste des visites technique</h2>
            <?php
            $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'parcautomobile_visitetechnique WHERE id_vehicule = ' . $vehicule_id;
            $resql = $db->query($sql);
            if ($db->num_rows($resql) == 0) {
                print '<p>Ce vehicule ne possede pas de visite technique</p><br>';
                print '
                        <form class="st-form" action="vehicule_paper.php?id=' . $vehicule_id . '&action=add_visite" method="POST" >
                            <h2>Evaluer le vehicule</h2>
                            <label for="libelle">Libelle</label>
                            <input type="text" name="libelle" id="libelle" required><br>
                            <label for="company">Entreprise</label>
                            <input type="text" name="company" id="company" required><br><br>
                            <label for="rapport">Rapport de la visite</label><br>
                            <Textarea name="rapport" id="rapport" required cols="40" rows="10"></Textarea><br>
                            <label for="date_evaluation">Date evaluation</label>
                            <input type="date" name="date_evaluation" id="date-evaluation" required><br>
                            <label for="date_limite">Date limite</label>
                            <input type="date" name="date_limite" id="date_limite" required><br><br>
                            <button type="submit" class="like-btn">Evaluer</button>
                        </form>
                        ';
            } else {
                print '
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ref</th>
                                        <th>Entreprise</th>
                                        <th>Date evaluation</th>
                                        <th>Date limite</th>
                                        <th>Rapport</th>
                                    </tr>
                                </thead>
                                <tbody>';
                $date_max_visit = new DateTime('2000-01-01 00:00:00.000000');
                while ($item = $db->fetch_object($resql)) {
                    $date5 = new DateTime($item->date_evaluation);
                    $date6 = new DateTime($item->date_limite);
                    if ($date6 > $date_max_visit) {
                        $date_max_visit = $date6;
                    }
                    print '
                                <tr>
                                    <td>' . $item->ref . '</td>
                                    <td>' . $item->company . '</td>
                                    <td>' . $date5->format('d M Y') . '</td>
                                    <td>' . $date6->format('d M Y') . '</td>
                                    <td style="width: 300px;">' . $item->rapport . '</td>
                                </tr>
                            
                            ';
                }
                print  '</tbody>
                            </table>
                        ';
                if ($date_max_visit < $dateOfToday) {
                    print '<br><br><span class="notif-error">Le vehicule a besoin une nouvelle visite technique !!</span><br><br>';
                    print '
                        <form class="st-form" action="vehicule_paper.php?id=' . $vehicule_id . '&action=add_visite" method="POST" >
                            <h2>Evaluer le vehicule</h2>
                            <label for="libelle">Libelle</label>
                            <input type="text" name="libelle" id="libelle" required><br>
                            <label for="company">Entreprise</label>
                            <input type="text" name="company" id="company" required><br><br>
                            <label for="rapport">Rapport de la visite</label><br>
                            <Textarea name="rapport" id="rapport" required cols="40" rows="10"></Textarea><br>
                            <label for="date_evaluation">Date evaluation</label>
                            <input type="date" name="date_evaluation" id="date-evaluation" required><br>
                            <label for="date_limite">Date limite</label>
                            <input type="date" name="date_limite" id="date_limite" required><br><br>
                            <button type="submit" class="like-btn">Evaluer</button>
                        </form>
                        ';
                }
            }
            ?>
        </div>
    </div>
    <div id="disque-container" style="display: none;">
        <div class="st-fiche-1">
            <h2>Liste des entretiens et des defaillances</h2>
            <?php
            $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'parcautomobile_cartedisque WHERE id_vehicule = ' . $vehicule_id . ' ORDER BY date DESC';
            $resql = $db->query($sql);
            if ($db->num_rows($resql) == 0) {
                print '<p>Aucun reparation ou defaillance signale</p><br>';
            } else {
                print ' <table>
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Etat actuel</th>
                                    <th>Rapport</th>
                                    <th>Date</th>
                                </tr>
                            </thead> 
                            <tbody>';
                while ($item = $db->fetch_object($resql)) {
                    if (isset($_GET['id_show'])) {
                        if ($_GET['id_show'] == $item->rowid) {
                            print   '<tr style="border: 1px dashed black">';
                        } else {
                            print   '<tr>';
                        }
                    } else {
                        print   '<tr>';
                    }
                    $date7 = new DateTime($item->date);
                    if ($item->status == 'not visible') {
                        if ($item->type == 'defaillance') {
                            print '<td> Defaillance (Regle)</td>';
                        } else {
                            print '<td> Entretien (Vu)</td>';
                        }
                    } else {
                        print '<td>' . $item->type . '</td>';
                    }
                    print              '<td>' . setEtat($item->etat) . '</td>
                                        <td>' . $item->rapport . '</td>
                                        <td>' . $date7->format('d M Y') . '</td>
                                    </tr>';
                }
                print '</tbody></table>';
            }
            ?>
            <form action="vehicule_paper.php?id=<?= $vehicule_id ?>&action=add_entretien" method="POST" class="st-form">
                <h2>Enregistrer une reparation ou defaillance</h2>
                <label for="type">Type</label>
                <select name="type" id="type" required>
                    <option value="reparation">Entretien</option>
                    <option value="defaillance">defaillance</option>
                </select><br><br>
                <label for="extincteur">Extincteur</label>
                <select name="extincteur" id="extincteur" required>
                    <option value="0">OUI</option>
                    <option value="1">NON</option>
                </select><br><br>
                <label for="etat">Etat</label>
                <select name="etat" id="etat" required>
                    <option value="1">En reparation</option>
                    <option value="2">Endommage</option>
                    <option value="3">Bonne etat</option>
                    <option value="4">Detruite</option>
                </select><br><br>
                <label for="rapport">Rapport de la visite</label><br>
                <Textarea name="rapport" id="rapport" required cols="40" rows="10"></Textarea><br><br>
                <button type="submit" class="like-btn">Enregistrer</button>
            </form>
        </div>
    </div>
    <div id="license_transport-container" style="display: none;">
        <?php
        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'parcautomobile_cartegrise WHERE id_vehicule = ' . $vehicule_id;
        $resql = $db->query($sql);
        if ($db->num_rows($resql) == 0) {
            print '<p>Ce vehicule ne possede pas de carte grise</p><br>';
            print '<button class="like-btn" id="btn-show-form-carte-grise">Creer une carte grise</button>';
        } else {
            $vehicule_cartegrise = $vehicule->fetchCarteGrise($vehicule_id);
            $date1 = new DateTime($vehicule_cartegrise->date_limite);
            $date2 = new DateTime($vehicule_cartegrise->date_immatriculation);
            print '<div>
                <h2>Informations de la carte grise </h2>';
            print '
                <p><label>Numero immatriculation</label> : ' . $vehicule_cartegrise->immatriculation . '</p>
                <p><label>Numero VIN</label> : ' . $vehicule_cartegrise->vin . '</p>
                <p><label>Informations du titulaire</label> : ' . $vehicule_cartegrise->titulaire . '</p>
                <p><label>Montant des taxes de la carte</label> : ' . $vehicule_cartegrise->taxe . ' FCFA</p>
                <p><label>Poids du vehicule</label> : ' . $vehicule_cartegrise->poids . ' tonnes</p>
                <p><label>Charge maximale</label> : ' . $vehicule_cartegrise->charge_max . ' tonnes</p>
                <p><label>Energie / Puissance</label> : ' . $vehicule_cartegrise->energie . ' Joule</p>
                <p><label>Nombre de place</label> : ' . $vehicule_cartegrise->place . '</p>
                <p><label>Date limite de validite</label> : ' . $date1->format('d M Y') . '</p>
                <p><label>Date immatriculation</label> : ' . $date2->format('d M Y') . '</p>
                
            ';
            print '</div>';

            if ($vehicule_cartegrise->date_limite < date('Y-m-d h:i:s')) {
                print ' <div><br>
                            <span class="notif-error">La date limite de cette carte grise est depassee !!</span><br><br>
                            <br>
                            <form action="vehicule_paper.php?id=' . $vehicule_id . '&action=prol_carte_grise" method="POST">
                                <label for="new_date_limit">Nouvelle date limite</label>
                                <input type="date" name="prol-date-carte-grise" id="new_date_limit" required><br><br>
                                <button class="like-btn">Prolonger</button>
                            </form>
                        </div>
                    ';
            }
        }


        ?>
        <form action="<?= 'vehicule_paper.php?id=' . $vehicule_id . '&action=add_carte_grise'  ?>" method="POST" id="form-carte-grise" style="display:none;" class="st-form">
            <h2>Creer une carte grise</h2>
            <label for="immatriculation">Immatriculation</label>
            <input type="text" name="immatriculation" id="immatriculation" value="<?= $vehicule_info->immatriculation ?>" required><br>
            <label for="poids">Poids du vehicule (tonne)</label>
            <input type="text" name="poids" id="poids" required><br>
            <label for="titulaire">Informations du titulaire</label>
            <input type="text" name="titulaire" id="titulaire" required><br>
            <label for="charge">Charge maximale (tonne)</label>
            <input type="text" name="charge" id="charge" required><br>
            <label for="energie">Energie & puissance (Joule)</label>
            <input type="text" name="energie" id="energie" required><br>
            <label for="place">Nombre de places</label>
            <input type="text" name="place" id="place" required><br>
            <label for="taxe">Montant des taxes (FCFA)</label>
            <input type="text" name="taxe" id="taxe" required><br>
            <label for="vin">Numero de VIN</label>
            <input type="text" name="vin" id="vin" required><br>
            <label for="date_limite">Date limite</label>
            <input type="date" name="date_limite" id="date_limite" required><br>
            <label for="date_immatriculation">Date d'immatriculation</label>
            <input type="date" name="date_immatriculation" id="date_immatriculation" required><br><br>
            <button type="submit" class="like-btn">Creer</button>
        </form>

    </div>

</div>

<style>
    .st-form {
        border-bottom: 1px solid black;
        border-right: 1px solid black;
        border-left: 1px solid black;
        border-top: 4px solid #263C5C;
        padding-left: 20px;
        padding-bottom: 10px;
    }

    label {
        font-size: 15px;
        font-weight: bold;
    }

    .st-fiche-1 {
        width: 90%;
    }

    .a-btn {
        font-size: 0.8rem;
        background-color: black;
        padding: 5px 10px;
        border-radius: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background-color: #263C5C;
        color: white;
        font-size: 15px;
        font-weight: bold;
        margin: 0;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    td,
    th {
        padding: 5px 0px;
        text-align: center;
        margin: 0;
    }

    #grise-container {
        display: grid;
        grid-template-columns: 40% 60%;
    }

    .notif-error {
        background-color: red;
        color: white;
        border-radius: 5px;
        padding: 10px;
        font-size: 1.05rem;
    }

    .like-btn {
        padding: 10px 15px;
        color: white;
        border-radius: 5px;
        background-color: #9B75A7;
        cursor: pointer;
        border: none;
        font-size: 1rem;
        font-weight: 500;
    }

    h2 {
        color: #263C5C;
        font-weight: 200;

    }

    .custom-nav {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        gap: 5px;
        border-bottom: 1px solid black;
        list-style-type: none;
        padding-left: 3px;
    }

    .custom-nav li {
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        padding: 5px 10px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        transition: all 0.5s;
    }

    .custom-nav li:hover {
        color: #263C5C;
    }

    .custom-nav-active {
        color: #263C5C;
        border-top: 1px solid black;
        border-left: 1px solid black;
        border-right: 1px solid black;
    }
</style>

<script>
    const grise = document.getElementById('grise');
    const assurance = document.getElementById('assurance');
    const vignette = document.getElementById('vignette');
    const disque = document.getElementById('disque');
    const visite = document.getElementById('visite');
    const license_transport = document.getElementById('license_transport');
    const griseContainer = document.getElementById('grise-container');
    const assuranceContainer = document.getElementById('assurance-container');
    const vignetteContainer = document.getElementById('vignette-container');
    const disqueContainer = document.getElementById('disque-container');
    const visiteContainer = document.getElementById('visite-container');
    const licenseTransportContainer = document.getElementById('license_transport-container');

    grise.addEventListener("click", () => {
        grise.classList.add('custom-nav-active');
        assurance.classList.remove('custom-nav-active');
        vignette.classList.remove('custom-nav-active');
        disque.classList.remove('custom-nav-active');
        visite.classList.remove('custom-nav-active');
        griseContainer.style.display = "block";
        assuranceContainer.style.display = "none";
        vignetteContainer.style.display = "none";
        disqueContainer.style.display = "none";
        visiteContainer.style.display = "none";

    });
    assurance.addEventListener("click", () => {
        grise.classList.remove('custom-nav-active');
        assurance.classList.add('custom-nav-active');
        vignette.classList.remove('custom-nav-active');
        disque.classList.remove('custom-nav-active');
        visite.classList.remove('custom-nav-active');
        griseContainer.style.display = "none";
        assuranceContainer.style.display = "block";
        vignetteContainer.style.display = "none";
        disqueContainer.style.display = "none";
        visiteContainer.style.display = "none";
    });
    vignette.addEventListener("click", () => {
        grise.classList.remove('custom-nav-active');
        assurance.classList.remove('custom-nav-active');
        vignette.classList.add('custom-nav-active');
        disque.classList.remove('custom-nav-active');
        visite.classList.remove('custom-nav-active');
        griseContainer.style.display = "none";
        assuranceContainer.style.display = "none";
        vignetteContainer.style.display = "block";
        disqueContainer.style.display = "none";
        visiteContainer.style.display = "none";
    });
    disque.addEventListener("click", () => {
        grise.classList.remove('custom-nav-active');
        assurance.classList.remove('custom-nav-active');
        vignette.classList.remove('custom-nav-active');
        disque.classList.add('custom-nav-active');
        visite.classList.remove('custom-nav-active');
        griseContainer.style.display = "none";
        assuranceContainer.style.display = "none";
        vignetteContainer.style.display = "none";
        disqueContainer.style.display = "block";
        visiteContainer.style.display = "none";
    });
    visite.addEventListener("click", () => {
        grise.classList.remove('custom-nav-active');
        assurance.classList.remove('custom-nav-active');
        vignette.classList.remove('custom-nav-active');
        disque.classList.remove('custom-nav-active');
        visite.classList.add('custom-nav-active');
        griseContainer.style.display = "none";
        assuranceContainer.style.display = "none";
        vignetteContainer.style.display = "none";
        disqueContainer.style.display = "none";
        visiteContainer.style.display = "block";
    });
    license_transport.addEventListener("click", () => {
        grise.classList.remove('custom-nav-active');
        assurance.classList.remove('custom-nav-active');
        vignette.classList.remove('custom-nav-active');
        disque.classList.remove('custom-nav-active');
        visite.classList.remove('custom-nav-active');
        licenseTransportContainer.classList.add('custom-nav-active');
        griseContainer.style.display = "none";
        assuranceContainer.style.display = "none";
        vignetteContainer.style.display = "none";
        disqueContainer.style.display = "none";
        visiteContainer.style.display = "none";
        licenseTransportContainer.style.display = "block";
    });

    const formCarteGrise = document.getElementById('form-carte-grise');
    const btnShowCarteGrise = document.getElementById('btn-show-form-carte-grise');

    btnShowCarteGrise.addEventListener("click", () => {
        formCarteGrise.style.display = 'block';
        btnShowCarteGrise.style.display = 'none';
    });
</script>
<?php

// End of page
llxFooter();
$db->close();
