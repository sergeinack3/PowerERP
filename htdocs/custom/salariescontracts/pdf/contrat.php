<?php
//============================================================+
// Author: Oubtou Mohamed 28/01/2017 : 12:17
//============================================================+
require_once DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';
dol_include_once('/salariescontracts/lib/salariescontracts.lib.php');
dol_include_once('/salariescontracts/common.inc.php');

$id 	 = GETPOST('id', 'int');
$form = new Form($db);
$sc = new Salariescontracts($db);
$sc->fetch($id);
$usr = new User($db);
$usr->fetch($sc->fk_user);

$Ausr = new User($db);
$Ausr->fetch($sc->fk_user);

if (!class_exists('TCPDF')) {
    die(sprintf("Class 'TCPDF' not found in %s", DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf.php'));
}

// create new PDF document
$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Oubtou Mohamed');

// set margins
$pdf->SetPrintHeader(false);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

$pdf->AddPage('P', 'A4');

$table ;

$societe = $conf->global->MAIN_INFO_SOCIETE_NOM;
$ville = $conf->global->MAIN_INFO_SOCIETE_TOWN;


$Agenre  = "";
if($Ausr->gender != ''){
$Agenre  = ( $Ausr->gender == "man" ) ? "Mr." : "Mlle.";
}
$adminame= "<b>".$Ausr->firstname." ".$Ausr->lastname."</b>";
$Afunction= "<b>".$Ausr->job."</b>";

$ugenre  = ( $usr->gender == "man" ) ? "Mr." : ($usr->gender != '') ? "Mlle." : "";
$ugenre  = $Agenre;
$username= "<b>".$usr->firstname." ".$usr->lastname."</b>";
$datenai = "__/__/____";
$cin 	 =  ( $usr->array_options['options_nx_cin'] ) ? $usr->array_options['options_nx_cin'] : "________";
$uville  = '<span style="color:#000;">..................................................</span>';

// $dt1 = split('-', $usr->array_options['options_nx_date_embauche']);
$dt1 = "";
if(!empty($Ausr->dateemployment))
	$dt1 = date('d/m/Y',$Ausr->dateemployment);

if($dt1)
	$udebut  = "<b>".$dt1."</b>";
else
	$udebut  = "<b>__/__/____</b>";
$ufunction= "<b>".$usr->job."</b>";
$nbrhour  = number_format($usr->weeklyhours);
$salaire  = number_format($usr->salary,2,',',' ');

$c_debut = date('d/m/Y',$sc->start_date);
$cc_debut = date('Y-m-d',$sc->start_date);
$c_fin   = $sc->end_date ? date('d/m/Y',$sc->end_date) : "<b>__/__/____</b>"; ;
$datenai   = $Ausr->birth ? date('d/m/Y',$Ausr->birth) : "<b>__/__/____</b>"; ;

$global_currency = $langs->getCurrencySymbol($conf->currency);

$contrattype = $sc->getContractTypeById($sc->type);

// print_r($Ausr);
if($type == 2){
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->Cell(0, 2,'',0,1,'C');
	$table .='<style>
	    .para{
	    	font-family: "Times New Roman (Titres CS)";
	    	font-size: 14px;
	    	line-height: 20px;
	    }
	</style>
	<h2 style="text-align:center;">CONTRAT DE TRAVAIL DE TYPE <br/> DUREE DETERMINEE - CDD</h2><h2></h2>';
	$table .='<p class="para"><br/><br/><br/><br/>
		Entre les soussignés :<br/>
		La société <b>'.$societe.'</b> dont le siège social est située à '.$ville.', représentée par '.$Agenre.' '.$adminame.' en sa qualité de '.$Afunction.'.<br/>
		D’une part <br/>
		Et '.$ugenre.' '.$username.'  Né le '.$datenai.' Titulaire de la CIN N° '.$cin.' <br/>

		Demeurant à '.$uville.' ,<br/>
		D’autre part <br/>
		Il a été convenu ce qui suit.<br/>

		<br/><b>Article 1 : Engagement </b><br/><br/>

		'.$ugenre.' '.$username.' est engagé au sein de la société '.$societe.', à compter du <b>'.$udebut.'</b> en qualité de <b>'.$ufunction.'</b>.<br/>

		<br/><b>Article 2 : Durée du contrat</b><br/><br/>

		Le présent contrat est conclu pour une durée déterminée, du <b>'.$c_debut.'</b> au <b>'.$c_fin.'</b>.<br/><br/>
		Les <b>3 mois</b> premiers de son exécution soit du <b>'.$c_debut.'</b> au <b>'.date("d/m/Y",strtotime(''.$cc_debut.' +3 month')).'</b>, constituent une période d\'essai renouvelable une seule fois, au cours de laquelle chacune des parties pourra mettre fin sans indemnité d\'aucune sorte, à charge pour la partie qui rompt la période d’essai de respecter les dispositions légales et conventionnelles.<br/>

		<br/><b>Article 3 : Fonctions</b><br/><br/>
		Le salariées engagé en qualité de : '.$ufunction.'. L\'employeur se réserve le droit d\'affecter le salarié à une autre fonction et ce, selon les besoins de l\'employeur et en considération de la formation et des qualifications dusalarié.<br/>

		<br/><b>Article 4 : Durée de travail</b><br/><br/>
		La durée de travail est de '.$nbrhour.'heures par semaine, réparties sur 6 jours ouvrables. <br/>
		Les horaires de travail pourront varier en fonction des besoins de service. <br/>

		<br/><b>Article 5 : Lieu de travail </b><br/><br/>
		Le lieu de travail est la ville de '.$ville.'.<br/>
		L\'employeur se réserve toutefois le droit de changer le lieu du travail du salarié sur le territoire du Maroc pour les besoins du service<br/>

		<br/><b>Article 6 : Rémunération</b><br/><br/>
		En contrepartie de ses fonctions, le salarié percevra une rémunération mensuelle nette de <b>'.$salaire.'</b> '.$global_currency.'.<br/>


		<br/><b>Article 7 : Remboursement de frais</b><br/><br/>
		La société remboursera au salarié les frais engagés par le salarié dans le cadre de l\'exercice de ses fonctions, sur présentation des justificatifs et conformément à la procédure de remboursement de frais applicable dans l\'entreprise.<br/>
		
		<br/><b>Article 8 : Congés payés </b><br/><br/>
		Vous bénéficierez des congés payés et la durée de vos congés payés sera de <b>18 jours ouvrables par an</b>. 


		<br/><br/><b>Article 9 : Retraite complémentaire </b><br/><br/>
		'.$ugenre.' '.$username.' bénéficiera des lois sociales instituées en faveur des salariés notamment en matière de sécurité sociale et en ce qui concerne le régime de l’assurance maladie obligatoire (AMO). La caisse de retraite est : <br/>
		Nom : <b>Caisse Nationale de Sécurité Sociale (CNSS)</b> <br/>


		
		<br/><b>Article 10 : Règlement intérieur </b><br/><br/>
		Les parties s’engagent à respecter les dispositions légales, réglementaires et conventionnelles en vigueur dans l’entreprise et le salarié déclare avoir pris connaissance du Règlement Intérieur.<br/>

		
		<br/><b>Article 11 : Clause de confidentialité</b><br/><br/>
		Vous vous engagez à observer la discrétion la plus stricte sur les informations se rapportant aux activités de la société auxquelles vous aurez accès à l’occasion et dans le cadre de vos fonctions.<br/>
		Notamment, vous ne divulguerez à quiconque les méthodes, recommandations, créations, devis, études, projets, savoir-faire de l’entreprise résultant de travaux réalisés dans l’entreprise qui sont couverts par le secret professionnel le plus strict. Vous serez lié par la même obligation vis-à-vis de tout renseignement ou document dont vous aurez pris connaissance chez des clients de la société.<br/>
		Tous les documents, lettres, notes de service, instructions, méthodes, organisation et/ou fonctionnement de l’entreprise dont vous pourrez avoir connaissance dans l’exercice de vos fonctions, seront confidentiels et resteront la propriété exclusive de la Société.<br/>
		Vous ne pourrez, sans accord écrit de la direction, publier aucune étude sous quelque forme que ce soit portant sur des travaux ou des informations couverts par l’obligation de confidentialité. Cette obligation de confidentialité se prolongera après la cessation du contrat de travail, quelle qu’en soit la cause.<br/>
		
		<br/><b>Article 12 : Obligation de fidélité</b><br/><br/>
		Pendant la durée du présent contrat, vous prenez l’engagement de ne participer, sous quelque forme que ce soit, à aucune activité susceptible de concurrencer en tout ou partie celle de la société qui vous emploie.<br/>
		
		<br/><b>Article 13 : Avertissementset résiliation</b><br/><br/>
		L’employeur peut rompre le contrat à durée déterminée avant le terme prévu dans le contrat ou avant que la durée minimale de celui-ci n’ait été atteinte en cas de quatre avertissements de faute grave du salarié en se référant à l’article 37 du code du travail.<br/>
		Sauf licenciement pour faute grave ou lourde, le contrat pourra être résilié, par l’une ou l’autre des parties, moyennant un délai-préavis d’un mois.<br/>

		<br/><br/>

		<br/><b>Article 14 : Modifications des informations personnelles</b><br/><br/>
		Vous vous engagez à informer la société dans les meilleurs délais de tout changement de votre situation personnelle (adresse, situation familiale…). Cette base d’informations est transmise au Comité d’Entreprise et lui permet d’attribuer d’éventuels avantages conditionnés à ses critères.
		Votre accord implique formellement que vous n\'êtes lié à aucune autre entreprise et que vous avez quitté votre précédent employeur libre de tout engagement. Vous vous engagez à consacrer toute votre activité professionnelle au service de la société.
		Vous voudrez bien nous confirmer votre accord en apposant votre signature précédée de la mention manuscrite "lu et approuvé" sur la dernière page.
		Nous vous prions de croire, '.$ugenre.' '.$username.', à l\'expression de nos salutations distinguées.<br/>

		<br/><br/><br/>
		Fait en trois exemplaires,<br/>
		A '.$ville.', le : <b>'.date("d/m/Y").'</b>

		<br/><br/><br/>
		Le salarié   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; La société '.$societe.'  
	</p>';
}elseif($type == 1){
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->Cell(0, 2,'',0,1,'C');
	$table .='<style>
	    .para{
	    	font-family: "Times New Roman (Titres CS)";
	    	font-size: 14px;
	    	line-height: 20px;
	    }
	</style>
	<h2 style="text-align:center;">CONTRAT DE TRAVAIL DE TYPE <br/> DUREE INDERTERMINE - CDI</h2><h2></h2>';
	$table .='<p class="para"><br/><br/><br/><br/>
		Entre les soussignés :<br/>


		La Société <b>'.$societe.'</b> dont le siège social est située à'.$ville.', représentée par '.$Agenre.' '.$adminame.'en sa qualité de '.$Afunction.'  Ci-après désignée la Société.
		D’une part <br/>
		Et '.$ugenre.' '.$username.'  Né le '.$datenai.' Titulaire de la CIN N° '.$cin.' <br/>

		Demeurant à '.$uville.' ,<br/>
		D’autre part <br/>
		
		Il a été convenu ce qui suit.<br/>


		<br/><b>Article 1 : Engagement </b><br><br>


		'.$ugenre.' '.$username.' est engagé au sein de la société '.$societe.', à compter du <b>'.$udebut.'</b> en qualité de <b>'.$ufunction.'</b>.<br/>
		 

		<br/><b>Article 2 : Durée du contrat</b><br/><br/>

		Le contrat de travail du Salarié est un contrat de travail à durée indéterminée et pourra prendre fin , à toute époque de la volonté  de l\'une ou l\'autre des parties, sous réserve du respect des délais de préavis légaux ou conventionnels applicables.<br/>

		Les 3 premiers mois de son exécution constituent une période d\'essai pour les cadres ou les 1 premiers  mois de période d’essai pour les non cadres, au cours de laquelle chacune des parties pourra mettre fin sans indemnité d\'aucune sorte, à charge pour la partie qui rompt la période d’essai de respecter les dispositions légales et conventionnelles. Cette période d\'essai pourra être prolongée exceptionnellement d\'une période de même durée.<br/>


		<br/><b>Article 3 : Fonctions </b><br/></br/>
		 

		<br>Le salariées engagé en qualité de : '.$ufunction.'. L\'employeur se réserve le droit d\'affecter le salarié à une autre fonction et ce, selon les besoins de l\'employeur et en considération de la formation et des qualifications dusalarié.<br/>
		 
		<br/><b>Article 4 : Durée de travail</b><br/><br/>
		La durée de travail est de '.$nbrhour.'heures par semaine, réparties sur 6 jours ouvrables. <br/>
		Les horaires de travail pourront varier en fonction des besoins de service. <br/>


		<br/><b>Article 5 : Lieu de travail et clause de mobilité </b><br/></br/>


		Le lieu de travail est la ville de '.$ville.'.<br/>
		L\'employeur se réserve toutefois le droit de changer le lieu du travail du salarié sur le territoire du Maroc pour les besoins du service<br/>

		 


	
		<br/><b>Article 6 : Rémunération</b><br/><br/>

		En contrepartie de ses fonctions, le salarié percevra une rémunération mensuelle nette de <b>'.$salaire.'</b> '.$global_currency.'.<br/>



		<br/><b>Article 7 : Remboursement de frais</b><br/><br/>

		La société remboursera au salarié les frais engagés par le salarié dans le cadre de l\'exercice de ses fonctions, sur présentation des justificatifs et conformément à la procédure de remboursement de frais applicable dans l\'entreprise. </br>

		 

		<br/><b>Article 8 : Congés payés</b><br/><br/>

		 

		Vous bénéficierez des congés payés et en l’absence d’accord commun, la date de vos congés payés sera déterminée par la société et sa durée 18 jours ouvrables par an.<br/>

		 

		<br/><b>Article 9 : Protection sociale</b><br/><br/>

		 

		Vous cotiserez aux différents régimes de retraite et de prévoyance en vigueur au sein de notre Société.<br/>

		 

		<br/><b>Article 10 : Règlement intérieur et Charte informatique</b><br/><br/>

		 

		Les parties s’engagent à respecter les dispositions légales, réglementaires et conventionnelles en vigueur dans l’entreprise et le salarié déclare avoir pris connaissance du Règlement Intérieur.

		Vous vous engagez également à accepter les modalités de la Charte informatique, dont un exemplaire est porté à votre connaissance au moment de votre arrivée.<br/>

		 

		<br/><b>Article 11 : Clause de confidentialité</b><br/><br/>

		 

		Vous vous engagez à observer la discrétion la plus stricte sur les informations se rapportant aux activités de la société auxquelles vous aurez accès à l’occasion et dans le cadre de vos fonctions.<br/>

		Notamment, vous ne divulguerez à quiconque les méthodes, recommandations, créations, devis, études, projets, savoir-faire de l’entreprise résultant de travaux réalisés dans l’entreprise qui sont couverts par le secret professionnel le plus strict. Vous serez lié (e) par la même obligation vis-à-vis de tout renseignement ou document dont vous aurez pris connaissance chez des clients de la société.<br/>

		Tous les documents, lettres, notes de service, instructions, méthodes, organisation et/ou fonctionnement de l’entreprise dont vous pourrez avoir connaissance dans l’exercice de vos fonctions, seront confidentiels et resteront la propriété exclusive de la Société.<br/>

		Vous ne pourrez, sans accord écrit de la direction, publier aucune étude sous quelque forme que ce soit portant sur des travaux ou des informations couverts par l’obligation de confidentialité. Cette obligation de confidentialité se prolongera après la cessation du contrat de travail, quelle qu’en soit la cause.<br/>

		 

		<br/><b>Article 12 : Obligation de fidélité</b><br/><br/>

		 

		Pendant la durée du présent contrat, vous prenez l’engagement de ne participer, sous quelque forme que ce soit, à aucune activité susceptible de concurrencer en tout ou partie celle de la société qui vous emploie.<br/>

		 


		<br/><b>Article 13 : Résiliation</b><br/><br/>

		 

		Sauf licenciement pour faute grave ou lourde, le contrat pourra être résilié, par l’une ou l’autre des parties, moyennant un délai-congé dont la durée est fixée par la convention collective en fonction du statut et de l’ancienneté dans l’entreprise.<br/>

		 

		<br/><b>Article 14 : Modifications des informations personnelles</b><br/><br/>

		 
		Vous vous engagez à informer la société dans les meilleurs délais de tout changement de votre situation personnelle (adresse, situation familiale…). Cette base d’informations est transmise au Comité d’Entreprise et lui permet d’attribuer d’éventuels avantages conditionnés à ses critères.
		Votre accord implique formellement que vous n\'êtes lié à aucune autre entreprise et que vous avez quitté votre précédent employeur libre de tout engagement. Vous vous engagez à consacrer toute votre activité professionnelle au service de la société.
		Vous voudrez bien nous confirmer votre accord en apposant votre signature précédée de la mention manuscrite "lu et approuvé" sur la dernière page.
		Nous vous prions de croire, '.$ugenre.' '.$username.', à l\'expression de nos salutations distinguées.<br/>

		<br/><br/><br/>
		 
		Fait en deux originaux,<br/>

		A '.$ville.', le : <b>'.date("d/m/Y").'</b>

		<br/><br/><br/>

		Le salarié   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; La société '.$societe.'  
	</p>';

}

// elseif(){
// 	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
// 	// set some language dependent data:
// 	$lg = Array();
// 	$lg['a_meta_charset'] = 'UTF-8';
// 	$lg['a_meta_dir'] = 'rtl';
// 	$lg['a_meta_language'] = 'fa';
// 	$lg['w_page'] = '';

// 	// set some language-dependent strings (optional)
// 	$pdf->setLanguageArray($lg);
// 	$pdf->setRTL(true);

// 	// set font
// 	$pdf->SetFont('aefurat', '', 18);
// 	// ---------------------------------------------------------

// 	$pdf->Cell(0, 2,'',0,1,'C');
// 	$table .='<style>
// 	    .para{
// 	    	font-size: 14px;
// 	    	line-height: 20px;
// 	    }
// 	</style>
// 	<p class="para">
// 	<h4 align="center" style="text-decoration: underline;">عقــد الشغــل محــدد المــدة</h4>
// 	<b> الطـرف الأول: </b><br>
	
// 	شـركــة "'.$societe.'" فـي شخـص ممثلهــا القـانـونـي والكـائـن مقـرهـا الاجتمـاعـي فـي تجزئة المستقبل، رقـم 14، ص.ب: 4131 - العيـون.
// 	<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; بـوصفهــا المشغــل 
	
// 	<br><br>
// 	<b>الطـرف الثــانــي: </b><br>
	
// 	السيد "'.$username.'" الحـامـل لبطـاقـة التعـريـف الـوطنيـة رقـم '.$cin.'
// 	<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; بـوصفه الأجيــر

// 	<h4 align="center" style="text-decoration: underline;">اتفقــا علــى مــا يلـي</h4>

// 	<b>المـادة 1: </b><br>
// 	يتــم إبراء هـدا العقـد لمــدة محــددة تبتدئ مـن '.$c_debut.' إلي غـايـة '.$c_fin.'.

// 	<br><br>
// 	<b>المـادة 2: </b><br>
// 	يستفيــد الأجيـر مـن جميـع الحقـوق المتضمنـة فـي تشـريـع الشغـل.

// 	<br><br>
// 	<b>المـادة 3: </b><br>
// 	يتـم تشغيـل الأجيـر كعـامل بـورش الشـركـة المشـار إليه أعـلاه بشـرط التـوفـر علـى شـروط الصحـة والليـاقـة البـدنيـة اللازمـة للقيام بهـذه المهمـة.
	
// 	<br><br>
// 	<b>المـادة 4: </b><br>
// 	تحـدد مـدة التجـربـة فـي 3 أشهر قـابلــة للتجـديـد مـرة واحـدة.

// 	<br><br>
// 	<b>المـادة 5:  التـزامـات الأجيـر  </b><br>
// 	-	الأجيـر مسـؤول عـن عملـه ويتحمـل كـل التبعـات المتـرتبـة عـن أي إهمال أو تقصيـر فـي القيـام بـالمهـام المنــوطـة اليــه،
// 	<br>-	يلتــزم الأجيــر باتباع التعليمـات والأوامـر المسطـرة لـه مـن طـرف مسـؤولـي الشـركـة، كـل مخـالفـة لهـذا البنـد تحمـل الأجيـر المسـؤوليـة الكـاملـة والتبعـات المتـرتبـة عـن ذلـك،
// 	<br>-	يلتـزم الأجيـر بـاحتـرام شـروط الصحـة والسـلامـة والتـي يصـرح أنـه علـى علـم بهـا،
// 	<br>-	يلتـزم الأجيـر بـاحتـرام تـوقيـت العمـل المعمـول بـه فـي الـورش المشـار إليه أعـلاه والمطلـع عليـه سلفـا مـن طـرف الأجيـر.

// 	<br><br>
// 	<b>المـادة 6: </b><br>
// 	يتــم فســخ هــدا العقــد فـي الحـالات التـاليـة:
// 	<br>1/ نهـايـة الأشغـال والخـدمـات بالورش المشـار إليه أعـلاه،
// 	<br>2/ فســخ العقــد الــرئيســي المبـرم بيـن الشــركــات المتعــاقــدة فــي الصفقــة،
// 	<br>3/ ارتكـــاب خطـأ جسيــم مـن طـرف الأجيـر مـع التـزام الأخيـر بأداء تعـويـض عـن الضـرر الـذي قـد يلحـق المشغـل جـراء ذلـك،
// 	<br>4/ اتفـاق الطـرفيـن،
// 	<br>5/ القـوة القـاهـرة،
// 	<br>6/ استقـالـة الأجيـر،
// 	<br>7/ عـرقلـة حسـن سيـر خدمــات الشـركـة مـن طـرف الأجيـر.

// 	<br><br>
// 	<b>المـادة 7: </b><br>
// 	بصفــة عــامــة، فـان عـلاقـة الشغـل بيـن الطـرفيـن تحكمهــا بنــود مـدونـة الشغــل

// 	<br><br><br><br>
// 	المشغــل &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; الأجيــر
// 	</p>';
// }




$tbl = <<<EOF
    $table
EOF;
$pdf->writeHTML($tbl, true, false, true, false, '');

$pdf->Output('contrat_'.$ugenre.'_'.$username.'.pdf', 'I');