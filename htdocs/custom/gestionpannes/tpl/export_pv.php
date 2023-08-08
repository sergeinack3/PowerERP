<?php
$id         = $_GET['id'];
$materiel_id2=$_GET['materiel_id'];
$user2 = new User($db);
$produit=new Product($db);

$html='<style type="text/css">
table td{border-top: 1px solid #F3F4F6;padding: 6px;}
table tr.pair td{background-color: #F3F4F6;}
table {width: 100%;text-align: center;border: 1px solid #F3F4F6;}
div.title{font-size: 1.78em;display: block;text-align: center;font-weight: bold;}
th{text-align: center;}
.taches td{text-align: center;}
td{border:none;border-color:transparent;text-align: left;color: #212121;}
td.width_20_cent{width: 25%}
td.width_rest{width: 75%;}
*{
        font-family: roboto,helvetica;
}
</style>';
$gestionpannesn =new gestionpannes($db);
$yearsexist2 = $gestionpannesn->getYears("date_Affectation");
$ii=0;


// echo "produit->ref : ".$produit->ref;

// print_r($produit);
//


//


   
$html.='<h3 style="text-align: center;">'.$langs->trans('materialp').'</h3>';
$html.='<h3 style="text-align: center;"><span style="text-decoration: underline; color: #ffffff;"><em><strong><span class="st"><span dir="ltr">&nbsp;</span></span></strong></em></span></h3>';

    if (!$user->rights->gestionpannes->gestion->consulter) 
{
accessforbidden();
}

$html.= '<table cellpadding="10px" cellspacing="0" class="info" width="100%" align="center"  border="0.0" border-color: #FFFFFF; cellspacing="0px" cellpadding="10px" >';
    $html.= '<tr '.$bc[$var].' >';
    $html.= '<td align="center">'.$langs->trans("Ref_l").'</td>';
    $html.= '<td align="center">'.$langs->trans('Label').'</td>';
    $html.= '<td align="center">'.$langs->trans('descreption').'</td>';
    $html.= '</tr>';

$colspn = 2;

if (count($gestionpannes->materiels) > 0) 
{ 
  foreach ($gestionpannes->materiels as $materiel_id => $etat) 
  {
    if($materiel_id2==$materiel_id)
    {

      $var = !$var;
      $html.= '<tr '.$bc[$var].' >';
      $html.= '<td align="center">';
      $produit->fetch($materiel_id);
      $html.= $produit->ref;
      $html.= '</td>';
      $html.= '<td align="center">';
      $html.= $produit->label;
      $html.= '</td>';
      $html.= '<td align="center">';
      $html.= $produit->description;
      $html.= '</td>';
      $html.= '</tr>';
    }
  }

}else
{
  $html.= '<tr><td align="center" colspan="7">Aucune donnée disponible dans le tableau</td></tr>';
}

$html.= '</table>';
//

global $powererp_main_data_root;
$produit->fetch($materiel_id2);
  
$dire2=$powererp_main_data_root.'/produit/'.$produit->ref.'/';
$images = scandir($dire2);

$html.=  '<table border="0" cellpadding="10px" cellspadding="5px">';
$html.= '<tr>';
$html.= '<td align="center">';
foreach ($images as $img) 
{
  if (!in_array($img,array(".","..","thumbs"))   ) 
    $html.= '<img height="95px" src="'.$dire2.$img.'" />';

}
$html.= '</td>'; 
$html.= '</tr>'; 
$html.=  '</table>';

$html.='<h3 style="text-align: center;"><span style="text-decoration: underline; color: #ffffff;"><em><strong><span class="st"><span dir="ltr">&nbsp;</span></span></strong></em></span></h3>';


$html.='<h3 style="text-align: center;">'.$langs->trans('utilis1').'</h3>';







$html.=  '<table  cellpadding="10px" cellspacing="0">';




if (!$user->rights->gestionpannes->gestion->consulter)
{
  accessforbidden();
}

$gestionpannes2 = new gestionpannes($db);
$gestionpannes2->getAffectaionsByYear($materiel_id2);

if (count($gestionpannes2->materiels) > 0) 
{

  foreach ($gestionpannes2->materiels as $year => $pannes) {


      
    $html.='<tr><td colspan="4"><br><h3 style="text-align: center;">'.$langs->trans('utilis').' '.$year.'</h3></td></tr>';

    $html.= '<tr bgcolor="#FF0000" class="pair" align="center">';

    $html.= '<td  style="width:30%;" style="border-color:transparent;" align="center"><b>'.$langs->trans('utilisateurp').'</b></td >';

    $html.= '<td  style="width:20%;" style="border-color:transparent;" align="center"><b>'.$langs->trans('Date_Affectationp').'</b></td > ';

    $html.= '<td  style="width:20%;" style="border-color:transparent;" align="center"><b>'.$langs->trans('Date_fin_Affectationp').'</b></td > ';

    $html.= '<td  style="width:30%;" style="border-color:transparent;" align="center"><b>'.$langs->trans('la_dureep').'</b></td ></tr>';
   // print_r($pannes);
   // die();
    foreach ($pannes as $key => $panne) {

        $vartheme="pair";

        
          $var = !$var;

          if($vartheme == "pair")
          $vartheme = "impair";
          elseif($vartheme == "impair")
          $vartheme = "pair";

          $html.= '<tr class="'.$vartheme.'" >';


          $html.= '<td style="width:25%;" align="center">';


          $user2->fetch($panne->iduser);
          $html.= $user2->firstname;
          $html.= " ";
          $html.= $user2->lastname;


          $html.= '</td>';

        $t=($panne->date_Affectation);
        $date2 = explode('-', $t);
        $dateaf = $date2[2]."/".$date2[1]."/".$date2[0];
          //$selected=0,$name='select_',$showempty=1,$id='',$attr=''
          $html.= '<td style="width:25%;" align="center">'.$dateaf.'</td>';
          $t=($panne->date_fin_affectation);

          $date2 = explode('-', $t);
          $datec3 = $date2[2]."/".$date2[1]."/".$date2[0];
          $html.= '<td style="width:25%;" align="center">'.$datec3.'</td>';


          $t=($panne->date_duree);

          $date2 = explode('-', $t);
          $datec3 = $date2[2]."/".$date2[1]."/".$date2[0];

          $d1 = new DateTime($panne->date_Affectation);
          $d2 = new DateTime($panne->date_fin_affectation);
          $diff = $d1->diff($d2);

          $nb_jours = ($diff->d)+1;  
          $nb_year = $diff->y; 
          $nbm=$diff->m;

          $html.= '<td style="width:25%;" align="center">';

          if($nb_year==0 & $nbm==0 & $nb_jours<>0)

          $html.= ''.$nb_jours.' jours ';


          else if ($nb_year <>0 & $nbm == 0 & $nb_jours==0)

          $html.= $nb_year.' année  ';
          else if ($nb_year ==0 & $nbm <> 0 & $nb_jours==0)

          $html.= '  mois '.$nbm.'';

          else if ($nb_year<>0 & $nbm == 0 & $nb_jours<>0)
          $html.= ''.$nb_year.' année et '.$nb_jours.' jours ';

          else if($nb_year==0 & $nbm<>0 & $nb_jours<>0)

          $html.= ''.$nb_jours.' jours et '.$nbm.' mois';

          else if ($nb_year<>0 & $nbm == 0 & $nb_jours<>0)
          $html.= $nb_year.' année et'.$nb_jours.' jours ';
          else if ($nb_year<>0 & $nbm <> 0 & $nb_jours==0)

          $html.= $nb_year.' année et '.$nbm.' mois ';
          else if ($nb_year==0 & $nbm == 0 & $nb_jours==0)
          $html.= 'un jour';
          else
          $html.= ''.$nb_jours.' jours et '.$nb_year.' année et '.$nbm.' mois';
          $html.= '</td>';

          $html.= '</tr>';



          

          
 
    }
  }

          }

          /*RERETYRETR*/


          //

          $html.=  '</table>'; 



// echo $html;
// die();


