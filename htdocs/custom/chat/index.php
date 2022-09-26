<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <31/12/2016 - 03/01/2017>  <AXeL dev - contact.axel.dev@gmail.com>
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
 * \file    index.php
 * \ingroup chat
 * \brief   Example PHP page.
 *
 * Put detailed description here.
 */

if (! defined('REQUIRE_JQUERY_LAYOUT'))  define('REQUIRE_JQUERY_LAYOUT','1');
//if (! defined('REQUIRE_JQUERY_BLOCKUI')) define('REQUIRE_JQUERY_BLOCKUI', 1);

$res = @include ("../main.inc.php"); // For root directory
if (! $res)
	$res = @include ("../../main.inc.php"); // For "custom" directory
if (! $res)
	die("Include of main fails");

dol_include_once('/chat/class/chat.class.php');
dol_include_once('/chat/lib/chat.lib.php');

global $db, $langs, $user, $conf;

// Load translation files required by the page
$langs->load("chat@chat");

// Get parameters
$action = GETPOST('action', 'alpha');

$text = GETPOST('text', 'alpha');
$user_to_id = GETPOST('user_to_id', 'int');
$filter_user = GETPOST('filter_user', 'alpha');

$msg_id = GETPOST('msg_id', 'alpha');
$enter_to_send = GETPOST('enter_to_send', 'alpha');
$sort_by_date = GETPOST('sort_by_date', 'alpha');

// Access control
if (! $user->rights->chat->lire) {
	// External user
	accessforbidden();
}

// global vars
$userstatic = new User($db);

// Load object
$object = new Chat($db);

/*
 * ACTIONS
 *
 * Put here all code to do according to value of "action" parameter
 */

if ($action == 'send') {

        if (empty($text) && empty($_FILES['attachment']['name']))
        {
            setEventMessage($langs->trans("PleaseTypeAMessage"), 'warnings');
        }
        else if (! empty($user_to_id) && $user_to_id == $user->id)
        {
            setEventMessage($langs->trans("CannotSendMessageToYourself"), 'warnings');
        }
        else
        {
            $now = dol_now();

            $myobject = new ChatMessage($db);
            $myobject->post_time = $now;
            $myobject->text = $text;
            $myobject->fk_user_to = $user_to_id;

            $attachment_id = 0;

            if (!empty($_FILES['attachment']['name'])) {
                    $attachment = new ChatMessageAttachment($db);
                    // on ajoute la date au nom du fichier pour différencier entre les fichiers qui ont le même nom et éviter que l'un écrase l'autre
                    $attachment->name = dol_sanitizeFileName($now.'_'.$_FILES['attachment']['name']);
                    $attachment->type = $_FILES['attachment']['type'];
                    $attachment->size = $_FILES['attachment']['size'];
                    $attachment_id = $attachment->add($user);
            }

            $myobject->fk_attachment = $attachment_id;

            $result = $myobject->send($user);
            if ($result > 0) {
                    // Creation OK
            } else {
                    // Creation KO
                    $mesg = $myobject->error;
                    //dol_print_error($db);
                    setEventMessages($mesg, $myobject->errors, 'errors');
            }
        }

        $action = empty($user_to_id) ? "" : "private_msg";
}

