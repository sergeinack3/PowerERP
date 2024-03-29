<?php
/* Copyright (C) 2011 Juanjo Menent           <2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU  *General Public License as published by
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
 */

/**
 *       \file       htdocs/pos/class/ticketsstats.class.php
 *       \ingroup    ticketss
 *       \brief      Fichier de la classe de gestion des stats des ticketss
 *       \version    $Id: ticketsstats.class.php,v 1.1 2011-06-23 10:32:25 jmenent Exp $
 */
include_once DOL_DOCUMENT_ROOT . "/core/class/stats.class.php";
include_once DOL_DOCUMENT_ROOT . "/tickets/class/tickets.class.php";
include_once DOL_DOCUMENT_ROOT . "/fourn/class/fournisseur.tickets.class.php";


/**
 *       \class      ticketsStats
 *       \brief      Classe permettant la gestion des stats des ticketss
 */
class ticketsStats extends Stats
{
    var $db;

    var $socid;
    var $where;

    var $table_element;
    var $field;


    /**
     * Constructor
     *
     * @param    $DB        Database handler
     * @param    $socid    Id third party
     * @param    $mode    Option
     * @return    ticketsStats
     */
    function ticketsStats($db, $socid = 0)
    {
        global $user, $conf;

        $this->db = $db;

        $this->socid = $socid;

        $object = new tickets($this->db);
        $this->from = MAIN_DB_PREFIX . $object->table_element . " as c";
        $this->from .= ", " . MAIN_DB_PREFIX . "societe as s";
        $this->field = 'total_ht';
        $this->where .= " c.fk_statut > 0";

        $this->where .= " AND c.fk_soc = s.rowid AND s.entity = " . $conf->entity;

        if (!$user->rights->societe->client->voir && !$this->socid) $this->where .= " AND c.fk_soc = sc.fk_soc AND sc.fk_user = " . $user->id;
        if ($this->socid) {
            $this->where .= " AND c.fk_soc = " . $this->socid;
        }

    }

    /**
     *  Returns the month number of ticketss for a year
     * @param    int        year to search
     * @return    array    Array of values
     */
    function getNbByMonth($year)
    {
        global $user;

        $sql = "SELECT date_format(c.date_tickets,'%m') as dm, count(*) nb";
        $sql .= " FROM " . $this->from;
        if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", " . MAIN_DB_PREFIX . "societe_commerciaux as sc";
        $sql .= " WHERE date_format(c.date_tickets,'%Y') = " . $year;
        $sql .= " AND " . $this->where;
        $sql .= " GROUP BY dm";
        $sql .= $this->db->order('dm', 'DESC');

        return $this->_getNbByMonth($year, $sql);
    }

    /**
     *    Returns the tickets number for year
     * @return    array    Array of values
     */
    function getNbByYear()
    {
        global $user;

        $sql = "SELECT date_format(c.date_tickets,'%Y') as dm, count(*), sum(c." . $this->field . ")";
        $sql .= " FROM " . $this->from;
        if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", " . MAIN_DB_PREFIX . "societe_commerciaux as sc";
        $sql .= " WHERE " . $this->where;
        $sql .= " GROUP BY dm";
        $sql .= $this->db->order('dm', 'DESC');

        return $this->_getNbByYear($sql);
    }

    /**
     *    Returns ticketss number for indicated year
     * @param  int    year year to search
     * @return    array    Array of values
     */
    function getAmountByMonth($year)
    {
        global $user;

        $sql = "SELECT date_format(c.date_tickets,'%m') as dm, sum(c." . $this->field . ")";
        $sql .= " FROM " . $this->from;
        if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", " . MAIN_DB_PREFIX . "societe_commerciaux as sc";
        $sql .= " WHERE date_format(c.date_tickets,'%Y') = " . $year;
        $sql .= " AND " . $this->where;
        $sql .= " GROUP BY dm";
        $sql .= $this->db->order('dm', 'DESC');

        return $this->_getAmountByMonth($year, $sql);
    }

    /**
     *    Returns ticketss number for indicated year
     * @param  int    year year to search
     * @return    array    Array of values
     */
    function getAverageByMonth($year)
    {
        global $user;

        $sql = "SELECT date_format(c.date_tickets,'%m') as dm, avg(c." . $this->field . ")";
        $sql .= " FROM " . $this->from;
        if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", " . MAIN_DB_PREFIX . "societe_commerciaux as sc";
        $sql .= " WHERE date_format(c.date_tickets,'%Y') = " . $year;
        $sql .= " AND " . $this->where;
        $sql .= " GROUP BY dm";
        $sql .= $this->db->order('dm', 'DESC');

        return $this->_getAverageByMonth($year, $sql);
    }


    /**
     *    Return nb, total and average
     * @return    array    Array of values
     */
    function getAllByYear()
    {
        global $user;

        $sql = "SELECT date_format(c.date_tickets,'%Y') as year, count(*) as nb, sum(c." . $this->field . ") as total, avg(" . $this->field . ") as avg";
        $sql .= " FROM " . $this->from;
        if (!$user->rights->societe->client->voir && !$this->socid) $sql .= ", " . MAIN_DB_PREFIX . "societe_commerciaux as sc";
        $sql .= " WHERE " . $this->where;
        $sql .= " GROUP BY year";
        $sql .= $this->db->order('year', 'DESC');

        return $this->_getAllByYear($sql);
    }
}
