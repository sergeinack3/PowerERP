<?php

/** Includes */
require_once 'chatMessage.class.php';

/**
 * Put your class' description here
 */

class Chat // extends CommonObject
{

    /** @var DoliDb Database handler */
	private $db;
    /** @var string Error code or message */
	public $error;
    /** @var array Several error codes or messages */
	public $errors = array();
    /** @var mixed An example property */
	public $users = array();
    /** @var mixed An example property */
	public $messages = array();
    /** @var int Total messages count */
	public $messages_count = 0;
    /** @var mixed An example property */
	public $settings;

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;

		return 1;
	}

	/**
	 * Load object in memory from database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetch_users($user, $self_exclusion = 0, $filter_user = '', $check_online = 0)
	{
		global $conf, $langs;

        // si l'utilisateur est inscrit on actualise la date de la dernière vérification effectuée
        if ($this->is_online_user($user))
        {
            $this->update_online_user($user);
        }
        else // si nn on l'inscrit en tant qu'utilisateur en ligne
        {
            $this->add_online_user($user);
        }

        // et on supprime les utilisateurs qui ne sont plus en ligne
        $this->delete_offline_users();

        // fetch users
        $sql = "SELECT u.rowid, u.lastname, u.firstname, u.admin, u.gender, u.photo, u.datelastlogin";
        //$sql.= ", m.text as last_private_msg, m.fk_user as last_private_msg_sender, m.post_time as last_private_msg_post_time";
        $sql.= ", (SELECT text FROM ".MAIN_DB_PREFIX."chat_msg WHERE (fk_user = u.rowid AND fk_user_to = ".$user->id.") OR (fk_user = ".$user->id." AND fk_user_to = u.rowid) ORDER BY post_time DESC LIMIT 1) as last_private_msg";
        $sql.= ", (SELECT fk_user FROM ".MAIN_DB_PREFIX."chat_msg WHERE (fk_user = u.rowid AND fk_user_to = ".$user->id.") OR (fk_user = ".$user->id." AND fk_user_to = u.rowid) ORDER BY post_time DESC LIMIT 1) as last_private_msg_sender";
        $sql.= ", (SELECT post_time FROM ".MAIN_DB_PREFIX."chat_msg WHERE (fk_user = u.rowid AND fk_user_to = ".$user->id.") OR (fk_user = ".$user->id." AND fk_user_to = u.rowid) ORDER BY post_time DESC LIMIT 1) as last_private_msg_post_time";
        if ($check_online && $user->rights->chat->see_online_users)
        {
            $sql.= ", (SELECT count(*) FROM ".MAIN_DB_PREFIX."chat_online WHERE online_user = u.rowid) as is_online";
        }
        $sql.= " FROM ".MAIN_DB_PREFIX."user as u";
        //$sql.= " LEFT JOIN (SELECT fk_user, fk_user_to, text, post_time FROM ".MAIN_DB_PREFIX."chat_msg) m ON (m.fk_user = u.rowid AND m.fk_user_to = ".$user->id.") OR (m.fk_user = ".$user->id." AND m.fk_user_to = u.rowid)";
        if(! empty($conf->multicompany->enabled) && $conf->entity == 1 && (! empty($conf->multicompany->transverse_mode) || (! empty($user->admin) && empty($user->entity))))
        {
                $sql.= " WHERE u.entity IS NOT NULL";
        }
        else
        {
                $sql.= " WHERE u.entity IN (".getEntity('user',1).")";
        }
        if ($self_exclusion)
        {
                $sql.= " AND u.rowid != ".$user->id;
        }
        if (! empty($filter_user))
        {
                $sql.= " AND (u.lastname LIKE '%".$filter_user."%'";
                $sql.= " OR u.firstname LIKE '%".$filter_user."%')";
        }
        $sql.= " AND u.statut = 1"; // get only active/enabled users

		dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                while ($obj = $this->db->fetch_object($resql))
                {
                        $this->users[$obj->rowid] = $obj;
                        //...
                }
            }

			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(__METHOD__ . " " . $this->error, LOG_ERR);

			return -1;
		}
	}

	/**
	 * Load object in memory from database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	public function is_online_user($user)
	{
		global $conf, $langs;

        // on vérifie si l'utilisateur est enregistré ou non dans la table chat_online
        $sql = "SELECT c.online_user";
        $sql.= " FROM ".MAIN_DB_PREFIX."chat_online as c";
        $sql.= " WHERE c.online_user = ".$user->id;

		dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
        {
            $num = $this->db->num_rows($resql);

            $this->db->free($resql);

            return $num;
		}
		else
		{
			return -1;
		}
	}

	/**
	 * Load object in memory from database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	public function add_online_user($user)
	{
		global $langs;

        $now = dol_now();
        $default_status = 0;
        $ip = $_SERVER["REMOTE_ADDR"];

		// Insert request
		$sql = "INSERT INTO " . MAIN_DB_PREFIX . "chat_online(";
		$sql.= " online_ip,";
        $sql.= " online_user,";
        $sql.= " online_time,";
		$sql.= " online_status";

		$sql.= ") VALUES (";
		$sql.= " '" . $ip . "',";
		$sql.= " '" . $user->id . "',";
                $sql.= " '" . $this->db->idate($now)."',";
		$sql.= " '" . $default_status . "'";

		$sql.= ")";

		$this->db->begin();

		dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$error ++;
			$this->errors[] = "Error " . $this->db->lasterror();
		}
                
        if (! $error) {
			//$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "chat_online");

			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action call a trigger.
				//// Call triggers
				//include_once DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php";
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
				$this->error.=($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();

			return -1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Load object in memory from database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	public function update_online_user($user)
	{
		global $langs;
                
        $now = dol_now();
                
		// Update request
		$sql = "UPDATE " . MAIN_DB_PREFIX . "chat_online SET";
        $sql.= " online_time = '".$this->db->idate($now)."'";
        $sql.= " WHERE online_user = ".$user->id;
                
		$this->db->begin();

		dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$error ++;
			$this->errors[] = "Error " . $this->db->lasterror();
		}

		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action call a trigger.
				//// Call triggers
				//include_once DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php";
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
				$this->error.=($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();

			return -1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user User that delete
	 * @param int $notrigger 0=launch triggers after, 1=disable triggers
	 * @return int <0 if KO, >0 if OK
	 */
	public function delete_offline_users($notrigger = 0)
	{
		global $conf, $langs;
		$error = 0;
                
        $now = dol_now();
        $refresh_time = ! empty($conf->global->CHAT_AUTO_REFRESH_TIME) ? $conf->global->CHAT_AUTO_REFRESH_TIME : 5;
        $timeout = $now - $refresh_time;

		$this->db->begin();

		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action call a trigger.
				//// Call triggers
				//include_once DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php";
				//$interface=new Interfaces($this->db);
				//$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
				//if ($result < 0) { $error++; $this->errors=$interface->errors; }
				//// End call triggers
			}
		}

		if (! $error) {
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "chat_online";
			$sql.= " WHERE online_time < '" . $this->db->idate($timeout)."'";

			dol_syslog(__METHOD__ . " sql=" . $sql);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ($this->errors as $errmsg) {
				dol_syslog(__METHOD__ . " " . $errmsg, LOG_ERR);
				$this->error.=($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();

			return -1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Load object in memory from database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetch_messages($user, $user_to_id = -1, $referrer = '')
	{
		global $conf, $langs;
                
        // récupération de la limite des messages à afficher
        $limit = ! empty($conf->global->CHAT_MAX_MSG_NUMBER) ? $conf->global->CHAT_MAX_MSG_NUMBER : 50;
        if ($referrer == 'popup') {
            $no_private = false;
        } else {
            $no_private = ! empty($conf->global->CHAT_HIDE_PRIVATE_MSG_IN_OPEN_ROOM) ? $conf->global->CHAT_HIDE_PRIVATE_MSG_IN_OPEN_ROOM : false;
        }

		$sql = "SELECT m.rowid as id, m.fk_user";
        //$sql.= ", (SELECT count(*) FROM ".MAIN_DB_PREFIX."chat_online WHERE online_user = m.fk_user) as is_online";
        $sql.= ", m.post_time, m.text, m.fk_user_to, m.status";
        $sql.= ", a.name as attachment_name, a.type as attachment_type, a.size as attachment_size";
        $sql.= " FROM ".MAIN_DB_PREFIX."chat_msg as m";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."chat_msg_attachment as a ON m.fk_attachment = a.rowid";
        if ($user_to_id > 0) {
            $sql.= " WHERE (m.fk_user = ".$user->id." AND m.fk_user_to = ".$user_to_id.")";
            $sql.= " OR (m.fk_user = ".$user_to_id." AND m.fk_user_to = ".$user->id.")";
        } else if ($no_private) {
            $sql.= " WHERE m.fk_user_to IS NULL";
        } else {
            $sql.= " WHERE m.fk_user_to IS NULL OR m.fk_user = ".$user->id." OR m.fk_user_to = ".$user->id;
        }
        $sql.= " ORDER BY m.post_time DESC";
        $sql.= " LIMIT ".$limit;

		dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
        {
			$num = $this->db->num_rows($resql);
			$i = 0;
                        
            // on récupère tout les utilisateurs
            $this->users = array();
            $result = $this->fetch_users($user);
            
            if ($result)
            { // si c'est bon on récupère les messages
                while ($i < $num)
                {
                        $obj = $this->db->fetch_object($resql);

                        $this->messages[$i] = $obj;
                        $this->messages[$i]->user = $this->users[$obj->fk_user];
                        if ($obj->fk_user_to > 0)
                        {
                            $this->messages[$i]->user_to = $this->users[$obj->fk_user_to];
                        }
                        //...

                        $i++;
                }
            }
                        
			$this->db->free($resql);
                        
            // on récupère le nombre total de message
            $this->get_messages_count($user, $user_to_id);

			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(__METHOD__ . " " . $this->error, LOG_ERR);

			return -1;
		}
	}

	/**
	 * Load object in memory from database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	public function get_messages_count($user, $user_to_id = -1)
	{
		global $conf, $langs;
                
        // on récupère le nombre total de message
        $sql = "SELECT count(rowid) as msg_count";
        $sql.= " FROM ".MAIN_DB_PREFIX."chat_msg as m";
        if ($user_to_id > 0) {
            $sql.= " WHERE (m.fk_user = ".$user->id." AND m.fk_user_to = ".$user_to_id.")";
            $sql.= " OR (m.fk_user = ".$user_to_id." AND m.fk_user_to = ".$user->id.")";
        }
        else {
            $sql.= " WHERE m.fk_user_to IS NULL OR m.fk_user = ".$user->id." OR m.fk_user_to = ".$user->id;
        }

        dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql)
        {
                $obj = $this->db->fetch_object($resql);

                $this->messages_count = $obj->msg_count;

                $this->db->free($resql);

                return 1;
        }
        else
        {
                return -1;
        }
	}

	/**
	 * Load object in memory from database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	public function get_settings($user)
	{
		global $conf;

        // on récupère les paramètres de l'utilisateur courant
        $sql = "SELECT s.name, s.value";
        $sql.= " FROM ".MAIN_DB_PREFIX."chat_settings as s";
        $sql.= " WHERE s.fk_user = ".$user->id;

        dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql)
        {
            $num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($resql);

				$name=$obj->name;

				if ($name)
				{
					if (! isset($this->settings) || ! is_object($this->settings)) $this->settings = new stdClass(); // For avoid error
					if (! isset($this->settings->$name) || ! is_object($this->settings->$name)) $this->settings->$name = new stdClass();
			                            $this->settings->$name = $obj->value;

				}
				$i++;
			}
			$this->db->free($resql);

            return 1;
        }
        else
        {
            return -1;
        }
	}

	/**
	 * Load object in memory from database
	 *
	 * @param int $id Id object
	 * @return int <0 if KO, >0 if OK
	 */
	public function set_settings($name, $value, $user)
	{
		global $conf;
                
        // Suppression de l'ancienne valeur (si elle existe)
        $sql = "DELETE FROM ".MAIN_DB_PREFIX."chat_settings";
        $sql.= " WHERE name = ".$this->db->encrypt($name,1);
        $sql.= " AND fk_user = ".$user->id;
        
        dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        
        // On insert la nouvelle valeur
        if (strcmp($value,''))	// true if different. Must work for $value='0' or $value=0
        {
            $sql = "INSERT INTO ".MAIN_DB_PREFIX."chat_settings(name,value,fk_user)";
            $sql.= " VALUES (";
            $sql.= $this->db->encrypt($name,1);
            $sql.= ", ".$this->db->encrypt($value,1);
            $sql.= ",'".$user->id."')";

            //print "sql".$value."-".pg_escape_string($value)."-".$sql;exit;
            //print "xx".$db->escape($value);
            dol_syslog(__METHOD__ . " sql=" . $sql, LOG_DEBUG);
            $resql = $this->db->query($sql);
        }

        if ($resql)
        {
            $this->db->commit();
            $this->settings->$name=$value;
            return 1;
        }
        else
        {
            $error=$this->db->lasterror();
            $this->db->rollback();
            return -1;
        }
	}

	/**
	 *  Clear all settings array of user
	 *
	 *  @return	void
	 *  @see	get_settings
	 */
	function clear_settings()
	{
		dol_syslog(get_class($this)."::clearchatsettings");
		$this->settings='';
	}
}
