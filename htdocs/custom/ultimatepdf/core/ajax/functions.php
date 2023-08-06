<?php
/* Copyright (C) 2019	Regis Houssin	<regis.houssin@inodbox.com>
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
 */

/**
 *       \file       ultimatepdf/core/ajax/functions.php
 *       \brief      File to return ajax result
 */
use VKR\SignatureToImage\SignatureToImage;

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1'); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
if (! defined('NOREQUIREHOOK'))   define('NOREQUIREHOOK',1);

$res=@include("../../../main.inc.php");						// For root directory
if (! $res && file_exists($_SERVER['DOCUMENT_ROOT']."/main.inc.php"))
	$res=@include($_SERVER['DOCUMENT_ROOT']."/main.inc.php"); // Use on dev env only
if (! $res) $res=@include("../../../../main.inc.php");		// For "custom" directory

dol_include_once("/ultimatepdf/lib/vendor/autoload.php");

$id			= GETPOST('id', 'int');			// id of element
$action		= GETPOST('action', 'alpha');	// action method
$json		= GETPOST('json');				// json data
$element	= GETPOST('element', 'alpha');	// type of element

/*
 * View
 */

// Ajout directives pour resoudre bug IE
//header('Cache-Control: Public, must-revalidate');
//header('Pragma: public');

//top_htmlhead("", "", 1);  // Replaced with top_httphead. An ajax page does not need html header.
top_httphead('application/json');

if (empty($conf->ultimatepdf->enabled)) {
	echo json_encode(array('status' => 'error'));
	$db->close();
	exit();
}

//print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

if (! empty($action) && is_numeric($id) && ! empty($element))
{
	if ($action == 'genSignature' && ! empty($json))
	{
		dol_syslog("ultimatepdf action=".$action." id=".$id." element=".$element, LOG_DEBUG);

		$imgSig = new SignatureToImage;
		$options = array(
			'imageSize' => array(240, 75),
			'bgColour' => 'transparent',
			'drawMultiplier' => 12
		);
		$img = $imgSig->sigJsonToImage($json, $options);
		if ($element == 'fichinter' || $element == 'ficheinter') 
		{
			$sigdir = $conf->ultimatepdf->dir_output.'/fichinter/temp';
			dol_mkdir($sigdir);
		}
		elseif ($element == 'propal') 
		{
			$sigdir = $conf->ultimatepdf->dir_output.'/proposals/temp';
			dol_mkdir($sigdir);
		}
		elseif ($element == 'commande') 
		{
			$sigdir = $conf->ultimatepdf->dir_output.'/orders/temp';
			dol_mkdir($sigdir);
		}
		
		$ret = imagepng($img, $sigdir.'/'.$id.'_signature.png');
		imagedestroy($img);

		if ($ret == true) $out = array('status' => 'ok');
		else $out = array('status' => 'error');

		echo json_encode($out);
	}
}

$db->close();
