<?php

require_once DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
dol_include_once('/salariescontracts/lib/salariescontracts.lib.php');
dol_include_once('/salariescontracts/common.inc.php');

global $conf, $mysoc, $langs;


$id 		= GETPOST('id', 'int');
$sc 		= new Salariescontracts($db);
$form 		= new Form($db);
$sal_usr 	= new User($db);

$sc->fetch($id);
$sal_usr->fetch($sc->fk_user);




// INFORMATIONS SOCIETE
$soc_manager 	= $conf->global->MAIN_INFO_SOCIETE_MANAGERS;
$soc_manager 	= ($soc_manager) ? $soc_manager : str_repeat(".", 50);
$soc_societe 	= $conf->global->MAIN_INFO_SOCIETE_NOM;
$soc_ville 		= $conf->global->MAIN_INFO_SOCIETE_TOWN;
$soc_adress 	= "".trim($langs->convToOutputCharset(dol_format_address($mysoc, 1, " ", $langs)))."";
$soc_currency	= $langs->getCurrencySymbol($conf->currency);




// INFORMATIONS SALARIE
$sal_nbrhour  	= number_format($sal_usr->weeklyhours);

if($sal_usr->salary){
	$sal_salaire  	= ($sal_usr->salary != '' ?price($sal_usr->salary, '', $langs, 1, -1, -1, strtolower($langs->transnoentitiesnoconv("Currency".$conf->currency))) : '');
	$sal_numword = '';
	$sal_numword .= $sc->numberToWordsFunction($sal_usr->salary, strtolower($langs->transnoentitiesnoconv("Currency".$conf->currency)), '');
	$sal_numword  = trim(strtolower($sal_numword));
	$sal_salaire  	.= " (".$sal_numword.")";
	// $sal_salaire  	= "2000 euros (deux mille euros)";
}else
	$sal_salaire  	= str_repeat(".", 50);
$sal_numsecsoc = '';
if(isset($sal_usr->array_options['options_salariescontractsusersoci']))
$sal_numsecsoc = $sal_usr->array_options['options_salariescontractsusersoci'];
$sal_numsecsoc 	= ($sal_numsecsoc) ? $sal_numsecsoc : str_repeat(".", 50);


$sal_adress 	= trim($langs->convToOutputCharset(dol_format_address($sal_usr, 1, ' ', $langs)));
$sal_c_debut 	= date('d/m/Y',$sc->start_date);
$sal_cc_debut 	= date('Y-m-d',$sc->start_date);
$sal_c_fin   	= $sc->end_date ? "".date('d/m/Y',$sc->end_date)."" : "____/____/______ ";
$sal_datenai   	= $sal_usr->birth ? date('d/m/Y',$sal_usr->birth) : "____/____/______ ";
$sal_contype 	= $sc->getContractTypeById($sc->type);
$sal_function 	= ($sal_usr->job) ? $sal_usr->job : str_repeat(".", 50);
$sal_function 	= "".$sal_function."";
$Agenre  = "";
if($sal_usr->gender != '') $Agenre = ($sal_usr->gender == "man") ? "Monsieur " : "Mlle. ";
$sal_username 	= $sal_usr->firstname." ".$sal_usr->lastname;
$sal_touser 	= $Agenre.$sal_username;
$dt1 = "";

if(!empty($sal_usr->dateemployment)) $dt1 = date('d/m/Y',$sal_usr->dateemployment);
$sal_udebut   = $dt1 ? "".$dt1."" : "____/____/______ ";




$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->Cell(0, 2,'',0,1,'C');


$brsp = '<br>';
$table = "";


$table .='<div style="background-color:#ddd;border:1px solid #000;">';
$table .='<h2 style="margin:0;text-align:center;font-size:16px;font-weight:initial;">CONTRAT DE TRAVAIL A DUREE INDERTERMINEE</h2>';
$table .='</div>';
$table .='<div style="clear:both"></div>';
$table .= str_repeat($brsp, 3);

$table .= "Faisant suite à nos différents entretiens, nous avons le plaisir de vous informer de votre engagement selon les modalités définies au présent contrat.";
$table .= str_repeat($brsp, 2);
$table .= "La présente offre est valable dans un délai de 15 jours à compter de la date d’envoi figurant en entête du présent contrat.";
$table .= str_repeat($brsp, 2);
$table .= "<b>Entre les soussignés</b>";
$table .= str_repeat($brsp, 2);

