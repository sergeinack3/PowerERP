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
 * \file    css/mycss.css.php
 * \ingroup mymodule
 * \brief   Example CSS.
 *
 * Put detailed description here.
 */

// TODO : optimise this css file (remove not used class/code, & avoid conflits with chat.css file)

//define('NOLOGIN', 1);
define('NOREDIRECTBYMAINTOLOGIN', 1);
define('NOTOKENRENEWAL', 1);

// Load Powererp environment
if (false === (@include '../../main.inc.php')) {  // From htdocs directory
	require '../../../main.inc.php'; // From "custom" directory
}

dol_include_once('/chat/class/chat.class.php');

global $conf, $db, $user;

$optioncss = GETPOST('optioncss');

header('Content-Type: text/css');

$dolibarr_version = explode('.', DOL_VERSION);
if ((int)$dolibarr_version[0] >= 11) {

?>

/**
 * Chat menu icon
 */

div.mainmenu.chat {
    background-image: none !important;
}
div.mainmenu.chat span:before {
    font-family: "Font Awesome 5 Free";
    font-size: 15px;
    content: "\f075";
    color: #<?php echo (isset($user->conf->TOPMENU_TEXT_COLOR) && ! empty($user->conf->TOPMENU_TEXT_COLOR) ? $user->conf->TOPMENU_TEXT_COLOR : 'ffffff'); ?>;
}
a.tmenuimage {
    text-decoration: none;
    outline: none;
}

<?php

}

?>

/*
 * Chat Popup CSS
 *
 */

<?php

$is_chat_index_page = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) == dol_buildpath('/chat/index.php', 1) ? true : false;

if ($conf->global->CHAT_ENABLE_POPUP && ! empty($conf->use_javascript_ajax) && ! $is_chat_index_page)
{

$chat = new Chat($db);
$chat->get_settings($user);

$background_color = ! empty($conf->global->CHAT_POPUP_USE_THEME_COLOR) ? (isset($user->conf->THEME_ELDY_TOPMENU_BACK1) ? '#'.$user->conf->THEME_ELDY_TOPMENU_BACK1 : (isset($conf->global->THEME_ELDY_TOPMENU_BACK1) ? '#'.$conf->global->THEME_ELDY_TOPMENU_BACK1 : '#505a78')) : (! empty($conf->global->CHAT_POPUP_BACKGROUND_COLOR) ? $conf->global->CHAT_POPUP_BACKGROUND_COLOR : "#e8e6e7");

?>

#chat_popup {
    <?php echo $optioncss == 'print' ? 'display: none;'."\n" : ''; ?>
    position: fixed;
    bottom: 0px;
    <?php echo empty($conf->global->CHAT_PULL_LEFT_POPUP) ? 'right: 0px;'."\n\t".'margin-right: 15px;'."\n" : 'left: 0px;'."\n\t".'margin-left: 15px;'."\n"; ?>
    width: <?php echo ! $chat->settings->CHAT_POPUP_OPENED && ! empty($conf->global->CHAT_POPUP_USE_MINIMUM_SPACE) ? "auto" : (! empty($conf->global->CHAT_POPUP_SIZE) ? $conf->global->CHAT_POPUP_SIZE : "25%"); ?>;
    z-index: 100;
    direction: ltr;
}

.panel-body {
    overflow-y: scroll;
    height: 250px;
}

#accordion {
    cursor: pointer;
}

#chat_popup_toolbox {
    position: relative;
    display: inline-block;
    float: left;
    text-align: left;
    font-size: 13px;
    color: #333;
    border-bottom: 1px solid #ddd;
    background: #f5f3f3;
    width: 100%;
    height: 39px;
    box-sizing: border-box;
}

#sound_switch {
    padding: 6px 10px;
}

.popup-option.pull-right {
    height: 38px;
}

.popup-option {
    display: inline-block;
    float: left;
    border-right: 1px solid #ddd;
}

.popup-option:hover {
    background: #fff;
}

#chat-popup-back-btn {
    padding: 10px;
    font-weight: bold;
}

#online-users-switch {
    width: 55%;
}

