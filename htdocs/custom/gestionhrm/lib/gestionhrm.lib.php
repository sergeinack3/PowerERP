<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
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
 *	\file		lib/events.lib.php
 *	\ingroup	events
 *	\brief		This file is an example module library
 *				Put some comments here
 */

/**
 *  Return array head with list of tabs to view object informations.
 *
 *  @return array               head array with tabs
 */
function gestionhrm_prepare_head($id)
{
    global $db, $langs, $conf;

    $h = 0;
    $head = array();
    $id = ($id? $id :GETPOST("id_event"));
    $head[$h][0] = dol_buildpath("/gestionhrm/card.php?id=".$id, 1);
    $head[$h][1] = $langs->trans('event');
    $head[$h][2] = 'card';
    $h++;

    $head[$h][0] = dol_buildpath("/gestionhrm/participants/list.php?id_event=".$id, 1);;
    $head[$h][1] = $langs->trans('participants');
    $head[$h][2] = 'participants';
    $h++;

    return $head;
}

