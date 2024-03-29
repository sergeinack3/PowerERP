<?php
/* Copyright (C) 2011	Juanjo Menent  <jmenent@2byte.es>
 * Copyright (C) 2014-2017 Ferran Marcet        <fmarcet@2byte.es>
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
 * or see http://www.gnu.org/
 */

/**
 *	    \file       htdocs/pos/backend/lib/tickets.lib.php
 *		\brief      Ensemble de fonctions de base pour le module pos
 * 		\ingroup	pos
 *
 *		Ensemble de fonctions de base de powererp sous forme d'include
 *
 *      @param    object $object
 *      @return   array
 */

function tickets_prepare_head($object)
{
    global $langs;
    $h = 0;
    $head = array();


    $head[$h][0] = dol_buildpath('/pos/backend/tickets.php', 1) . '?id=' . $object->id;
    $head[$h][1] = $langs->trans('Cardtickets');
    $head[$h][2] = 'tickets';
    $h++;

    $head[$h][0] = dol_buildpath('/pos/backend/info.php', 1) . '?id=' . $object->id;;
    $head[$h][1] = $langs->trans('Info');
    $head[$h][2] = 'info';

    return $head;
}