#online-users-switch label {
    padding: 10px;
    display: inline-block;
    width: 100%;
}

#online-users-switch .dropdown-content {
    overflow-y: auto;
    max-height: 150px;
    padding: 0px;
    width: 100%;
    background: #fff;
    border-radius: 0px;
}

#users_container .conversation {
    padding: 10px;
}

#users_container .conversation:hover {
    background: #FBF9FA;
}

#online-users-switch .dropdown-content a, #online-users-switch .dropdown-content div:not(.conversation) {
    padding: 0px;
}

#online-users-switch .dropdown-content a:hover, #online-users-switch .dropdown-content div:not(.conversation):hover {
    background: transparent;
}

#users_container .last-private-msg {
    display: none;
}

#users_container .user-image img {
    width: 32px;
    height: 32px;
}

#users_container .media-heading {
    font-size: 13px;
    margin: 5px;
}

#users_container .online-icon {
    display: none;
}

#msg_input {
    border-radius: 3px 0px 0px 3px;
    margin: 0px;
}

#send_btn {
    border-radius: 0px 3px 3px 0px;
}

.caret {
    bottom: 0;
    margin: auto;
    position: absolute;
    right: 15px;
    top: 0;
}

/*---- Bootstrap ----*/

.panel {
    /*margin-bottom: 20px;*/
    background-color: #fff;
    border: 1px solid transparent;
    -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
    box-shadow: 0 1px 1px rgba(0,0,0,.05);
}

.panel-default {
    border-color: <?php echo $background_color; ?>;
}

.panel-heading {
    padding: 10px 15px;
    border-bottom: 1px solid transparent;
}

.panel-default > .panel-heading {
    color: <?php echo ! empty($conf->global->CHAT_POPUP_TEXT_COLOR) ? $conf->global->CHAT_POPUP_TEXT_COLOR : "#333"; ?>;
    background-color: <?php echo $background_color; ?>;
    border-color: <?php echo $background_color; ?>;
}

#chat_popup_title {
    font-weight: bold;
}

.btn-group, .btn-group-vertical {
    position: relative;
    display: inline-block;
    vertical-align: middle;
}

.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}

.btn-default {
    color: #333;
    background-color: #fff;
    border-color: #ccc;
}

.btn-primary {
    color: #fff;
    background-color: #428bca;
    border-color: #357ebd;
}

.btn-xs, .btn-group-xs > .btn {
    padding: 1px 5px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}

.btn-sm, .btn-group-sm > .btn {
    padding: 5px 10px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 0px;
}

.pull-right {
    float: right !important;
}

.collapse.in {
    display: block;
}

.collapse {
    display: none;
}

.panel-body {
    padding: 15px;
}

.panel-footer {
    padding: 10px 15px;
    background-color: #f5f5f5;
    border-top: 1px solid #ddd;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
}

.panel-footer .btn:hover, .panel-heading .btn:hover
{
    color:#666;
    /*background: #f8f8f8;*/
    background: #FBF9FA;
    text-decoration: none;
}

.panel-footer .btn:active, .panel-footer .btn:hover, .panel-footer .btn:focus
{
    box-shadow: none;
}

.input-group {
    position: relative;
    display: table;
    border-collapse: separate;
}

.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}

.form-control:focus {
    border-color: #66AFE9;
    outline: 0px none;
    box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(102, 175, 233, 0.6);
}

.form-control:-moz-placeholder{
    color:#999
}

.form-control::-moz-placeholder{
    color:#999;
    opacity:1
}

.form-control:-ms-input-placeholder{
    color:#999
}

.form-control::-webkit-input-placeholder{
    color:#999
}

.form-control[disabled],.form-control[readonly],fieldset[disabled] .form-control{
    cursor:not-allowed;
    background-color:#eee
}

