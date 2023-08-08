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

// TODO: Remove old/unused class/code

define('NOLOGIN', 1);
define('NOREDIRECTBYMAINTOLOGIN', 1);
define('NOTOKENRENEWAL', 1);

// Load PowerERP environment
if (false === (@include '../../main.inc.php')) {  // From htdocs directory
	require '../../../main.inc.php'; // From "custom" directory
}

global $conf;

header('Content-Type: text/css');

?>

/* ECM */

#containerlayout .ecm-layout-pane { /* all 'panes' */
    background: #FFF;
    border:     1px solid #BBB;
    /* DO NOT add scrolling (or padding) to 'panes' that have a content-div,
       otherwise you may get double-scrollbars - on the pane AND on the content-div
    */
    padding:    0px;
    overflow:   auto;
    direction: ltr;
}
/* (scrolling) content-div inside pane allows for fixed header(s) and/or footer(s) */
#containerlayout .ecm-layout-content {
	padding:    10px;
	position:   relative; /* contain floated or positioned elements */
	overflow:   auto; /* add scrolling to content-div */
}

.ecm-layout-toggler {
    border-top: 1px solid #AAA; /* match pane-border */
    border-right: 1px solid #AAA; /* match pane-border */
    border-bottom: 1px solid #AAA; /* match pane-border */
    background-color: #CCC;
    }
.ecm-layout-toggler-open {
	height: 48px !important;
	width: 6px !important;
    -moz-border-radius:0px 10px 10px 0px;
	-webkit-border-radius:0px 10px 10px 0px;
	border-radius:0px 10px 10px 0px;
}
.ecm-layout-toggler-closed {
	height: 48px !important;
	width: 6px !important;
}

.ecm-layout-toggler .content {	/* style the text we put INSIDE the togglers */
    color:          #666;
    font-size:      12px;
    font-weight:    bold;
    width:          100%;
    padding-bottom: 0.35ex; /* to 'vertically center' text inside text-span */
}
#ecm-layout-west-resizer {
	width: 6px !important;
}

.ecm-layout-resizer  { /* all 'resizer-bars' */
    border:         1px solid #BBB;
    border-width:   0;
    }
.ecm-layout-resizer-closed {
}

.ecm-in-layout-center {
    border-left: 1px !important;
    border-right: 0px !important;
    border-top: 0px !important;
}

.ecm-in-layout-south {
    border-top: 0px !important;
    border-left: 0px !important;
    border-right: 0px !important;
    border-bottom: 0px !important;
    padding: 4px 0 4px 4px !important;
}

#ecm-layout-center {
overflow: hidden !important;
}

.ecm-in-layout-center {
height: 100%;
}

#chatForm .form-control:focus {
    border-color: #66AFE9 !important;
}

/*---- Bootstrap ----*/

.form-control, .form-control:before, .form-control:after {
    box-sizing: border-box;
}

.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857;
    color: #555;
    vertical-align: middle;
    background-color: #FFF;
    background-image: none;
    border: 1px solid #CCC;
    border-radius: 4px;
    box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset;
    transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
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

textarea.form-control {
    height: 85px;
}

.btn {
    display: inline-block;
    padding: 8px;
    margin-bottom: 0px;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    -moz-user-select: none;
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
    margin: 0px;
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

.input-group {
    position: relative;
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
    padding: 6px;
    cursor: pointer;
}

#smiley-dropdown td:hover {background-color: #f1f1f1}

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
    height: 76%;
    overflow: auto;
    font-size: 15px;
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
    width: 85%;
    padding: 0px;
    font-size: 14px;
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

#custom-search-input {
    background: #e8e6e7;
}

#users_container .conversation {
    padding: 10px;
}

.more-width {
    min-width: 200px;
}

#chat_head {
    background: #F5F3F3;
    float: left;
    display: block;
    width: 100%;
    margin: 0px;
    padding: 10px;
    box-sizing: border-box;
}

#chat_container {
    height: calc(100% - 225px); /* 100% - 55px (chat head) - 150px (send form) - 20px (chat container padding (10px top + 10px bottom)) */
}

.chat-head-clickable {
    display: inline-block;
    padding: 10px 5px;
    cursor: pointer;
}

#chat_head .grey-bold-text, .grey-bold-text {
    color: #333;
    font-size: 13px;
    font-weight: bold;
    text-decoration: none;
    outline: none;
}

.delete-checkbox {
    margin: 10px;
}

#chat_head .hidden {
    display: none !important;
}

.users-filter {
    background: #f5f3f3;
    border-top: 1px solid #dddddd;
    width: 100%;
    box-sizing: border-box;
}

.users-filter label {
    display: block;
    text-align: left;
    font-size: 14px;
    color: #333;
    padding: 10px;
    border-bottom: 1px solid #dddddd;
    border-radius: 0px;
}

.caret {
    bottom: 0;
    margin: auto;
    position: absolute;
    right: 15px;
    top: 0;
}

.users-filter .dropdown-content {
    background: #f5f3f3;
    border-radius: 0;
    margin-top: 0;
    padding: 0;
    width: 100%;
    box-sizing: border-box;
    border: none;
}

.users-filter .dropdown-content div {
    padding: 0px;
}

.user-anchor {
    outline: none;
}

.user-anchor .media-body {
    margin: 10px 5px 0px 10px;
    color: #333;
}

.user-anchor:hover {
    text-decoration: none;
}

.conversation:hover {
    background: #FBF9FA;
}

.chat-head-user {
    display: inline-block;
    outline: none;
}

.chat-head-user:hover {
    text-decoration: none;
}

.chat-head-user .user-image {
    display: inline-block;
    margin-right: 10px;
}

#chat_head .user-name, .user-anchor .media-heading span {
    color: #003bb3;
}

.user-anchor .media-heading {
    margin-bottom: 10px;
}

.last-private-msg {
    font-size: 13px;
}

.chat-head-user small {
    color: #333;
}

.divider {
    border-left: 1px solid #ddd;
    display: inline-block;
    margin: 0px 15px;
    margin-top: -10px;
    margin-bottom: -10px;
    padding: 20px 0px;
}

#custom-search-input {
    position: relative;
    padding-right: 40px;
}

#sound_switch {
    right: 10px;
}

<?php
