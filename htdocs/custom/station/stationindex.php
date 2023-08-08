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
 *	\file       station/stationindex.php
 *	\ingroup    station
 *	\brief      Home page of station top menu
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
$langs->loadLangs(array("station@station"));

$action = GETPOST('action', 'aZ09');


// Security check
// if (! $user->rights->station->myobject->read) {
// 	accessforbidden();
// }
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;
$now = dol_now();


/*
 * Actions
 */

// **************************** TOTAL CUVE ********************************
// Calcul du total des ventes d'aujourd'hui
$sql_cuve = "SELECT SUM(c.qty) AS total_cuve_initiale, SUM(c.max_qty) AS total_cuve_max FROM llx_station_cuve c WHERE c.status = 1";
$result_cuve = $db->query($sql_cuve);
$row_cuve = $db->fetch_array($result_cuve);
$total_cuve_initiale = $row_cuve['total_cuve_initiale'];
$total_cuve_max = $row_cuve['total_cuve_max'];


// Calcul du pourcentage de différence
$difference_cuve = number_format(($total_cuve_initiale * 100) / $total_cuve_max, 1);



// **************************** TOTAL DEPOTAGE ********************************
// Calcul du total des quantités de dépotage du mois actuel
$sql_depotage_actuel = "SELECT SUM(d.qty) AS total_depotage_actuel FROM llx_station_depotage d WHERE d.status = 1 AND YEAR(d.date_creation) = YEAR(CURDATE()) AND MONTH(d.date_creation) = MONTH(CURDATE())";
$result_depotage_actuel = $db->query($sql_depotage_actuel);
// $row_depotage_actuel = $db->fetch_array($result_depotage_actuel);
$total_depotage_actuel = $row_depotage_actuel['total_depotage_actuel'];
// Calcul du total des quantités de dépotage du mois précédent
$sql_depotage_precedent = "SELECT SUM(d.qty) AS total_depotage_precedent FROM llx_station_depotage d WHERE d.status = 1 AND YEAR(d.date_creation) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND MONTH(d.date_creation) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
$result_depotage_precedent = $db->query($sql_depotage_precedent);
// $row_depotage_precedent = $db->fetch_array($result_depotage_precedent);
$total_depotage_precedent = $row_depotage_precedent['total_depotage_precedent'];

// Calcul du pourcentage de différence
$difference_depotage = $total_depotage_actuel - $total_depotage_precedent;
if ($total_depotage_precedent == 0) {
	$pourcentage_depotage_difference = abs(number_format($difference_depotage, 1));
} else {
	$pourcentage_depotage_difference = abs(number_format(($difference_depotage / $total_depotage_precedent) * 100, 1));
}

// Détermination de l'icône à afficher
$icone_depotage = ($difference_depotage > 0) ? 'chart-up' : 'chart-down';




// **************************** TOTAL VENTES ********************************
// Calcul du total des ventes d'aujourd'hui
$sql_aujourdhui = "SELECT SUM(r.vente) AS total_ventes_aujourdhui FROM llx_station_releve r WHERE r.status = 2 AND DATE(r.date_creation) = CURDATE()";
$result_aujourdhui = $db->query($sql_aujourdhui);
$row_aujourdhui = $db->fetch_array($result_aujourdhui);
$total_ventes_aujourdhui = $row_aujourdhui['total_ventes_aujourdhui'];

// var_dump($total_ventes_aujourdhui);
// Calcul du total des ventes de la date précédente
$sql_precedentes = "SELECT SUM(r.vente) AS total_ventes_precedentes FROM llx_station_releve r WHERE r.status = 2 AND DATE(r.date_creation) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$result_precedentes = $db->query($sql_precedentes);
$row_precedentes = $db->fetch_array($result_precedentes);
$total_ventes_precedentes = $row_precedentes['total_ventes_precedentes'];


// Calcul du pourcentage de différence
$difference = $total_ventes_aujourdhui - $total_ventes_precedentes;
if ($total_ventes_precedentes == 0) {
	$pourcentage_difference = number_format(abs($difference), 1);
} else {
	$pourcentage_difference = number_format(abs(($difference / $total_ventes_precedentes) * 100), 1);
}

