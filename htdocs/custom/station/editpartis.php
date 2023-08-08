<?php 

print load_fiche_titre($langs->trans("Partis"), '', 'object_'.$object->picto);

print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">'; //'.$_SERVER["PHP_SELF"].'
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="id" value="'.$object->id.'">';
if ($backtopage) {
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
}
if ($backtopageforcancel) {
	print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
}

print dol_get_fiche_head();

print '<table class="border centpercent tableforfieldedit">'."\n";

// Common attributes
include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';

// Other attributes
include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_edit.tpl.php';

print '</table>';

print '<button type="submit" name="submitFilter" class="butAction" style="margin:1rem auto;">Valider</button>';

print dol_get_fiche_end();

print '</form>';