<?php
/* Copyright (C) 2017	AXeL dev	<contact.axel.dev@gmail.com>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *       \file       htdocs/chat/tpl/popup.tpl.php
 *       \brief      Template of chat popup
 */

dol_include_once('/chat/class/chat.class.php');

global $db, $conf, $user, $langs, $object;

$langs->load('chat@chat');

$chat = new Chat($db);
$chat->get_settings($user);
// <?php echo printSmileyList(dol_buildpath('/chat/', 1));


?>
<div id="chat_popup">
    <div class="panel panel-default">
        <div class="panel-heading" id="accordion">
            <span id="chat_popup_counter" class="label label-danger hidden">&nbsp;</span>
            <img class="align-middle" title="" alt="" src="<?php echo dol_buildpath('/chat/img/'.($conf->global->CHAT_POPUP_TEXT_COLOR == '#fff' ? 'chat-16-white.png' : 'chat-16.png'), 1); ?>" />
            <span id="chat_popup_title" class="align-middle"><?php echo $langs->trans("Module452000Name"); ?></span>
        </div>
        <div class="panel-collapse collapse <?php echo $chat->settings->CHAT_POPUP_OPENED ? 'in" style="display: block;' : ''; ?>" id="collapseOne">
            <div id="chat_popup_toolbox">
                <label id="chat-popup-back-btn" class="popup-option align-middle cursor-pointer hidden"><img class="btn-icon" title="" alt="" src="<?php echo dol_buildpath('/chat/img/arrow-back.png', 1); ?>" /><?php echo ' '.$langs->trans("Back"); ?></label>
                <?php
                    if ($user->rights->chat->see_online_users){
                ?>
                <div id="online-users-switch" class="dropdown-click popup-option">
                    <label class="drop-btn cursor-pointer">
                        <img class="btn-icon" title="" alt="" src="<?php echo dol_buildpath('/chat/img/online.png', 1); ?>" />
                        <?php echo ' '.$langs->trans("OnlineUsers"); ?>
                        <span id="online-users-counter">(<?php echo count($object->users); ?>)</span>
                        <img class="btn-icon caret" title="" alt="" src="<?php echo dol_buildpath('/chat/img/arrow-down.png', 1); ?>" />
                    </label>
                    <div class="dropdown-content dropdown-bottom">
                        <div id="users_container">
                            <?php
                            dol_include_once('/chat/tpl/user.tpl.php');
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                    }
                ?>
                <span class="popup-option pull-right">
                    <audio id="notification_sound">
                        <source src="<?php echo dol_buildpath('/chat/sounds/notification.wav', 1); ?>"></source>
                    </audio>
                    <?php
                    $chat->get_settings($user);

                    if ($chat->settings->CHAT_ENABLE_SOUND)
                        {
                    ?>
                    <img id="sound_switch" class="cursor-pointer" title="<?php echo $langs->trans("DisableSound"); ?>" alt="on" src="<?php echo dol_buildpath('/chat/img/sound-on.png', 1); ?>" />
                    <?php
                        }
                        else
                        {
                    ?>
                    <img id="sound_switch" class="cursor-pointer" title="<?php echo $langs->trans("EnableSound"); ?>" alt="off" src="<?php echo dol_buildpath('/chat/img/sound-off.png', 1); ?>" />
                    <?php
                        }
                    ?>
                </span>
            </div>
            <div id="chat_container" class="panel-body msg-wrap">
                <?php
                dol_include_once('/chat/tpl/message.tpl.php');
                ?>
            </div>

            <div class="panel-footer">
                <div class="input-group">
                    <input id="msg_input" type="text" class="form-control input-sm" placeholder="<?php echo $langs->trans("TypeAMessagePlaceHolder"); ?>" />
                    <span class="input-group-btn">
                        <!-- Smiley -->
                        <!-- <div class="dropdown-click">
                            <label id="smiley-btn" class="drop-btn btn btn-default btn-sm"><img class="btn-icon" title="" alt="" src="<?php echo dol_buildpath('/chat/img/smiley.png', 1); ?>" /></label>
                            <div id="smiley-dropdown" class="dropdown-content dropdown-top">

                            </div>
                        </div> -->
                        <!-- Send -->
                        <button class="btn btn-default btn-sm" id="send_btn">
                            <img class="align-middle" title="" alt="" src="<?php echo dol_buildpath('/chat/img/send.png', 1); ?>" />
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
