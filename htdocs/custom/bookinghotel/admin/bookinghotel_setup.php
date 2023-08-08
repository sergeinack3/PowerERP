<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	\file		admin/bookinghotel.php
 * 	\ingroup	bookinghotel
 * 	\brief		This file is an example module setup page
 * 				Put some comments here
 */
// powererp environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}
die();
// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once '../lib/bookinghotel.lib.php';

// Translations
$langs->load("bookinghotel@bookinghotel");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */
if (preg_match('/set_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (powererp_set_const($db, $code, GETPOST($code), 'chaine', 0, '', $conf->entity) > 0)
	{
		header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}
	
if (preg_match('/del_(.*)/',$action,$reg))
{
	$code=$reg[1];
	if (powererp_del_const($db, $code, 0) > 0)
	{
		Header("Location: ".$_SERVER["PHP_SELF"]);
		exit;
	}
	else
	{
		dol_print_error($db);
	}
}

/*
 * View
 */
$page_name = "bookinghotelSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
    . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = bookinghotelAdminPrepareHead();
dol_fiche_head(
    $head,
    'settings',
    $langs->trans("ModuleName"),
    0,
    "bookinghotel@bookinghotel"
);



// Setup page goes here
$form=new Form($db);
$var=false;
print '<table class="noborder" width="100%">';

dol_include_once('bookinghotel/www/class/context.class.php');
$context = Context::getInstance();
//$context = new Context();
_print_input_form_part('BOOKINGHOTEL_ROOT_URL',false,'',array('placeholder'=>'http://'),'input','BOOKINGHOTEL_ROOT_URL_HELP');
print '<tr>';
print '<td colspan="3" ><a target="_blank" href="'.$context->getRootUrl().'" ><i class="fa fa-arrow-right" ></i> '.$langs->trans('AccessToCustomerGate').'</a></td>'."\n";
print '</tr>';
_print_input_form_part('BOOKINGHOTEL_TITLE',false,'',array(),'input','BOOKINGHOTEL_TITLE_HELP');
_print_input_form_part('BOOKINGHOTEL_GOBACK_URL',false,'',array(),'input','BOOKINGHOTEL_GOBACK_URL_HELP');
_print_input_form_part('BOOKINGHOTEL_PHONE');
_print_input_form_part('BOOKINGHOTEL_EMAIL',false,'',array(),'input','BOOKINGHOTEL_EMAIL_HELP');


_print_input_form_part('BOOKINGHOTEL_PRIMARY_COLOR', false, '', array('type'=>'color'),'input','BOOKINGHOTEL_PRIMARY_COLOR_HELP');
_print_input_form_part('BOOKINGHOTEL_HEADER_IMG',false,'',array('placeholder'=>'http://'),'input','BOOKINGHOTEL_HEADER_IMG_HELP');

_print_title('BOOKINGHOTEL_ACTIVATE_MODULES');
_print_on_off('BOOKINGHOTEL_ACTIVATE_INVOICES',false, 'BOOKINGHOTEL_need_some_rights');

_print_on_off('BOOKINGHOTEL_ACTIVATE_PROPALS',false, 'BOOKINGHOTEL_need_some_rights');
_print_on_off('BOOKINGHOTEL_ACTIVATE_ORDERS',false, 'BOOKINGHOTEL_need_some_rights');
//_print_on_off('BOOKINGHOTEL_ACTIVATE_EXPEDITIONS');
//_print_on_off('BOOKINGHOTEL_ACTIVATE_FORMATIONS');


_print_input_form_part('BOOKINGHOTEL_LOGIN_EXTRA_HTML',false,'',array(),'textarea');
if(empty($conf->global->BOOKINGHOTEL_RGPD_MSG)){
    powererp_set_const($db,'BOOKINGHOTEL_RGPD_MSG',$langs->trans('BOOKINGHOTEL_RGPD_MSG_default',$conf->global->MAIN_INFO_SOCIETE_NOM), 'chaine', 0, '', $conf->entity) ;
}
_print_input_form_part('BOOKINGHOTEL_RGPD_MSG',false,'',array(),'textarea');


print '</table>';

llxFooter();

$db->close();


function _print_title($title="")
{
    global $langs;
    print '<tr class="liste_titre">';
    print '<td>'.$langs->trans($title).'</td>'."\n";
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="center" ></td>'."\n";
    print '</tr>';
}

function _print_on_off($confkey, $title = false, $desc ='')
{
    global $var, $bc, $langs, $conf;
    $var=!$var;
    
    print '<tr '.$bc[$var].'>';
    print '<td>'.($title?$title:$langs->trans($confkey));
    if(!empty($desc))
    {
        print '<br><small>'.$langs->trans($desc).'</small>';
    }
    print '</td>';
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="center" width="300">';
    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="set_'.$confkey.'">';
    print ajax_constantonoff($confkey);
    print '</form>';
    print '</td></tr>';
}

function _print_input_form_part($confkey, $title = false, $desc ='', $metas = array(), $type='input', $help = false)
{
    global $var, $bc, $langs, $conf, $db;
    $var=!$var;
    
    $form=new Form($db);
    
    $defaultMetas = array(
        'name' => $confkey
    );
    
    if($type!='textarea'){
        $defaultMetas['type']   = 'text';
        $defaultMetas['value']  = $conf->global->{$confkey};
    }
    
    
    $metas = array_merge ($defaultMetas, $metas);
    $metascompil = '';
    foreach ($metas as $key => $values)
    {
        $metascompil .= ' '.$key.'="'.$values.'" ';
    }
    
    print '<tr '.$bc[$var].'>';
    print '<td>';
    
    if(!empty($help)){
        print $form->textwithtooltip( ($title?$title:$langs->trans($confkey)) , $langs->trans($help),2,1,img_help(1,''));
    }
    else {
        print $title?$title:$langs->trans($confkey);
    }
    
    if(!empty($desc))
    {
        print '<br><small>'.$langs->trans($desc).'</small>';
    }
    
    print '</td>';
    print '<td align="center" width="20">&nbsp;</td>';
    print '<td align="right" width="300">';
    print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="set_'.$confkey.'">';
    if($type=='textarea'){
        print '<textarea '.$metascompil.'  >'.dol_htmlentities($conf->global->{$confkey}).'</textarea>';
    }
    else {
        print '<input '.$metascompil.'  />';
    }
    
    print '<input type="submit" class="butAction" value="'.$langs->trans("Modify").'">';
    print '</form>';
    print '</td></tr>';
}