else if ($action == 'delete') {
    if (! empty($msg_id))
    {
        $error = 0;

        $myobject = new ChatMessage($db);

        foreach (explode(',', $msg_id) as $m_id)
        {
            if (is_numeric($m_id))
            {
                $result = $myobject->fetch($m_id);

                if ($result > 0)
                {
                    $is_mine = $myobject->fk_user == $user->id;

                    if ($user->rights->chat->delete->all || ($is_mine && $user->rights->chat->delete->mine))
                    {
                        // we delete message
                        $result = $myobject->delete($user);

                        if ($result > 0) {
                                // Delete OK
                        } else {
                                $error ++;
                                // Delete KO
                                $mesg = $myobject->error;
                                //dol_print_error($db);
                                setEventMessages($mesg, $myobject->errors, 'errors');
                        }

                        // next, we delete attachment if exists
                        if (! $error && $myobject->fk_attachment > 0)
                        {
                            $attachment = new ChatMessageAttachment($db);
                            $result = $attachment->fetch($myobject->fk_attachment);

                            if ($result > 0)
                            {
                                $result = $attachment->delete($user);

                                if ($result > 0) {
                                    // Delete attachment OK
                                } else {
                                    // Delete attachment KO
                                    $mesg = $attachment->error;
                                    //dol_print_error($db);
                                    setEventMessages($mesg, $attachment->errors, 'errors');
                                }
                            }
                        }

                    }
                    else
                    {
                        setEventMessage($langs->trans("CannotDeleteMessage"), 'warnings');
                    }
                }
            } // fin if (is_numeric($m_id))
        } // fin foreach()
    }

    $action = empty($user_to_id) ? "" : "private_msg";
}

/*
 * VIEW
 *
 * Put here all code to build page
 */

// Define height of file area (depends on $_SESSION["dol_screenheight"])
$maxheightwin=(isset($_SESSION["dol_screenheight"]) && $_SESSION["dol_screenheight"] > 466)?($_SESSION["dol_screenheight"]-96):660;	// Also into index_auto.php file

$morejs=array('/chat/js/jquery.min.js', '/chat/js/jquery.layout.min.js'); // fix for powererp 6.0

$moreheadcss="
<!-- dol_screenheight=".$_SESSION["dol_screenheight"]." -->
<link rel=\"stylesheet\" type=\"text/css\" href=\"".dol_buildpath("/chat/css/chat.css.php", 1)."\">
<style type=\"text/css\">
    #containerlayout {
        height:     ".$maxheightwin."px;
        margin:     0 auto;
        width:      100%;
        min-width:  700px;
        _width:     700px; /* min-width for IE6 */
    }
</style>";

