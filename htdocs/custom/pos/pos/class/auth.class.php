<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier      <jeremie.o@laposte.net>
 * Copyright (C) 2008-2011 Laurent Destailleur   <eldy@uers.sourceforge.net>
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
 * Class ot manage authentication for pos module (cashdesk)
 */
class Auth
{

    public $db;

    public $login;
    public $passwd;

    public $reponse;

    public $sqlQuery;


    public function __construct($DB)
    {

        $this->db = $DB;
        $this->reponse(null);

    }

    public function login($aLogin)
    {

        $this->login = $aLogin;

    }

    public function passwd($aPasswd)
    {

        $this->passwd = $aPasswd;


    }

    public function reponse($aReponse)
    {

        $this->reponse = $aReponse;

    }

    public function verif($aLogin, $aPasswd)
    {
        global $conf, $powererp_main_authentication, $langs;

        $ret = -1;

        $login = '';

        // Authentication mode
        if (empty($powererp_main_authentication)) $powererp_main_authentication = 'http,powererp';

        // Set authmode
        $authmode = explode(',', $powererp_main_authentication);

        // No authentication mode
        if (!count($authmode) && empty($conf->login_method_modules)) {
            $langs->load('main');
            dol_print_error('', $langs->trans("ErrorConfigParameterNotDefined", 'powererp_main_authentication'));
            exit;
        }


        $test = true;

        // Validation of third party module login method
        if (is_array($conf->login_method_modules) && !empty($conf->login_method_modules)) {
            include_once(DOL_DOCUMENT_ROOT . "/core/lib/security.lib.php");
            $login = getLoginMethod();
            if ($login) $test = false;
        }

        // Validation tests user / password
        // If ok, the variable will be initialized login
        // If error, we will put error message in session under the name dol_loginmesg
        $goontestloop = false;
        if (isset($_SERVER["REMOTE_USER"]) && in_array('http', $authmode)) $goontestloop = true;
        if (isset($aLogin) || GETPOST('openid_mode', 'alpha', 1)) $goontestloop = true;

        if ($test && $goontestloop) {
            foreach ($authmode as $mode) {
                if ($test && $mode && !$login) {
                    $authfile = DOL_DOCUMENT_ROOT . '/core/login/functions_' . $mode . '.php';
                    $result = include_once($authfile);
                    if ($result) {
                        $this->login($aLogin);
                        $this->passwd($aPasswd);
                        $entitytotest = $conf->entity;

                        $function = 'check_user_password_' . $mode;
                        $login = $function($aLogin, $aPasswd, $entitytotest);
                        if ($login) // Login is successfull
                        {
                            $test = false;
                            $ret = 0;
                        }
                    } else {
                        dol_syslog("Authentification ko - failed to load file '" . $authfile . "'", LOG_ERR);
                        sleep(1);
                        $ret = -1;
                    }
                }
            }
        }

        return $ret;
    }

}