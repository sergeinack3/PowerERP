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
 * \file    js/myjs.js.php
 * \ingroup mymodule
 * \brief   Example JavaScript.
 *
 * Put detailed description here.
 */

//define('NOLOGIN', 1);
define('NOREDIRECTBYMAINTOLOGIN', 1);
define('NOTOKENRENEWAL', 1);

// Load Powererp environment
if (false === (@include '../../main.inc.php')) {  // From htdocs directory
	require '../../../main.inc.php'; // From "custom" directory
}

global $conf, $langs, $user;

header('Content-Type: text/javascript');

$is_chat_index_page = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) == dol_buildpath('/chat/index.php', 1) ? true : false;

if ($conf->global->CHAT_ENABLE_POPUP && ! empty($conf->use_javascript_ajax) && ! $is_chat_index_page && $user->rights->chat->lire)
{

$langs->load('chat@chat');

?>

$(document).ready(function() {

<?php

print "         var user_to_id = -1;
                var new_loop_count = 0;

                chatScroll();

                ".($conf->global->CHAT_SHOW_IMAGES_PREVIEW ? "showGif();" : "")."

                $('#accordion').click(function(e) {
                    if ($('#collapseOne').hasClass('in')) {
                        $('#collapseOne').slideUp('slow', function() {
                            ".($conf->global->CHAT_POPUP_USE_MINIMUM_SPACE ? "$('#chat_popup').css('width', 'auto');" : "")."
                        }).removeClass('in');
                        setPopupState(0);
                    }
                    else {
                        $('#chat_popup').css('width', '".$conf->global->CHAT_POPUP_SIZE."');
                        $('#collapseOne').slideDown().addClass('in');
                        chatScroll();
                        hidePopupCounter();
                        setPopupState(1);
                    }
                });

                $('#send_btn').click(function(e) {
                    if ($('#msg_input').val() != '') { // if message field is not empty
                        $.post('".dol_buildpath('/chat/ajax/ajax.php', 1)."', {
                                action: \"send_msg\",
                                msg: $('#msg_input').val(),
                                user_to_id: user_to_id
                        },
                        function(response, status) {
                                //alert(\"Response: \" + response + \"\\nStatus: \" + status);
                                $('#msg_input').val('');
                                getMessages(true, false, true);
                        });
                    }
                    else {
                        $('#msg_input').focus();
                    }

                    hidePopupCounter();
                });

                $('#msg_input').keydown(function(event) {
                    if (event.keyCode == 13 && $('#msg_input').val() != '') {
                        $('#send_btn').click().focus();
                        return false;
                     }
                });

                $('#msg_input').click(function(e) {
                    hidePopupCounter();
                });

                $('#chat_container').click(function(e) {
                    hidePopupCounter();
                });

                $(document).click(function() {
                    $('.dropdown-click .dropdown-content').removeClass('show');
                });

                $('.drop-btn').click(function(e) {
                    e.stopPropagation();
                    $('.dropdown-click .dropdown-content').removeClass('show');
                    $(this).next().addClass('show');
                });

                setUserAnchorClickEvent();

                $('#chat-popup-back-btn').click(function(e) {
                    $('#chat_popup_title').html('".$langs->trans("Module452000Name")."');
                    $('#chat-popup-back-btn').addClass('hidden');
                    user_to_id = -1;
                    getMessages(true, true, true);
                });

                setPrivateMsgAnchorClickEvent();

                $('#smiley-dropdown img.smiley').click(function() {
                    var new_val = $('#msg_input').val() + $(this).attr('title');
                    $('#msg_input').val(new_val).focus();
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
                                $('#sound_switch').attr('src', '".dol_buildpath('/chat/img/sound-on.png', 1)."').attr('alt', 'on').attr('title', '".$langs->transnoentities("DisableSound")."');
                            }
                            else {
                                $('#sound_switch').attr('src', '".dol_buildpath('/chat/img/sound-off.png', 1)."').attr('alt', 'off').attr('title', '".$langs->transnoentities("EnableSound")."');
                            }
                    });
                });

                function chatScroll() {
                    $(\"#chat_container\").scrollTop($(\"#chat_container\")[0].scrollHeight);
                }

                function getMessages(disableCounter = false, forceDisplay = false, resetAjax = false) {
                    $.get( '".dol_buildpath('/chat/ajax/ajax.php', 1)."', {
                            action: \"fetch_msgs\",
                            referrer: \"popup\",
                            filter_by_user: user_to_id
                    },
                    function(response) {
                            //console.log(disableCounter + '-' + forceDisplay);
                            //console.log($(response).filter('#msg_number').val() + '-' + $('#msg_number').val());
                            // s'il y'a des nouveaux messages (ou message(s) supprimé(s))
                            var new_msg_number = $(response).filter('#msg_number').val() - $('#msg_number').val();
                            if (forceDisplay || new_msg_number != 0)
                            {
                                $('#chat_container').html(response);
                                chatScroll();
                                setPrivateMsgAnchorClickEvent();
                                ".($conf->global->CHAT_SHOW_IMAGES_PREVIEW ? "showGif();" : "")."

                                if (! disableCounter && new_msg_number > 0) {
                                    var unseen_msg_number = parseInt($('#chat_popup_counter').html());
                                    if (unseen_msg_number > 0) new_msg_number += unseen_msg_number;
                                    $('#chat_popup_counter').html(new_msg_number).removeClass('hidden');
                                    playNotificationSound();
                                }
                            }

                            if (resetAjax) {
                                fetchMessages(); // call new ajax loop (the old one will be stoped)
                                new_loop_count++;
                                //console.log('New ajax loop [' + new_loop_count + ']');
                            }
                    });
                }

                function fetchMessages() {
                    setTimeout( function(){
                            if (new_loop_count == 0) {
                                //console.log('[Ajax loop]');
                                getMessages();

                                fetchMessages(); // re-loop
                            }
                            else {
                                //console.log('[Disable ajax loop] (loop count : ' + new_loop_count + ')');
                                new_loop_count--; // disable current loop
                                //console.log('New loop count : ' + new_loop_count);
                            }
                    }, ".(! empty($conf->global->CHAT_AUTO_REFRESH_TIME) ? $conf->global->CHAT_AUTO_REFRESH_TIME * 1000 : 5000 ).");
                }

                fetchMessages();

                ".($user->rights->chat->see_online_users ? "

                function fetchUsers() {
                    setTimeout( function(){
                        //console.log('[Fetch users loop]');
                        $.get( '".dol_buildpath('/chat/ajax/ajax.php', 1)."', {
                                action: \"fetch_users\",
                                only_online: true
                        },
                        function(response) {
                                $('#users_container').html(response);
                                // set online users number
                                $('#online-users-counter').html('(' + $(response).filter('.user-anchor').length + ')');
                                // set click event on user anchor
                                setUserAnchorClickEvent();
                        });

                        fetchUsers(); // re-loop
                    }, ".(! empty($conf->global->CHAT_AUTO_REFRESH_TIME) ? $conf->global->CHAT_AUTO_REFRESH_TIME * 1000 : 5000 ).");
                }

                fetchUsers();

                " : "")."

                function hidePopupCounter() {
                    // hide popup counter if shown (+ free html)
                    if (! $('#chat_popup_counter').hasClass('hidden')) {
                        $('#chat_popup_counter').html('').addClass('hidden');
                    }
                }

                function setUserAnchorClickEvent() {
                    $('.user-anchor').click(function(e) {
                        $('.dropdown-click .dropdown-content').removeClass('show');
                        $('#chat_popup_title').html($(this).find('.media-heading span').html());
                        $('#chat-popup-back-btn').removeClass('hidden');
                        var user_anchor_href = $(this).attr('href');
                        user_to_id = parseInt(user_anchor_href.substr(user_anchor_href.lastIndexOf('=') + 1));
                        getMessages(true, true, true);

                        return false;
                    });
                }

                function setPrivateMsgAnchorClickEvent() {
                    $('.msg .private').click(function(e) {
                        if ($(this).attr('href') != '#') {
                            $('#chat_popup_title').html($(this).attr('alt'));
                            $('#chat-popup-back-btn').removeClass('hidden');
                            var user_anchor_href = $(this).attr('href');
                            user_to_id = parseInt(user_anchor_href.substr(user_anchor_href.lastIndexOf('=') + 1));
                            getMessages(true, true, true);

                            return false;
                        }
                    });
                }

                function setPopupState(state) {
                    $.post( '".dol_buildpath('/chat/ajax/ajax.php', 1)."', {
                            action: \"set_settings\",
                            name: \"CHAT_POPUP_OPENED\",
                            value: state
                    },
                    function(response, status) {
                            //alert(\"Response: \" + response + \"\\nStatus: \" + status);
                    });
                }

                function showGif() {
                    // show gif images & add play/stop control
                    Gifffer();
                }

                function playNotificationSound() {
                    if ($('#sound_switch').attr('alt') == 'on') {
                        $('#notification_sound')[0].play();
                    }
                }

                ";

?>

});

<?php

} // fin if (! empty($conf->use_javascript_ajax))
