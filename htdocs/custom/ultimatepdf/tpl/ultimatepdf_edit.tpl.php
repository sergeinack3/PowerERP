<?php
/* Copyright (C) 2009-2012 Regis Houssin <regis.houssin@capnetworks.com>
 * Copyright (C) 2011-2020 Philippe Grand <philippe.grand@atoo-net.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 */
 
// Protection to avoid direct call of template
if (empty($conf) || ! is_object($conf))
{
	print "Error, template page can't be called as URL";
	exit;
}
?>
 
<!-- BEGIN PHP TEMPLATE -->
<?php require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php'; ?>
<?php require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php'; ?>

<?php $form = new Form($db); ?>
<?php $formfile = new FormFile($db); ?> 

<?php 
echo $this->tpl['action_delete'];
?>

<form name="form_index" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="id" value="<?php echo GETPOST('id'); ?>" />

<div style="text-align:center" class="info">
	<em><b><?php echo $langs->trans("CreateYourModel"); ?></em></b>
</div>
<table class="noborder">
<tr class="liste_titre">
	<td width="35%"><?php echo $langs->trans("DesignInfo"); ?></td>
	<td><?php echo $langs->trans("Value"); ?></td>
</tr>

<tr class="oddeven">
	<td><span class="fieldrequired"><?php echo $langs->trans("Label"); ?></span></td>
	<td><input name="label" size="30" value="<?php echo $this->tpl['label']; ?>" /></td>
</tr>

<tr class="oddeven">
	<td valign="top"><?php echo $langs->trans("Description"); ?></td>
	<td><textarea class="flat" name="description" cols="60" rows="<?php echo ROWS_3; ?>"><?php echo $this->tpl['description']; ?></textarea></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetFontToWhatYouWant"), $langs->trans("SetFontToWhatYouWantDescription")); ?></td>
	<td><?php echo $this->tpl['select_otherfont']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("UseBackGround"), $langs->trans("UseBackGroundDescription")); ?></td>
	<td><input name="usebackground" size="30" value="<?php echo $this->tpl['usebackground']; ?>" /></td>
</tr>
</table>
<br>
<div style="text-align:center" class="info">
	<em><b><?php echo $langs->trans("SetUpHeader"); ?></em></b>
</div>
<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetLogoHeigth"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<?php global $mysoc; ?>
<?php  if (! empty($mysoc->logo))
    {
		$urllogo=DOL_URL_ROOT.'/viewimage.php?modulepart=mycompany&amp;file='.urlencode('logos/'.$mysoc->logo);
	}
?>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetLogoHeigth"), $langs->trans("SetLogoHeigthDescription")); ?></td>
	<td>
		<div id="container_logo" class="ui-widget-content">
			<div id="ui-state-active" class="ui-state-active"> 
				<img id="resizable-1" src="<?php echo (empty($urllogo)?DOL_URL_ROOT.'/public/theme/common/nophoto.png':$urllogo); ?>" />
			</div>
		</div>
	</td>
	<td><input type="text" name="logoheight" id="logoheight" size="30" placeholder="<?php echo $langs->trans("Height"); ?>" value="<?php echo $this->tpl['logoheight']; ?>" /><br><input type="text" name="logowidth" id="logowidth" size="30" placeholder="<?php echo $langs->trans("Width"); ?>" value="<?php echo $this->tpl['logowidth']; ?>" /><br><span id="resizable-2"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>		
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SelectAnOtherlogo"); ?></td>
	<td width="33%"><?php echo $langs->trans("OtherLogoName"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SelectAnOtherlogo"), $langs->trans("OtherlogoDescription")); ?></td>
	<td><input type="text" name="name" size="30" value="<?php echo $this->tpl['otherlogoname']; ?>" /></td>
	<td><input type="file" id="otherlogo" name="otherlogo" size="40" value="<?php echo $this->tpl['select_otherlogo']; ?>" />
	<input type="hidden" id="otherlogo_file" name="otherlogo_file" value="<?php echo $this->tpl['select_otherlogo_file']; ?>" />
	</td>
	<?php $logofilename = $conf->global->ULTIMATE_OTHERLOGO; ?>
	<td><button type="button" id="maj_otherlogo"><?php echo $langs->trans("Add"); ?></button>
</tr>

<tr class="oddeven">
<?php // OtherLogos files management
	$id = $this->dao->id; //to get id
	$upload_dir	= $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/';	
	$filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview.*\.png)$', $sortfield, (strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC), 1);	
	$formfile->list_of_documents($filearray, null, 'ultimatepdf', '&type=otherlogo', 1, '/otherlogo/'.$id.'/', 1, 0, $langs->trans("NoFileFound"), 0, $langs->trans("OtherLogo"), '', 0, 0, $upload_dir, 'position_name', 'ASC');
	$urlotherlogo = DOL_URL_ROOT.'/viewimage.php?modulepart=ultimatepdf&amp;file='.urlencode('otherlogo/'.$id.'/'.$filearray[0]['name']);
