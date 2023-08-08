<?php
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // For root directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php"); // For "custom" 

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

dol_include_once('/approbation/class/approbation.class.php');
dol_include_once('/approbation/class/approbation_demandes.class.php');
dol_include_once('/approbation/class/approbation_types.class.php');
dol_include_once('/approbation/lib/approbation.lib.php');
dol_include_once('/core/class/html.form.class.php');

$modname = $langs->trans("Mes_approbations");

$langs->load('approbation@approbation');

$employe        = new User($db);
$demandes       = new approbation_demandes($db);
$demande       = new approbation_demandes($db);
$demandes2      = new approbation_demandes($db);
$selectyear     = GETPOST('selectyear');

$fk_user = (GETPOST('fk_user') ? GETPOST('fk_user'):$user->id);
$filter .=($fk_user ? 'AND fk_user ='.$fk_user :'');

if (!empty($selectyear) && $selectyear != -1 ) {
  $filter .= " AND YEAR(date_depot) = '".$selectyear."'";
}
elseif($selectyear == -1){
  $filter.='';
}

$limit  = $conf->liste_limit+1;
$page   = GETPOST("page",'int');
$page = is_numeric($page) ? $page : 0;
$page = $page == -1 ? 0 : $page;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;


  
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
  $nbtotalofrecords = $demandes2->fetchAll($sortorder, $sortfield, "", "", $filter);
  if (($page * $limit) > $nbtotalofrecords) // if total resultset is smaller then paging size (filtering), goto and load page 0
  {
    $page = 0;
    $offset = 0;
  }
}



$nbrtotal = $demandes->fetchAll($sortorder, $sortfield, '', '', $filter);
// $nbrtotalnofiltr = $demandes2->fetchAll($sortorder, $sortfield, 0, 0, $filter);


$gridorlist = '';
if(isset($_GET['gridorlist'])){
  if ($_GET['gridorlist'] == "GRID") {
    $res = powererp_set_const($db, 'APPROBATION_CHOOSE_GRIDORLIST', 'GRID', 'chaine', 0, '', $conf->entity);
    $gridorlist = "GRID";
  } else {
    $res = powererp_set_const($db, 'APPROBATION_CHOOSE_GRIDORLIST', 'LIST', 'chaine', 0, '', $conf->entity);
    $gridorlist = "LIST";
  } 
}else{
  if(powererp_get_const($db,'APPROBATION_CHOOSE_GRIDORLIST',$conf->entity))
    $gridorlist = powererp_get_const($db,'APPROBATION_CHOOSE_GRIDORLIST',$conf->entity);
}

if ($gridorlist == "LIST"){
    header('Location: '.dol_buildpath('/approbation/mesapprobations/list.php',2));
}

// $arretiquette = $etiquette->getEtiquetteByRowid();


$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);

print_barre_liste($modname, $page, $_SERVER["PHP_SELF"], "", $sortfield, $sortorder, "", $nbrtotal, $nbtotalofrecords);




// print '<link rel="stylesheet" href="https://www.jqwidgets.com/jquery-widgets-documentation/jqwidgts/styles/jqx.base.css" type="text/css" />';
print '<link rel="stylesheet" href="'.dol_buildpath('/approbation/css/css_kanban/kanban.css',2).'" type="text/css" />';
print '<script type="text/javascript" src="'.dol_buildpath('/approbation/js/js_kanban/jqxcore.js',2).'"></script>';
print '<script type="text/javascript" src="'.dol_buildpath('/approbation/js/js_kanban/jqxsortable.js',2).'"></script>';
print '<script type="text/javascript" src="'.dol_buildpath('/approbation/js/js_kanban/jqxkanban.js',2).'"></script>';
print '<script type="text/javascript" src="'.dol_buildpath('/approbation/js/js_kanban/jqxdata.js',2).'"></script>';

?>