.input-sm, .form-horizontal .form-group-sm .form-control {
    height: 30px;
    padding: 5px 10px;
    font-size: 13px;
    line-height: 1.5;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.input-group .form-control {
    position: relative;
    z-index: 2;
    float: left;
    width: 100%;
    margin-bottom: 0;
}

.input-group-addon, .input-group-btn, .input-group .form-control {
    display: table-cell;
}

.input-group-btn > .btn {
    position: relative;
}

.input-group-addon, .input-group-btn {
    width: 1%;
    white-space: nowrap;
    vertical-align: middle;
}

.input-group-btn:last-child > .btn, .input-group-btn:last-child > .btn-group > .btn, .input-group-btn:last-child > .dropdown-toggle, .input-group-btn:first-child > .btn:not(:first-child), .input-group-btn:first-child > .btn-group:not(:first-child) > .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.input-group-btn:last-child > .btn, .input-group-btn:last-child > .btn-group {
    margin-left: -1px;
}

.pull-right {
    float: right;
}

.pull-left {
    float: left;
}

.media > .pull-left {
    margin-right: 10px;
}

.media, .media-body {
    overflow: hidden;
}

.media-heading {
    margin: 0px 0px 5px;
    font-size: 14px;
}

small, .small {
    font-size: 85%;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.label {
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}

.label-danger {
    background-color: <?php echo ! empty($conf->global->CHAT_POPUP_COUNTER_COLOR) ? $conf->global->CHAT_POPUP_COUNTER_COLOR : "#d9534f"; ?>;
}

/*---- end of Bootstrap ----*/

/*---- dropdown ----*/

.dropbtn {
    margin-left: 5px;
    margin-bottom: 5px;
    display: inline-block;
    cursor: pointer;
}

.dropdown, .dropdown-click {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 100px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 9999;
}

.dropdown-right {
    right: 0;
}

.dropdown-top {
    bottom: 100%;
}

.more-width {
    min-width: 140px;
}

.cursor-pointer {
    cursor: pointer;
}

.dropdown-image {
    max-width: 300px;
    max-height: 200px;
    display: inline-block;
    float: left;
}

.drop-btn {
    padding: 8px 12px;
}

.dropdown-content a, .dropdown-content div {
    color: #555;
    font-size: 13px;
    padding: 8px 12px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover, .dropdown-content div:hover {background-color: #f1f1f1}

.dropdown:hover .dropdown-content {
    display: block;
}

.show {
    display: block;
}

/*---- end dropdown ----*/

.align-middle {
    vertical-align: middle;
}

.hidden {
    display: none;
    visibility: hidden;
}

#custom-search-input {
    background: #F5F3F3;
    margin: 0px;
    padding: 10px;
}

#custom-search-input a {
    margin-right: 2px;
    margin-top: 7px;
    padding: 2px 5px;
    position: absolute;
    right: 0px;
    top: 0px;
    z-index: 9999;
}

#custom-search-input .search-query {
    padding-right: 30px;
}

#users_container {
    overflow: auto;
}

.btn-icon, .smiley {
    vertical-align: middle;
}

.btn-small-icon {
    width: 11px;
}

#smiley-dropdown td img {
    padding: 5px;
    cursor: pointer;
}

#smiley-dropdown td:hover {background-color: #f1f1f1}

#smiley-dropdown {
    right: 0px;
}

#smiley-btn {
    padding: 5px 10px;
    margin-left: -1px;
}

#send_btn {
    margin-left: -5px;
    outline: none;
}

#send_btn::-moz-focus-inner {
    border: 0;
}

#private-msg-to-user {
    margin-bottom: 10px;
}

#private-msg-to-user span {
    font-weight: bold;
    color: #003BB3;
}

#private-msg-textarea {
    height: 60px;
}

.private-msg {
    background: #FBF9FA;
    border-radius: 20px;
    border: 1px solid #f3f3f3;
}

.msg-text {
    width: 85%;
    padding-right: 15px;
    padding-left: 15px;
    float: left;
}

.msg-attachment {
    padding: 5px;
    display: inline-block;
    border-radius: 4px;
    background: #F5F3F3;
    /*margin-left: 15px;*/
    margin-top: 5px;
    border: 1px solid #eee;
}

.msg-attachment a {
    color: #555;
    font-size: 13px;
    display: inline-block;
    word-break: break-all;
}

.conversation
{
    padding:5px;
    border-bottom:1px solid #ddd;
    margin:0;
    font-size: 14px;

}