?>
</tr>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetLogoHeigth"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>
<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetLogoHeigth"), $langs->trans("SetLogoHeigthDescription")); ?></td>
	<td>
		<div id="container_otherlogo" class="ui-widget-content">
			<div id="ui-state-active" class="ui-state-active"> 
				<img id="resizable-3" src="<?php echo (empty($urlotherlogo)?DOL_URL_ROOT.'/public/theme/common/nophoto.png':$urlotherlogo); ?>" />			
			</div>
		</div>
	</td>
	<td><input type="text" name="otherlogoheight" id="otherlogoheight" size="30" placeholder="<?php echo $langs->trans("Height"); ?>" value="<?php echo $this->tpl['otherlogoheight']; ?>" /><br><input type="text" name="otherlogowidth" id="otherlogowidth" size="30" placeholder="<?php echo $langs->trans("Width"); ?>" value="<?php echo $this->tpl['otherlogowidth']; ?>" /><br><span id="resizable-4"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>
</tr>
</table>
<br>

<?php // Show new sender name ?>
<table class="noborder">
<tr class="liste_titre">
	<td width="35%"><?php echo $langs->trans("SelectAnAliasCompanyName"); ?></td>
	<td><?php echo $langs->trans("Value"); ?></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyName"); ?></td>
	<td><input type="text"  name="aliascompany" size="30" value="<?php echo $this->tpl['aliascompany']; ?>" /></td>
</tr>
</table>
<br>

<?php // Show new sender informations ?>
<table class="noborder">
<tr class="liste_titre">
	<td width="35%"><?php echo $langs->trans("SelectAliasInformation"); ?></td>
	<td><?php echo $langs->trans("Value"); ?></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyAddress"); ?></td>
	<td><input type="text"  name="aliasaddress" size="30" value="<?php echo $this->tpl['aliasaddress']; ?>" /></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyZip"); ?></td>
	<td><input type="text"  name="aliaszip" size="30" value="<?php echo $this->tpl['aliaszip']; ?>" /></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyTown"); ?></td>
	<td><input type="text"  name="aliastown" size="30" value="<?php echo $this->tpl['aliastown']; ?>" /></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyCountry"); ?></td>
	<td><?php echo $this->tpl['select_country'] ?></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyPhone"); ?></td>
	<td><input type="text"  name="aliasphone" size="30" value="<?php echo $this->tpl['aliasphone']; ?>" /></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyFax"); ?></td>
	<td><input type="text"  name="aliasfax" size="30" value="<?php echo $this->tpl['aliasfax']; ?>" /></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyEmail"); ?></td>
	<td><input type="text"  name="aliasemail" size="30" value="<?php echo $this->tpl['aliasemail']; ?>" /></td>
</tr>
<tr class="oddeven">
	<td><?php echo $langs->trans("AliasCompanyUrl"); ?></td>
	<td><input type="text"  name="aliasurl" size="30" value="<?php echo $this->tpl['aliasurl']; ?>" /></td>
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetAddressesBlocks"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetAddressesBlocks"), $langs->trans("SetAddressesBlocksDescription")); ?></td>
	<td>
		<div id="container_AddressesBlocks" class="ui-widget-content">	
			<div id="sender_frame"> sender frame</div> 
			<div id="recipient_frame"> recipient frame</div> 
		</div>
	</td>
	<td><input type="text" name="widthrecbox" id="widthrecbox" size="30" placeholder="<?php echo $langs->trans("SenderBlockWidth"); ?>" value="<?php echo $this->tpl['widthrecbox']; ?>" /><span id="resizable-24"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>		
</tr>
</table>
<br>

<div style="text-align:center" class="info" >
	<em><b><?php echo $langs->trans("SetCoreBloc"); ?></em></b>
</div>

