<?php
/* Copyright (C) 2018       Marcello Gribaudo   <marcello.gribaudo@opigi.com>
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
 *	\file       htdocs/crmenhanced/activity.class.php
 *	\inactivity    crmenhanced
 *	\brief      File of class to manage activities
 */

/**
 *	Class to manage activities
 */

class Activity extends CommonObject {

    public $picto = 'activity';

    public $datec = '';
    public $fk_campaign = 0;
    public $priority = 0;

    /**
     *	Constructor
     *
     *  @param		DoliDB		$db     Database handler
     */
    function __construct($db) {
        $this->db = $db;
    }

    /**
     * 	Load activity into memory from database
     *
     * 	@param		int		$id		Id of activity
     *  @param		string          $label          Label of activity
     * 	@return		int				<0 if KO, >0 if OK
     */
    function fetch($id) {
        global $conf;

        // Check parameters
        if (empty($id))
            return -1;

        $sql = "SELECT datec, fk_campaign, priority, activity_type, fk_mode, fk_status, fk_thirdparty, fk_contact, fk_owner, duration, fk_object, note, fk_next_owner, next_date, next_note";
        $sql.= " FROM ".MAIN_DB_PREFIX."crm_activity";
        $sql.= " WHERE rowid = ".$id;

        dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql) {
            if ($this->db->num_rows($resql) > 0) {
                $res = $this->db->fetch_array($resql);

                $this->id		= $id;
                $this->datec            = $res['datec'];
                $this->fk_campaign      = $res['fk_campaign'];
                $this->priority         = $res['priority'];
                $this->activity_type    = $res['activity_type'];
                $this->fk_mode          = $res['fk_mode'];
                $this->fk_status        = $res['fk_status'];
                $this->fk_thirdparty    = $res['fk_thirdparty'];
                $this->fk_contact       = $res['fk_contact'];
                $this->fk_owner         = $res['fk_owner'];
                $this->duration         = $res['duration'];
                $this->fk_object        = $res['fk_object'];
                $this->note             = $res['note'];
                $this->fk_next_owner    = $res['fk_next_owner'];
                $this->next_date        = $res['next_date'];
                $this->next_note        = $res['next_note'];

                $this->db->free($resql);

                return 1;
            } else {
                return 0;
            }
        } else {
            dol_print_error($this->db);
            return -1;
        }
    }

    /**
     * 	Add activity into database
     *
     * 	@return	int                                     -1 : SQL error
     *          					-2 : new ID unknown
     *          					-3 : Invalid activity
     */
    function create() {
        global $conf, $langs;

        $error=0;
        dol_syslog(get_class($this).'::create', LOG_DEBUG);

        $sql = "INSERT INTO ".MAIN_DB_PREFIX."crm_activity (";
        $sql.= "datec, fk_campaign, fk_thirdparty, priority, activity_type, fk_mode, fk_status, fk_contact, fk_owner, duration, fk_object, note, fk_next_owner, next_date, next_note";
        $sql.= ") VALUES (";
        $sql.= (strval($this->datec)!='' ? "'".$this->db->idate($this->datec)."'" : 'null').",";
        $sql.= ($this->fk_campaign?$this->fk_campaign:'null').",";
        $sql.= $this->fk_thirdparty.",";
        $sql.= $this->priority.",";
        $sql.= "'".$this->activity_type."',";
        $sql.= $this->fk_mode.",";
        $sql.= $this->fk_status.", ";
        $sql.= ($this->fk_contact && $this->fk_contact != -1?$this->fk_contact:'null').",";
        $sql.= ($this->fk_owner && $this->fk_owner != -1?$this->fk_owner:'null').",";
        $sql.= (strval($this->duration)!='' ? "'".$this->db->idate($this->duration)."'" : 'null').",";;
        $sql.= $this->fk_object.",";
        $sql.= "'".$this->db->escape($this->note)."',";
        $sql.= ($this->fk_next_owner && $this->fk_next_owner != -1?$this->fk_next_owner:'null').",";
        $sql.= (strval($this->next_date)!='' ? "'".$this->db->idate($this->next_date)."'" : 'null').",";;
        $sql.= "'".$this->db->escape($this->next_note)."'";
        $sql.= ")";
        $res = $this->db->query($sql);
        if ($res) {
            $id = $this->db->last_insert_id(MAIN_DB_PREFIX."crm_activity");
            return $id;
        } else {
            $this->error=$this->db->error();
            return -1;
        }
    }

    /**
     * 	Update activity
     *
     *	@param	string                $id		Activity id
     * 	@return	int		 			1 : OK
     *          					-1 : SQL error
     *          					-2 : invalid activity
     */
    function update($id) {

        $sql = "UPDATE ".MAIN_DB_PREFIX."crm_activity";
        $sql.= " SET datec = ".(strval($this->datec)!='' ? "'".$this->db->idate($this->datec)."'" : 'null').",";
        if ($this->fk_campaign)
            $sql.= " fk_campaign = ".$this->fk_campaign.",";
        $sql.= " priority = ".$this->priority.",";
        $sql.= " activity_type = '".$this->activity_type."',";
        $sql.= " fk_mode = ".$this->fk_mode.",";
        $sql.= " fk_status = ".$this->fk_status.", ";
        $sql.= " fk_contact = ".($this->fk_contact && $this->fk_contact != -1?$this->fk_contact:'null').",";
        $sql.= " fk_owner = ".($this->fk_owner && $this->fk_owner != -1?$this->fk_owner:'null').",";
        $sql.= " duration = ".(strval($this->duration)!='' ? "'".$this->db->idate($this->duration)."'" : 'null').",";;
        $sql.= " fk_object = ".$this->fk_object.",";
        $sql.= " note = '".$this->db->escape($this->note)."',";
        $sql.= " fk_next_owner = ".($this->fk_next_owner && $this->fk_next_owner != -1?$this->fk_next_owner:'null').",";
        $sql.= " next_date = ".(strval($this->next_date)!='' ? "'".$this->db->idate($this->next_date)."'" : 'null').",";;
        $sql.= " next_note = '".$this->db->escape($this->next_note)."'";
        $sql .= " WHERE rowid = ".$id;

        dol_syslog(get_class($this)."::update", LOG_DEBUG);
        if ($this->db->query($sql)) {
            return $id;
        } else {
            $this->error=$this->db->lasterror();
            return -1;
        }
    }

    /**
     * 	Delete a activity from database
     *
     *	@return	int                 <0 KO >0 OK
     */
    function delete() {

        $error=0;
        
        dol_syslog(get_class($this)."::remove");

        $sql = "DELETE FROM ".MAIN_DB_PREFIX."crm_activity";
        $sql .= " WHERE rowid = ".$this->id;
        if (!$this->db->query($sql)) {
            $this->error=$this->db->lasterror();
            $error++;
            return False;
        } else {
            return True;
        }
    }


    /**
    *	Return label of activity type
    *
    *	@param          string			$type       I=Incoming, O=outcoming
    * 	@return 	string                              Label of contact type
    */
   function getType($type)   {
        global $langs;
       
        if ($type == 'O')
           return $langs->trans('Outcoming');
        elseif ($type == 'I')
           return $langs->trans('Incoming');
       else 
           return '';
   }

    /**
    *	Trsnsform an activity position to string activity type
    *
    *	@param          int			$pos       0='', 1="O" (Outcoming),2="I"(Incoming)
    * 	@return 	string                              Label of contact type
    */
   function setType($pos)   {
        return substr(' OI',(int)$pos,1);
   }
   
    /**
    *	Return label of activity status
    *
    *	@param          int			$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=short label + Picto
    * 	@return 	string                              Label of contact status
    */
   function getStatut($mode)   {
       return '';
   }
        
}
