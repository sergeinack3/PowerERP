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
 *	\file       emprunt/empruntindex.php
 *	\ingroup    emprunt
 *	\brief      Home page of emprunt top menu
 */

// Load PowerERP environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
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

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

// load emprunt, engagement and state libraries
dol_include_once('/emprunt/class/emprunt.class.php');
dol_include_once('/emprunt/class/typeengagement.class.php');
dol_include_once('/emprunt/class/etat.class.php');


// Load translation files required by the page
$langs->loadLangs(array("emprunt@emprunt"));

$action = GETPOST('action', 'aZ09');

// Security check
if (empty($conf->emprunt->enabled)) {
	accessforbidden('Module not enabled');
}


$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

// Initialize technical objects
$emprunt = new Emprunt($db);
$engagement = new TypeEngagement($db);
$state = new Etat($db);

$numberEmprunt = count($emprunt->fetchAll());
$numberEngagement = count($engagement->fetchAll());
$numberState = count($state->fetchAll());


$max = 5;
$now = dol_now();


/*
 * Actions
 */




/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("Dashoard"));

print '<div class="container" style="margin-top:100px">';

print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">'."\n";
print '<input name="pagem" type="hidden" value="'.$page.'">';
print '<input name="offsetm" type="hidden" value="'.$offset.'">';
print '<input name="limitm" type="hidden" value="'.$limit.'">';
print '<input name="filterm" type="hidden" value="'.$filter.'">';

    //'.$langs->trans(EMPRUNTDASHBOARD).'
    
    print '<div id="grid_title">';
        print '<div class=title style="text-align: center">';
            print '<span class="title_name"> <h1>'.$langs->trans('Dashboard').'</h1><hr style="width:50px;>';
            print '<span class="title_name" style="font-size:18px"> <p>'.$langs->trans('Description_dashboard').'</p> </span>';
            print '</div>';
            
    print '</div>';

	print '<div id="grid_demande" style="text-align:center;">';

		print '<div class="element">';
			print '<div class="element_child">';
				
				print '<div class="img"> <img src="'.dol_buildpath('/emprunt/img/object_emprunt.png', 2).'"></div>';

                    print '<div class="textwithdemand">';
                            print '<span class="name_demand">';
                            // print $value->nom;
                                print '<span class="classfortooltip" style="font-size:18px">'.$langs->trans('TypeEngagementsTitle').'</span>';
                            print '</span>';
                        // print '<br><br>';
                            print '<div class="countdemand">';
                                print '<a class="add_demande" href="'.dol_buildpath('/emprunt/typeengagement_card.php?action=create',2).'"><span>'.$langs->trans('NewTypeEngagement').'</span></a>';
                                print '<span class="nb_review"><a href="'.dol_buildpath('/emprunt/typeengagement_list.php',2).'">'.$langs->trans("for_view").': '.$numberEngagement.'</a></span>';
                            print '</div>';
                    print '</div>';
			print '</div>';
            
            
		print '</div>';

        print '<div class="element">';
			print '<div class="element_child">';
				
				print '<div class="img"> <img src="'.dol_buildpath('/emprunt/img/object_emprunt.png', 2).'"></div>';

                    print '<div class="textwithdemand">';
                            print '<span class="name_demand">';
                            // print $value->nom;
                                print '<span class="classfortooltip" style="font-size:18px">'.$langs->trans('loan').'</span>';
                            print '</span>';
                        // print '<br><br>';
                            print '<div class="countdemand">';
                                print '<a class="add_demande" href="'.dol_buildpath('/emprunt/emprunt_card.php?action=create',2).'"><span>'.$langs->trans('NewEmprunt').'</span></a>';
                                print '<span class="nb_review"><a href="'.dol_buildpath('/emprunt/emprunt_list.php',2).'">'.$langs->trans("for_view").': '.$numberEmprunt.'</a></span>';
                            print '</div>';
                    print '</div>';
			print '</div>';
            
            
		print '</div>';

        print '<div class="element">';
			print '<div class="element_child">';
				print '<div class="img"> <img src="'.dol_buildpath('/emprunt/img/object_emprunt.png', 2).'"></div>';

                    print '<div class="textwithdemand">';
                            print '<span class="name_demand">';
                            // print $value->nom;
                                print '<span class="classfortooltip" style="font-size:18px">'.$langs->trans('Etats mensuel').'</span>';
                            print '</span>';
                        // print '<br><br>';
                            print '<div class="countdemand">';
                                print '<a class="add_demande" href="'.dol_buildpath('/emprunt/etat_list.php',2).'"><span>'.$langs->trans('findstate').'</span></a>';
                                print '<span class="nb_review"><a href="'.dol_buildpath('/emprunt/etat_list.php',2).'">'.$langs->trans("totalstate").': '.$numberEmprunt.'</a></span>';
                            print '</div>';
                    print '</div>';
			print '</div>';
            
            
		print '</div>';


		
	print '</div>';