<table class="noborder">
<tr class="liste_titre">
	<td width="35%"><?php echo $langs->trans("DesignInfo"); ?></td>
	<td><?php echo $langs->trans("Value"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $langs->trans("BackgroundColorByDefault"); ?></td>
	<td><?php echo $this->tpl['select_bgcolor']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $langs->trans("SenderBackgroundColorByDefault"); ?></td>
	<td><?php echo $this->tpl['select_senderstyle']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $langs->trans("ReceiptBackgroundColorByDefault"); ?></td>
	<td><?php echo $this->tpl['select_receiptstyle']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetOpacityForBackgroundColor"),$langs->trans("SetOpacityForBackgroundColorDescription")); ?></td>
	<td><input type="text" name="opacity" id="opacity" size="12" value="<?php echo $this->tpl['select_opacity']; ?>" /></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetRadiusForRoundBorder"),$langs->trans("SetRadiusForRoundBorderDescription")); ?></td>
	<td><input type="text" name="roundradius" id="roundradius" size="12" value="<?php echo $this->tpl['select_roundradius']; ?>" /></td>
</tr>

<tr class="oddeven">
	<td><?php echo $langs->trans("BorderColorByDefault"); ?></td>
	<td><?php echo $this->tpl['select_bordercolor']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $langs->trans("SetBorderToDashDotted"); ?></td>
	<td><?php echo $this->tpl['select_dashdotted']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $langs->trans("TextcolorByDefault"); ?></td>
	<td><?php echo $this->tpl['select_textcolor']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $langs->trans("QRcodeColorByDefault"); ?></td>
	<td><?php echo $this->tpl['select_qrcodecolor']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("HideByDefaultProductTvaInsideUltimatepdf"), $langs->trans("SelectWithoutVatDescription")); ?></td>
	<td><?php echo $this->tpl['select_withoutvat']; ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetInvertSenderRecipient"), $langs->trans("SetInvertSenderRecipientDescription")); ?></td>
	<td><?php echo $this->tpl['invertSenderRecipient']; ?></td>
</tr>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetPdfMargin"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetPdfMargin"), $langs->trans("SetPdfMarginDescription")); ?></td>
	<td>
		<div id="container2" class="ui-widget-content">
			<div id="resizable-5" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetPdfMargin"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="marge_gauche" id="marge_gauche" size="30" value="<?php echo $this->tpl['marge_gauche']; ?>" /><br><input type="text" name="marge_droite" id="marge_droite" size="30" value="<?php echo $this->tpl['marge_droite']; ?>" /><br><input type="text" name="marge_haute" id="marge_haute" size="30" value="<?php echo $this->tpl['marge_haute']; ?>" /><br><input type="text" name="marge_basse" id="marge_basse" size="30" value="<?php echo $this->tpl['marge_basse']; ?>" /><br><span id="resizable-6"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>	
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetNumberingColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetNumberingWidth"), $langs->trans("SetNumberingWidthDescription")); ?></td>
	<td>
		<div id="container3" class="ui-widget-content">
			<div id="resizable-13" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetNumberingWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthnumbering" id="widthnumbering" size="30"  placeholder="<?php echo $langs->trans("Width"); ?>" value="<?php echo $this->tpl['widthnumbering']; ?>" /><br><span id="resizable-14"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>	
</tr>
</table>
<br> 

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetRefColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SelectWithRef"), $langs->trans("SelectWithRefDescription")); ?></td>
	<td colspan="3"><?php echo $this->tpl['select_withref']; ?></td>
</tr>
<br>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetRefWidth"), $langs->trans("SetRefWidthDescription")); ?></td>
	<td>
		<div id="container4" class="ui-widget-content">
			<div id="resizable-7" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetRefWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthref" id="widthref" size="30" value="<?php echo $this->tpl['select_widthref']; ?>" /><br><span id="resizable-8"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetImageColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>
<br>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetImageWidth"), $langs->trans("SetImageWidthDescription")); ?></td>
	<td>
		<div id="container4" class="ui-widget-content">
			<div id="resizable-9" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetImageWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="imglinesize" id="imglinesize" size="30" value="<?php echo $this->tpl['imglinesize']; ?>" /><br><span id="resizable-10"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetDateColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>
<br>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetDateWidth"), $langs->trans("SetDateWidthDescription")); ?></td>
	<td>
		<div id="container10" class="ui-widget-content">
			<div id="resizable-25" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetDateWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthdate" id="widthdate" size="30" value="<?php echo $this->tpl['widthdate']?$this->tpl['widthdate']:20; ?>" /><br><span id="resizable-26"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetTypeColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>
