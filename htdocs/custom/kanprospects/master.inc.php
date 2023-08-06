<?php

//
// Copyright (C) 2018-2019 ProgSI (contact@progsi.ma)
//

if (!defined('KANPROSPECTS_VERSION'))
	define('KANPROSPECTS_VERSION', '1.1.0');

//$res = 0;
//$res = @include_once dirname(__DIR__) . '/master.inc.php';
//if ($res) {
//	define('KANPROSPECTS_DOCUMENT_ROOT', DOL_DOCUMENT_ROOT . '/kanprospects');
//	define('KANPROSPECTS_URL_ROOT', DOL_URL_ROOT . '/kanprospects');
//}
//else {
//	$res = @include_once dirname(dirname(__DIR__)) . '/master.inc.php';
//	if ($res) {
//		define('KANPROSPECTS_DOCUMENT_ROOT', $conf->file->dol_document_root['alt0'] . '/kanprospects');
//		define('KANPROSPECTS_URL_ROOT', DOL_URL_ROOT . $conf->file->dol_url_root['alt0'] . '/kanprospects');
//	}
//	else {
//		die("Include of master file fails." . "\n" . "L'inclusion du fichier master a échoué.");
//	}
//}

define('KANPROSPECTS_DOCUMENT_ROOT_RELATIVE', str_replace(DOL_DOCUMENT_ROOT . '/', '', 'KANPROSPECTS_DOCUMENT_ROOT'));
if (!empty(DOL_URL_ROOT)) {
	define('KANPROSPECTS_URL_ROOT_RELATIVE', str_replace(DOL_URL_ROOT . '/', '', KANPROSPECTS_URL_ROOT));
}
else {
	define('KANPROSPECTS_URL_ROOT_RELATIVE', KANPROSPECTS_URL_ROOT);
}

if (!defined('module'))
	define('module', 'kanprospects');

define('LIB_DOCUMENT_ROOT', KANPROSPECTS_DOCUMENT_ROOT . '/lib');
define('LIB_URL_ROOT', KANPROSPECTS_URL_ROOT . '/lib');

// attention : $langs n'est pas défini dans master.inc.php
if (!defined('mytitle'))
	define('mytitle', 'Kanban');

// raccourci pour le MAIN_DB_PREFIX
if (!defined('LLX_'))
	define('LLX_', MAIN_DB_PREFIX);


include_once('rights.php');
