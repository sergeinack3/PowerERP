<?php
	
if(is_file(__DIR__.'/../main.inc.php'))$dir = __DIR__.'/../';
else  if(is_file(__DIR__.'/../../../main.inc.php'))$dir = __DIR__.'/../../../';
else $dir = __DIR__.'/../../';


	if(!defined('INC_FROM_POWERERP') && defined('INC_FROM_CRON_SCRIPT') ) {
		include($dir."master.inc.php");
	}
	elseif(!defined('INC_FROM_POWERERP')) {
		include($dir."main.inc.php");
	} else {
		global $powererp_main_db_host, $powererp_main_db_name, $powererp_main_db_user, $powererp_main_db_pass;
	}

	if(!defined('DB_HOST')) {
		define('DB_HOST',$powererp_main_db_host);
		define('DB_NAME',$powererp_main_db_name);
		define('DB_USER',$powererp_main_db_user);
		define('DB_PASS',$powererp_main_db_pass);
		define('DB_DRIVER',$powererp_main_db_type);
	}

	