<br>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetTypeWidth"), $langs->trans("SetTypeWidthDescription")); ?></td>
	<td>
		<div id="container12" class="ui-widget-content">
			<div id="resizable-29" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetTypeWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthtype" id="widthtype" size="30" value="<?php echo $this->tpl['widthtype']?$this->tpl['widthtype']:20; ?>" /><br><span id="resizable-30"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetProjectColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>
<br>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetProjectWidth"), $langs->trans("SetDateWidthDescription")); ?></td>
	<td>
		<div id="container11" class="ui-widget-content">
			<div id="resizable-27" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetProjectWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthproject" id="widthproject" size="30" value="<?php echo $this->tpl['widthproject']?$this->tpl['widthproject']:20; ?>" /><br><span id="resizable-28"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetTvaColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetTvaWidth"), $langs->trans("SetTvaWidthDescription")); ?></td>
	<td>
		<div id="container6" class="ui-widget-content">
			<div id="resizable-15" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetTvaWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthvat" id="widthvat" size="30" placeholder="<?php echo $langs->trans("Width"); ?>" value="<?php echo $this->tpl['widthvat']; ?>" /><br><span id="resizable-16"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>	
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetUpColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetUpWidth"), $langs->trans("SetUpWidthDescription")); ?></td>
	<td>
		<div id="container7" class="ui-widget-content">
			<div id="resizable-17" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetUpWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthup" id="widthup" size="30" value="<?php echo $this->tpl['widthup']; ?>" /><br><span id="resizable-18"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>	
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetQtyColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetQtyWidth"), $langs->trans("SetQtyWidthDescription")); ?></td>
	<td>
		<div id="container8" class="ui-widget-content">
			<div id="resizable-19" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetQtyWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthqty" id="widthqty" size="30" value="<?php echo $this->tpl['widthqty']; ?>" /><br><span id="resizable-20"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>	
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetUnitColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetUnitWidth"), $langs->trans("SetUnitWidthDescription")); ?></td>
	<td>
		<div id="container_unit" class="ui-widget-content">
			<div id="resizable_unit" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetUnitWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthunit" id="widthunit" size="30" value="<?php echo $this->tpl['widthunit']; ?>" /><br><span id="resizable_unit2"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>	
</tr>
</table>
<br>

<table class="noborder">
<tr class="liste_titre">
	<td width="33%"><?php echo $langs->trans("SetDiscountColumn"); ?></td>
	<td width="33%"><?php echo $langs->trans("Parameters"); ?></td>
	<td width="33%"><?php echo $langs->trans("Value"); ?></td>
	<td style="text-align:right"><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetDiscountWidth"), $langs->trans("SetDiscountWidthDescription")); ?></td>
	<td>
		<div id="container9" class="ui-widget-content">
			<div id="resizable-21" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("SetDiscountWidth"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="widthdiscount" id="widthdiscount" size="30" value="<?php echo $this->tpl['widthdiscount']; ?>" /><br><span id="resizable-22"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>	
</tr>
</table>
<br>

<div style="text-align:center" class="info" >
	<em><b><?php echo $langs->trans("SetFooterBloc"); ?></em></b>
</div>
<table class="noborder">
<tr class="liste_titre">
	<td width="35%"><?php echo $langs->trans("Parameters"); ?></td>
	<td colspan="2"><?php echo $langs->trans("Value"); ?></td>
	<td><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $langs->trans("SetFontSizeForFreeText"); ?></td>
	<td><input name="freetextfontsize" id="freetextfontsize" class="changeMe" size="25" value="<?php echo $this->tpl['select_freetextfontsize']; ?>" /></td><td id="freetextfontsize_text" style="font-size:<?php echo $this->tpl['select_freetextfontsize'].'px'; ?>"><?php echo $langs->trans("Lorem ipsum dolor sit amet, consectetur adipiscing elit"); ?></td><td><button type="button" id="maj_freetext"><?php echo $langs->trans("Update"); ?></button></td>
</tr>
<br>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("SetHeightForFreeText"), $langs->trans("SetHeightForFreeTextDescription")); ?></td>
	<td>
		<div id="container5" class="ui-widget-content">
			<div id="resizable-11" class="ui-state-active"> 				
				<h3 class="ui-widget-header"><?php echo $langs->trans("Resizable"); ?></h3>
			</div>
		</div>
	</td>
	<td><input type="text" name="heightforfreetext" id="heightforfreetext" size="30" value="<?php echo $this->tpl['select_heightforfreetext']; ?>" /><br><span id="resizable-12"></span></td>
	<td><button type="button" id="maj_img"><?php echo $langs->trans("Update"); ?></button></td>