// $table .= "SASU ".$soc_societe.", représentée par ".$soc_manager.", Directeur Général, ayant son siège social au ".$soc_adress.".";
$table .= "Société ".$soc_societe."</b>, représentée par ".$soc_manager.", Directeur Général, ayant son siège social au ".$soc_adress.".";
$table .= str_repeat($brsp, 2);
$table .= "Ci-après dénommée « La Société »";
$table .= str_repeat($brsp, 2);
$table .= "<b>D’une part,</b>";
$table .= str_repeat($brsp, 2);
$table .= "Et ".$sal_touser.", né le ".$sal_datenai.", enregistré sous le numéro de sécurité sociale ".$sal_numsecsoc.", demeurant ".$sal_adress.".";
$table .= str_repeat($brsp, 2);
$table .= "Ci-après dénommé « Le Salarié »";
$table .= str_repeat($brsp, 2);
$table .= "<b>D’autre part,</b>";
$table .= str_repeat($brsp, 2);
$table .= "Il a été convenu ce qui suit :";


$table .= str_repeat($brsp, 2);
$table .= "<b><u>ARTICLE 1 - EMPLOI ET QUALIFICATION</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Le Salarié est engagé à compter du ".$sal_c_debut." en qualité de ".$sal_function." sous la Direction de ".$soc_manager." – Directeur Général.";
$table .= str_repeat($brsp, 2);
$table .= "En fonction des nécessités d'organisation du travail, la Société pourra affecter le Salarié aux divers postes de travail correspondant à la nature de son emploi.";
$table .= str_repeat($brsp, 2);
$table .= "".$sal_touser." déclare formellement être libre de tout engagement vis-à-vis de son précédent employeur à la date définie au 1er paragraphe ci-dessus et n’être lié à aucune autre entreprise. En particulier, ".$sal_touser." déclare formellement n’être tenu par aucune clause de non concurrence pouvant faire obstacle à la mise en œuvre du présent contrat.";
$table .= str_repeat($brsp, 2);
$table .= "La durée hebdomadaire du travail est fixée à 39 heures par semaine et se repartit entre les jours de la semaine, conformément à l’horaire affiché dans l’entreprise.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 2 - OBJET ET DUREE DU CONTRAT</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Ce contrat est conclu pour une durée indéterminée à compter du ".$sal_c_debut.".";
$table .= str_repeat($brsp, 2);
$table .= "Il pourra toujours cesser à l'initiative de l'une ou l'autre des parties conformément aux dispositions légales et conventionnelles en vigueur.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 3 - PERIODE D'ESSAI</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Le présent contrat deviendra définitif à l’expiration d’une période d’essai de 2 mois à compter de la date de prise des fonctions, fixée le ".$sal_c_debut.". Pendant la période d’essai, chacune des parties pourra mettre fin au présent Contrat conformément à la loi applicable.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 4 – DUREE DU TRAVAIL</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Le Salarié est assujetti à une durée du travail de 39 heures correspondant à la durée légale de 35 heures à laquelle s’ajoutent 4 supplémentaires.";
$table .= str_repeat($brsp, 2);
$table .= "Il est précisé que la rémunération du Salarié visée à l’article 5, du présent contrat comprend la rémunération des heures effectuées dans la limite de la durée légale du travail, plus 4 heures supplémentaires par semaine. En conséquence, seules les heures supplémentaires effectuées au-delà de 4 heures supplémentaires donneront lieu à paiement majoré et/ou repos compensateur de remplacement.";
$table .= str_repeat($brsp, 2);
$table .= "Les heures supplémentaires éventuellement effectuées au-delà de cette durée, après autorisation préalable ou demande expresse du supérieur hiérarchique, seront soumises aux dispositions légales (majoration, repos compensateur de remplacement) et ce dans la limite du contingent annuel d’heures supplémentaire.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 5 - REMUNERATION</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "En rémunération de ses services, le Salarié percevra une rémunération brute mensuelle de ".$sal_salaire.", payable en 12 mensualités égales comprenant d’ores et déjà 4 heures supplémentaires.";
$table .= str_repeat($brsp, 2);
$table .= "Le Salarié bénéficiera en outre des avantages sociaux consentis au personnel de sa catégorie, notamment, en ce qui concerne les remboursements de frais de missions et déplacements et le régime de retraite et de prévoyance.";
$table .= str_repeat($brsp, 2);
$table .= "<b><u>ARTICLE 6 - LIEU DE TRAVAIL</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Le lieu de travail, à la date de conclusion du présent contrat, est situé au ".$soc_adress.".";
$table .= str_repeat($brsp, 2);
$table .= "Cependant, compte tenu de la nature de ses fonctions et de la particularité de l’activité de la Société, le Salarié pourra être amené à effectuer des missions de plus ou moins longue durée chez des clients de la Société, situés en France Métropolitaine.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 7 - CONGES PAYES</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Le Salarié bénéficie d'un congé annuel payé, conformément aux dispositions en vigueur dans l'établissement.";
$table .= str_repeat($brsp, 2);
$table .= "Les modalités de ce congé seront déterminées, par accord avec la direction, compte tenu des nécessités de service.";
$table .= str_repeat($brsp, 2);
$table .= "La période des congés sera déterminée par décision de l'employeur qui sera portée en temps utile à la connaissance du personnel. Par mesure d’organisation et selon les services, nous demandons à recevoir votre demande de congés au moins et au minimum un mois avant celui-ci de façon à ce qu’il soit validé par vos supérieurs.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 8 - ABSENCES/REMPLACEMENT</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "En cas d’empêchement d’exercer son activité et d’accomplir ses obligations, le Salarié devra en aviser la Société dans les 48 heures en indiquant les motifs et la durée probable de cet empêchement et en lui adressant un certificat médical.";
$table .= str_repeat($brsp, 2);
$table .= "Le non-respect par le Salarié de cette obligation pourra entraîner la rupture du contrat de travail à ses torts.";
$table .= str_repeat($brsp, 2);
$table .= "En tout état de cause, le Salarié mettra tout en œuvre pour avertir ne serait-ce que par téléphone, SMS ou courriel dans un premier temps, l’entreprise de son absence.";
$table .= str_repeat($brsp, 2);
$table .= "Afin de pallier l’éventuelle absence du Salarié et afin d’éviter tout dysfonctionnement de l’activité, la Société se réserve la faculté de procéder au remplacement provisoire du Salarié dans ses fonctions par toute personne de son choix.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 9 - EXCLUSIVITE DES SERVICES</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Pendant toute la durée du présent contrat, le Salarié devra réserver à la Société l’exclusivité de ses services et ne pourra avoir aucune autre activité professionnelle, pour son compte, pour le compte d’une autre entreprise même non-concurrente, ou pour le compte d’un tiers, sauf accord écrit préalable de la Direction.";
$table .= str_repeat($brsp, 2);
$table .= "Dans une telle hypothèse, le Salarié s’engage à respecter les limites légales en matière de durée du travail.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 10 - RESPECT DES BIENS, DROITS ET INTERETS DE L’ENTREPRISE</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Le Salarié s’engage à exercer ses activités, et notamment à utiliser les supports, informations et matériels qui lui seront remis par l’entreprise à cet effet, dans le respect des droits de l’entreprise et en conformité avec ses intérêts.";
$table .= str_repeat($brsp, 2);
$table .= "Les biens de toute nature qui sont remis au Salarié pour l’exécution de ses fonctions ne sont détenus par lui qu’à titre précaire.";
$table .= str_repeat($brsp, 1);
$table .= "Le Salarié est responsable de leur maintien en parfait état.";
$table .= str_repeat($brsp, 2);
$table .= "Le Salarié ne peut ni les céder, ni les prêter, ni les louer à des tiers.";
$table .= str_repeat($brsp, 1);
$table .= "La Société se réserve le droit de contrôler cette obligation à tout moment.";
$table .= str_repeat($brsp, 2);
$table .= "Le Salarié devra être en mesure de remettre à l’entreprise, à la première demande de l’un de ses représentants, tous biens, matériels, documents, tarifs, programmes, instructions, fichiers lui appartenant qu’elle aurait mis à sa disposition.";
$table .= str_repeat($brsp, 2);
$table .= "Le Salarié s’interdit de façon expresse d’utiliser ou de reproduire à des fins personnelles, ni pendant, ni à l’issue de son contrat de travail, la documentation, les logiciels ou toutes informations, sous quelque forme que ce soit, appartenant à l’entreprise dont il aura connaissance du fait de sa présence dans l’entreprise ou chez les clients de cette dernière.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 11 - SECRET PROFESSIONNEL ET OBLIGATION DE DISCRETION </u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Pendant la durée et après la rupture de son contrat de travail, le Salarié est tenu, indépendamment d’une obligation de réserve générale, à une discrétion absolue sur tous les faits dont il pourrait avoir connaissance du fait de sa présence dans l’entreprise ou chez les clients de cette dernière.";
$table .= str_repeat($brsp, 2);
$table .= "Cette obligation de réserve concerne plus particulièrement la gestion et le fonctionnement de la Société ainsi que la situation financière et les projets relatifs à ses clients et fournisseurs.";
$table .= str_repeat($brsp, 2);
$table .= "Les documents ou rapports que le Salarié pourra établir dans le cadre de ses fonctions ou dont la communication lui sera donnée, demeureront la propriété de la Société. En conséquence, le Salarié ne pourra ni en conserver de copies ou de photocopies, ni en donner communication à des tiers, sans l’accord écrit de la Société.";
$table .= str_repeat($brsp, 2);
$table .= "Toute violation par le Salarié des dispositions susvisées entrainera sont licenciement pour faute grave, sans préavis ni indemnité nonobstant les dommages et intérêts qui pourront lui être demandées en justice.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 12 – CESSATION DU CONTRAT DE TRAVAIL</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "<b>12.1 Préavis</b>";
$table .= str_repeat($brsp, 1);
$table .= "Hormis le cas de force majeure, de faute grave ou lourde le présent contrat ne pourra être rompu qu’après avoir respecté un préavis réciproque de deux mois.";
$table .= str_repeat($brsp, 2);
$table .= "<b>12.2 Restitution des supports d’activité</b>";
$table .= str_repeat($brsp, 1);
$table .= "Le Salarié s’engage à restituer à la Société, à l’issue de son contrat de travail, tous biens ou objets qui lui sont confiés par la Société (tels que documents, pièces, outillages, équipements…) spontanément et en tout cas à la première demande formulée par un représentant de l’entreprise.";
$table .= str_repeat($brsp, 2);
$table .= "Dans l’hypothèse où le Salarié ne déférait pas à cette demande, la Société se réservera le droit d’appliquer une compensation financière entre la valeur des biens qui lui appartiennent et les sommes qui lui seront versées au titre du solde de tout compte, sans préjudice d’éventuelles poursuites civiles et/ou pénales.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 13 – INTERDICTION DE DEBAUCHAGE </u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Pendant une période de deux années après la rupture de son contrat de travail, qu’elle qu’en soit la cause, il est fait interdiction à ".$sal_touser." d’embaucher, directement ou indirectement, pour son compte ou pour le compte d’un tiers, des salariés de la Société ".$soc_societe.".";
$table .= str_repeat($brsp, 2);
$table .= "En cas d’infraction aux dispositions du présent article, le Salarié sera redevable à l’égard de la Société, à titre de clause pénale, d’une indemnité égale à douze fois le dernier salaire mensuel brut de la personne ainsi débauchée.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 14 - MUTUELLE </u></b>";
$table .= str_repeat($brsp, 2);
$table .= "La société ".$soc_societe." adhère à une mutuelle obligatoire pour l’ensemble du personnel dont l’entreprise participe à hauteur de 50 %, le contrat et les conditions sont remis au Salarié dés l’embauche.";
$table .= str_repeat($brsp, 2);
$table .= "Pour tous refus à l’adhésion, le Salarié doit remettre au plus vite une attestation de Mutuelle Valide.";
$table .= str_repeat($brsp, 2);

