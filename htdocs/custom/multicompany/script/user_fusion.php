#!/usr/bin/env php
<?php
/* Copyright (C) 2020-2024 iPowerWorld
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 *      \file       multicompany/script/user_fusion.php
 *		\ingroup    multicompany
 *      \brief      Script to
 */

$sapi_type = php_sapi_name();
$script_file = basename(__FILE__);
$path=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type, 0, 3) == 'cgi') {
    echo "Error: You are using PHP for CGI. To execute ".$script_file." from command line, you must use PHP for CLI mode.\n";
    exit;
}

// Global variables
$version='1';
$error=0;

// Include PowerERP environment
echo $path."../../../master.inc.php\n";
require_once $path."../../../master.inc.php";

// After this $db, $mysoc, $langs and $conf->entity are defined. Opened handler to database will be closed at end of file.

//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
$langs->load("main");				// To load language file for default language
@set_time_limit(0);					// No timeout for this script


print "***** ".$script_file." (".$version.") *****\n";
if (! isset($argv[1]) || ! isset($argv[2])) {	// Check parameters
    print "Usage: ".$script_file." <old_userid> <new_userid> ...\n";
    exit;
}
print '--- start'."\n";
print 'old_userid='.$argv[1]."\n";
print 'new_userid='.$argv[2]."\n";

$old_userid = $argv[1];
$new_userid = $argv[2];

// Start of transaction
$this->db->begin();

// Eviter les contraintes
$sql = "SET foreign_key_checks = 0";
$resql=$this->db->query($sql);

$list_tables = $this->db->DDLListTables($this->db->database_name);

$pattern = '/(user|user\_alert|usergroup\_user|user\_param|user\_rib|user\_rights|user\_clicktodial)+$/';

if (is_array($list_tables))
{
	$infotables=array();

	// Pour chaque table : vérif si présence d'un champ entity
	foreach($list_tables as $table)
	{
		if (!preg_match($pattern, $table))
		{
			$sql="SHOW FULL COLUMNS FROM ".$table." WHERE Field LIKE ('fk_user%') AND Field NOT LIKE ('fk_usergroup');";

			$result = $this->db->query($sql);
			while($row = $this->db->fetch_row($result))
			{
				$infotables[$table][] = $row[0];
			}
		}
	}

	foreach($infotables as $table => $fields)
	{
		print "--- " . $table . " ---\n";

		foreach ($fields as $field)
		{
			print $field . "\n";
		}

		print "-----------------------\n";
	}

}

// Put option as origin
$sql = "SET foreign_key_checks = 1";
$resql=$this->db->query($sql);


// -------------------- END OF YOUR CODE --------------------

if (! $error)
{
	$this->db->commit();
	print '--- end ok'."\n";
}
else
{
	print '--- end error code='.$errorcode."\n";
	$this->db->rollback();
}

$this->db->close();	// Close database opened handler

return $error;