$icone = ($difference > 0) ? 'chart-up' : 'chart-down';




// **************************** TOTAL QTY ********************************
// Calcul du total des ventes d'aujourd'hui
$sql_qty_aujourdhui = "SELECT SUM(r.qty) AS total_qty_aujourdhui FROM llx_station_releve r WHERE r.status = 2 AND DATE(r.date_creation) = CURDATE()";
$result_qty_aujourdhui = $db->query($sql_qty_aujourdhui);
$row_qty_aujourdhui = $db->fetch_array($result_qty_aujourdhui);
$total_qty_aujourdhui = $row_qty_aujourdhui['total_qty_aujourdhui'];

// var_dump($total_ventes_aujourdhui);
// Calcul du total des ventes de la date précédente
$sql_qty_precedentes = "SELECT SUM(r.qty) AS total_qty_precedentes FROM llx_station_releve r WHERE r.status = 2 AND DATE(r.date_creation) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
$result_qty_precedentes = $db->query($sql_qty_precedentes);
$row_qty_precedentes = $db->fetch_array($result_qty_precedentes);
$total_qty_precedentes = $row_qty_precedentes['total_qty_precedentes'];


// Calcul du pourcentage de différence
$difference_qty = $total_qty_aujourdhui - $total_qty_precedentes;
if ($total_qty_precedentes == 0) {
	$pourcentage_qty_difference = number_format(abs($difference_qty), 1);
} else {
	$pourcentage_qty_difference = number_format(abs(($difference_qty / $total_qty_precedentes) * 100), 1);
}


$icone_qty = ($difference_qty > 0) ? 'chart-up' : 'chart-down';

/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("StationArea"));

// print load_fiche_titre($langs->trans("StationArea"), '', 'station.png@station');

print '<div class="fichecenter"><div class="fichethirdleft">';

print '</div>';

?>

<style>
	.cards {
		margin-top: 1.5rem;
	}

	.cards h3,
	.cards h5 {
		line-height: 1.2;
	}

	.cards h3 {
		font-size: 1.6rem;
		font-weight: 700;
		margin: 0;
	}

	.cards h5 {
		font-size: 1rem;
	}

	.cards__container {
		display: grid;
		grid-template-columns: repeat(4, 1fr);
		gap: 1.5rem;
	}

	.card {
		background: rgba(241, 238, 238, 0.6);
		padding: 1rem 2rem 0 2rem;
		border-radius: 2rem;
		transition: all 400ms ease;
	}

	.card:hover {
		box-shadow: 0 .3rem .3rem rgba(0, 0, 0, 0.3);
		z-index: 1;
	}

	.card:nth-child(2) .card__icon {
		background: #FCF3DA;
		color: #FFC43D;
	}

	.card:nth-child(3) .card__icon {
		background: #ECF6F0;
		color: #62DDA1;
	}

	.card:nth-child(4) .card__icon {
		background: #FDEDE6;
		color: #FEA485;
	}

	.card:nth-child(2) h5 {
		font-size: .8rem;
	}

	.card__icon {
		background: #F2F2FF;
		color: #6c63ff;
		padding: 0.7rem;
		font-size: 1.5rem;
		border-radius: 0.9rem;
	}

	.card h5 {
		margin: 2rem 0 1rem;
	}

	.card p {
		font-size: .8rem;
		display: flex;
		align-items: center;
		gap: .3rem;
		color: #747575;
	}

	.card p .percentage__up {
		color: #40AE58;
	}

	.card p .percentage__down {
		color: #EC3A3B;
	}

	.card p i {
		margin-right: 5px;
	}

	.card__header {
		display: flex;
		align-items: center;
		gap: 1rem;
		padding-bottom: 1rem;
	}

	#id-right {
		padding: 0 !important;
	}

	.fiche {
		margin: 0 !important;
	}

	.description {
		/* margin-top: 1rem; */
		margin-bottom: 0;
	}

	.row-desc {
		display: grid;
		grid-template-columns: 1fr;
		align-items: center;
	}

	.row-desc__pic {
		height: 100%;
		max-width: 100%;
	}

	.row-desc__pic img {
		/* display: block; */
		/* width: 40rem; */
		max-width: 100%;
		height: auto;
	}

	.row-desc__text {
		font-size: 2rem;
		font-weight: 700;
	}

	.row-desc__text a {
		font-size: 1.2rem;
		text-decoration: none;
		background: #263C5C;
		color: white;
		padding: 1rem;
		border-radius: 2rem;
	}

	.row-desc__text a:hover {
		background: #0A1464;
	}