</tr>
</table>
<br>
<table class="noborder">
<tr class="liste_titre">
	<td width="35%"><?php echo $langs->trans("Parameters"); ?></td>
	<td colspan="2"><?php echo $langs->trans("Value"); ?></td>
	<td><?php echo $langs->trans("Action"); ?></td>
</tr>

<tr class="oddeven">
	<td><?php echo $form->textwithpicto($langs->trans("PDFFooterAddressForging"), $langs->trans("ShowDetailsInPDFPageFoot")); ?></td>
	<td colspan="3"><?php echo $this->tpl['select_showdetails']; ?></td>
</tr>
<br>

<tr class="oddeven">
	<td><?php echo $langs->trans("SetFooterTextcolorByDefault"); ?></td>
	<td ><?php echo $this->tpl['select_footertextcolor']?$this->tpl['select_footertextcolor']:array('19','19','19'); ?></td>
	<td ><?php echo '&nbsp'; ?></td>
</tr>
</table>
<br>

<div class="tabsAction">
<input type="submit" class="butAction linkobject" name="update" value="<?php echo $langs->trans('Update'); ?>" />
<input type="submit" class="butAction linkobject" name="cancel" value="<?php echo $langs->trans("Cancel"); ?>" />
</div>
<!-- Javascript -->
    <script>
        $(function() {
			$( "#resizable-1" ).css({
				height: function(){return Math.round(<?php echo (empty($this->tpl['logoheight'])?1:$this->tpl['logoheight']); ?>);},
				width: function(){return Math.round(<?php echo (empty($this->tpl['logowidth'])?1:$this->tpl['logowidth']); ?>);}
				});
            $( "#resizable-1" ).resizable({ 
				containment: "#container_logo",
			    minHeight: 80,
			    minWidth: 160,
				maxHeight: 160,
				maxWidth: 320,
                resize: function (event, ui)
                {
					$("#resizable-2").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(ui.size.width/4) + "px" +
						", <?php echo $langs->trans("Height"); ?> = " + Math.round(ui.size.height/4) + "px");
					$("#logoheight").val(Math.round(ui.size.height/4));
					$("#logowidth").val(Math.round(ui.size.width/4));
                }
            });
        });
    </script>
	<script>
        $(function() {
			$( "#resizable-3" ).css({
				height: function(){return Math.round(<?php echo (empty($this->tpl['otherlogoheight'])?1:$this->tpl['otherlogoheight']); ?>);},
				width: function(){return Math.round(<?php echo (empty($this->tpl['otherlogowidth'])?1:$this->tpl['otherlogowidth']); ?>);}
				});
            $( "#resizable-3" ).resizable({ 
				containment: "#container_otherlogo",
			    minHeight: 80,
			    minWidth: 160,
				maxHeight: 160,
				maxWidth: 320,
                resize: function (event, ui)
                {
					$("#resizable-4").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(ui.size.width/4) + "px" +
						", <?php echo $langs->trans("Height"); ?> = " + Math.round(ui.size.height/4) + "px");
					$("#otherlogoheight").val(Math.round(ui.size.height/4));
					$("#otherlogowidth").val(Math.round(ui.size.width/4));
                }
            });	
			$('#maj_otherlogo').click(function() {
				var files = $('#otherlogo')[0].files;

				if (files.length > 0) {
					// On part du principe qu'il n'y qu'un seul fichier
					// étant donné que l'on a pas renseigné l'attribut "multiple"
					var file = files[0];
					$image_preview = $('#resizable-3');
		 
					// Ici on injecte les informations recoltées sur le fichier pour l'utilisateur
					//$image_preview.find('.thumbnail').removeClass('hidden');
					$image_preview.attr('src', window.URL.createObjectURL(file));
					//$image_preview.find('h4').html(file.name);
					//$image_preview.find('.caption p:first').html(file.size +' bytes');
				}
			});
        });	
    </script>

	<script>
		$(function() {
		
			$( "#resizable-5" ).css({
			posleft: function(){return Math.round(<?php echo (empty($this->tpl['marge_gauche'])?1:$this->tpl['marge_gauche']); ?>);},
			posright: function(){return Math.round(<?php echo (empty($this->tpl['marge_droite'])?1:$this->tpl['marge_droite']); ?>);},
			postop: function(){return Math.round(<?php echo (empty($this->tpl['marge_haute'])?1:$this->tpl['marge_haute']); ?>);},
			posbottom: function(){return Math.round(<?php echo (empty($this->tpl['marge_basse'])?1:$this->tpl['marge_basse']); ?>);}
			});
			$("#resizable-5").resizable({ 
			containment: "#container2",
			minHeight: 257,
			minWidth: 170,
			maxHeight: 297,
			maxWidth: 210,
			resize: function (event, ui)
				{
				var posleft=ui.position.left;
				var posright=210 - ui.size.width - ui.position.left;
				var postop=ui.position.top;			
				var posbottom=297 - ui.size.height - ui.position.top;		
					if(posleft < 0)
						posleft=0;
					if(posright < 0)
						posright=0;
					if(postop < 0)
						postop=0;
					if(posbottom < 0)
						posbottom=0;	
					$("#resizable-6").text ("<?php echo $langs->trans("MargeGauche"); ?> = " + Math.round(posleft) + "mm" +
						", <?php echo $langs->trans("MargeDroite"); ?> = " + Math.round(posright) + "mm" +
						", <?php echo $langs->trans("MargeHaute"); ?> = " + Math.round(postop) + "mm" +
						", <?php echo $langs->trans("MargeBasse"); ?> = " + Math.round(posbottom) + "mm");								
					$("#marge_gauche").val(Math.round(posleft));
					$("#marge_droite").val(Math.round(posright));
					$("#marge_haute").val(Math.round(postop));
					$("#marge_basse").val(Math.round(posbottom));
				},
			handles: "n, e, s, w" });
			var handles = $("#resizable-5").resizable("option", "handles");
			$("#resizable-5").resizable("option", "handles", "n, e, s, w");
			$("#marge_gauche").change(function() {
				var margeleft = parseInt($(this).val());
				var margecurrentleft = parseInt($('#resizable-5').css('left').replace('px',''));
				var margewidth = parseInt($('#resizable-5').css('width').replace('px',''));
				var blockwidth = (margecurrentleft + margewidth) - margeleft;
				$('#resizable-5').css({'left': margeleft + 'px', 'width': blockwidth + 'px'});
				$('#resizable-6').text("<?php echo $langs->trans("MargeGauche"); ?> = " + margeleft + 'px');
			});
			$("#marge_droite").change(function() {
				var margeright = parseInt($(this).val());
				var margecurrentright = parseInt($('#resizable-5').css('right').replace('px',''));
				var margewidth = parseInt($('#resizable-5').css('width').replace('px',''));
				var blockwidth = (margecurrentright + margewidth) - margeright;
				$('#resizable-5').css({'right': margeright + 'px', 'width': blockwidth + 'px'});
				$('#resizable-6').text("<?php echo $langs->trans("MargeDroite"); ?> = " + margeright + 'px');
			});
			$("#marge_haute").change(function() {
				var margetop = parseInt($(this).val());
				var margecurrenttop = parseInt($('#resizable-5').css('top').replace('px',''));
				var margeheight = parseInt($('#resizable-5').css('height').replace('px',''));
				var blockheight = (margecurrenttop + margeheight) - margetop;
				$('#resizable-5').css({'top': margetop + 'px', 'height': blockheight + 'px'});
				$('#resizable-6').text("<?php echo $langs->trans("MargeHaute"); ?> = " + margetop + 'px');
			});
			$("#marge_basse").change(function() {
				var margebottom = parseInt($(this).val());
				var margecurrentbottom = parseInt($('#resizable-5').css('bottom').replace('px',''));
				var margeheight = parseInt($('#resizable-5').css('height').replace('px',''));
				var blockheight = (margecurrentbottom + margeheight) - margebottom;
				$('#resizable-5').css({'bottom': margebottom + 'px', 'height': blockheight + 'px'});
				$('#resizable-6').text("<?php echo $langs->trans("MargeBasse"); ?> = " + margebottom + 'px');
			});
		});
	</script>
	<script>
        $(function() {
			
            $( "#resizable-7" ).resizable({ 
				containment: "#container4",
				minHeight: 297,
			    minWidth: 10,
				maxWidth: 80,
                resize: function (event, ui)
                {
					var widthref=ui.size.width;
					$("#resizable-8").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthref) + "px");
					$("#widthref").val(Math.round(widthref));
                }
			});				
            $("#widthref").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-7').css({'width': blockwidth + 'px'});
			$('#resizable-8').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});
		});
    </script>
	<script>
        $(function() {
		
			$( "#resizable-9" ).css({
				blockwidth: function(){return Math.round(<?php echo (empty($this->tpl['imglinesize'])?1:$this->tpl['imglinesize']); ?>);}
				});
            $( "#resizable-9" ).resizable({ 
				containment: "#container5",
				minHeight: 297,
			    minWidth: 16,
				maxWidth: 80,
                resize: function (event, ui)
                {
					var imglinesize=ui.size.width;
					$("#resizable-10").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(imglinesize) + "px");
					$("#imglinesize").val(Math.round(imglinesize));
                },
				handles: "w,sw" });
			var handles = $("#resizable-9").resizable("option", "handles");
			$("#resizable-9").resizable("option", "handles", "w,sw");
			$('.ui-resizable-sw').addClass('ui-icon ui-icon-gripsmall-diagonal-sw');
			$("#imglinesize").change(function() {
			var blockwidth = parseInt($(this).val());
			var blockwidthcurrent = parseInt($('#resizable-9').css('width').replace('px',''));
			var blockleftcurrent = parseInt($('#resizable-9').css('left').replace('px',''));
			var blockleft = blockleftcurrent + (blockwidthcurrent - blockwidth);
			$('#resizable-9').css({'width': blockwidth + 'px'});
			$('#resizable-9').css({'left': blockleft + 'px'});
			$('#resizable-10').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});
		});
    </script>
	<script>
        $(function() {
		
			$( "#resizable-11" ).css({
				blockheight: function(){return Math.round(<?php echo (empty($this->tpl['select_heightforfreetext'])?1:$this->tpl['select_heightforfreetext']); ?>);}
				});
            $( "#resizable-11" ).resizable({ 
				containment: "#container5",
			    minHeight: 10,
			    minWidth: 210,
				maxHeight: 80,
                resize: function (event, ui)
                {
					$("#resizable-12").text ("<?php echo $langs->trans("Height"); ?> = " + Math.round(ui.size.height) + "px");
					$("#heightforfreetext").val(Math.round(ui.size.height));
                },
				handles: "n" });
			var handles = $("#resizable-11").resizable("option", "handles");
			$("#resizable-11").resizable("option", "handles", "n"); 
			$("#heightforfreetext").change(function() {	
			var blockheight = parseInt($(this).val());
			var blockheightcurrent = parseInt($('#resizable-11').css('height').replace('px',''));
			var blocktopcurrent = parseInt($('#resizable-11').css('top').replace('px',''));
			var blocktop = blocktopcurrent + (blockheightcurrent - blockheight);
			$('#resizable-11').css({'height': blockheight + 'px'});
			$('#resizable-11').css({'top': blocktop + 'px'});
			$('#resizable-12').text("<?php echo $langs->trans("Height"); ?> = " + blockheight + 'px');
			});	
			$('#maj_freetext').click(function() {
			$('#freetextfontsize_text').css("font-size", $("#freetextfontsize").val() + "px");
			});
		});
    </script>
	<script>
        $(function() {
			
            $( "#resizable-13" ).resizable({ 
				containment: "#container3",
				minHeight: 297,
			    minWidth: 5,
				maxWidth: 15,
                resize: function (event, ui)
                {
					var widthnumbering=ui.size.width;
					$("#resizable-14").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthnumbering) + "px");
					$("#widthnumbering").val(Math.round(widthnumbering));
                }
			});				
            $("#widthnumbering").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-13').css({'width': blockwidth + 'px'});
			$('#resizable-14').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});
		});
    </script>
	<script>
        $(function() {
            $( "#resizable-15" ).resizable({ 
				containment: "#container6",
				minHeight: 297,
			    minWidth: 5,
				maxWidth: 20,
                resize: function (event, ui)
                {
					var widthvat=ui.size.width;
					$("#resizable-16").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthvat) + "px");
					$("#widthvat").val(Math.round(widthvat));
                }
			});	
			$("#widthvat").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-15').css({'width': blockwidth + 'px'});
			$('#resizable-16').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});			
        });
    </script>
	<script>
        $(function() {
            $( "#resizable-17" ).resizable({ 
				containment: "#container7",
				minHeight: 297,
			    minWidth: 20,
				maxWidth: 30,
                resize: function (event, ui)
                {
					var widthup=ui.size.width;
					$("#resizable-18").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthup) + "px");
					$("#widthup").val(Math.round(widthup));
                }
			});
			$("#widthup").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-17').css({'width': blockwidth + 'px'});
			$('#resizable-18').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});				
        });
    </script>
	<script>
        $(function() {
            $( "#resizable-19" ).resizable({ 
				containment: "#container8",
				minHeight: 297,
			    minWidth: 10,
				maxWidth: 30,
                resize: function (event, ui)
                {
					var widthqty=ui.size.width;
					$("#resizable-20").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthqty) + "px");
					$("#widthqty").val(Math.round(widthqty));
                }
			});
			$("#widthqty").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-19').css({'width': blockwidth + 'px'});
			$('#resizable-20').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});				
        });
    </script>
	<script>
        $(function() {
            $( "#resizable_unit" ).resizable({ 
				containment: "#container_unit",
				minHeight: 297,
			    minWidth: 10,
				maxWidth: 15,
                resize: function (event, ui)
                {
					var widthunit=ui.size.width;
					$("#resizable_unit2").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthunit) + "px");
					$("#widthunit").val(Math.round(widthunit));
                }
			});
			$("#widthunit").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable_unit').css({'width': blockwidth + 'px'});
			$('#resizable_unit2').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});				
        });
    </script>
	<script>
        $(function() {
            $( "#resizable-21" ).resizable({ 
				containment: "#container9",
				minHeight: 297,
			    minWidth: 10,
				maxWidth: 30,
                resize: function (event, ui)
                {
					var widthdiscount=ui.size.width;
					$("#resizable-22").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthdiscount) + "px");
					$("#widthdiscount").val(Math.round(widthdiscount));
                }
			});
			$("#widthdiscount").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-21').css({'width': blockwidth + 'px'});
			$('#resizable-22').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});				
        });
    </script>
	<script>
        $(function() {
           $("#sender_frame").resizable({
			   maxWidth: 120,
			   minWidth: 70
		   });
			$('#sender_frame').resize(function(event, ui){
				var widthrecbox=ui.size.width;
				$("#resizable-24").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthrecbox) + "px");
				$("#widthrecbox").val(Math.round(widthrecbox));
			    $('#recipient_frame').width($("#container_AddressesBlocks").width()-$("#sender_frame").width()); 
			});
			$(window).resize(function(){
			   $('#recipient_frame').width($("#container_AddressesBlocks").width()-$("#sender_frame").width()); 
			   $('#sender_frame').height($("#container_AddressesBlocks").height()); 
			});
			   
			$("#widthrecbox").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#sender_frame').css({'width': blockwidth + 'px'});
			$('#resizable-24').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});				
        });
    </script>
	<script>
        $(function() {
            $( "#resizable-25" ).resizable({ 
				containment: "#container10",
				minHeight: 297,
			    minWidth: 10,
				maxWidth: 30,
                resize: function (event, ui)
                {
					var widthdate=ui.size.width;
					$("#resizable-26").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthdate) + "px");
					$("#widthdate").val(Math.round(widthdate));
                }
			});
			$("#widthdate").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-25').css({'width': blockwidth + 'px'});
			$('#resizable-26').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});				
        });
    </script>
	<script>
        $(function() {
            $( "#resizable-27" ).resizable({ 
				containment: "#container11",
				minHeight: 297,
			    minWidth: 10,
				maxWidth: 30,
                resize: function (event, ui)
                {
					var widthproject=ui.size.width;
					$("#resizable-28").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthproject) + "px");
					$("#widthproject").val(Math.round(widthproject));
                }
			});
			$("#widthproject").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-27').css({'width': blockwidth + 'px'});
			$('#resizable-28').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});				
        });
    </script>
	<script>
        $(function() {
            $( "#resizable-29" ).resizable({ 
				containment: "#container12",
				minHeight: 297,
			    minWidth: 10,
				maxWidth: 30,
                resize: function (event, ui)
                {
					var widthtype=ui.size.width;
					$("#resizable-30").text ("<?php echo $langs->trans("Width"); ?> = " + Math.round(widthtype) + "px");
					$("#widthtype").val(Math.round(widthtype));
                }
			});
			$("#widthtype").change(function() {	
			var blockwidth = parseInt($(this).val());
			$('#resizable-29').css({'width': blockwidth + 'px'});
			$('#resizable-30').text("<?php echo $langs->trans("Width"); ?> = " + blockwidth + 'px');
			});				
        });
    </script>

</form>

<!-- END PHP TEMPLATE -->