<?php 
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");       // For root directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php"); // For "custom" 
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
// require_once DOL_DOCUMENT_ROOT.'/ged_categories/class/ged_categories.class.php';


dol_include_once('/approbation/class/approbation_demandes.class.php');
dol_include_once('/core/class/html.form.class.php');
dol_include_once('/approbation/class/ged_categories.class.php');
dol_include_once('/approbation/class/ged_tags.class.php');
dol_include_once('/approbation/lib/approbation.lib.php');
dol_include_once('/approbation/class/ged_files.class.php');

$langs->load('approbation@approbation');

$modname = $langs->trans("documents");

// Initial Objects
$demande = new approbation_demandes($db);
$user_    = new User($db);
$form   = new Form($db);

// Get parameters
$request_method = $_SERVER['REQUEST_METHOD'];

$action         = GETPOST('action', 'alpha');
$urlfile        = GETPOST('urlfile', 'alpha');
$confirm        = GETPOST('confirm', 'alpha');
$page           = GETPOST('page');
$action_2       = GETPOST('action_2');
$id             = (int) ( (!empty($_GET['id'])) ? $_GET['id'] : GETPOST('id') ) ;

if($action == "delete") {
    if (!$user->rights->approbation->supprimer) {
      accessforbidden();
    }
}

if($action == "confirm_deletefile" && GETPOST('confirm') == 'yes' && $urlfile && $id) {
        $filtodlt = $conf->approbation->dir_output.'/demandes/'.$id.'/'.$urlfile;
    @chmod($filtodlt, octdec(777));
    dol_delete_file($filtodlt);
    clearstatcache();

    header('Location: '.dol_buildpath('/approbation/files.php?id='.$id,2));
}

if($action == 'update'){
            
    // $titredoc = GETPOST('titre_doc');
    // $titredoc = $files->stripAccents(GETPOST('titre_doc'));
    // die($titredoc);
    $demande    = new approbation_demandes($db);
    $demande->fetch($id);

    // $categories->fetch($file->categorie);
    // $oldpath  = $categories->getRelativePath().$file->id.'/';

    $oldpath = $powererp_main_data_root;
    // $oldlabel = $file->fullname;
    $txt_users = '';
    
    
    $newfullpath = $file->fullpath;

    if( !empty($_FILES['userfile']['name'] )){

        $TFile = $_FILES['userfile'] ;

        $upload_dir = $conf->approbation->dir_output.'/demandes/'.$id.'/';
        dol_syslog('dol_add_file_process upload_dir='.$upload_dir.' allowoverwrite=0, donotupdatesession=1, savingdocmask=""', LOG_DEBUG);

        $nbfile = count($TFile['name']);

        if (dol_mkdir($upload_dir) >= 0)
        {
            for ($i=0; $i < $nbfile; $i++) { 
                $destfull = $upload_dir.$TFile['name'][$i];
                $info = pathinfo($destfull);
                $destfile = $TFile['name'][$i];
                
                $filname    = dol_sanitizeFileName($TFile['name'][$i]);

                $destfull   = $info['dirname'].'/'.$filname;
                $destfile   = $filname;

                $destfile   = dol_string_nohtmltag($destfile);
                $destfull   = dol_string_nohtmltag($destfull);

                $resupload  = dol_move_uploaded_file($TFile['tmp_name'][$i], $destfull, 0, 0, $TFile['error'][$i], 0, $varfiles);

                $oldlabel   = $filname;
            }
        }
    }

    if (1) {
        header('Location: '.dol_buildpath('/approbation/files.php?urlfile='.urlencode($oldlabel).'&id='.$id,1));
        // header('Location: ./documents.php?cat='.$cat);
        exit;
    }
}

$morejs  = array();
llxHeader(array(), $modname,'','','','',$morejs,0,0);

print_fiche_titre($modname);

$error  = false;

$demande->fetch($id);
$head = approbation_prepare_head($id);
dol_fiche_head($head, 'documents', '', -1, '');

?>

<script src="pdfworker/pdf.js"></script>
<script src="pdfworker/pdf.worker.js"></script>

<?php

