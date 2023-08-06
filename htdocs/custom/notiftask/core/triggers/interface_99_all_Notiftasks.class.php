<?php
/* Copyright (C) 2005-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *  \file       htdocs/core/triggers/interface_90_all_Demo.class.php
 *  \ingroup    core
 *  \brief      Fichier de demo de personalisation des actions du workflow
 *  \remarks    Son propre fichier d'actions peut etre cree par recopie de celui-ci:
 *              - Le nom du fichier doit etre: interface_99_modMymodule_Mytrigger.class.php
 *				                           ou: interface_99_all_Mytrigger.class.php
 *              - Le fichier doit rester stocke dans core/triggers
 *              - Le nom de la classe doit etre InterfaceMytrigger
 *              - Le nom de la methode constructeur doit etre InterfaceMytrigger
 *              - Le nom de la propriete name doit etre Mytrigger
 */


/**
 *  Class of triggers for demo module
 */
class InterfaceNotiftasks
{
    var $db;
    
    /**
     *   Constructor
     *
     *   @param		DoliDB		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
    
        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "notification";
        $this->description = "Triggers of this module are empty functions. They have no effect. They are provided for tutorial purpose only.";
        $this->version = 'powererp';            // 'development', 'experimental', 'powererp' or version
        $this->picto = 'email';
    }
    
    
    /**
     *   Return name of trigger file
     *
     *   @return     string      Name of trigger file
     */
    function getName()
    {
        return $this->name;
    }
    
    /**
     *   Return description of trigger file
     *
     *   @return     string      Description of trigger file
     */
    function getDesc()
    {
        return $this->description;
    }

    /**
     *   Return version of trigger file
     *
     *   @return     string      Version of trigger file
     */
    function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'development') return $langs->trans("Development");
        elseif ($this->version == 'experimental') return $langs->trans("Experimental");
        elseif ($this->version == 'powererp') return DOL_VERSION;
        elseif ($this->version) return $this->version;
        else return $langs->trans("Unknown");
    }
    
    /**
     *      Function called when a PowerERPr business event is done.
     *      All functions "run_trigger" are triggered if file is inside directory htdocs/core/triggers
     *
     *      @param	string		$action		Event action code
     *      @param  Object		$object     Object
     *      @param  User		$user       Object user
     *      @param  Translate	$langs      Object langs
     *      @param  conf		$conf       Object conf
     *      @return int         			<0 if KO, 0 if no triggered ran, >0 if OK
     */
	function run_trigger($action,$object,$user,$langs,$conf)
    {
        // Put here code you want to execute when a PowerERP business events occurs.
        // Data and type of action are stored into $object and $action   
    
        // Project tasks
        if ($action == 'TASK_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
            
            include_once(DOL_DOCUMENT_ROOT."/core/class/CMailFile.class.php");
            $langs->load("notiftask@notiftask");
            
            //Busquem l'e-mail de l'usuari al que s'ha assignat la tasca
            $userid = GETPOST('userid', 'int');
            $sql_email = "SELECT email FROM ".MAIN_DB_PREFIX."user WHERE rowid = '$userid'";
            $res_email = $this->db->query($sql_email);
            if ($res_email){
                $num=$this->db->num_rows($res_email);
                while ($obj=$this->db->fetch_array($res_email)) {
                    $email = $obj[0]; 
                }            
            }
            
            //Busquem el nom del projecte a partir de l'id, format per ( id= fk_projet_fk_task_parent)
            $id = GETPOST('task_parent', 'alpha');
            $projecte = explode("_",$id);
            $sql_projecte = "SELECT p.ref, p.title FROM ".MAIN_DB_PREFIX."projet p , ".MAIN_DB_PREFIX."projet_task pt WHERE pt.fk_projet = '".$projecte[0]."' AND pt.fk_projet = p.rowid";
            $res_projecte = $this->db->query($sql_projecte);
            if ( $res_projecte ) {
                $num=$this->db->num_rows($res_projecte);
                while ($obj=$this->db->fetch_array($res_projecte)) {
                    $proj = $obj[0]." - ".$obj[1]; 
                }                  
            }
            
            $file = "";

            $subject = $langs->trans("subject_notif");
            $to = $bcc = $email;
            $from = ''.$conf->global->MAIN_INFO_SOCIETE_MAIL.' <'.$conf->global->MAIN_INFO_SOCIETE_MAIL.'>';
            $message = $langs->trans("textmail1").' <strong>'.GETPOST('label', 'alpha').'</strong> '.$langs->trans("textmail2").' <strong>'.$proj.'</strong>';
            $mailfile = new CMailFile($subject,$to,$from,$message,array($file),'','','', '', 0, -1,'','');
            $mailfile->sendfile();
            setEventMessage($langs->trans("missatgenotif").' '.$email, 'mesgs');
        }
		return 0;
    }

}
?>
