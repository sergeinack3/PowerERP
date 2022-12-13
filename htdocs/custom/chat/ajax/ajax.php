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
 *       \file       htdocs/chat/ajax/ajax.php
 *       \brief      File to do ajax actions
 */

define('NOTOKENRENEWAL', 1);

// Load PowerERP environment
if (false === (@include '../../main.inc.php')) {  // From htdocs directory
	require '../../../main.inc.php'; // From "custom" directory
}

global $db, $langs, $user;

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";

dol_include_once('/chat/class/chat.class.php'); // this function support custom folder
dol_include_once('/chat/class/chatMessage.class.php');

// Get parameters
$action	= GETPOST('action','alpha');
$filter_user = GETPOST('filter_user','alpha');
$show_date = GETPOST('show_date','alpha');
$user_to_id = GETPOST('user_to_id','int') > 0 ? GETPOST('user_to_id','int') : GETPOST('filter_by_user','int');
$only_online = GETPOST('only_online','alpha');
$setting_name = GETPOST('name','alpha');
$setting_value = GETPOST('value','alpha');
$referrer = GETPOST('referrer','alpha');

// Access control
if (! $user->rights->chat->lire) {
	// External user
	accessforbidden();
}

/*
 * View
 */

top_httphead();

//print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

// Actions
if (isset($action) && ! empty($action))
{
	if ($action == 'fetch_msgs')
	{
            $object = new Chat($db);

            // récupération des messages
            $result = $object->fetch_messages($user, $user_to_id, $referrer);

            if ($result)
            {
                dol_include_once('/chat/tpl/message.tpl.php');
            }
    } // fin if ($action == 'fetch_msgs')
    else if ($action == 'fetch_users')
	{
            $object = new Chat($db);

            // récupération des utilisateurs
            $result = $object->fetch_users($user, 1, $filter_user, 1);

            if ($only_online == 'true') {
                // filter online users
                foreach ($object->users as $user_rowid => $f_user) {
                    if (! $f_user->is_online) {
                        unset($object->users[$user_rowid]);
                    }
                }
            }

            if ($result)
            {
            	dol_include_once('/chat/tpl/user.tpl.php');
            }
    } // fin if ($action == 'fetch_users')
    else if ($action == 'set_settings')
	{
            $object = new Chat($db);

            // set settings
            $result = $object->set_settings($setting_name, $setting_value, $user);

            //if ($result > 0)
            //{
                //print 'saved';
            //}
            //else
            //{
                //print 'error';
            //}
    } // fin if ($action == 'set_settings')
    else if ($action == 'send_msg')
	{
            $msg = GETPOST('msg', 'alpha');

            if (! empty($msg))
            {
                $now = dol_now();

                $myobject = new ChatMessage($db);
                $myobject->post_time = $now;
                $myobject->text = $msg;
                $myobject->fk_user_to = $user_to_id;

                // TODO: add ajax attachment support

                $result = $myobject->send($user);

                //print $result;
            }
            //else
            //{
                //print 'empty msg';
            //}
    } // fin if ($action == 'send_msg')
}
