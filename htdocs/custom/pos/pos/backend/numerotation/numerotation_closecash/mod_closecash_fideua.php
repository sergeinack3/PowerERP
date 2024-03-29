<?php
/* Copyright (C) 2003-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2007 Regis Houssin        <regis@powererp.fr>
 * Copyright (C) 2008      Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
 * Copyright (C) 2010	   Denis Martin	<denimartin@hotmail.fr>
 * Copyright (C) 2017      Ferran Marcet        <fmarcet@2byte.es>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/pointofsale/inc/model/num/mod_facture_pointofsale.php
 *	\ingroup    pointofsale
 *	\brief      File containing class for numbering module Pointofsale
 *	\version    $Id: mod_facture_pointofsale.php,v 1.1 2010/10/29 16:40:50 hregis Exp $
 */
dol_include_once('/pos/backend/numerotation/numerotation_closecash/modules_closecash.php');


/**
 *	\class      mod_facture_pointofsale
 *	\brief      Classe du modele de numerotation de reference de facture Pointofsale
 */
class mod_closecash_fideua extends ModeleNumRefCloseCash
{
    var $version = 'powererp';        // 'development', 'experimental', 'powererp'
    var $prefixclosecash = 'CC';
    var $prefixarq = 'AR';
    var $error = '';

    /**     \brief      Renvoi la description du modele de numerotation
     *      \return     string      Texte descripif
     */
    function info()
    {
        global $langs;
        $langs->load("bills");
        return $langs->trans('POSCloseCashRefModelDesc', $this->prefixclosecash);
    }

    /**     \brief      Renvoi un exemple de numerotation
     *      \return     string      Example
     */
    function getExample()
    {
        return $this->prefixclosecash . "0501-0001";
    }

    /**     \brief      Test si les numeros deja en vigueur dans la base ne provoquent pas de
     *                  de conflits qui empechera cette numerotation de fonctionner.
     *      \return     boolean     false si conflit, true si ok
     */
    function canBeActivated()
    {
        global $langs, $conf, $db;

        $langs->load("bills");

        // Check closecash num
        $fayymm = '';
        $max = '';

        $posindice = 8;
        $sql = "SELECT MAX(SUBSTRING(ref FROM " . $posindice . ")) as max";    // This is standard SQL
        $sql .= " FROM " . MAIN_DB_PREFIX . "pos_control_cash";
        $sql .= " WHERE ref LIKE '" . $this->prefixclosecash . "____-%'";
        $sql .= " AND entity = " . $conf->entity;

        $resql = $db->query($sql);
        if ($resql) {
            $row = $db->fetch_row($resql);
            if ($row) {
                $fayymm = substr($row[0], 0, 6);
                $max = $row[0];
            }
        }
        if ($fayymm && !preg_match('/' . $this->prefixclosecash . '[0-9][0-9][0-9][0-9]/i', $fayymm)) {
            $langs->load("errors");
            $this->error = $langs->trans('ErrorNumRefModel', $max);
            return false;
        }

        return true;
    }

    /**     \brief      Renvoi prochaine valeur attribuee
     * @param      object        Object objsoc
     * @param      object        Object facture
     * @return     string      Valeur
     */
    function getNextValue($objsoc, $facture)
    {
        global $db, $conf;

        if ($facture->type_control == 0) $prefix = $this->prefixarq;
        else $prefix = $this->prefixclosecash;

        // D'abord on recupere la valeur max
        $posindice = 8;
        $sql = "SELECT MAX(SUBSTRING(ref FROM " . $posindice . ")) as max";    // This is standard SQL
        $sql .= " FROM " . MAIN_DB_PREFIX . "pos_control_cash";
        $sql .= " WHERE ref LIKE '" . $prefix . "____-%'";
        $sql .= " AND entity = " . $conf->entity;

        $resql = $db->query($sql);
        dol_syslog("mod_closecash_fideua::getNextValue sql=" . $sql);
        if ($resql) {
            $obj = $db->fetch_object($resql);
            if ($obj) $max = intval($obj->max);
            else $max = 0;
        } else {
            dol_syslog("mod_closecash_fideua::getNextValue sql=" . $sql, LOG_ERR);
            return -1;
        }

        $date = $facture->date;    // This is closecash date (not creation date)
        if (!$date) $date = dol_now();
        $yymm = strftime("%y%m", $date);
        $num = sprintf("%04s", $max + 1);

        dol_syslog("mod_closecash_fideua::getNextValue return " . $prefix . $yymm . "-" . $num);
        return $prefix . $yymm . "-" . $num;
    }

    /**
     * \brief      Return next free value
     * @param      object      Object third party
     * @param      object    Object for number to search
     * @return     string      string      Next free value
     */
    function getNumRef($objsoc, $objforref)
    {
        return $this->getNextValue($objsoc, $objforref);
    }

}