<?php
print '<form method="get" action="'.$_SERVER["PHP_SELF"].'" class="kanban_approv">'."\n";
  print '<div style="float: left; margin-bottom: 8px; width:100%;">';
      print '<div style="width:10%; float:left;" >';
          print '<a class="icon_list" data-type="list" href="'.dol_buildpath('/approbation/mesapprobations/list.php?gridorlist=LIST',2).'"> <img  src="'.dol_buildpath('/approbation/img/list.png',2).'" style="height:30px" id="list" ></a>';
          print '<a class="icon_list" data-type="grid" href="'.dol_buildpath('/approbation/mesapprobations/index.php?gridorlist=GRID',2).'"> <img src="'.dol_buildpath('/approbation/img/grip.png',2).'" style="height:30px" id="grid" ></a> ';
      print '</div>';

      print '<div class="colors_status_approv" style="">';
         
              print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['a_soumettre'].';"></span>';

                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('a_soumettre').'</span></span>&nbsp';
                print $langs->trans('a_soumettre');
              print '</span>';

              print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['soumi'].';"></span>';

                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('soumis').'</span></span>&nbsp';
                print $langs->trans('soumis');
              print '</span>';

              print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['confirme_resp'].';"></span>';
                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('confirme_resp').'</span></span>&nbsp';
                print $langs->trans('confirme_resp');
              print '</span>';

              print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['refuse'].';"></span>';

                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('refuse').'</span></span>&nbsp';
                print $langs->trans('refus');
              print '</span>';

              print '<span class="statusname STATUSPROPAL_0">';
                print '<span class="colorstatus" style="background:'.approbation_demandes::COLORS_STATUS['annuler'].';"></span>';

                print '<span class="labelstatus"><span class="counteleme">'.$demande->nb_demand_by_etat('annuler').'</span></span>&nbsp';
                print$langs->trans('annuler') ;
              print '</span>';
      print '</div>';

      print '<div style="width:15%; float:right;" >';
          print '<a href="dashboard.php" class="butAction" id="add" >'.$langs->trans("Add").'</a>';
      print '</div>';
  print '</div>';

print '</form>';
print '<div style="clear:both;"></div>';
print '<div id="kanban_approv"></div>';





$data_colors =[
  "a_soumettre" =>'#62B0F7',
  "soumis" =>'#DBE270',
  "confirme_resp" =>'#59D859',
  "refuse" =>'#F59A9A',
  "annuler" =>'#FFB164',
]
?>