$table .= "<b><u>ARTICLE 15 – INDEPENDANCE DES CLAUSES</u></b>";
$table .= str_repeat($brsp, 2);
$table .= "Si l’une ou plusieurs dispositions du présent contrat devaient être annulées ou déclarées sans effet, il n’en résulterait pas, pour autant, la nullité de l’ensemble de la convention ou d’une ou plusieurs de ses autres dispositions.";
$table .= str_repeat($brsp, 3);


$table .= "<table border='0' cellpadding='9px'>";
$table .= "<tr>";
$table .= "<td> </td>";
$table .= "<td>";
$table .= "Fait à ".$soc_ville;
$table .= str_repeat($brsp, 1);
$table .= "Le ".date("d/m/Y");
$table .= str_repeat($brsp, 1);
$table .= "</td>";
$table .= "</tr>";

$table .= "<tr>";
$table .= "<td></td>";
$table .= "<td>";
$table .= "Pour la société ".$soc_societe."";
$table .= "</td>";
$table .= "</tr>";

$table .= "<tr>";
$table .= "<td>";
$table .= "<b>".$sal_username."</b>";
$table .= "</td>";
$table .= "<td>";
$table .= "<b>".$soc_manager."</b>";
$table .= "</td>";
$table .= "</tr>";
$table .= "</table>";

$table .= str_repeat($brsp, 8);
$table .= '<span style="font-size:11px">';
$table .= "Signature précédée de la mention";
$table .= str_repeat($brsp, 1);
$table .= "« Lu et approuvé »";
$table .= '</span>';


$tbl = <<<EOF
    $table
EOF;
$pdf->writeHTML($tbl, true, false, true, false, '');

$pdf->Output('contrat_'.$ugenre.'_'.$sal_username.'.pdf', 'I');