if($action == "delete") {
    $formquestion = array();
    print $form->formconfirm('files.php?id='.$id.'&urlfile='.$urlfile, $langs->trans("DeleteFile"), $langs->trans("ConfirmDeleteFile"), 'confirm_deletefile', $formquestion, 0, 1);
}

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'" enctype="multipart/form-data" >';
print '<input type="hidden" name="id" value="'.$id.'">';
print '<input type="hidden" name="action" value="update" />';
print '<table class="border nc_table" id="add_doc" width="100%">';
    print '<tr>';
        print '<td class="td_titre">'.$langs->trans('fichier').'</td>';
        print '<td>';
            $max=$conf->global->MAIN_UPLOAD_DOC;        // In Kb
            $maxphp=@ini_get('upload_max_filesize');    // In unknown
            if (preg_match('/k$/i', $maxphp)) $maxphp=$maxphp*1;
            if (preg_match('/m$/i', $maxphp)) $maxphp=$maxphp*1024;
            if (preg_match('/g$/i', $maxphp)) $maxphp=$maxphp*1024*1024;
            if (preg_match('/t$/i', $maxphp)) $maxphp=$maxphp*1024*1024*1024;
            $maxphp2=@ini_get('post_max_size');         // In unknown
            if (preg_match('/k$/i', $maxphp2)) $maxphp2=$maxphp2*1;
            if (preg_match('/m$/i', $maxphp2)) $maxphp2=$maxphp2*1024;
            if (preg_match('/g$/i', $maxphp2)) $maxphp2=$maxphp2*1024*1024;
            if (preg_match('/t$/i', $maxphp2)) $maxphp2=$maxphp2*1024*1024*1024;
            // Now $max and $maxphp and $maxphp2 are in Kb
            $maxmin = $max;
            $maxphptoshow = $maxphptoshowparam = '';
            if ($maxphp > 0)
            {
                $maxmin=min($max, $maxphp);
                $maxphptoshow = $maxphp;
                $maxphptoshowparam = 'upload_max_filesize';
            }
            if ($maxphp2 > 0)
            {
                $maxmin=min($max, $maxphp2);
                if ($maxphp2 < $maxphp)
                {
                    $maxphptoshow = $maxphp2;
                    $maxphptoshowparam = 'post_max_size';
                }
            }

            if ($maxmin > 0)
            {
                // MAX_FILE_SIZE doit précéder le champ input de type file
            print '<input type="hidden" name="max_file_size" value="'.($maxmin*1024).'">';
            }
            // print '<span class="name_file"></span>';
            // print '<a class="upload">'.$langs->trans('upload').'</a>';
            print '<input class="flat minwidth400 userfile" style="" multiple type="file" name="userfile[]" >';
            print '<input type="submit" value="'.$langs->trans("Upload").'" class="butAction">';
            print ' ';
        print '</td>';
    print '</tr>';
print '</table>';
print '</form>';


print '<div id="data_files">';

$dirfiles = $conf->approbation->dir_output.'/demandes/'.$id.'/';
$dirf = '/demandes/'.$id.'/';

$dirfls = glob($dirfiles.'*', GLOB_BRACE);

if(!empty($dirfls) && is_array($dirfls)){

    $i = 0;
    foreach($dirfls as $fls) {
        $idoftr = 'row-POS'.($i+1);
        print '<div class="oneuploadedfile" id="'.$idoftr.'">';
            $i++;

            print '<span class="fileimg">';
            $modulepart = "approbation";
            

            $ext = pathinfo($fls, PATHINFO_EXTENSION);;
            $filename = basename($fls);
            $urladvancedpreview=getAdvancedPreviewUrl($modulepart,$dirf.$filename, 1, 'perm=download');
            if (image_format_supported($filename) > 0)
            {
                $minifile=getImageFileNameForSize($filename, '');

                $urlforhref=DOL_URL_ROOT.'/viewimage.php?modulepart=approbation&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file=demandes/'.$id.'/'.$minifile.'&perm=download';

                print '<a href="'.$urladvancedpreview['url'].'" class="'.$urladvancedpreview['css'].'" target="'.$urladvancedpreview['target'].'" mime="'.$urladvancedpreview['mime'].'" >';
                print '<img class="photo" height="100px" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=approbation&entity='.(!empty($object->entity)?$object->entity:$conf->entity).'&file=demandes/'.$id.'/'.$minifile.'&perm=download" title="">';
                print '</a>';
            }else{
                if($ext == "pdf" ){        
                    if(count($urladvancedpreview)){
                        print '<div class="pdf-main-container">';
                            print '<div class="pdf-contents">';
                                print '<a href="'.$urladvancedpreview['url'].'" class="pictopreview documentpreview pdf-total-pages" mime="application/pdf" title="">';
                                print '<canvas class="pdf-canvas" width="550"></canvas>';
                                print '</a>';
                                // print '<div class="page-loader">Loading page ...</div>';

                            print '</div>';
                            print '<div class="pdf-loader">';
                            print '<a href="'.$urladvancedpreview['url'].'" class="pictopreview documentpreview pdf-total-pages" mime="application/pdf" title="">';
                            print $langs->trans("Loading").'...';
                            print '</a>';
                            print '</div>';
                        print '</div>';
                        print "<script>$(window).on('load', function() {";
                        print 'showPDF("'.$urladvancedpreview['url'].'","'.$idoftr.'")';
                        print '});</script>';
                    }
                }else{
                    print '<img src="'.$demande->array_img($ext).'">';
                }
            }
            print '</span>';

            print '<span class="filetextname">';
            print dol_trunc(urldecode($filename), 22);
            print '</span>';

            print '<span class="deletefile">';
                if(count ($dirfls) > 1)
                print '<a href="'.dol_buildpath('/approbation/files.php?action=delete&urlfile='.urlencode($filename).'&id='.$id,1).'" class="deletefile butActionBTNC butActionDelete" rel="'.urlencode($filename).'" >'.$langs->trans('Delete').'</a>';
            print '</span>';
        print '</div>';
    }
}
print '<div style="clear:both;">';
print '</div>';

?>
<script>
</script>

<?php
llxFooter();