.message-wrap
{
    box-shadow: 0 0 3px #ddd;
    padding:0;

}
.msg
{
    padding:10px;
    /*border-bottom:1px solid #ddd;*/
    /*margin:0;*/
    margin-bottom: 10px;
    overflow: visible;
}
.msg-wrap
{
    padding:10px;
    font-size: 12px;
}

.time
{
    color:#bfbfbf;
}

#chatForm
{
    position: absolute;
    left: 0px;
    bottom: 0px;
    width: 100%;
}

.send-wrap
{
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    padding:10px;
    /*background: #f8f8f8;*/
    background: #FBF9FA;
}

.send-message
{
    resize: none;
}

.highlight
{
    background-color: #f7f7f9;
    border: 1px solid #e1e1e8;
}

.send-message-btn
{
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    width: 30%;
}

.btn-panel .btn
{
    color:#b8b8b8;
    outline: none;
    overflow: hidden;
    transition: 0.2s all ease-in-out;
}

.btn-panel .btn:hover
{
    color:#666;
    /*background: #f8f8f8;*/
    background: #FBF9FA;
    text-decoration: none;
}
.btn-panel .btn:active
{
    background: #f8f8f8;
    box-shadow: 0 0 1px #ddd;
}

.btn-panel-conversation .btn,.btn-panel-msg .btn
{

    background: #f8f8f8;
}
.btn-panel-conversation .btn:first-child
{
    border-right: 1px solid #ddd;
}

.msg-wrap .media-heading, .media-heading a
{
    color:#003bb3;
    font-weight: bold;
}

.media-heading a {
    text-decoration: none;
}


.msg-date
{
    background: none;
    text-align: center;
    color:#aaa;
    border:none;
    box-shadow: none;
    border-bottom: 1px solid #ddd;
}


body::-webkit-scrollbar {
    width: 12px;
}


/* Let's get this party started */
::-webkit-scrollbar {
    width: 6px;
}

/* Track */
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
/*        -webkit-border-radius: 10px;
    border-radius: 10px;*/
}

/* Handle */
::-webkit-scrollbar-thumb {
/*        -webkit-border-radius: 10px;
    border-radius: 10px;*/
    background:#ddd;
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
}
::-webkit-scrollbar-thumb:window-inactive {
    background: #ddd;
}

/*---- reskin ----*/

.img-circle, .user-image img {
    border-radius: 50%;
}

.user-image img {
    display: inline-block;
    float: left;
}

.user-image {
    outline: none;
}

#chat_container .msg {
    padding: 15px 10px 0;
}

.media > .pull-right {
    margin-left: 10px;
}

#chat_container .media-body {
    background: #FBF9FA;
    padding: 10px;
    margin-bottom: 10px;
    line-height: 1.4;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

#chat_container .overflow-visible {
    overflow: visible;
}

#chat_container .margin-left {
    margin-left: 50px;
}

#chat_container .margin-right {
    margin-right: 50px;
}

#chat_container .msg-text {
    width: 70%;
    padding: 0px;
    font-size: 13px;
    color: #333;
}

.private {
    border: 2px solid <?php echo ! empty($conf->global->CHAT_PRIVATE_MSG_BORDER_COLOR) ? $conf->global->CHAT_PRIVATE_MSG_BORDER_COLOR : "#eea236"; ?>;
    border-radius: 50%;
}

.private-body {
    background: <?php echo ! empty($conf->global->CHAT_PRIVATE_MSG_BACKGROUND_COLOR) ? $conf->global->CHAT_PRIVATE_MSG_BACKGROUND_COLOR : "#fbf9fa"; ?> !important;
    border-radius: 2px;
}

.dropdown-content {
    padding: 5px 0;
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    border: 1px solid #ccc;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 4px;
}

.msg-attachment + .dropdown-content {
    padding: 0px;
}

.clearfix:after {
    content: "";
    display: table;
    clear: both;
}

.float-clear-left {
    float: left;
    clear: left;
}

/*---- end of reskin ----*/

<?php

} // fin if (! $is_chat_index_page)
