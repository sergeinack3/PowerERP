<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       localisation/localisationindex.php
 *	\ingroup    localisation
 *	\brief      Home page of localisation top menu
 */

// Load Powererp environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';

// Load translation files required by the page
$langs->loadLangs(array("localisation@localisation"));

$action = GETPOST('action', 'aZ09');


// Security check
if (!$user->rights->localisation->entrepot->read) {
	accessforbidden();
}

$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;
$now = dol_now();

llxHeader("", $langs->trans("LocalisationArea"));
print '<div class="st">'; ?>

<img src="./img/back.jpg" class="st-img">
<div class="cont2">
	<h2 id="texte"></h2>
	<p>Powered by IPOWERWORLD</p>
</div>

<?php

$NBMAX = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;
$max = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;

print '</div>';

// End of page
llxFooter();
$db->close();

?>

<style>
	.st {
		width: 100%;
		height: 125vh;
		overflow: hidden;
		position: relative;
	}

	#id-right {
		margin: 0;
		padding: 0;
	}

	div .fiche {
		margin: 0;
		padding: 0;
	}

	.st-img {
		width: 100%;
		height: 100%;
		opacity: 1;
		filter: brightness(50%);
		z-index: 0;
	}

	.cont2 {
		position: absolute;
		z-index: 5;
		width: 100%;
		height: 100%;
		top: 20%;
		left: 100px;
		background-color: transparent;
		color: white;
	}

	.cont2 h2 {
		font-size: 110px;
		letter-spacing: 5px;
		margin: 0;
		padding: 0;
		font-family: Verdana;
	}

	.cont2 p {
		color: white;
		margin-left: 13px;
		letter-spacing: 2px;
	}
</style>

<script>
	const texteElement = document.getElementById('texte');
	const texte = '<?php print $langs->trans('LocalisationArea'); ?>';

	let i = 0;
	const interval = setInterval(() => {
		if (texteElement.innerHTML.length == texte.length) {
			clearInterval(interval);
		} else {
			texteElement.innerHTML += texte[i];
		}
		i++;
	}, 100);
</script>