print '</form>';

print '</div>';

?>


<style>

    body {
        font-family: "Segoe UI";
    }


    #grid_demande {
        width: 100%;
        display: inline-block;
        padding-bottom: 10px;
        background: #fff;
    }

    #grid_demande .element{
        background-color: #fff;
        width: 33.33%;
        float: left;
        font-family: "Segoe UI";
    }

    #grid_demande .element_child{
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin: 10px 5px 0px;
        padding: 11px 3px;
        height: 90px;
        background: #efefef;
        box-shadow:3px 0px 0px 0px #00A09D;
        font-family: "Segoe UI";
    }

    #grid_demande .img img {
        height: auto;
        width: auto;
        max-width: 100%;
        max-height: 66px;
    } 
    #grid_demande .img{
    width: 65px;
    float: left;
    text-align: center;
    margin-right: 10px;
    }

    #grid_demande span.name_demand a {
        color: #000 !important;
        font-weight: 600;
    }
    #grid_demande .div_titre{
    border: none !important;
    background-color: aliceblue;
    width: 95%;
    padding: 10px;
    font-family: "Segoe UI";
    }

    
    @media only screen and (max-width: 1024px){
        #grid_demande .element{
            width: 50%;
        }
    }
    @media only screen and (max-width: 600px){
        #grid_demande .element{
            width: 100%;
        }
    }
    @media only screen and (min-width: 1440px){
        #grid_demande .element{
            width: 25%;
        }
    }
    

    #grid_demande span.name_demand {
        text-align:left;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
        width: 100%;
        height: 40px;
    }

    #grid_demande span.type_demand {
        font-size: 10px;
        color: #a5a0a0;
    }
    #grid_demande .textwithdemand {
    display: inline-block;
    height: 70px;
        width: calc(100% - 75px);
    }
    
    #grid_demande .element a.add_demande {
        font-size: 12px;
        background-color: #00A09D;
        color: #fff;
        padding: 6px 7px;
        border-radius: 3px;
        float: left;
    }
    #grid_demande .element a.add_demande:hover {
        background-color: #007a77;
    }
    #grid_demande .countdemand {
        display: inline-block;
        width: 100%;
    }
    #grid_demande span.nb_review {
        display: inline-block;
        float: right;
        padding-right: 5px;

    }
    #grid_demande span.nb_review a{
        float: right;
        color: #103982;
        /*color: #00A09D;*/
        font-size: 11px;
        font-weight: bold;
    padding: 6px 4px;
    border-radius: 4px;
    }
    #grid_demande span.nb_review a:hover {
        background-color: #ebebeb;
        border-color: #ebebeb;
        color: #212529;
    }

    #grid_demande .element a:hover{
    text-decoration: none;
    cursor: pointer;
    }


</style>

<?php


// End of page
llxFooter();
$db->close();