$moreheadjs=empty($conf->use_javascript_ajax)?"":"
<script type=\"text/javascript\">
    var jQuery_1_12_0 = $.noConflict(true); // fix for powererp 6.0

    jQuery(document).ready(function () {
        var chatlayout = jQuery_1_12_0('#containerlayout').layout({
        	name: \"ecmlayout\"
        ,   paneClass:    \"ecm-layout-pane\"
        ,   resizerClass: \"ecm-layout-resizer\"
        ,   togglerClass: \"ecm-layout-toggler\"
        ,   center__paneSelector:   \"#ecm-layout-center\"
        ,   west__paneSelector:     \"#ecm-layout-west\"
        ,   resizable: true
        ,   west__size:         340
        ,   west__minSize:      280
        ,   west__slidable:     true
        ,   west__resizable:    true
        ,   west__togglerLength_closed: '100%'
        ,   useStateCookie:     true
            });

        $('.menutoggle').click(function() {
            chatlayout.resizeAll();
        });

        function chatScroll() {
            $(\"#chat_container\").scrollTop($(\"#chat_container\")[0].scrollHeight);
        }

        // activate sort by date if on
        if ($('#sort-by-date').is(':checked')) {
            $('.msg-date').show();
        }

        chatScroll();

        function showGif() {
            // show gif images & add play/stop control
            Gifffer();
        }

        ".($conf->global->CHAT_SHOW_IMAGES_PREVIEW ? "showGif();" : "")."

        var disableAjax = false;

        function fetchMessages() {
            setTimeout( function(){
                    if (! disableAjax) {
                        $.get( '".dol_buildpath('/chat/ajax/ajax.php', 1)."', {
                                action: \"fetch_msgs\",
                                show_date: $('#sort-by-date').is(':checked'),
                                filter_by_user: ".(empty($user_to_id) ? "-1" : $user_to_id)."
                        },
                        function(response) {
                                // s'il y'a des nouveaux messages (ou message(s) supprimé(s))
                                if ($(response).filter('#msg_number').val() != $('#msg_number').val())
                                {
                                    $('#chat_container').html(response);
                                    chatScroll();
                                    setDeleteCheckboxClickEvent();
                                    setAnchorClickEvent($('.msg .private'));
                                    playNotificationSound();
                                    ".($conf->global->CHAT_SHOW_IMAGES_PREVIEW ? "showGif();" : "")."
                                }
                        });
                    }

                    fetchUsers(); // on n'oublie pas de rafraîchir la liste des utilisateurs aussi

                    fetchMessages();
            }, ".(! empty($conf->global->CHAT_AUTO_REFRESH_TIME) ? $conf->global->CHAT_AUTO_REFRESH_TIME * 1000 : 5000 ).");
        }

        function fetchUsers() {
                    $.get( '".dol_buildpath('/chat/ajax/ajax.php', 1)."', {
                            action: \"fetch_users\",
                            filter_user: \"".$filter_user."\"
                    },
                    function(response) {
                            $('#users_container').html(response);
                            filterUsers();
                            setAnchorClickEvent($('.user-anchor'));
                    });
        }

        fetchMessages();

        $('#add-attachment').click(function() {
            $('#attachment-input').click();
        });

        $('#attachment-input').change(function() {
            $('#add-attachment span').text($(this).val());
        });

        $(document).click(function() {
            $('.dropdown-click .dropdown-content').removeClass('show');
        });

        $('.drop-btn').click(function(e) {
            e.stopPropagation();
            $('.dropdown-click .dropdown-content').removeClass('show');
            $(this).next().addClass('show');
        });

        $('#smiley-dropdown img.smiley').click(function() {
            var new_val = $('textarea.send-message').val() + $(this).attr('title');
            $('textarea.send-message').val(new_val);
        });

        $('.send-wrap textarea').keydown(function(event) {
            if (event.keyCode == 13 && $('#enter-to-send').is(':checked')) {
                $('#chatForm').submit();
                return false;
             }
        });

        $('#sort-by-date').click(function() {
            if ($(this).is(':checked')) {
                $('.msg-date').removeClass('hidden');
            }
            else {
                $('.msg-date').addClass('hidden');
            }
        });

        $('#new-msg').click(function() {
            $('textarea.send-message').focus();
        });

        $('#delete-msg').click(function() {
            disableAjax = true;
            $('.delete-checkbox').removeClass('hidden').prop('checked', false);
            $('#confirm-delete-msg').removeClass('hidden');
            $('#delete-msg-select-all').removeClass('hidden');
            $('#cancel-delete-msg').removeClass('hidden');
            $('#chat-head-options').addClass('hidden');
            $('#confirm-delete-msg').attr('href', $('#confirm-delete-msg').attr('href').replace(/\?.*/,'?action=delete&user_to_id=".$user_to_id."&msg_id='));
        });

        $('#cancel-delete-msg').click(function() {
            $('.delete-checkbox').addClass('hidden');
            $('#confirm-delete-msg').addClass('hidden');
            $('#delete-msg-select-all').addClass('hidden');
            $('#cancel-delete-msg').addClass('hidden');
            $('#chat-head-options').removeClass('hidden');
            disableAjax = false;
        });

        $('#delete-msg-select-all').click(function() {
            $('.delete-checkbox').prop('checked', true);
            var msg_id = '';
            $('.delete-checkbox').each(function(i){
                msg_id += $(this).val() + ',';
            });
            $('#confirm-delete-msg').attr('href', $('#confirm-delete-msg').attr('href').replace(/\?.*/,'?action=delete&user_to_id=".$user_to_id."&msg_id=' + msg_id));
        });

        function setDeleteCheckboxClickEvent()
        {
            $('.delete-checkbox').click(function() {
                if ($(this).is(':checked')) {
                    $('#confirm-delete-msg').attr('href', $('#confirm-delete-msg').attr('href') + $(this).val() + ',');
                }
                else {
                    $('#confirm-delete-msg').attr('href', $('#confirm-delete-msg').attr('href').replace(RegExp($(this).val() + ',', 'g'),''));
                }
            });
        }

        setDeleteCheckboxClickEvent();

        $('.users-filter .dropdown-content label').click(function() {
            // swap html between clicked & selected
            var saved_html = $('#selected-users-filter').html();
            $('#selected-users-filter').html($(this).html());
            $(this).html(saved_html);

            filterUsers();
        });

        function filterUsers()
        {
            var filter = $('#selected-users-filter img').attr('alt');

            if (filter == 'online-users') {
                $('.conversation:not(.is_online)').hide();
                $('.is_online').show();
            }
            else if (filter == 'admin') {
                $('.conversation:not(.is_admin)').hide();
                $('.is_admin').show();
            }
            else {
                $('.conversation').show();
            }
        }

        function setAnchorClickEvent(selector)
        {
            selector.click(function() {
                $(this).attr('href', $(this).attr('href') + '&enter_to_send=' + ($('#enter-to-send').is(':checked') ? 'on' : '') + '&sort_by_date=' + ($('#sort-by-date').is(':checked') ? 'on' : ''));
            });
        }

        setAnchorClickEvent($('.user-anchor'));
        setAnchorClickEvent($('.msg .private'));

        $('#chat-head-back-btn').click(function() {
            $(this).attr('href', $(this).attr('href') + '?enter_to_send=' + ($('#enter-to-send').is(':checked') ? 'on' : '') + '&sort_by_date=' + ($('#sort-by-date').is(':checked') ? 'on' : ''));
        });

        $('#sound_switch').click(function() {
            var state = $(this).attr('alt') == 'on' ? 0 : 1;
            $.post( '".dol_buildpath('/chat/ajax/ajax.php', 1)."', {
                    action: \"set_settings\",
                    name: \"CHAT_ENABLE_SOUND\",
                    value: state
            },
            function(response, status) {
                    //console.log(\"Response: \" + response + \"\\nStatus: \" + status);
                    if (state) {
                        $('#sound_switch').attr('src', 'img/sound-on.png').attr('alt', 'on').attr('title', '".$langs->transnoentities("DisableSound")."');
                    }
                    else {
                        $('#sound_switch').attr('src', 'img/sound-off.png').attr('alt', 'off').attr('title', '".$langs->transnoentities("EnableSound")."');
                    }
            });
        });

        function playNotificationSound() {
            if ($('#sound_switch').attr('alt') == 'on') {
                $('#notification_sound')[0].play();
            }
        }
    });
</script>";

llxHeader($moreheadcss.$moreheadjs, $langs->trans('ChatIndexPageName'),'','','','',$morejs);

// Page content

if (! empty($conf->use_javascript_ajax)) $classviewhide='hidden';
else $classviewhide='visible';

// Start container of all panels
?>
<div id="containerlayout"> <!-- begin div id="containerlayout" -->
<div id="ecm-layout-west" class="<?php echo $classviewhide; ?>">
<?php
// Start left area

    // formulaire de recherche
?>

    <form id="searchForm" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <div id="custom-search-input">
            <div class="input-group">
                <input name="filter_user" class="search-query form-control" placeholder="<?php echo $langs->trans("SearchPlaceHolder"); ?>" type="text" value="<?php echo $filter_user;  ?>">
                <a href="javascript:void()" onclick="document.getElementById('searchForm').submit();" class="btn btn-danger">
                    <img class="" title="" alt="" src="img/search.png" />
                </a>
            </div>
            <audio id="notification_sound">
                <source src="sounds/notification.wav"></source>
            </audio>
            <?php
                $object->get_settings($user);

                if ($object->settings->CHAT_ENABLE_SOUND)
                {
            ?>
            <img id="sound_switch" class="cursor-pointer caret" title="<?php echo $langs->trans("DisableSound"); ?>" alt="on" src="img/sound-on.png" />
            <?php
                }
                else
                {
            ?>
            <img id="sound_switch" class="cursor-pointer caret" title="<?php echo $langs->trans("EnableSound"); ?>" alt="off" src="img/sound-off.png" />
            <?php
                }
            ?>
        </div>
    </form>

    <div class="dropdown-click users-filter">
        <img class="btn-icon caret" title="" alt="" src="img/arrow-down.png" />
        <label id="selected-users-filter" class="drop-btn btn"><img class="btn-icon" title="" alt="" src="img/users.png" /><?php echo ' '.$langs->trans("AllUsers"); ?></label>
        <div class="dropdown-content dropdown-bottom">
            <?php
                if ($user->rights->chat->see_online_users)
                {
            ?>
            <div>
                <label class="align-middle cursor-pointer"><img class="btn-icon" title="<?php echo $langs->trans("Online"); ?>" alt="online-users" src="img/online.png" /><?php echo ' '.$langs->trans("OnlineUsers"); ?></label>
            </div>
            <?php
                }
            ?>
            <div>
                <label class="align-middle cursor-pointer"><img class="btn-icon" title="<?php echo $langs->trans("Administrator"); ?>" alt="admin" src="img/star.png" /><?php echo ' '.$langs->trans("Administrator"); ?></label>
            </div>
        </div>
    </div>

    <div id="users_container">
<?php

    // récupération des utilisateurs (filtrage inclus)
    $result = $object->fetch_users($user, 1, $filter_user, 1);

    if ($result)
    {
    	dol_include_once('/chat/tpl/user.tpl.php');
    }

// End left panel
?>
    </div> <!-- end div id="users_container" -->
</div>
<div id="ecm-layout-center" class="<?php echo $classviewhide; ?>">
<div class="pane-in ecm-in-layout-center">
<?php
// Start right panel
?>
    <div id="chat_head">
        <div class="pull-left">
            <?php
                if ($action == "private_msg" && ! empty($user_to_id))
                {
                    $userstatic->fetch($user_to_id);
            ?>
            <a id="chat-head-back-btn" class="pull-left chat-head-clickable grey-bold-text" href="<?php echo dol_buildpath('/chat/index.php', 1); ?>">
                <img class="btn-icon" title="" alt="" src="img/arrow-back.png" />
                <?php echo ' '.$langs->trans("Back"); ?>
            </a>
            <span class="pull-left divider">&nbsp;</span>
            <a class="pull-left chat-head-user" target="_blank" href="<?php echo DOL_URL_ROOT.'/user/card.php?id='.$user_to_id; ?>">
                <span class="user-image pull-left">
                    <?php
                        echo Form::showphoto('userphoto', $userstatic, 32, 32, 0, '', 'small', 0, 1);
                    ?>
                </span>
                <div class="media-body">
                    <h5 class="media-heading">
                        <span class="user-name grey-bold-text"><?php echo $userstatic->getFullName($langs); ?></span>
                        <?php
                            if ($userstatic->admin)
                            {
                                print img_picto($langs->trans("Administrator"),'star');
                            }
                        ?>
                    </h5>
                    <small><?php echo $langs->trans("LastLogin").' '.dol_print_date($userstatic->datelastlogin,"dayhour"); ?></small>
                </div>
            </a>
            <?php
                }
                else
                {
            ?>
            <span id="new-msg" class="chat-head-clickable grey-bold-text">
                <img class="btn-icon" title="" alt="" src="img/plus.png" />
                <?php echo ' '.$langs->trans("NewMessage"); ?>
            </span>
            <?php
                }
            ?>
        </div>
        <?php if ($user->rights->chat->delete->all || $user->rights->chat->delete->mine) { ?>
        <div class="pull-right">
            <div id="chat-head-options" class="dropdown-click">
                <label class="drop-btn btn grey-bold-text"><img class="btn-icon" title="" alt="" src="img/edit.png" /><?php echo ' '.$langs->trans("Options"); ?> <img class="btn-icon" title="" alt="" src="img/arrow-down.png" /></label>
                <div class="dropdown-content dropdown-bottom">
                    <?php if ($user->rights->chat->delete->all || $user->rights->chat->delete->mine) { ?>
                    <div>
                        <label id="delete-msg" class="align-middle cursor-pointer"><?php echo ' '.$langs->trans("DeleteMessage"); ?></label>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <a id="confirm-delete-msg" class="chat-head-clickable grey-bold-text hidden" href="<?php echo dol_buildpath('/chat/index.php', 1).'?action=delete&user_to_id='.$user_to_id.'&msg_id='; ?>">
                <img class="btn-icon" title="" alt="" src="img/delete.png" />
                <?php echo ' '.$langs->trans("DeleteMessage"); ?>
            </a>
            <span id="delete-msg-select-all" class="chat-head-clickable grey-bold-text hidden">
                <img class="btn-icon" title="" alt="" src="img/select_all.png" />
                <?php echo ' '.$langs->trans("SelectAll"); ?>
            </span>
            <span id="cancel-delete-msg" class="chat-head-clickable grey-bold-text hidden">
                <img class="btn-icon" title="" alt="" src="img/cancel.png" />
                <?php echo ' '.$langs->trans("Cancel"); ?>
            </span>
        </div>
        <?php } ?>
    </div><!-- end div id="chat_head" -->

    <div id="chat_container" class="msg-wrap">
<?php

    // récupération des messages
    $result = $object->fetch_messages($user, $user_to_id);

    if ($result)
    {
        dol_include_once('/chat/tpl/message.tpl.php');
    }
?>
    </div> <!-- end div id="chat_container" class="msg-wrap" -->

<?php

    print '<form id="chatForm" method="POST" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data">';
    print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
    print '<input type="hidden" name="action" value="send">';
    print '<input type="hidden" name="user_to_id" value="'.$user_to_id.'">';
?>
    <div class="send-wrap ">
        <textarea name="text" class="form-control send-message" rows="3" placeholder="<?php echo $langs->trans("TypeAMessagePlaceHolder"); ?>"></textarea>
    </div>

    <div class="btn-panel">
        <!-- Smiley -->
        <div class="dropdown-click">
            <label class="drop-btn btn"><img class="btn-icon" title="" alt="" src="img/smiley.png" /></label>
            <div id="smiley-dropdown" class="dropdown-content dropdown-top">
                <?php echo printSmileyList(); ?>
            </div>
        </div>
        <!-- Settings -->
        <div class="dropdown-click">
            <label class="drop-btn btn"><img class="btn-icon" title="" alt="" src="img/settings.png" /></label>
            <div class="dropdown-content dropdown-top">
                <div class="more-width">
                    <input class="align-middle" type="checkbox" id="sort-by-date" name="sort_by_date" <?php echo empty($sort_by_date) ? "" : "checked"; ?>/>
                    <label for="sort-by-date" class="align-middle cursor-pointer"><?php echo ' '.$langs->trans("SortMessagesByDate"); ?></label>
                </div>
                <div class="more-width">
                    <input class="align-middle" type="checkbox" id="enter-to-send" name="enter_to_send" <?php echo empty($enter_to_send) ? "" : "checked"; ?>/>
                    <label for="enter-to-send" class="align-middle cursor-pointer"><?php echo ' '.$langs->trans("EnterToSend"); ?></label>
                </div>
            </div>
        </div>
        <!-- Add File -->
        <span>
            <input type="file" class="hidden" name="attachment" id="attachment-input"/>
            <label class="btn send-message-btn" id="add-attachment">
                <img class="btn-icon" title="" alt="" src="img/attachment.png" />
                <span><?php echo ' '.$langs->trans("AddFiles");?></span>
            </label>
        </span>
        <!-- Send -->
        <a href="javascript:void()" onclick="document.getElementById('chatForm').submit();" class="text-right btn send-message-btn pull-right"><img class="btn-icon" title="" alt="" src="img/send.png" /><?php echo ' '.$langs->trans("SendMessage");?></a>
    </div>
<?php

    print '</form>'."\n";

// End right panel
?>
</div>
</div>
</div> <!-- end div id="containerlayout" -->
<?php

// End of page
llxFooter();
