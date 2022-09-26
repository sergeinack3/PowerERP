<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <year>  <name of author>
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
 * \file    admin/setup.php
 * \ingroup chat
 * \brief   Example module setup page.
 *
 * Put detailed description here.
 */

// Load Powererp environment
if (false === (@include '../../main.inc.php')) {  // From htdocs directory
	require '../../../main.inc.php'; // From "custom" directory
}

global $db, $langs, $user, $conf, $bc;

// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once '../lib/chat.lib.php';

// Translations
$langs->load("admin");
$langs->load("chat@chat");

// Access control
if (! $user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action','alpha');
$value = GETPOST('value','alpha');

/*
 * Actions
 */

if ($action == 'set_CHAT_AUTO_REFRESH_TIME')
{
        $error = 0;
        
	$auto_refresh_time = GETPOST('value_CHAT_AUTO_REFRESH_TIME', 'int');

        if (! empty($auto_refresh_time) && $auto_refresh_time > 0)
        {
            $res = powererp_set_const($db, "CHAT_AUTO_REFRESH_TIME",$auto_refresh_time,'chaine',0,'',$conf->entity);
            if (! $res > 0) $error++;
        }
        else
        {
            $error++;
        }

        if (! $error)
        {
            setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
        }
        else
        {
            setEventMessages($langs->trans("Error"), null, 'errors');
        }
}

// set max shown messages number
else if ($action == 'set_CHAT_MAX_MSG_NUMBER')
{
        $error = 0;
        
	$max_msg_number = GETPOST('value_CHAT_MAX_MSG_NUMBER', 'int');

        if (! empty($max_msg_number) && $max_msg_number > 0)
        {
            $res = powererp_set_const($db, "CHAT_MAX_MSG_NUMBER",$max_msg_number,'chaine',0,'',$conf->entity);
            if (! $res > 0) $error++;
        }
        else
        {
            $error++;
        }

        if (! $error)
        {
            setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
        }
        else
        {
            setEventMessages($langs->trans("Error"), null, 'errors');
        }
}

// set show images preview constant
else if ($action == 'set_CHAT_SHOW_IMAGES_PREVIEW')
{
    $res = powererp_set_const($db, "CHAT_SHOW_IMAGES_PREVIEW",$value,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        //setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set hide private messages in open room
else if ($action == 'set_CHAT_HIDE_PRIVATE_MSG_IN_OPEN_ROOM')
{
    $res = powererp_set_const($db, "CHAT_HIDE_PRIVATE_MSG_IN_OPEN_ROOM",$value,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        //setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set attachments security constant
else if ($action == 'set_MAIN_DOCUMENT_IS_OUTSIDE_WEBROOT_SO_NOEXE_NOT_REQUIRED')
{
    $res = powererp_set_const($db, "MAIN_DOCUMENT_IS_OUTSIDE_WEBROOT_SO_NOEXE_NOT_REQUIRED",$value,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        //setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set enable chat popup constant
else if ($action == 'set_CHAT_ENABLE_POPUP')
{
    $res = powererp_set_const($db, "CHAT_ENABLE_POPUP",$value,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        //setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set chat popup use minimum space constant
else if ($action == 'set_CHAT_POPUP_USE_MINIMUM_SPACE')
{
    $res = powererp_set_const($db, "CHAT_POPUP_USE_MINIMUM_SPACE",$value,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        //setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set chat popup size constant
else if ($action == 'set_CHAT_POPUP_SIZE')
{
    $length = strlen($value) - 1;
    $size = $value[$length] == '%' ? $value : $value.'%';
    $res = powererp_set_const($db, "CHAT_POPUP_SIZE",$size,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set chat popup use theme color constant
else if ($action == 'set_CHAT_POPUP_USE_THEME_COLOR')
{
    $res = powererp_set_const($db, "CHAT_POPUP_USE_THEME_COLOR",$value,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set chat popup background color constant
else if ($action == 'set_CHAT_POPUP_BACKGROUND_COLOR')
{
    $value = GETPOST('CHAT_POPUP_BACKGROUND_COLOR', 'alpha');
    $color = $value[0] == '#' ? $value : '#'.$value;
    $res = powererp_set_const($db, "CHAT_POPUP_BACKGROUND_COLOR",$color,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set chat popup text color constant
else if ($action == 'set_CHAT_POPUP_TEXT_COLOR')
{
    $res = powererp_set_const($db, "CHAT_POPUP_TEXT_COLOR",$value,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set chat popup counter color constant
else if ($action == 'set_CHAT_POPUP_COUNTER_COLOR')
{
    $value = GETPOST('CHAT_POPUP_COUNTER_COLOR', 'alpha');
    $color = $value[0] == '#' ? $value : '#'.$value;
    $res = powererp_set_const($db, "CHAT_POPUP_COUNTER_COLOR",$color,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set chat private message(s) border color constant
else if ($action == 'set_CHAT_PRIVATE_MSG_BORDER_COLOR')
{
    $value = GETPOST('CHAT_PRIVATE_MSG_BORDER_COLOR', 'alpha');
    $color = $value[0] == '#' ? $value : '#'.$value;
    $res = powererp_set_const($db, "CHAT_PRIVATE_MSG_BORDER_COLOR",$color,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set chat private message(s) background color constant
else if ($action == 'set_CHAT_PRIVATE_MSG_BACKGROUND_COLOR')
{
    $value = GETPOST('CHAT_PRIVATE_MSG_BACKGROUND_COLOR', 'alpha');
    $color = $value[0] == '#' ? $value : '#'.$value;
    $res = powererp_set_const($db, "CHAT_PRIVATE_MSG_BACKGROUND_COLOR",$color,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// set visibility of chat main menu
if ($action == 'set_REPLACE_CHAT_MAIN_MENU_WITH_TOP_RIGHT_SHORTCUT')
{
    global $menumanager;
    
    $res = powererp_set_const($db, "REPLACE_CHAT_MAIN_MENU_WITH_TOP_RIGHT_SHORTCUT",$value,'chaine',0,'',$conf->entity);
    
    if (! $res > 0) $error++;
    
    if (! $error)
    {
        $menumanager->loadMenu(); // reload menu to apply
        //setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

// pull left the chat popup
else if ($action == 'set_CHAT_PULL_LEFT_POPUP')
{
    $res = powererp_set_const($db, "CHAT_PULL_LEFT_POPUP",$value,'chaine',0,'',$conf->entity);

    if (! $res > 0) $error++;

    if (! $error)
    {
        //setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    }
    else
    {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

/*
 * View
 */

$page_name = "ChatSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">'
	. $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = chatAdminPrepareHead();
dol_fiche_head(
	$head,
	'settings',
	$langs->trans("Module452000Name"),
	0,
	"chat@chat"
);

// Setup page goes here

/*
 * Chat config.
 */

print load_fiche_titre($langs->trans("ChatConf"));

$var=true;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center" width="300">'.$langs->trans("Value").'</td>'."\n";
print '</tr>'."\n";

// auto refresh time
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("ChatAutoRefreshTime").'</td>';
print '<td align="right">';
print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="set_CHAT_AUTO_REFRESH_TIME" />';
print '<input size="3" type="text" class="flat" name="value_CHAT_AUTO_REFRESH_TIME" value="'.$conf->global->CHAT_AUTO_REFRESH_TIME.'">';
print '&nbsp;&nbsp;&nbsp;';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td>';
print '</tr>';

// max messages number
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("ChatMaxMsgNumber").'</td>';
print '<td align="right">';
print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="set_CHAT_MAX_MSG_NUMBER" />';
print '<input size="3" type="text" class="flat" name="value_CHAT_MAX_MSG_NUMBER" value="'.$conf->global->CHAT_MAX_MSG_NUMBER.'">';
print '&nbsp;&nbsp;&nbsp;';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td>';
print '</tr>';

// show images preview
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("ShowImagesPreview").'</td><td align="right">';
if (empty($conf->global->CHAT_SHOW_IMAGES_PREVIEW))
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_SHOW_IMAGES_PREVIEW&amp;value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
}
else
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_SHOW_IMAGES_PREVIEW&amp;value=0">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
}
print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td></tr>';

// replace chat main menu with top right shortcut
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("ReplaceChatMainMenu").'</td><td align="right">';
if (empty($conf->global->REPLACE_CHAT_MAIN_MENU_WITH_TOP_RIGHT_SHORTCUT))
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_REPLACE_CHAT_MAIN_MENU_WITH_TOP_RIGHT_SHORTCUT&amp;value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
}
else
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_REPLACE_CHAT_MAIN_MENU_WITH_TOP_RIGHT_SHORTCUT&amp;value=0">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
}
print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td></tr>';

// enable/disable attachments security
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("AttachmentsSecurity").'</td><td align="right">';
if (empty($conf->global->MAIN_DOCUMENT_IS_OUTSIDE_WEBROOT_SO_NOEXE_NOT_REQUIRED))
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MAIN_DOCUMENT_IS_OUTSIDE_WEBROOT_SO_NOEXE_NOT_REQUIRED&amp;value=1">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
}
else
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_MAIN_DOCUMENT_IS_OUTSIDE_WEBROOT_SO_NOEXE_NOT_REQUIRED&amp;value=0">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
}
print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td></tr>';

// hide private messages in open room
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("HidePrivateMessagesInOpenRoom").'</td><td align="right">';
if (empty($conf->global->CHAT_HIDE_PRIVATE_MSG_IN_OPEN_ROOM))
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_HIDE_PRIVATE_MSG_IN_OPEN_ROOM&amp;value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
}
else
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_HIDE_PRIVATE_MSG_IN_OPEN_ROOM&amp;value=0">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
}
print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td></tr>';

print '</table>';

/*
 * Chat popup config.
 */

$formother = new FormOther($db);

print load_fiche_titre($langs->trans("ChatPopupConf"));

$var=true;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td>'."\n";
print '<td align="center">'.$langs->trans("Value").'</td>'."\n";
print '</tr>'."\n";

// enable chat popup
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("EnableChatPopup").'</td><td align="right">';
if (empty($conf->global->CHAT_ENABLE_POPUP))
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_ENABLE_POPUP&amp;value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
}
else
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_ENABLE_POPUP&amp;value=0">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
}
print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td></tr>';

if ($conf->global->CHAT_ENABLE_POPUP) {

// pull left the chat popup
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("PullLeftChatPopup").'</td><td align="right">';
if (empty($conf->global->CHAT_PULL_LEFT_POPUP))
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_PULL_LEFT_POPUP&amp;value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
}
else
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_PULL_LEFT_POPUP&amp;value=0">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
}
print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td></tr>';

// use the minimum of available space
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("UseTheMinimumSpace").'</td><td align="right">';
if (empty($conf->global->CHAT_POPUP_USE_MINIMUM_SPACE))
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_POPUP_USE_MINIMUM_SPACE&amp;value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
}
else
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_POPUP_USE_MINIMUM_SPACE&amp;value=0">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
}
print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td></tr>';

// popup size
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("ChatPopupSize").'</td>';
print '<td align="right">';
print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="set_CHAT_POPUP_SIZE" />';
print '<input type="range" class="flat" name="value" style="vertical-align: middle;" min="22" max="50" value="'.str_replace('%', '', $conf->global->CHAT_POPUP_SIZE).'" />';
print '&nbsp;&nbsp;&nbsp;';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td>';
print '</tr>';

// use theme color
$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("UseThemeColor").'</td><td align="right">';
if (empty($conf->global->CHAT_POPUP_USE_THEME_COLOR))
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_POPUP_USE_THEME_COLOR&amp;value=1">'.img_picto($langs->trans("Disabled"),'switch_off').'</a>';
}
else
{
    print '<a href="'.$_SERVER['PHP_SELF'].'?action=set_CHAT_POPUP_USE_THEME_COLOR&amp;value=0">'.img_picto($langs->trans("Enabled"),'switch_on').'</a>';
}
print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
print '</td></tr>';

if (empty($conf->global->CHAT_POPUP_USE_THEME_COLOR))
{
    // popup background color
    $var=!$var;
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans("ChatPopupBackgroundColor").'</td>';
    print '<td align="right">';
    print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
    print '<input type="hidden" name="action" value="set_CHAT_POPUP_BACKGROUND_COLOR" />';
    print $formother->selectColor(colorArrayToHex(colorStringToArray(str_replace('#', '', $conf->global->CHAT_POPUP_BACKGROUND_COLOR),array()),''),'CHAT_POPUP_BACKGROUND_COLOR','formcolor',1);
    print '&nbsp;&nbsp;&nbsp;';
    print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
    print '</form>';
    print '</td>';
    print '</tr>';
}

// popup text color
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("ChatPopupTextColor").'</td>';
print '<td align="right">';
print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="set_CHAT_POPUP_TEXT_COLOR" />';
print '<span id="popup-text-color-preview" style="display: inline-block; padding: 10px; border: 2px solid #ddd; border-radius: 50%; vertical-align: middle; margin-right: 10px; background: '.$conf->global->CHAT_POPUP_TEXT_COLOR.';"></span>';
print '<select class="flat" name="value" onchange="$(\'#popup-text-color-preview\').css(\'background\', $(this).val());">';
print '<option value="#fff"'.($conf->global->CHAT_POPUP_TEXT_COLOR == '#fff' ? ' selected' : '').'>'.$langs->trans("White").'</option>';
print '<option value="#333"'.($conf->global->CHAT_POPUP_TEXT_COLOR == '#333' ? ' selected' : '').'>'.$langs->trans("Black").'</option>';
print '</select>';
print '&nbsp;&nbsp;&nbsp;';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td>';
print '</tr>';

// popup counter color
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("ChatPopupCounterColor").'</td>';
print '<td align="right">';
print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="set_CHAT_POPUP_COUNTER_COLOR" />';
print $formother->selectColor(colorArrayToHex(colorStringToArray(str_replace('#', '', $conf->global->CHAT_POPUP_COUNTER_COLOR),array()),''),'CHAT_POPUP_COUNTER_COLOR','formcolor',1);
print '&nbsp;&nbsp;&nbsp;';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td>';
print '</tr>';

// private message border color
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("ChatPrivateMessageBorderColor").'</td>';
print '<td align="right">';
print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="set_CHAT_PRIVATE_MSG_BORDER_COLOR" />';
print $formother->selectColor(colorArrayToHex(colorStringToArray(str_replace('#', '', $conf->global->CHAT_PRIVATE_MSG_BORDER_COLOR),array()),''),'CHAT_PRIVATE_MSG_BORDER_COLOR','formcolor',1);
print '&nbsp;&nbsp;&nbsp;';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td>';
print '</tr>';

// private message background color
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans("ChatPrivateMessageBackgroundColor").'</td>';
print '<td align="right">';
print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'" />';
print '<input type="hidden" name="action" value="set_CHAT_PRIVATE_MSG_BACKGROUND_COLOR" />';
print $formother->selectColor(colorArrayToHex(colorStringToArray(str_replace('#', '', $conf->global->CHAT_PRIVATE_MSG_BACKGROUND_COLOR),array()),''),'CHAT_PRIVATE_MSG_BACKGROUND_COLOR','formcolor',1);
print '&nbsp;&nbsp;&nbsp;';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print '</td>';
print '</tr>';

} // end of if ($conf->global->CHAT_ENABLE_POPUP)

print '</table>';

echo '<br>'.info_admin($langs->trans('ChatPopupNotice'));

// Page end
dol_fiche_end();
llxFooter();

$db->close();