</style>

<div>

	<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">

	<!-- CATEGORIES -->
	<!-- <section class="cards">
		<div class="container cards__container">
			<article class="card">
				<div class="card__header">
					<span class="card__icon"><i class="uil uil-archive"></i></span>
					<h5>Total Cuve</h5>
				</div>
				<h3><?= ($total_cuve_initiale == "") ? '0' : $total_cuve_initiale ?> l</h3>
				<p>
					<span class="">
						<?= $difference_cuve ?>%
					</span>
				</p>
			</article>

			<article class="card">
				<div class="card__header">
					<span class="card__icon"><i class="uil uil-truck"></i></span>
					<h5>Total Depotage</h5>
				</div>
				<h3><?= ($total_depotage_actuel == "") ? '0' : $total_depotage_actuel ?> l</h3>
				<p>
					<?php if ($icone_depotage == 'chart-up') : ?>
						<span class="percentage__up">
							<i class="uil uil-arrow-growth"></i><?= $pourcentage_depotage_difference ?>%
						</span>
						Plus
					<?php elseif ($icone_depotage == 'chart-down') : ?>
						<span class="percentage__down">
							<i class="uil uil-chart-down"></i><?= $pourcentage_depotage_difference ?>%
						</span>
						Moins
					<?php endif; ?>
					mois passé
				</p>
			</article>

			<article class="card">
				<div class="card__header">
					<span class="card__icon"><i class="uil uil-money-stack"></i></span>
					<h5>Total Ventes</h5>
				</div>
				<h3><?= ($total_ventes_aujourdhui == "") ? '0' : $total_ventes_aujourdhui ?> FCFA</h3>
				<p>
					<?php if ($icone == 'chart-up') : ?>
						<span class="percentage__up">
							<i class="uil uil-arrow-growth"></i><?= $pourcentage_difference ?>%
						</span>
						Plus
					<?php elseif ($icone == 'chart-down') : ?>
						<span class="percentage__down">
							<i class="uil uil-chart-down"></i><?= $pourcentage_difference ?>%
						</span>
						Moins
					<?php endif; ?>
					qu'hier
				</p>
			</article>

			<article class="card">
				<div class="card__header">
					<span class="card__icon"><i class="uil uil-pump"></i></span>
					<h5>Total Qty</h5>
				</div>
				<h3><?= ($total_qty_aujourdhui == "") ? '0' : $total_qty_aujourdhui ?> l</h3>
				<p>
					<?php if ($icone_qty == 'chart-up') : ?>
						<span class="percentage__up">
							<i class="uil uil-arrow-growth"></i><?= $pourcentage_qty_difference ?>%
						</span>
						Plus
					<?php elseif ($icone_qty == 'chart-down') : ?>
						<span class="percentage__down">
							<i class="uil uil-chart-down"></i><?= $pourcentage_qty_difference ?>%
						</span>
						Moins
					<?php endif; ?>
					qu'hier
				</p>
			</article>

		</div>
	</section> -->

	<section class="description">
		<div class="row-desc">
			<!-- <div class="row-desc__text">
				<p>
					Nous rendons la gestion de votre station service plus facile pour tous.
				</p>
				<a href="<?= DOL_URL_ROOT . '/custom/station/admin/about.php' ?>" class="rounded-pill btn-rounded border-primary">Apprendre plus</a>
			</div> -->
			<div class="">
				<div class="row-desc__pic">
					<img src="./img/station-img.jpg" alt="station illustration">
				</div>
			</div>
		</div>
	</section>

</div>

<style>

</style>


<?php

// End of page
llxFooter();
$db->close();
