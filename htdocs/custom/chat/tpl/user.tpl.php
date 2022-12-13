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
 *       \file       htdocs/chat/tpl/message.tpl.php
 *       \brief      Template of message(s)
 */

dol_include_once('/chat/lib/chat.lib.php');

global $db, $conf, $user, $langs, $object;

$langs->load('chat@chat');

$userstatic = new User($db);

// affichage des utilisateurs
foreach ($object->users as $obj)
{
    $userstatic->id=$obj->rowid;
    $userstatic->firstname=$obj->firstname;
    $userstatic->lastname=$obj->lastname;
    $userstatic->gender=$obj->gender;
    $userstatic->photo=$obj->photo;
    // j'ai spécifié juste ces attributs car ce sont les attributs nécessaires pour les fonctions de récupération de nom complet et photo
?>
    <a class="user-anchor" href="<?php echo dol_buildpath('/chat/index.php', 1).'?action=private_msg&user_to_id='.$obj->rowid; ?>" title="<?php echo $langs->trans("SendPrivateMessage"); ?>">
        <div class="media conversation <?php echo ($obj->is_online ? "is_online" : "").($obj->admin ? " is_admin" : ""); ?>">
            <span class="pull-left user-image">
                <?php
                    echo Form::showphoto('userphoto', $userstatic, 64, 64, 0, '', 'small', 0, 1);
                ?>
            </span>
            <div class="media-body">
                <h5 class="media-heading">
                    <span>
                        <?php
                            echo $userstatic->getFullName($langs);
                        ?>
                    </span>
                    <?php
                        if (! empty($conf->multicompany->enabled) && $obj->admin && ! $obj->entity)
                        {
                            print img_picto($langs->trans("SuperAdministrator"),'redstar');
                        }
                        else if ($obj->admin)
                        {
                            print img_picto($langs->trans("Administrator"),'star');
                        }

                        // si utilisateur en ligne
                        if ($obj->is_online)
                        {
                            print ' <img class="online-icon align-middle" title="'.$langs->trans("Online").'" alt="" src="'.dol_buildpath('/chat/img/online.png', 1).'"/>';
                        }
                    ?>
                </h5>
                <?php
                    if ($obj->last_private_msg)
                    {
                ?>
                    <div class="last-private-msg">
                        <?php
                            if ($obj->last_private_msg_sender == $user->id) {
                                print img_picto('out','out@chat');
                            }
                            else {
                                print img_picto('in','in@chat');
                            }
                        ?>
                        <span><?php echo dol_trunc($obj->last_private_msg, 24); ?></span>
                        <small class="pull-right time"><?php echo dol_print_date($db->jdate($obj->last_private_msg_post_time),"hour"); ?></small>
                    </div>
                <?php
                    }
                ?>
            </div>
        </div>
    </a>
<?php

} // fin foreach

// si aucun utilisateur PowerERP trouvé
//if (count($object->users) == 0)
//{
//    echo $langs->trans("NoUserFound");
//}
