<?php
$id2         = $_GET['id'];

	$user3 = new User($db);
	$produit=new Product($db);
	$gestionpannes  = new gestionpannes($db);
	$gestionpannes->fetch($id);
	$item = $gestionpannes;

$html='<style type="text/css">
table.details td{border-top: 1px solid #F3F4F6;padding: 6px;}
table.details tr.pair td{background-color: #F3F4F6;}
table.details {width: 100%;text-align: center;border: 1px solid #F3F4F6;}
div.title{font-size: 1.78em;display: block;text-align: center;font-weight: bold;}
th{text-align: center;}
.taches td{text-align: center;}
.details td{border:none;border-color:transparent;text-align: left;color: #212121;}
.details td.width_20_cent{width: 25%}
.details td.width_rest{width: 75%;}
*{
        font-family: roboto,helvetica;
}
</style>';
   
$html.='<h3 style="text-align: center;">'.$langs->trans('materialp').'</h3>';
$html.='<h3 style="text-align: center;"><span style="text-decoration: underline; color: #ffffff;"><em><strong><span class="st"><span dir="ltr">&nbsp;</span></span></strong></em></span></h3>';

    if (!$user->rights->gestionpannes->gestion->consulter) 
{
accessforbidden();
}

$html.= '<table cellpadding="10px" cellspacing="0" class="details" width="100%" align="center"  border="0.0" border-color: #FFFFFF; cellspacing="0px" cellpadding="10px" >';
      $var = !$var;
 
 $html.= '<tr '.$bc[$var].' >';
    $html.= '<td align="center">'.$langs->trans("Ref_l").'</td>';
    $html.= '<td align="center">'.$langs->trans('Label').'</td>';
    $html.= '<td align="center">'.$langs->trans('descreption').'</td>';
    $html.= '</tr>';
$colspn = 2;



      $var = !$var;
      $html.= '<tr '.$bc[$var].' >';
      $html.= '<td align="center">';
      $produit->fetch($item->matreil_id);
      $html.= $produit->ref;
      $html.= '</td>';
      $html.= '<td align="center">';
      $html.= $produit->label;
      $html.= '</td>';
      $html.= '<td align="center">';
      $html.= $produit->description;
      $html.= '</td>';
      $html.= '</tr>';



$html.= '</table>';
//

global $powererp_main_data_root;
$produit->fetch($item->matreil_id);
  
$html .= $produit->ref;
$html.=  '<table border="0" cellpadding="10px" cellspadding="5px">';
$html.= '<tr>';
$html.= '<td align="center">';
$dire2=$powererp_main_data_root.'/produit/'.$produit->ref.'/';
if(file_exists($dire2)){
  $images = scandir($dire2);
  if($images){
    foreach ($images as $img) 
    {
      $im =$produit->ref.'-'.$img;
      if (!in_array($img,array(".","..","thumbs"))   ) 
        $html.= '<img height="95px" src="'.$dire2.$im.'" />';

    }
  }
}
$html.= '</td>'; 
$html.= '</tr>'; 
$html.=  '</table>';
//
$html.='<h3 style="text-align: center;"><span style="text-decoration: underline; color: #ffffff;"><em><strong><span class="st"><span dir="ltr">&nbsp;</span></span></strong></em></span></h3>';


$html.='<h3 style="text-align: center;">'.$langs->trans('utilis3').'</h3>';


$html.=  '<table  cellpadding="10px" cellspacing="0" class="details">';




  $html.= '<tr bgcolor="#FF0000" class="pair" align="center">';



  $html.= '<td  style="width:30%;" style="border-color:transparent;" align="center"><b>'.$langs->trans('utilisateurp').'</b></td >';

  $html.= '<td  style="width:20%;" style="border-color:transparent;" align="center"><b>'.$langs->trans('Date_Affectationp').'</b></td > ';

  $html.= '<td  style="width:20%;" style="border-color:transparent;" align="center"><b>'.$langs->trans('Date_fin_Affectationp').'</b></td > ';

  $html.= '<td  style="width:30%;" style="border-color:transparent;" align="center"><b>'.$langs->trans('la_dureep').'</b></td ></tr>';


   
   
            $var = !$var;


        $html.= '<tr><td style="width:25%;" align="center">';


        $user3->fetch($item->iduser);
        $html.= $user3->firstname;
        $html.= " ";
        $html.= $user3->lastname;


        $html.= '</td>';

        $t=($item->date_Affectation);

        $date2 = explode('-', $t);
        $dateaf = $date2[2]."/".$date2[1]."/".$date2[0];

        $html.= '<td style="width:25%;" align="center">'.$dateaf.'</td>';
        $t=($item->date_fin_affectation);

        $date2 = explode('-', $t);
        $datec3 = $date2[2]."/".$date2[1]."/".$date2[0];
        $html.= '<td style="width:25%;" align="center">'.$datec3.'</td>';


        $t=($item->date_duree);

        $date2 = explode('-', $t);
        $datec3 = $date2[2]."/".$date2[1]."/".$date2[0];

        $d1 = new DateTime($item->date_Affectation);
        $d2 = new DateTime($item->date_fin_affectation);
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





$html.=  '</table>'; 

$html.= '<div style="font-size:16px;text-align:center;"> <h3> '.$langs->trans('list_photo').' '. $date.' </h3></div>';
$html.=  '<table border="0" cellpadding"5px" cellspacing="5px">';
  


  
$dir = $conf->gestionpannes->dir_output.'/'.$id.'/photo/';
if(file_exists($dir)){
    if(is_dir($dir)){
        $documents=scandir($dir);
    }

    foreach ($documents as  $doc) {
        if (!in_array($doc,array(".","..","files"))) 
        { 
            $minifile = getImageFileNameForSize($doc, '');  
            $dt_files = getAdvancedPreviewUrl('gestionpannes', '/'.$id.'/photo/'.$minifile, 1, '&entity='.(!empty($object->entity)?$object->entity:$conf->entity));

            $html.= '<tr><td align="center"><img height="160px" src="'.$dir.$minifile.'" border="0" name="image" ></td></tr>';

               
        }
    }
}

if(empty(count($documents)))
  $html.= '<tr><td align="center"> Aucun image</td></tr>';



$html.=  '</table>';





$html.= '<div style="font-size:16px;text-align:center;"> <h3> '.$langs->trans('list_photoapr').' '. $date.' </h3></div>';
$html.=  '<table border="0" cellpadding"5px" cellspacing="5px" >';



  
$dir = $conf->gestionpannes->dir_output.'/'.$id.'/photo_materiel/';
if(file_exists($dir)){
    if(is_dir($dir)){
        $documents=scandir($dir);
    }
    foreach ($documents as  $doc) {
        if (!in_array($doc,array(".","..","files"))) 
        { 
            $minifile = getImageFileNameForSize($doc, '');  

            $html.= '<tr><td align="center"><img height="160px" src="'.$dir.$minifile.'" border="0" name="image" ></td></tr>';

               
        }
    }
}



if($sumimg2 == 0)
  $html.= '<tr><td align="center"> Aucun image</td></tr>';

$html.=  '</table>';