<script>
  $(document).ready(function () {
            
      var fields = [
          { name: "id", type: "string" },
          { name: "status", map: "state", type: "string" },
          { name: "text", map: "label", type: "string" },
          { name: "tags", type: "string" },
          { name: "color", map: "hex", type: "string" },
          { name: "resourceId", type: "number" }
      ];
      var source =
       {
           localData: [

              <?php foreach ($demandes->rows as $key => $value){ 
                  $type = new approbation_types($db);
                  $type->fetch($value->fk_type);
                  $demande->fetch($value->rowid);
                  $approvers = explode(',', $demande->approbateurs);
                  if(in_array($user->id,$approvers)){
                      $cl = 'permis_confirm';
                  }else{
                      $cl = '';
                  }

                  print '{ id: "'.$value->rowid.'", state: "'.$value->etat.'", label: "<span class=\'span_'.$value->etat.' '.$cl.'\'><b>'.$value->nom.'</b></span><br><span class=\'type\'>'.$type->nom.'</span>", tags: "'.$tag.'", hex: "'.$data_colors[$value->etat].'", resourceId: '.$value->rowid.' },';
              } ?>
              
              
           ],
           dataType: "array",
           dataFields: fields
       };
       $data_colors ={
          "a_soumettre" :'#62B0F7',
          "soumis" :'#DBE270',
          "confirme_resp" :'#59D859',
          "refuse" :'#F59A9A',
          "annuler" :'#FFB164',
      }
      var dataAdapter = new $.jqx.dataAdapter(source);
      var resourcesAdapterFunc = function () {
          var resourcesSource =
          {
              localData: [
                  <?php
                      foreach ($demandes->rows as $key => $value){ 
                          $demande->fetch($value->rowid);
                          if($demande->fk_user){
                              $user_ = new User($db);
                              $user_->fetch($demande->fk_user);
                              
                              // print $contact->firstname.' '.$contact->lastname;
                              $name = $langs->trans('Request_Owner').' : \n';
                              $name .= '  '.$langs->trans("Name").' : '.$user_->firstname.' '.$user_->lastname ;
                              if (! empty($user_->login))
                                  $name.= '('.$user_->login.') \n';
                              else 
                                  $name .= '\n';
                              if (! empty($user_->email))
                                  $name.= '  '.$langs->trans("Email").': '.$user_->email;
                              if (! empty($user_->admin))
                                  $name .= '\n'.'  '.$langs->trans("Administrator").' : '.yn($user_->admin).'\n';
                              $src = DOL_URL_ROOT.'/viewimage.php?modulepart=userphoto&entity='.$conf->entity.'&file='.$demande->fk_user.'/'.$user_->photo.'&perm=download';
                              print '{ id: "'.$demande->rowid.'", name: "'.html_entity_decode($name).'", image: "'.$src.'", common: true },';
                          }else{
                              print '{ id: "'.$demande->rowid.'", name: "'.$langs->trans("aucun_resp").'", image: "'.dol_buildpath('/recrutement/img/user.png',2).'", common: true },';
                          }
                      }

                      // print '{ id: "", name: "", image: "'.dol_buildpath('/approbation/img/user.png',2).'", common: true },';
                  ?>
                    
              ],
              dataType: "array",
              dataFields: [
                  { name: "id", type: "number" },
                  { name: "name", type: "string" },
                  { name: "image", type: "string" },
                  { name: "common", type: "boolean" }
              ]
          };
          var resourcesDataAdapter = new $.jqx.dataAdapter(resourcesSource);
          return resourcesDataAdapter;
      }

      $('#kanban_approv').jqxKanban({
          resources: resourcesAdapterFunc(),
          source: dataAdapter,
          height: 590,
          columns: [
            { text: "<?php echo $langs->trans('a_soumettre') ?>", dataField: "a_soumettre" },
            { text: "<?php echo $langs->trans('soumis') ?>", dataField: "soumis" },
            { text: "<?php echo $langs->trans('confirme_resp') ?>", dataField: "confirme_resp" },
            { text: "<?php echo $langs->trans('refus') ?>", dataField: "refuse" },
            { text: "<?php echo $langs->trans('annuler') ?>", dataField: "annuler" },
          ]
      }); 

      var log = new Array();
      var updateLog = function () {
          var count = 0;
          var str = "";
          for (var i = log.length - 1; i >= 0; i--) {
              str += log[i] + "<br/>";
              count++;
              if (count > 10)
                  break;
          }
          $("#log").html(str);
      }

      $('#kanban_approv').on('itemMoved', function (event) {


          var args = event.args;
          var itemId = args.itemId;
          var oldParentId = args.oldParentId;
          var newParentId = args.newParentId;
          var itemData = args.itemData;
          var oldColumn = args.oldColumn;
          var newColumn = args.newColumn;
          $id_demande=itemId;
          $id_old=oldColumn['dataField'];
          $id_new=newColumn['dataField'];
          $id_old=$id_old;
          $id_new=$id_new;
         
          
          $.ajax({
              data:{'id_demande':$id_demande,'etat':$id_new},
              url:"<?php echo dol_buildpath('/approbation/mesapprobations/chande_etat.php',2); ?>",
              type:'POST',
              success:function(data){
                if(data=="refuse"){
                  location.reload();
                }
                if(data=="success"){
                  var color = $data_colors[$id_new];
                  console.log('color'+$data_colors[$id_new]+'id_new:'+$id_new);
                  $('#kanban_approv_'+$id_demande).find('.jqx-kanban-item-color-status').css('background-color','5px solid '+$data_colors[$id_new]);
                }
              }
          });

          log.push("itemMoved is raised");
          updateLog();
      });
      $('#kanban_approv').on('columnCollapsed', function (event) {
          var args = event.args;
          var column = args.column;
          // log.push("columnCollapsed is raised");
          updateLog();
      });
      $('#kanban_approv').on('columnExpanded', function (event) {
          var args = event.args;
          var column = args.column;
          log.push("columnExpanded is raised");
          updateLog();
      });
      $('#kanban_approv').on('itemAttrClicked', function (event) {
          var args = event.args;
          var itemId = args.itemId;
          var attribute = args.attribute; // template, colorStatus, content, keyword, text, avatar
          log.push("itemAttrClicked is raised");
          updateLog();
      });
      $('#kanban_approv').on('columnAttrClicked', function (event) {
          var args = event.args;
          var column = args.column;
          var cancelToggle = args.cancelToggle; // false by default. Set to true to cancel toggling dynamically.
          var attribute = args.attribute; // title, button
          log.push("columnAttrClicked is raised");
          updateLog();
      });



      $('.jqx-kanban-item-text').find('b').click(function(){
          $id=$(this).parent().parent().parent().attr('id');
          $id=$id.split('_');
          var id = $id[$id.length-1];
          location.href="<?php echo dol_buildpath('/approbation/mesapprobations/card.php?id=',2)?>"+id;

          // location.href=$('#show_8').attr('href');
      });

  });
</script>


<script>
  $(document).ready(function() {
  })
</script>


<?php
llxFooter();
if (is_object($db)) $db->close();
?>