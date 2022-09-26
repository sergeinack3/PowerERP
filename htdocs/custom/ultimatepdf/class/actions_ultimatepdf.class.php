<?php
/* Copyright (C) 2011-2019 Regis Houssin  <regis.houssin@capnetworks.com>
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
 */

/**
 *	\file       /ultimatepdf/class/actions_ultimatepdf.class.php
 *	\ingroup    ultimatepdf
 *	\brief      ultimatepdf designs actions class files
 */

dol_include_once('/ultimatepdf/class/dao_ultimatepdf.class.php','DaoUltimatepdf');
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/ajax.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

/**
 *	\class      ActionsUltimatepdf
 *	\brief      Ultimatepdf designs actions class files
 */
class ActionsUltimatepdf
{
	/**
     * @var DoliDb Database handler
     */
    public $db;

	/**
     * @var string instance of class
     */
    public $dao;

	/**
     * @var string instance of class
     */
    public $mesg;

	/**
	 * @var string[] Array of error strings
	 */
	public $errors = array();

	/**
	 * @var int Error number
	 */
	public $errno = 0;

	/**
	 * @var int The object identifier
	 */
	public $id;

	/**
	 * @var	mixed template_dir
	 */
	public $template_dir;

	/**
	 * @var mixed template
	 */
	public $template;

	/**
	 * @var	string label
	 */
	public $label;

	/**
	 * @var	string description
	 */
	public $description;

	/**
	 * @var mixed value
	 */
	public $value;

	/**
	 * @var mixed cancel
	 */
	public $cancel;

	/**
	 * @var
	 */
	public $dashdotted;

	/**
	 * @var
	 */
	public $bgcolor;

	/**
	 * @var
	 */
	public $opacity;

	/**
	 * @var
	 */
	public $roundradius;

	/**
	 * @var
	 */
	public $bordercolor;

	/**
	 * @var
	 */
	public $receiptstyle;

	/**
	 * @var
	 */
	public $senderstyle;

	/**
	 * @var
	 */
	public $textcolor;

	/**
	 * @var
	 */
	public $footertextcolor;

	/**
	 * @var
	 */
	public $qrcodecolor;

	/**
	 * @var
	 */
	public $widthnumbering;

	/**
	 * @var
	 */
	public $widthdate;

	/**
	 * @var
	 */
	public $widthtype;

	/**
	 * @var
	 */
	public $widthproject;

	/**
	 * @var
	 */
	public $widthvat;

	/**
	 * @var
	 */
	public $widthup;

	/**
	 * @var
	 */
	public $widthqty;

	/**
	 * @var
	 */
	public $widthunit;

	/**
	 * @var
	 */
	public $widthdiscount;

	/**
	 * @var
	 */
	public $withref;

	/**
	 * @var
	 */
	public $widthref;

	/**
	 * @var
	 */
	public $withoutvat;

	/**
	 * @var
	 */
	public $showdetails;

	/**
	 * @var
	 */
	public $otherlogo;

	/**
	 * @var
	 */
	public $newfont;

	/**
	 * @var
	 */
	public $otherfont;

	/**
	 * @var
	 */
	public $heightforfreetext;

	/**
	 * @var
	 */
	public $freetextfontsize;

	/**
	 * @var
	 */
	public $usebackground;

	/**
	 * @var
	 */
	public $imglinesize;

	/**
	 * @var
	 */
	public $logoheight;

	/**
	 * @var
	 */
	public $logowidth;

	/**
	 * @var
	 */
	public $otherlogoname;

	/**
	 * @var
	 */
	public $otherlogoheight;

	/**
	 * @var
	 */
	public $otherlogowidth;

	/**
	 * @var
	 */
	public $invertSenderRecipient;

	/**
	 * @var
	 */
	public $widthrecbox;

	/**
	 * @var
	 */
	public $marge_gauche;

	/**
	 * @var
	 */
	public $marge_droite;

	/**
	 * @var
	 */
	public $marge_haute;

	/**
	 * @var
	 */
	public $marge_basse;

	/**
	 * @var
	 */
	public $aliascompany;

	/**
	 * @var
	 */
	public $aliasaddress;

	/**
	 * @var
	 */
	public $aliaszip;

	/**
	 * @var
	 */
	public $aliastown;

	/**
	 * @var
	 */
	public $aliasphone;

	/**
	 * @var
	 */
	public $aliasfax;

	/**
	 * @var
	 */
	public $aliasemail;

	/**
	 * @var
	 */
	public $aliasurl;

	/**
	 * @var
	 */
	public $country_id;

	/**
	 * @var
	 */
	public $options=array();

	/**
	 * @var
	 */
	public $designs=array();

	/**
	 * @var
	 */
	public $tpl=array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;


	/**
	 * 	Constructor
	 *
	 * 	@param	DoliDB	$db		Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Instantiation of DAO class
	 *
	 * @return	void
	 */
	private function getInstanceDao()
	{
		if (! is_object($this->dao))
		{
			$this->dao = new DaoUltimatepdf($this->db);
		}
	}


	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 */
	
	public function doActions($parameters, &$object, &$action)
	{
		global $conf, $user, $langs, $hookmanager;

		// Translations
		$langs->loadLangs(array("companies", "dict", "ultimatepdf@ultimatepdf"));

		$error = 0; // Error counter

		$this->getInstanceDao();

		$id = GETPOST('id','int');
		$label = GETPOST('label','alpha');
		$description = GETPOST('description','alpha');
		$value = GETPOST('value','int');
		$cancel = GETPOST('cancel');
		$dashdotted = GETPOST('dashdotted');
		$bgcolor = GETPOST('bgcolor');
		$opacity = GETPOST('opacity');
		$roundradius = GETPOST('roundradius');
		$bordercolor = GETPOST('bordercolor');
		$senderstyle = GETPOST('senderstyle');
		$receiptstyle = GETPOST('receiptstyle');
		$textcolor = GETPOST('textcolor');
		$footertextcolor = GETPOST('footertextcolor');
		$qrcodecolor = GETPOST('qrcodecolor');
		$widthnumbering = GETPOST('widthnumbering');
		$widthdate = GETPOST('widthdate');
		$widthtype = GETPOST('widthtype');
		$widthproject = GETPOST('widthproject');
		$widthvat = GETPOST('widthvat');
		$widthup = GETPOST('widthup');
		$widthunit = GETPOST('widthunit');
		$widthqty = GETPOST('widthqty');
		$widthdiscount = GETPOST('widthdiscount');
		$withref = GETPOST('withref');
		$widthref = GETPOST('widthref');
		$withoutvat = GETPOST('withoutvat');
		$showdetails = GETPOST('showdetails');
		$otherlogo = GETPOST('otherlogo');
		$otherlogo_file = GETPOST('otherlogo_file');
		$otherlogoname = GETPOST('otherlogoname');
		$newfont = GETPOST('newfont');
		$otherfont = GETPOST('otherfont');
		$heightforfreetext = GETPOST('heightforfreetext');
		$freetextfontsize = GETPOST('freetextfontsize');
		$usebackground = GETPOST('usebackground');
		$imglinesize = GETPOST('imglinesize');
		$logoheight = GETPOST('logoheight');
		$logowidth = GETPOST('logowidth');
		$otherlogoheight = GETPOST('otherlogoheight');
		$otherlogowidth = GETPOST('otherlogowidth');
		$invertSenderRecipient = GETPOST('invertSenderRecipient');
		$widthrecbox = GETPOST('widthrecbox');
		$marge_gauche = GETPOST('marge_gauche');
		$marge_droite = GETPOST('marge_droite');
		$marge_haute = GETPOST('marge_haute');
		$marge_basse = GETPOST('marge_basse');
		$aliascompany = GETPOST('aliascompany');
		$aliasaddress = GETPOST('aliasaddress');
		$aliaszip = GETPOST('aliaszip');
		$aliastown = GETPOST('aliastown');
		$aliasphone = GETPOST('aliasphone');
		$aliasfax = GETPOST('aliasfax');
		$aliasemail = GETPOST('aliasemail');
		$aliasurl = GETPOST('aliasurl');
		$country_id = GETPOST('country_id', 'int');
		$urlfile = urldecode(GETPOST('urlfile', 'alpha'));
		$confirm = GETPOST('confirm', 'alpha');
		$type = GETPOST('type', 'alpha');
		
		// Action to merge documents on each element (invoices, proposal etc...)
		if ($action == 'filemerge' && is_object($object) && ! empty( $object->id ))
		{
			dol_include_once ( '/ultimatepdf/class/documentmergedpdf.class.php' );

			$filetomerge_file_array = GETPOST('filetoadd');

			//Delete all files already associated
			$filetomerge = new DocumentMergedPdf ( $this->db );

			$filetomerge->delete_by_element ($user);

			//for each file checked add it to the $object->element
			if (is_array($filetomerge_file_array))
			{
				foreach ($filetomerge_file_array as $filetomerge_file)
				{
					$filetomerge->fk_element = $object->id;
					$filetomerge->file_name = $filetomerge_file;
					$filetomerge->element_name = $object->element;
					$ret = $filetomerge->create ( $user );
					if ($ret == - 1) 
					{
						$langs->load("errors");
						setEventMessages($langs->trans("ErrorBadCreat"), null, 'errors');
					}
				}
			}
			return 0;
		}


		if (GETPOST('add') && empty($this->cancel) && $user->admin)
		{
			$error = 0;

			if (! $label)
			{
				$error++;
				array_push($this->errors, $langs->trans("ErrorFieldRequired", $langs->transnoentities("Label")));
				$action = 'create';
			}

			// Verify if label already exist in database
			if ($label)
			{
				$this->dao->getDesigns();
				if (! empty($this->dao->designs))
				{
					$label = strtolower(trim($label));

					foreach ($this->dao->designs as $design)
					{
						if (strtolower($design->label) == $label) $error++;
					}
					if ($error)
					{
						array_push($this->errors, $langs->trans("ErrorDesignLabelAlreadyExist"));
						$action = 'create';
					}
				}
			}

			if (! $error)
			{
				$this->db->begin();

				$this->dao->label = $label;
				$this->dao->description = $description;

				$this->dao->options['dashdotted'] = $dashdotted;
				$this->dao->options['bgcolor'] = $bgcolor;
				$this->dao->options['opacity'] = $opacity;
				$this->dao->options['roundradius'] = $roundradius;
				$this->dao->options['bordercolor'] = $bordercolor;
				$this->dao->options['senderstyle'] = $senderstyle;
				$this->dao->options['receiptstyle'] = $receiptstyle;
				$this->dao->options['textcolor'] = $textcolor;
				$this->dao->options['footertextcolor'] = $footertextcolor;
				$this->dao->options['qrcodecolor'] = $qrcodecolor;
				$this->dao->options['widthnumbering'] = $widthnumbering;
				$this->dao->options['widthdate'] = $widthdate;
				$this->dao->options['widthtype'] = $widthtype;
				$this->dao->options['widthproject'] = $widthproject;
				$this->dao->options['widthvat'] = $widthvat;
				$this->dao->options['widthup'] = $widthup;
				$this->dao->options['widthqty'] = $widthqty;
				$this->dao->options['widthunit'] = $widthunit;
				$this->dao->options['widthdiscount'] = $widthdiscount;
				$this->dao->options['withref'] = $withref;
				$this->dao->options['widthref'] = $widthref;
				$this->dao->options['withoutvat'] = $withoutvat;
				$this->dao->options['showdetails'] = $showdetails;
				$this->dao->options['otherlogo'] = $otherlogo;
				$this->dao->options['otherlogo_file'] = $otherlogo_file;
				$this->dao->options['otherlogoname'] = $otherlogoname;
				$this->dao->options['newfont'] = $newfont;
				$this->dao->options['otherfont'] = $otherfont;
				$this->dao->options['heightforfreetext'] = $heightforfreetext;
				$this->dao->options['freetextfontsize'] = $freetextfontsize;
				$this->dao->options['usebackground'] = $usebackground;
				$this->dao->options['imglinesize'] = $imglinesize;
				$this->dao->options['logoheight'] = $logoheight;
				$this->dao->options['logowidth'] = $logowidth;
				$this->dao->options['otherlogoheight'] = $otherlogoheight;
				$this->dao->options['otherlogowidth'] = $otherlogowidth;
				$this->dao->options['invertSenderRecipient'] = $invertSenderRecipient;
				$this->dao->options['widthrecbox'] = $widthrecbox;
				$this->dao->options['marge_gauche'] = $marge_gauche;
				$this->dao->options['marge_droite'] = $marge_droite;
				$this->dao->options['marge_haute'] = $marge_haute;
				$this->dao->options['marge_basse'] = $marge_basse; 
				$this->dao->options['aliascompany'] = $aliascompany;
				$this->dao->options['aliasaddress'] = $aliasaddress;
				$this->dao->options['aliaszip'] = $aliaszip;
				$this->dao->options['aliastown'] = $aliastown;
				$this->dao->options['aliasphone'] = $aliasphone;
				$this->dao->options['aliasfax'] = $aliasfax;
				$this->dao->options['aliasemail'] = $aliasemail;
				$this->dao->options['aliasurl'] = $aliasurl;
				$this->dao->options['country_id'] = $country_id;

				$id = $this->dao->create($user);
				if ($id <= 0)
				{
					$error++;
					$errors = ($this->dao->error ? array($this->dao->error) : $this->dao->errors);
					$action = 'create';
				}

				if (! $error && $id > 0)
				{
					$this->db->commit();
				}
				else
				{
					$this->db->rollback();
				}
			}
		}

		if ($action == 'edit' && $user->admin)
		{
			$error = 0;

			if ($this->dao->fetch($id) < 0)
			{
				$error++;
				//array_push($this->errors, $langs->trans("ErrorDesignIsNotValid"));
				$_GET["action"] = $_POST["action"] = '';
			}
		}

		if (GETPOST('update') && $id && $user->admin)
		{
			$error = 0;

			$ret = $this->dao->fetch($id);
			if ($ret < 0)
			{
				$error++;
				array_push($this->errors, $langs->trans("ErrorDesignIsNotValid"));
				$action = '';
			}
			else if (! $label)
			{
				$error++;
				array_push($this->errors, $langs->trans("ErrorFieldRequired", $langs->transnoentities("Label")));
				$action = 'edit';
			}

			if (! $error)
			{
				$this->db->begin();

				$this->dao->label = $label;
				$this->dao->description	= $description;

				$this->dao->options['dashdotted'] = (GETPOST('dashdotted') ? GETPOST('dashdotted') : null);
				$this->dao->options['bgcolor'] = (GETPOST('bgcolor') ? GETPOST('bgcolor') : null);
				$this->dao->options['opacity'] = (GETPOST('opacity') ? GETPOST('opacity') : null);
				$this->dao->options['roundradius'] = (GETPOST('roundradius') ? GETPOST('roundradius') : null);
				$this->dao->options['bordercolor'] = (GETPOST('bordercolor') ? GETPOST('bordercolor') : null);
				$this->dao->options['senderstyle'] = (GETPOST('senderstyle') ? GETPOST('senderstyle') : null);
				$this->dao->options['receiptstyle'] = (GETPOST('receiptstyle') ? GETPOST('receiptstyle') : null);
				$this->dao->options['textcolor'] = (GETPOST('textcolor') ? GETPOST('textcolor') : null);
				$this->dao->options['footertextcolor'] = (GETPOST('footertextcolor') ? GETPOST('footertextcolor') : null);
				$this->dao->options['qrcodecolor'] = (GETPOST('qrcodecolor') ? GETPOST('qrcodecolor') : null);
				$this->dao->options['widthnumbering'] = (GETPOST('widthnumbering') ? GETPOST('widthnumbering') : null);
				$this->dao->options['widthdate'] = (GETPOST('widthdate') ? GETPOST('widthdate') : null);
				$this->dao->options['widthtype'] = (GETPOST('widthtype') ? GETPOST('widthtype') : null);
				$this->dao->options['widthproject'] = (GETPOST('widthproject') ? GETPOST('widthproject') : null);
				$this->dao->options['widthvat'] = (GETPOST('widthvat') ? GETPOST('widthvat') : null);
				$this->dao->options['widthup'] = (GETPOST('widthup') ? GETPOST('widthup') : null);
				$this->dao->options['widthqty'] = (GETPOST('widthqty') ? GETPOST('widthqty') : null);
				$this->dao->options['widthunit'] = (GETPOST('widthunit') ? GETPOST('widthunit') : null);
				$this->dao->options['widthdiscount'] = (GETPOST('widthdiscount') ? GETPOST('widthdiscount') : null);
				$this->dao->options['withref'] = (GETPOST('withref') ? GETPOST('withref') : 'no');
				$this->dao->options['widthref'] = (GETPOST('widthref') ? GETPOST('widthref') : null);
				$this->dao->options['withoutvat'] = (GETPOST('withoutvat') ? GETPOST('withoutvat') : 'no');
				$this->dao->options['showdetails'] = (GETPOST('showdetails') ? GETPOST('showdetails') : null);
				$this->dao->options['otherlogo'] = (GETPOST('otherlogo') ? GETPOST('otherlogo') : null);
				$this->dao->options['otherlogo_file'] = (GETPOST('otherlogo_file') ? GETPOST('otherlogo_file') : null);
				$this->dao->options['otherlogoname'] = (GETPOST('otherlogoname') ? GETPOST('otherlogoname') : null);
				$this->dao->options['newfont'] = (GETPOST('newfont') ? GETPOST('newfont') : null);
				$this->dao->options['otherfont'] = (GETPOST('otherfont') ? GETPOST('otherfont') : null);
				$this->dao->options['heightforfreetext'] = (GETPOST('heightforfreetext') ? GETPOST('heightforfreetext') : null);
				$this->dao->options['freetextfontsize'] = (GETPOST('freetextfontsize') ? GETPOST('freetextfontsize') : null);
				$this->dao->options['usebackground'] = (GETPOST('usebackground') ? GETPOST('usebackground') : null);
				$this->dao->options['imglinesize'] = (GETPOST('imglinesize') ? GETPOST('imglinesize') : null);
				$this->dao->options['logoheight'] = (GETPOST('logoheight') ? GETPOST('logoheight') : null);
				$this->dao->options['logowidth'] = (GETPOST('logowidth') ? GETPOST('logowidth') : null);
				$this->dao->options['otherlogoheight'] = (GETPOST('otherlogoheight') ? GETPOST('otherlogoheight') : null);
				$this->dao->options['otherlogowidth'] = (GETPOST('otherlogowidth') ? GETPOST('otherlogowidth') : null);
				$this->dao->options['invertSenderRecipient'] = (GETPOST('invertSenderRecipient') ? GETPOST('invertSenderRecipient') : 'no');
				$this->dao->options['widthrecbox'] = (GETPOST('widthrecbox') ? GETPOST('widthrecbox') : null);
				$this->dao->options['marge_gauche'] = (GETPOST('marge_gauche') ? GETPOST('marge_gauche') : null);
				$this->dao->options['marge_droite'] = (GETPOST('marge_droite') ? GETPOST('marge_droite') : null);
				$this->dao->options['marge_haute'] = (GETPOST('marge_haute') ? GETPOST('marge_haute') : null);
				$this->dao->options['marge_basse'] = (GETPOST('marge_basse') ? GETPOST('marge_basse') : null);
				$this->dao->options['aliascompany'] = (GETPOST('aliascompany') ? GETPOST('aliascompany') : null);
				$this->dao->options['aliasaddress'] = (GETPOST('aliasaddress') ? GETPOST('aliasaddress') : null);
				$this->dao->options['aliaszip'] = (GETPOST('aliaszip') ? GETPOST('aliaszip') : null);
				$this->dao->options['aliastown'] = (GETPOST('aliastown') ? GETPOST('aliastown') : null);
				$this->dao->options['aliasphone'] = (GETPOST('aliasphone') ? GETPOST('aliasphone') : null);
				$this->dao->options['aliasfax'] = (GETPOST('aliasfax') ? GETPOST('aliasfax') : null);
				$this->dao->options['aliasemail'] = (GETPOST('aliasemail') ? GETPOST('aliasemail') : null);
				$this->dao->options['aliasurl'] = (GETPOST('aliasurl') ? GETPOST('aliasurl') : null);
				$this->dao->options['country_id'] = (GETPOST('country_id') ? GETPOST('country_id') : null);

				$ret = $this->dao->update($id, $user);

				if ($ret <= 0)
				{
					$error++;
					$errors = ($this->dao->error ? array($this->dao->error) : $this->dao->errors);
					$action = 'edit';
				}

				if (! $error && $ret > 0)
				{
					powererp_set_const($this->db, "ULTIMATE_DASH_DOTTED", $dashdotted, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_BGCOLOR_COLOR", $bgcolor, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_BGCOLOR_OPACITY", $opacity, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_SET_RADIUS", $roundradius, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_BORDERCOLOR_COLOR", $bordercolor, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_SENDER_STYLE", $senderstyle, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_RECEIPT_STYLE", $receiptstyle, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_TEXTCOLOR_COLOR", $textcolor, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_FOOTERTEXTCOLOR_COLOR", $footertextcolor, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_QRCODECOLOR_COLOR", $qrcodecolor, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_NUMBERING_WIDTH", $widthnumbering, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_DATE_WIDTH", $widthdate, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_TYPE_WIDTH", $widthtype, 'chaine', 0,'', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_PROJECT_WIDTH", $widthproject, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_TVA_WIDTH", $widthvat, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_UP_WIDTH", $widthup, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_QTY_WIDTH", $widthqty, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_UNIT_WIDTH", $widthunit, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_DISCOUNT_WIDTH", $widthdiscount, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_REF", $withref, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_REF_WIDTH", $widthref, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_GENERATE_DOCUMENTS_WITHOUT_VAT", $withoutvat, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS", $showdetails, 'chaine', 0, '', $conf->entity);

					$id = $this->dao->id; //to get id
					$dirforimage = $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/';
					$arrayofimages = array('otherlogo', 'otherlogo_squarred');					
					foreach ($arrayofimages as $varforimage)
					{
						if ($_FILES[$varforimage]["tmp_name"])
						{
							$reg = array();
							if (preg_match('/([^\\/:]+)$/i', $_FILES[$varforimage]["name"], $reg))
							{
								$otherlogo = $reg[1];								
								$extension = pathinfo($_FILES[$varforimage]['name'], PATHINFO_EXTENSION);
								if (GETPOST('name', 'alpha')) $otherlogoname = GETPOST('name', 'alpha') . '.' . $extension;
								$otherlogo = $otherlogoname ? $otherlogoname : $otherlogo;
								$isimage = image_format_supported($otherlogo);
								if ($isimage >= 0)
								{
									dol_syslog("Move file ".$_FILES[$varforimage]["tmp_name"]." to ".$dirforimage.$otherlogo);
									if (!is_dir($dirforimage))
									{
										dol_mkdir($dirforimage);
									}
									$result = dol_move_uploaded_file($_FILES[$varforimage]["tmp_name"], $dirforimage.$otherlogo, 1, 0, $_FILES[$varforimage]['error']);
									
									if ($result > 0)
									{
										$constant = "ULTIMATE_OTHERLOGO";
										if ($varforimage == 'otherlogo_squarred') $constant = "ULTIMATE_OTHERLOGO_SQUARRED";

										powererp_set_const($this->db, $constant, $otherlogo, 'chaine', 0, '', $conf->entity);
										$this->dao->options['otherlogo'] = $otherlogo;
										$ret = $this->dao->update($id, $user);
										if ($ret <= 0)
										{
											$error++;
											$errors = ($this->dao->error ? array($this->dao->error) : $this->dao->errors);
											$action = 'edit';
										}
										
										// Create thumbs of logo (Note that PDF use original file and not thumbs)
										if ($isimage > 0)
										{
											// Create thumbs
											//$object->addThumbs($newfile);    // We can't use addThumbs here yet because we need name of generated thumbs to add them into constants. TODO Check if need such constants. We should be able to retreive value with get...

											// Create small thumb, Used on logon for example
											$imgThumbSmall = vignette($dirforimage.$otherlogo, 160, 120, '_small', 50);
											if (image_format_supported($imgThumbSmall) >= 0 && preg_match('/([^\\/:]+)$/i', $imgThumbSmall, $reg))
											{
												$imgThumbSmall = $reg[1]; // Save only basename
												powererp_set_const($this->db, $constant."_SMALL", $imgThumbSmall, 'chaine', 0, '', $conf->entity);
											}
											else dol_syslog($imgThumbSmall);
										}
										else dol_syslog("ErrorImageFormatNotSupported", LOG_WARNING);
									} 
									elseif (preg_match('/^ErrorFileIsInfectedWithAVirus/', $result)) 
									{
										$error++;
										$langs->load("errors");
										$tmparray = explode(':', $result);
										setEventMessages($langs->trans('ErrorFileIsInfectedWithAVirus', $tmparray[1]), null, 'errors');
									}
									else
									{
										$error++;
										setEventMessages($langs->trans("ErrorFailedToSaveFile"), null, 'errors');
									}
								}
								else
								{
									$error++;
									$langs->load("errors");
									setEventMessages($langs->trans("ErrorBadImageFormat"), null, 'errors');
								}
							}
						}
					}

					powererp_set_const($this->db, "ULTIMATE_OTHERLOGO_FILE", $otherlogo_file, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_OTHERLOGO_NAME", $otherlogoname, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATEPDF_NEW_FONT", $newfont, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "MAIN_PDF_FORCE_FONT", $otherfont, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "MAIN_PDF_FREETEXT_HEIGHT", $heightforfreetext, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATEPDF_FREETEXT_FONT_SIZE", $freetextfontsize, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "MAIN_USE_BACKGROUND_ON_PDF", $usebackground, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "MAIN_DOCUMENTS_WITH_PICTURE_WIDTH", $imglinesize, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_LOGO_HEIGHT", $logoheight, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_LOGO_WIDTH", $logowidth, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_OTHERLOGO_HEIGHT", $otherlogoheight, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_OTHERLOGO_WIDTH", $otherlogowidth, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_INVERT_SENDER_RECIPIENT", $invertSenderRecipient, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_WIDTH_RECBOX", $widthrecbox, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_MARGIN_LEFT", $marge_gauche, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_MARGIN_RIGHT", $marge_droite, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_MARGIN_TOP", $marge_haute, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_MARGIN_BOTTOM", $marge_basse, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_COMPANY", $aliascompany, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_ADDRESS_EMETTEUR", $aliasaddress, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_ZIP_EMETTEUR", $aliaszip, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_TOWN_EMETTEUR", $aliastown, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_PHONE_EMETTEUR", $aliasphone, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_FAX_EMETTEUR", $aliasfax, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_EMAIL_EMETTEUR", $aliasemail, 'chaine', 0, '', $conf->entity);

					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_URL_EMETTEUR", $aliasurl, 'chaine', 0, '', $conf->entity);

					$country_id = GETPOST('country_id', 'int');
					$tmparray = getCountry($country_id, 'all');
					$country_code	= $tmparray['code'];
					$country_label	= $tmparray['label'];
	
					powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_COUNTRY_EMETTEUR", $country_id.':'.$country_code.':'.$country_label, 'chaine', 0, '', $conf->entity);
					$this->dao->options['country_id'] = $country_id.':'.$country_code.':'.$country_label;

					$ret = $this->dao->update($id, $user);
					if ($ret <= 0)
					{
						$error++;
						$errors = ($this->dao->error ? array($this->dao->error) : $this->dao->errors);
						$action = 'edit';
					}
					$this->db->commit();
				}
				else
				{
					$this->db->rollback();
				}
			}
		}

		if ($action == 'delete' && $type == "otherlogo")
		{
			$id = $this->dao->id; //to get id
			$form = new Form($this->db);
			$formconfirm = $form->formconfirm('?urlfile='.$urlfile, $langs->trans("DeleteFile"), $langs->trans("ConfirmDeleteFile").' '.$urlfile.' ?', 'delete_ok', '', 1, (int) $conf->use_javascript_ajax);
			print $formconfirm;
		}

		if ($action === 'delete_ok' && $confirm === 'yes') 
		{
			$id = $this->dao->id; //to get id
			$urlfile_dirname = pathinfo($urlfile, PATHINFO_DIRNAME);
			$urlfile_filename = pathinfo($urlfile, PATHINFO_FILENAME);
			$urlfile_ext = pathinfo($urlfile, PATHINFO_EXTENSION);
			$urlfile = $urlfile_filename.'.'.$urlfile_ext;			
			$urlfile_small = $urlfile_filename . '_small.' . $urlfile_ext;
			if (dol_delete_file($conf->ultimatepdf->dir_output.$urlfile_dirname.'/'.$urlfile, 1) && dol_delete_file($conf->ultimatepdf->dir_output.$urlfile_dirname.'/thumbs/'.$urlfile_small, 1)) 
			{
				$mesg = pathinfo($urlfile, PATHINFO_FILENAME).' '.$langs->trans("Deleted");
				setEventMessages($mesg, null, 'mesgs');
			} 
			else 
			{
				$mesg = $langs->trans("ErrorFailToDeleteFile",  $urlfile_filename);
				setEventMessages($mesg, null, 'errors');
			}
		}

		if ($action == 'confirm_delete_design' && GETPOST('confirm') == 'yes' && $user->admin)
		{
			$error=0;

			if ($id == 1)
			{
				$error++;
				array_push($this->errors, $langs->trans("ErrorNotDeleteMasterDesign") );
				$action = '';
			}

			if (! $error)
			{
				if ($this->dao->fetch($id) > 0)
				{
					if ($this->dao->delete($id) > 0)
					{
						$this->mesg = $langs->trans('ConfirmedDesignDeleted');
					}
					else
					{
						$this->error = $this->dao->error;
						$action = '';
					}
				}
			}
		}

		if ($action == 'setactive' && $user->admin)
		{
			$this->dao->setDesign($id, 'active', $value);
		}
	}
	

	/**
	 *	Return combo list of designs.
	 *
	 *	@param	int		$selected	Preselected design
	 *	@param	int		$htmlname	Name
	 *	@param	string	$option		Option
	 *	@param	int		$login		If use in login page or not
	 *	@return	string
	 */
	function select_designs($selected='', $htmlname='design', $option='')
	{
		global $user, $langs;

		$this->getInstanceDao();

		$this->dao->getDesigns();

		$out = '';

		if (is_array($this->dao->designs))
		{
			$out = '<select class="flat maxwidth200onsmartphone" id="'.$htmlname.'" name="'.$htmlname.'"'.$option.'>';

			foreach ($this->dao->designs as $design)
			{
				if ($design->active == 1)
				{
					$out.= '<option value="'.$design->id.'" ';
					if ($selected == $design->id) {
						$out.= 'selected="selected"';
					}
					$out.= '>';
					$out.= $design->label;
					$out.= '</option>';
				}
			}

			$out.= '</select>';
		}
		else {
			$out.= $langs->trans('NoDesignAvailable');
		}

		// Make select dynamic
		include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
		$out.= ajax_combobox($htmlname);

		return $out;
	}

	/**
	 *    Switch to another design.
	 *    @param	id		Id of the destination design
	 */
	function switchDesign($id)
	{
		global $conf,$user;

		$this->getInstanceDao();

		if ($this->dao->fetch($id) > 0)
		{
			// Controle des droits sur le changement
			if($this->dao->verifyRight($id,$user->id) || $user->admin || $user->rights->ultimatepdf->write)
			{
				powererp_set_const($this->db, "ULTIMATE_DESIGN", $id,'chaine',0,'',$conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DASH_DOTTED", $this->dao->options['dashdotted'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_BGCOLOR_COLOR", $this->dao->options['bgcolor'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_BGCOLOR_OPACITY", $this->dao->options['opacity'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_SET_RADIUS", $this->dao->options['roundradius'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_BORDERCOLOR_COLOR", $this->dao->options['bordercolor'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_SENDER_STYLE", $this->dao->options['senderstyle'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_RECEIPT_STYLE", $this->dao->options['receiptstyle'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_TEXTCOLOR_COLOR", $this->dao->options['textcolor'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_FOOTERTEXTCOLOR_COLOR", $this->dao->options['footertextcolor'],'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_QRCODECOLOR_COLOR", $this->dao->options['qrcodecolor'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_NUMBERING_WIDTH", $this->dao->options['widthnumbering'],'chaine',0,'',$conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_DATE_WIDTH", $this->dao->options['widthdate'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_TYPE_WIDTH", $this->dao->options['widthtype'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_PROJECT_WIDTH", $this->dao->options['widthproject'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_TVA_WIDTH", $this->dao->options['widthvat'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_UP_WIDTH", $this->dao->options['widthup'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_QTY_WIDTH", $this->dao->options['widthqty'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_UNIT_WIDTH", $this->dao->options['widthunit'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_DISCOUNT_WIDTH", $this->dao->options['widthdiscount'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_REF", $this->dao->options['withref'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_DOCUMENTS_WITH_REF_WIDTH", $this->dao->options['widthref'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_GENERATE_DOCUMENTS_WITHOUT_VAT", $this->dao->options['withoutvat'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS", $this->dao->options['showdetails'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_OTHERLOGO", $this->dao->options['otherlogo'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_OTHERLOGO_FILE", $this->dao->options['otherlogo_file'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_OTHERLOGO_NAME", $this->dao->options['otherlogoname'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATEPDF_NEW_FONT", $this->dao->options['newfont'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "MAIN_PDF_FORCE_FONT", $this->dao->options['otherfont'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "MAIN_PDF_FREETEXT_HEIGHT", $this->dao->options['heightforfreetext'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATEPDF_FREETEXT_FONT_SIZE", $this->dao->options['freetextfontsize'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "MAIN_USE_BACKGROUND_ON_PDF", $this->dao->options['usebackground'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "MAIN_DOCUMENTS_WITH_PICTURE_WIDTH", $this->dao->options['imglinesize'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_LOGO_HEIGHT", $this->dao->options['logoheight'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_LOGO_WIDTH", $this->dao->options['logowidth'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_OTHERLOGO_HEIGHT", $this->dao->options['otherlogoheight'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_OTHERLOGO_WIDTH", $this->dao->options['otherlogowidth'],'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_INVERT_SENDER_RECIPIENT", $this->dao->options['invertSenderRecipient'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_MARGIN_LEFT", $this->dao->options['marge_gauche'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_MARGIN_RIGHT", $this->dao->options['marge_droite'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_MARGIN_TOP", $this->dao->options['marge_haute'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_MARGIN_BOTTOM", $this->dao->options['marge_basse'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_COMPANY", $this->dao->options['aliascompany'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_ADDRESS_EMETTEUR", $this->dao->options['aliasaddress'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_ZIP_EMETTEUR", $this->dao->options['aliaszip'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_TOWN_EMETTEUR", $this->dao->options['aliastown'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_PHONE_EMETTEUR", $this->dao->options['aliasphone'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_FAX_EMETTEUR", $this->dao->options['aliasfax'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_EMAIL_EMETTEUR", $this->dao->options['aliasemail'], 'chaine', 0, '', $conf->entity);
				
				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_URL_EMETTEUR", $this->dao->options['aliasurl'], 'chaine', 0, '', $conf->entity);

				powererp_set_const($this->db, "ULTIMATE_PDF_ALIAS_COUNTRY_EMETTEUR", $this->dao->options['country_id'], 'chaine', 0, '', $conf->entity);

				return 1;
			}
			else
			{
				return -2;
			}
		}
		else
		{
			return -1;
		}
	}

	/**
	 * 	Get design info
	 * 	@param	id	Object id
	 */
	function getInfo($id)
	{
		$this->getInstanceDao();
		$this->dao->fetch($id);

		$this->id			= $this->dao->id;
		$this->label		= $this->dao->label;
		$this->description	= $this->dao->description;
		$this->active		= $this->dao->active;
	}

	/**
	 * 	Get action title
	 * 	@param	action	Type of action
	 */
	function getTitle($action='')
	{
		global $langs;

		if ($action == 'create') return $langs->trans("AddDesign");
		else if ($action == 'edit') return $langs->trans("EditDesign");
		else return $langs->trans("DesignsManagement");
	}

	/**
	 *    Assigne les valeurs pour les templates
	 *    @param      action     Type of action
	 */
	function assign_values(&$action = 'view')
	{
		global $conf, $langs, $user;
		global $form, $formother, $formadmin;

		$this->getInstanceDao();

		$this->template_dir = dol_buildpath('/ultimatepdf/tpl/');

		if ($action == 'create')
		{
			$this->template = 'ultimatepdf_create.tpl.php';
		}
		else if ($action == 'edit')
		{
			$this->template = 'ultimatepdf_edit.tpl.php';

			if (!empty($id)) $ret = $this->dao->fetch($id);
		}

		if ($action == 'create' || $action == 'edit')
		{
			// Label
			$this->tpl['label'] = ($label?$label:$this->dao->label);

			// Description
			$this->tpl['description'] = ($description?$description:$this->dao->description);

			// Dash dotted
			$ddvalue = array('0' => $langs->trans('ContinuousLine'), '8, 2' => $langs->trans('DottedLine'));
			$this->tpl['select_dashdotted'] = $form->selectarray( 'dashdotted', $ddvalue, ($dashdotted?$dashdotted:$this->dao->options['dashdotted']));

			// Bgcolor
			$this->tpl['select_bgcolor'] = $formother->selectColor(($bgcolor?$bgcolor:$this->dao->options['bgcolor']), 'bgcolor', '', 1);

			// Bgcolor opacity
			$this->tpl['select_opacity'] = ($opacity?$opacity:$this->dao->options['opacity']);

			// Set roundradius
			$this->tpl['select_roundradius'] = ($roundradius?$roundradius:$this->dao->options['roundradius']);

			// Bordercolor
			$this->tpl['select_bordercolor'] = $formother->selectColor(($bordercolor?$bordercolor:$this->dao->options['bordercolor']), 'bordercolor', '', 1);

			// Senderstyle
			$stylevalue = array('S' => $langs->trans('WhiteBackground'), 'FD' => $langs->trans('ColoredBackground'));
			$this->tpl['select_senderstyle'] = $form->selectarray('senderstyle', $stylevalue, ($senderstyle?$senderstyle:$this->dao->options['senderstyle']));

			// receiptstyle
			$receiptstylevalue = array('S' => $langs->trans('WhiteBackground'), 'FD' => $langs->trans('ColoredBackground'));
			$this->tpl['select_receiptstyle'] = $form->selectarray('receiptstyle',$receiptstylevalue,($receiptstyle?$receiptstyle:$this->dao->options['receiptstyle']));

			// Textcolor
			$this->tpl['select_textcolor'] = $formother->selectColor(($textcolor?$textcolor:$this->dao->options['textcolor']), 'textcolor', '', 1);

			// FooterTextcolor
			$this->tpl['select_footertextcolor'] = $formother->selectColor(($footertextcolor?$footertextcolor:$this->dao->options['footertextcolor']), 'footertextcolor', '', 1);

			// QRcodecolor
			$this->tpl['select_qrcodecolor'] = $formother->selectColor(($qrcodecolor?$qrcodecolor:$this->dao->options['qrcodecolor']), 'qrcodecolor', '', 1);

			// widthnumbering
			$this->tpl['widthnumbering'] = ($widthnumbering?$widthnumbering:$this->dao->options['widthnumbering']);

			// widthdate
			$this->tpl['widthdate'] = ($widthdate?$widthdate:$this->dao->options['widthdate']);

			// widthtype
			$this->tpl['widthtype'] = ($widthtype?$widthtype:$this->dao->options['widthtype']);

			// widthproject
			$this->tpl['widthproject'] = ($widthproject?$widthproject:$this->dao->options['widthproject']);

			// widthvat
			$this->tpl['widthvat'] = ($widthvat?$widthvat:$this->dao->options['widthvat']);

			// widthup
			$this->tpl['widthup'] = ($widthup?$widthup:$this->dao->options['widthup']);

			// widthqty
			$this->tpl['widthqty'] = ($widthqty?$widthqty:$this->dao->options['widthqty']);

			// widthunit
			$this->tpl['widthunit'] = ($widthunit?$widthunit:$this->dao->options['widthunit']);

			// widthdiscount
			$this->tpl['widthdiscount'] = ($widthdiscount?$widthdiscount:$this->dao->options['widthdiscount']);

			// withref
			$this->tpl['select_withref'] = $form->selectyesno('withref',($withref?$withref:$this->dao->options['withref']), 0, false);

			// Ref width
			$this->tpl['select_widthref'] = ($widthref?$widthref:$this->dao->options['widthref']);

			// withoutvat
			$this->tpl['select_withoutvat'] = $form->selectyesno('withoutvat',($withoutvat?$withoutvat:$this->dao->options['withoutvat']), 0, false);

			// showdetails
			$arraydetailsforpdffoot = array(
				0 => $langs->trans('NoDetails'),
				1 => $langs->trans('DisplayCompanyInfo'),
				2 => $langs->trans('DisplayManagersInfo'),
				3 => $langs->trans('DisplayCompanyInfoAndManagers')
			);
			$this->tpl['select_showdetails'] = $form->selectarray('showdetails', $arraydetailsforpdffoot, ($showdetails?$showdetails:$this->dao->options['showdetails']));

			// Set Otherlogo
			//$conf->global->ULTIMATE_OTHERLOGO = 0;			
			if (! empty($conf->global->ULTIMATE_OTHERLOGO))
			{
				$other_file = urlencode('/otherlogo/'.$conf->global->ULTIMATE_OTHERLOGO);
				$otherlogo = DOL_URL_ROOT . '/viewimage.php?modulepart=ultimatepdf&amp;file='.$other_file ;
			}
			$this->tpl['select_otherlogo'] = ($otherlogo ? $otherlogo : $this->dao->options['otherlogo']);

			// Set otherlogo file
			$this->tpl['select_otherlogo_file'] = ($other_file ? $other_file : $this->dao->options['otherlogo_file']);
			
			// Set otherlogo name
			$this->tpl['otherlogoname'] = ($otherlogoname ? $otherlogoname : $this->dao->options['otherlogoname']);

			// NewFont
			if (! empty($conf->global->ULTIMATEPDF_NEW_FONT))
			{
				$extension = pathinfo($_FILES['fontfile']['name'], PATHINFO_EXTENSION);

				if ($extension == 'ttf' || $extension == 'TTF')
				{
					$srcfile	= dol_buildpath('ultimatepdf', 0).'/newfont/';
					$fontfile	= $_FILES['fontfile']['tmp_name'];
					$finalfile	= $_FILES['fontfile']['name'];
					$targetpath = DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/fonts/';
					$newfont = addTTFfont ('TrueTypeUnicode', '', 32, $targetpath, 3, 1, true, false, $srcfile.$finalfile);
					dolCopyDir($targetpath, DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/fonts/', 0, 1);
					$moved		= dol_move_uploaded_file($fontfile, $srcfile.$finalfile, 1, 0, $_FILES['fontfile']['error']);
					if ($moved > 0)
					{
						$targetpath = DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/fonts/';
						$newfont = addTTFfont ('TrueTypeUnicode', '', 32, $targetpath, 3, 1, true, false, $srcfile.$finalfile);
						if ($newfont === false)	setEventMessages($langs->trans("ParamAddFontKo", $newfont), null, 'errors');
						else
						{
							dolCopyDir($targetpath, DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/fonts/', 0, 1);
							dolCopyDir($targetpath, TCPDF_PATH.'fonts', 0, 1);
							array_map('unlink', glob($targetpath.'*'));
							setEventMessages($langs->trans("ParamAddFontOk", $newfont), null, 'mesgs');
						}
					}
				}
				else	setEventMessages($langs->trans("ParamAddTTFKo", $_FILES['fontfile']['name']), null, 'errors');
			}
			$this->tpl['select_newfont'] = ($newfont?$newfont:$this->dao->options['newfont']);

			// Other font
			$dirfonts	= DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/fonts/';
			$listfonts	= dol_dir_list($dirfonts, 'files');
			$fontvalue	= array();
			foreach ($listfonts as $font)
			{
				$extension	= pathinfo($font['name'], PATHINFO_EXTENSION);
				if ($extension == 'php')
				{
					$fontname = pathinfo($font['name'], PATHINFO_FILENAME);

					include_once($font['fullname']);
					if ($name != '')
					{
						$fontvalue[$fontname] = $name;
					}
				}
			}
			$this->tpl['select_otherfont'] = $form->selectarray('otherfont', $fontvalue, ($this->dao->options['otherfont']?$this->dao->options['otherfont']:'helvetica'));

			// heightforfreetext
			$this->tpl['select_heightforfreetext'] = ($heightforfreetext?$heightforfreetext:$this->dao->options['heightforfreetext']);

			// freetextfontsize
			$this->tpl['select_freetextfontsize'] = ($freetextfontsize?$freetextfontsize:$this->dao->options['freetextfontsize']);

			// Use background on pdf
			$this->tpl['usebackground'] = ($usebackground?$usebackground:$this->dao->options['usebackground']);

			// Set image width
			$this->tpl['imglinesize'] = ($imglinesize?$imglinesize:$this->dao->options['imglinesize']);

			// Set logo height
			$this->tpl['logoheight'] = ($logoheight?$logoheight:$this->dao->options['logoheight']);

			// Set logo width
			$this->tpl['logowidth'] = ($logowidth?$logowidth:$this->dao->options['logowidth']);

			// Set otherlogo height
			$this->tpl['otherlogoheight'] = ($otherlogoheight?$otherlogoheight:$this->dao->options['otherlogoheight']);

			// Set otherlogo width
			$this->tpl['otherlogowidth'] = ($otherlogowidth?$otherlogowidth:$this->dao->options['otherlogowidth']);

			// Invert sender and recipient
			$this->tpl['invertSenderRecipient'] = $form->selectyesno('invertSenderRecipient', ($invertSenderRecipient?$invertSenderRecipient:$this->dao->options['invertSenderRecipient']), 0, false);

			// Set widthrecbox
			$this->tpl['widthrecbox'] = ($widthrecbox?$widthrecbox:$this->dao->options['widthrecbox']);

			// Set marge_gauche
			$this->tpl['marge_gauche'] = ($marge_gauche?$marge_gauche:$this->dao->options['marge_gauche']);

			// Set marge_droite
			$this->tpl['marge_droite'] = ($marge_droite?$marge_droite:$this->dao->options['marge_droite']);

			// Set marge_haute
			$this->tpl['marge_haute'] = ($marge_haute?$marge_haute:$this->dao->options['marge_haute']);

			// Set marge_basse
			$this->tpl['marge_basse'] = ($marge_basse?$marge_basse:$this->dao->options['marge_basse']);

			// Set alias name sender
			$this->tpl['aliascompany'] = ($aliascompany?$aliascompany:$this->dao->options['aliascompany']);

			// Set alias sender address
			$this->tpl['aliasaddress'] = ($aliasaddress?$aliasaddress:$this->dao->options['aliasaddress']);

			// Set alias sender zip
			$this->tpl['aliaszip'] = ($aliaszip?$aliaszip:$this->dao->options['aliaszip']);

			// Set alias sender town
			$this->tpl['aliastown'] = ($aliastown?$aliastown:$this->dao->options['aliastown']);

			// Set alias sender phone
			$this->tpl['aliasphone'] = ($aliasphone?$aliasphone:$this->dao->options['aliasphone']);

			// Set alias sender phone
			$this->tpl['aliasfax'] = ($aliasfax?$aliasfax:$this->dao->options['aliasfax']);

			// Set alias sender email
			$this->tpl['aliasemail'] = ($aliasemail?$aliasemail:$this->dao->options['aliasemail']);

			// Set alias sender url
			$this->tpl['aliasurl'] = ($aliasurl?$aliasurl:$this->dao->options['aliasurl']);

			// Set alias sender country
			// We define country_id
			if (! empty($conf->global->ULTIMATE_PDF_ALIAS_COUNTRY_EMETTEUR))
			{
				$tmp = explode(':', $conf->global->ULTIMATE_PDF_ALIAS_COUNTRY_EMETTEUR);
				$country_id = $tmp[2]?$tmp[2]:$tmp[0];
			}
			else
			{
				$aliascountry = 0;
			} 
			
			$this->tpl['select_country'] = $form->select_country($country_id?$country_id:$this->dao->options['country_id'], 'country_id');
		}
		else
		{
			$this->dao->getDesigns();

			$this->tpl['designs']		= $this->dao->designs;
			$this->tpl['img_on'] 		= img_picto($langs->trans("Activated"), 'on');
			$this->tpl['img_off'] 		= img_picto($langs->trans("Disabled"), 'off');
			$this->tpl['img_modify'] 	= img_edit();
			$this->tpl['img_delete'] 	= img_delete();

			// Confirm delete 
			if ($_GET["action"] == 'delete')
			{
				if (GETPOST('type') == 'otherlogo')
				{
					$urlfile = GETPOST('urlfile');
					$this->tpl['action_delete'] = $form->formconfirm($_SERVER["PHP_SELF"].'?urlfile='.$urlfile, $langs->trans("DeleteFile"), $langs->trans("ConfirmDeleteFile").' '.$urlfile.' ?', 'delete_ok', '', 1, (int) $conf->use_javascript_ajax);
					$this->template = 'ultimatepdf_edit.tpl.php';				
				}
				else
				{
					$this->tpl['action_delete'] = $form->formconfirm($_SERVER["PHP_SELF"]."?id=".GETPOST('id'),$langs->trans("DeleteDesign"),$langs->trans("ConfirmDeleteDesign"),"confirm_delete_design",'',0,1);
					$this->template = 'ultimatepdf_view.tpl.php';
				}
			}
			else
			{
				$this->template = 'ultimatepdf_view.tpl.php';
			}			
		}
	}

	/**
	 *    Display the template
	 */
	function display()
	{
		global $conf, $langs;

		include($this->template_dir.$this->template);
	}

	/**
	 *
	 */
	function printTopRightMenu()
	{
		return $this->getTopRightMenu();
	}

	/**
	 * 	Show design info
	 */
	private function getTopRightMenu()
	{
		global $conf, $langs;

		$langs->load('ultimatepdf@ultimatepdf');

		$out='';

		if (($conf->global->MAIN_THEME === 'eldy' && empty($conf->global->ULTIMATEPDF_DROPDOWN_MENU_DISABLED) && ! GETPOSTISSET('theme')) || (GETPOSTISSET('theme') && GETPOST('theme', 'aZ', 1) === 'eldy'))
		{
			$out.= $this->getDropdownMenu();
		}
		else
		{
			$form=new Form($this->db);

			$this->getInstanceDao();

			$this->dao->getDesigns();

			if (is_array($this->dao->designs))
			{
				$htmltext ='<u>'.$langs->trans("Design").'</u>'."\n";
				foreach ($this->dao->designs as $design)
				{
					if ($design->active == 1)
					{
						if ($conf->global->ULTIMATE_DESIGN == $design->id)
						{
							$htmltext.='<br><b>'.$langs->trans("Label").'</b>: '.$design->label."\n";
							$htmltext.='<br><b>'.$langs->trans("Description").'</b>: '.$design->description."\n";
						}
					}
				}
			}

			$text = img_picto('', 'object_ultimatepdf@ultimatepdf','id="switchdesign" class="design linkobject"');

			$out.= $form->textwithtooltip('', $htmltext, 2, 1, $text, 'login_block_elem' ,2);

			$out.= '<script type="text/javascript">
			$("#switchdesign").click(function() {
				$("#dialog-switchdesign").dialog({
					modal: true,
					width: "'.($conf->dol_optimize_smallscreen ? 300 : 400).'",
					buttons: {
						 "'.$langs->trans('Ok').'": function() {
							choice= "ok";
							$.get( "'.dol_buildpath('/ultimatepdf/ajaxswitchdesign.php', 1).'", {
								action: "switchdesign",
								design: $( "#design" ).val()
							},
							function(content) {
								$( "#dialog-switchdesign" ).dialog( "close" );
								location.href="'.$_SERVER["REQUEST_URI"].'";
							});
						},
						"'.$langs->trans('Cancel').'": function() {
							choice= "ko";
							$(this).dialog( "close" );
						}
					},
					close: function(event, ui) {
						if (choice == "ok") {
							location.href="'.DOL_URL_ROOT.'";
						}
					}
				});
			});
			</script>';

			$out.= '<div id="dialog-switchdesign" class="hideobject" title="'.$langs->trans('SwitchToAnotherDesign').'">'."\n";
			$out.= '<br>'.$langs->trans('SelectADesign').': ';
			$out.= ajax_combobox('design');
			$out.= $this->select_designs($conf->global->ULTIMATE_DESIGN)."\n";
			$out.= '</div>'."\n";
		}

		$this->resprints = $out;
		return 0;
	}

	/**
	 *
	 * @return string
	 */
	private function getDropdownMenu()
	{
		global $conf, $user, $langs;

		$this->getInstanceDao();

		$this->dao->getDesigns();

		$dropdownBody = '<span id="topmenuupdfmoreinfo-btn"><i class="fa fa-caret-right"></i> '.$langs->trans("ShowMoreInfos").'</span>';
		$dropdownBody.= '<div id="topmenuupdfmoreinfo" >';

		if (is_array($this->dao->designs))
		{
			$dropdownBody.= '<br><u>'.$langs->trans("Design").'</u>'."\n";
			foreach ($this->dao->designs as $design)
			{
				if ($design->active == 1)
				{
					if ($conf->global->ULTIMATE_DESIGN == $design->id)
					{
						$dropdownBody.= '<br><b>'.$langs->trans("Label").'</b>: '.$design->label."\n";
						$dropdownBody.= '<br><b>'.$langs->trans("Description").'</b>: '.$design->description."\n";
					}
				}
			}
		}

		$dropdownBody.= '</div>';

		$updfSwitchLink ='<div id="switchmodel-menu" class="button-top-menu-dropdown"><i class="fa fa-random"></i> '.$langs->trans("Select").'</div>';
		$updfConfigLink ='<a class="button-top-menu-dropdown" href="'.dol_buildpath('ultimatepdf/admin/setup.php', 1).'?action=edit&id='.$design->id.'"><i class="fa fa-cogs"></i>  '.$langs->trans("Setup").'</a>';

		$out = '<div class="inline-block nowrap">';
		$out.= '<div class="inline-block login_block_elem login_block_elem_name float-left" style="padding: 0px;">';

		$out.= '<div id="topmenu-updf-dropdown" class="atoplogin updfdropdown updf-menu">';
		$out.= '<span class="fa updficon atoplogin updf-dropdown-toggle" data-toggle="updfdropdown" id="updf-dropdown-icon">';
		$out.= '<span class="fa fa-chevron-down padding-left20" id="updf-dropdown-icon-down"></span>';
		$out.= '<span class="fa fa-chevron-up padding-left20 hidden" id="updf-dropdown-icon-up"></span>';
		$out.= '</span>';

		$out.= '<div class="updf-dropdown-menu">';

		$out.= '<div class="updf-header">';
		$out.= '<span class="fa updficon-large dropdown-updf-image"></span>';
		$out.= '<br><br>'.$langs->trans('SwitchToAnotherDesign').': ';
		$out.= $this->select_designs($conf->global->ULTIMATE_DESIGN);
		$out.= '</div>';

		$out.= '<div class="updf-body">'.$dropdownBody.'</div>';

		$out.= '<div class="updf-footer">';
		$out.= '<div class="pull-left">';
		if (! empty($user->admin)) {
			$out.= $updfConfigLink;
		}
		$out.= '</div>';

		$out.= '<div class="pull-right">';
		$out.= $updfSwitchLink;
		$out.= '</div>';

		$out.= '<div style="clear:both;"></div>';

		$out.= '</div>';
		$out.= '</div>';
		$out.= '</div>';

		$out.= '</div></div>';

		$out.= '
		<script type="text/javascript">
		$(document).ready(function() {
			$(document).on("click", function(event) {
				if (!$(event.target).closest("#topmenu-updf-dropdown").length) {
					// Hide the menus.
					$("#topmenu-updf-dropdown").removeClass("open");
					$("#updf-dropdown-icon-down").show();
					$("#updf-dropdown-icon-up").hide();
				}
			});
			$("#topmenu-updf-dropdown .updf-dropdown-toggle").on("click", function(event) {
				$("#topmenu-updf-dropdown").toggleClass("open");
				$("#updf-dropdown-icon-down").toggle();
				$("#updf-dropdown-icon-up").toggle();
			});
			$("#topmenuupdfmoreinfo-btn").on("click", function() {
				$("#topmenuupdfmoreinfo").slideToggle();
			});
			$("#switchmodel-menu").on("click",function() {
				$.get("'.dol_buildpath('/ultimatepdf/ajaxswitchdesign.php', 1).'", {
					action: "switchdesign",
					design: $("#design").val()
				},
				function(content) {
					location.href="'.$_SERVER["REQUEST_URI"].'";
				});
			});
		';
		$out.= '
		});
		</script>';

		return $out;
	}


	/**
     * Show about informations
     * @param unknown_type $html
     */
	public function accordion($html)
	{
		global $conf,$langs;
        if ($conf->use_javascript_ajax)
		{
			$html='<script type="text/javascript">';
			$html.='$( function() {';
			$html.='$( "#accordion" ).accordion();';
			$html.='})';
			$html.='</script>';

			$html.='<div id="accordion">';
			$html.='<h3>Module for PDF models management</h3>';
			$html.='<div>';
			$html.='<p>';
			$html.='This module required Powererp >=10.0.0 stable installation</br>';
			$html.='Formation en ligne sur le module Ultimatepdf : installation, configuration, trucs et astuces. <a href="https://www.youtube.com/watch?v=bw3a08poCmU" target="_blank">Voir la video:</a>';
			$html.='</p>';
			$html.='</div>';
			$html.='<h3>Licence</h3>';
			$html.='<div>';
			$html.='<p>
			GPLv3 or (at your option) any later version.
			</p>';
			$html.='<p>
			See COPYING for more information.
			</p>';
			$html.='<p>';
			$html.='<a href="'.dol_buildpath('/ultimatepdf/COPYING',1).'">';
			$html.='<img src="'.dol_buildpath('/ultimatepdf/img/gplv3.png',1).'"/>';
			$html.='</a>';
			$html.='</p>';
			$html.='</div>';
			$html.='<h3>Contact</h3>';
			$html.='<div>';
			$html.='<p>
			This module is developped by <a href="mailto:philippe.grand@atoo-net.com">philippe.grand@atoo-net.com</>;
			</p>';
			$html.='</div>';
			$html.='<h3>Translating</h3>';
			$html.='<div>';
			$html.='<p>';
			$html.='<a href="https://www.transifex.com/atoo-net/ultimatepdf/dashboard" target="_blank">You can participate to a better translation of your language on Transifex</a>';
			$html.='</p>';
			$html.='</div>';
			$html.='<h3>'.$langs->trans("MoreModules") . $langs->trans("MoreModulesLink").'</h3>';
			$html.='<div>';
			$html.='<p>';
			$url=$langs->trans("MoreModulesLink2");
			$html.='<a href="'.$url.'" target="_blank"><img border="0" width="180" src="'.DOL_URL_ROOT.'/theme/dolistore_logo.png"></a><br><br><br>';
			$html.='</p>';
			$html.='</div>';
		}
        return $html;
	}

	/**
     * Complete doc forms (set this->resprint).
     *
     * @param	array	$parameters		Array of parameters
     * @param	object	$object			Object
     * @return  int 		        	<0 if KO,
     *                          		=0 if OK but we want to process standard actions too,
     *  	                            >0 if OK and we want to replace standard actions.
     */
    function formBuilddocOptions($parameters,&$object)
    {
        global $langs, $user, $conf, $form;

        $langs->load("ultimatepdf@ultimatepdf");

        $out='';

		$morefiles=array();

		if ($parameters['modulepart'] == 'propal')
        {
        	$staticpdf=glob($conf->ultimatepdf->dir_output."/proposals/*.pdf");
        	$modelpdf=glob($conf->ultimatepdf->dir_output."/proposals/pdf_*.modules.php");
        }
        if ($parameters['modulepart'] == 'order'   || $parameters['modulepart'] == 'commande')
        {
        	$staticpdf=glob($conf->ultimatepdf->dir_output."/orders/*.pdf");
        	$modelpdf=glob($conf->ultimatepdf->dir_output."/orders/pdf_*.modules.php");
        }
        if ($parameters['modulepart'] == 'invoice' || $parameters['modulepart'] == 'facture')
        {
        	$staticpdf=glob($conf->ultimatepdf->dir_output."/invoices/*.pdf");
        	$modelpdf=glob($conf->ultimatepdf->dir_output."/invoices/pdf_*.modules.php");
        }

		if (! empty($staticpdf))
		{
			foreach ($staticpdf as $filename)
			{
				$morefiles[] = basename($filename, ".pdf");
			}
		}
		if (! empty($modelpdf))
		{
			foreach ($modelpdf as $filename)
			{
				$morefiles[] = basename($filename, ".php");
			}
		}
		if (! empty($morefiles))
		{
			$out.='<tr class="liste_titre">';
			$out.='<td align="left" colspan="4" valign="top" class="formdoc">';
			$out.=$langs->trans("ConcatenateFile").' ';
			$out.= $form->selectarray('ultimatepdffile',$morefiles,-1,1,0,1);
		}
		$out.='</td></tr>';

		$this->resprints = $out;

        return 0;
    }

	/**
	 * formObjectOptions Method Hook Call
	 *
	 * @param array $parameters parameters
	 * @param Object	&$object			Object to use hooks on
	 * @param string	&$action			Action code on calling page ('create', 'edit', 'view', 'add', 'update', 'delete'...)
	 * @param object $hookmanager class instance
	 * @return void
	 */
	function formObjectOptions($parameters, &$object, &$action)
	{
		global $langs, $conf, $user;

		$langs->load('ultimatepdf@ultimatepdf');

		dol_syslog(__METHOD__, LOG_DEBUG);
		$html = '';
		$mergedpdf = array(
			'propal' => array(
				'upperconst' => 'PROPOSALS',
				'classpath' => 'propal',
				'rights' => 'propal',
				'subdir' => '/'
			),
			'facture' => array(
				'upperconst' => 'INVOICES',
				'classpath' => 'invoice',
				'rights' => 'invoice',
				'subdir' => '/'
			),
			'commande' => array(
				'upperconst' => 'ORDERS',
				'classpath' => 'order',
				'rights' => 'order',
				'subdir' => '/'
			),
			'contrat' => array(
				'upperconst' => 'CONTRACTS',
				'classpath' => 'contract',
				'rights' => 'contract',
				'subdir' => '/'
			),
			'order_supplier' => array(
				'upperconst' => 'SUPPLIERORDERS',
				'classpath' => 'supplierorder',
				'rights' => 'fournisseur',
				'subdir' => '/commande/'
			),
			'supplier_proposal' => array(
				'upperconst' => 'SUPPLIERPROPOSAL',
				'classpath' => 'supplierproposal',
				'rights' => 'supplier_proposal',
				'subdir' => '/'
			)
		);

		// Add javascript Jquery to add button Select doc form
		$upperconst = "ULTIMATEPDF_GENERATE_".$mergedpdf[$object->element]['upperconst']."_WITH_MERGED_PDF";
		if (array_key_exists($object->element, $mergedpdf) && ! empty($object->id) && ! empty($conf->global->$upperconst))
		{
			require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
			$classpath = $mergedpdf[$object->element]['classpath'];
			$ucfClasspath = ucfirst($classpath);
			dol_include_once('/ultimatepdf/class/documentmergedpdf.class.php');
			$filetomerge = new DocumentMergedPdf ( $this->db );
			$result = $filetomerge->fetch_by_element( $object );
			$rights = $mergedpdf[$object->element]['rights'];
			$subdir = $mergedpdf[$object->element]['subdir'];

			if (! empty ($conf->$rights->enabled))
				$upload_dir = $conf->$rights->dir_output.$subdir.dol_sanitizeFileName( $object->ref);

			$filearray = dol_dir_list($upload_dir, "files", 0, '', '\.meta$', 'name', SORT_ASC, 1 );

			// For each file build select list with PDF extention
			if (count ( $filearray ) > 0)
			{
				$html .= '<BR><BR>';
				// Actual file to merge is :
				if (count($filetomerge->lines)>0)
				{
					$html .= '<div class=\"fichecenter\">';
					$html .= '<div class=\"fichehalfleft\">';
					$html .= '<div class=\"titre\">';
					$html .= '<br>';
					$html .= $langs->trans($ucfClasspath.'MergePdf'.$ucfClasspath.'ActualFile');
					$html .= '</div>';
				}

				$html .= '<form name=\"filemerge\" action=\"' . $_SERVER['PHP_SELF']. '?id=' . $object->id . '\" method=\"post\">';
				$html .= '<input type=\"hidden\" name=\"token\" value=\"' . $_SESSION ['newtoken'] . '\">';
				$html .= '<input type=\"hidden\" name=\"action\" value=\"filemerge\">';

				if (count($filetomerge->lines) == 0)
				{
					$html .= '<div class=\"fichecenter\">';
					$html .= '<div class=\"fichehalfleft\">';
					$html .= '<div class=\"titre\">';
					$html .= '<br>';
					$html .= $langs->trans($ucfClasspath.'MergePdf'.$ucfClasspath.'ChooseFile');
					$html .= '</div>';
				}

				$html .= '<table class=\"noborder\" width=\"100%\">';
				$html .= '<tbody>';
				$html .= '<tr class=\"liste_titre\">';
				$html .= '<th>'. $langs->trans ( 'Documents' ) .'';
				$html .= '</th></tr>';
				$html .= '</tbody>';

				$hasfile = false;
				foreach ( $filearray as $filetoadd )
				{
					if (($ext = pathinfo ( $filetoadd ['name'], PATHINFO_EXTENSION ) == 'pdf') && ($filename = pathinfo ( $filetoadd ['name'], PATHINFO_FILENAME ) != $object->ref))
					{
						$checked = '';
						$filename = $filetoadd ['name'];

						if (array_key_exists($filetoadd ['name'],$filetomerge->lines))
						{
							$checked =' checked=\"checked\" ';
						}

						$hasfile = true;
						$icon ='<img border=\"0\" title=\"Fichier: '.$filename.'\" alt=\"Fichier: '.$filename.'\" src=\"'. DOL_URL_ROOT .'/theme/common/mime/pdf.png\">';
						$html .= '<tr class=\"oddeven\"><td class=\"nowrap\" style=\"font-weight:bold\">';

						$html .= '<input type=\"checkbox\" '.$checked.' name=\"filetoadd[]\" id=\"filetoadd\" value=\"'.$filetoadd ['name'].'\"> '.$icon.' '.$filename.'</input>';
						$html .= '</td></tr>';
					}
				}

				if (! $hasfile)
				{
					$html .= '<tr><td>';
					$warning = '<img border=\"0\" src=\"'. DOL_URL_ROOT .'/theme/eldy/img/warning.png\">';
					$html .= $warning.' '.$langs->trans('GotoDocumentsTab');
					$html .= '</td></tr>';
				}

				if ($hasfile)
				{
					$html .= '<tr><td>';
					$html .= '<input type=\"submit\" class=\"button\" name=\"save\" value=\"' . $langs->trans('Save') . '\">';
					$html .= '<br><br>';
					$html .= '</td></tr>';
				}

				$html .= '</table>';
				$html .= '</form>';
				$html .= '</div>';
				$html .= '</div>';

				if ($conf->use_javascript_ajax)
				{
					print "\n".'<script type="text/javascript" language="javascript">';
					print 'jQuery(document).ready(function () {
					jQuery(function() {jQuery(".fiche").append("' . $html . '");});
					})';
					print '</script>'."\n";
				}
			}
		}
		//$this->resprints = $html;
		return 0;
	}

	/**
	 * Return action of hook
	 *
	 * @param array $parameters
	 * @param object $object
	 * @param string $action
	 * @param object $hookmanager class instance
	 * @return void
	 */
	function afterPDFCreation($parameters, &$object, &$action = '', $hookmanager)
	{
		global $langs,$conf;
        global $hookmanager;

        $outputlangs=$langs;

        //var_dump($parameters['object']);

        $ret=0; $deltemp=array();
        dol_syslog(get_class($this).'::executeHooks action='.$action);

        $check='alpha';
        if (! empty($conf->global->ULTIMATEPDF_MULTIPLE_CONCATENATION_ENABLED))
        {
        	$check='array';
        }
        $ultimatepdffile = GETPOST('ultimatepdffile',$check);
        if (! is_array($ultimatepdffile) && ! empty($ultimatepdffile)) $ultimatepdffile = array($ultimatepdffile);

        $element='';
        if ($parameters['object']->element == 'propal')  $element='proposals';
        if ($parameters['object']->element == 'order'   || $parameters['object']->element == 'commande') $element='orders';
        if ($parameters['object']->element == 'invoice' || $parameters['object']->element == 'facture')  $element='invoices';

        $filetoconcat1=array($parameters['file']);
        $filetoconcat2=array();

        if (! empty($ultimatepdffile) && $ultimatepdffile[0] != -1)
        {
        	foreach($ultimatepdffile as $ultimatefile)
        	{
        		if (preg_match('/^pdf_(.*)+\.modules/', $ultimatefile))
        		{
        			require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");

        			$file = $conf->ultimatepdf->dir_output.'/'.$element.'/'.$ultimatefile.'.php';
        			$classname = str_replace('.modules', '', $ultimatefile);
        			require_once($file);
        			$obj = new $classname($this->db);

        			// We save charset_output to restore it because write_file can change it if needed for
        			// output format that does not support UTF8.
        			$sav_charset_output=$outputlangs->charset_output;
        			// Change the output dir
        			$srctemplatepath = $conf->ultimatepdf->dir_output->dir_temp;
        			// Generate pdf
        			$obj->write_file($parameters['object'], $outputlangs, $srctemplatepath, $hidedetails, $hidedesc, $hideref, $hookmanager);
        			// Restore charset output
        			$outputlangs->charset_output=$sav_charset_output;

        			$objectref = dol_sanitizeFileName($parameters['object']->ref);
        			$dir = $conf->ultimatepdf->dir_output->dir_temp . "/" . $objectref;

        			$filetoconcat2[] = $dir . "/" . $objectref . ".pdf";

        			$deltemp[] = $dir;
        		}
        		else
        		{
        			$filetoconcat2[] = $conf->ultimatepdf->dir_output.'/'.$element.'/'.$ultimatefile.'.pdf';
        		}
        	}

        	dol_syslog(get_class($this).'::afterPDFCreation '.$filetoconcat1.' - '.$filetoconcat2);

        	if (! empty($filetoconcat2) && ! empty($ultimatepdffile) && $ultimatepdffile != '-1')
        	{
        		$filetoconcat = array_merge($filetoconcat1, $filetoconcat2);

        		// Create empty PDF
        		$pdf=pdf_getInstance();
        		if (class_exists('TCPDF'))
        		{
        			$pdf->setPrintHeader(false);
        			$pdf->setPrintFooter(false);
        		}
        		$pdf->SetFont(pdf_getPDFFont($outputlangs));

        		if ($conf->global->MAIN_DISABLE_PDF_COMPRESSION) $pdf->SetCompression(false);
        		//$pdf->SetCompression(false);

        		$pagecount = $this->concat($pdf, $filetoconcat);

        		if ($pagecount)
        		{
        			$pdf->Output($filetoconcat1[0],'F');
        			if (! empty($conf->global->MAIN_UMASK))
        			{
        				@chmod($file, octdec($conf->global->MAIN_UMASK));
        			}
        			if (! empty($deltemp))
        			{
        				// Delete temp files
        				foreach($deltemp as $dirtemp)
        				{
        					dol_delete_dir_recursive($dirtemp);
        				}
        			}
        		}

        		// Save selected files and order
        		$params['ultimatepdf'] = $ultimatepdffile;
        		$parameters['object']->extraparams = array_merge($parameters['object']->extraparams, $params);
        	}
        }
        else
        {
        	// Remove extraparams for ultimatepdf
        	unset($parameters['object']->extraparams['ultimatepdf']);
        }

        $result=$parameters['object']->setExtraParameters();

        return $ret;
	}

	/**
	 * concat
	 * @param unknown_type $pdf    Pdf
	 * @param unknown_type $files  Files
	 */
	function concat(&$pdf,$files)
	{
		foreach($files as $file)
		{
			$pagecount = $pdf->setSourceFile($file);
			for ($i = 1; $i <= $pagecount; $i++)
			{
				$tplidx = $pdf->ImportPage($i);
				$s = $pdf->getTemplatesize($tplidx);
				$pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L');
				$pdf->useTemplate($tplidx);
				if (method_exists($pdf, 'AliasNbPages')) $pdf->AliasNbPages();
			}
		}

		return $pagecount;
	}

	/**
	 *
	 */
	function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf;

		$langs->load("ultimatepdf@ultimatepdf");

		$out='';

		if (is_array($parameters) && ! empty($parameters))
		{
			foreach($parameters as $key=>$value)
			{
				$$key=$value;
			}
		}

		$currentcontext = explode(':', $parameters['context']);

		if (in_array('propalcard', $currentcontext) && $object->element == 'propal' && $object->statut == Propal::STATUS_VALIDATED)
		{
			$out.= '<aside id="modalSignature" class="modal" aria-hidden="true" role="dialog" aria-labelledby="titlemodal">';
			$out.= '<div class="modal-wrapper">';
			$out.= '<div class="sigPad">';
			//$out.= '<label for="name">'.$langs->trans("Print your name").'</label>';
			//$out.= '<input type="text" name="name" id="name" class="name">';
			//$out.= '<p class="typeItDesc">'.$langs->trans("Review your signature").'</p>';
			$out.= '<p class="drawItDesc">'.$langs->trans("DrawYourSignature").'</p>';
			$out.= '<ul class="sigNav">';
			$out.= '<li class="typeIt"><a href="#statut=3" class="current">'.$langs->trans("Refuser").'</a></li>';
			$out.= '<li class="drawIt"><a href="#draw-it" >'.$langs->trans("DrawIt").'</a></li>';
			$out.= '<li class="clearButton"><a href="#clear">'.$langs->trans("Clear").'</a></li></ul>';
			$out.= '<div class="sig sigWrapper">';
			$out.= '<div class="typed"></div>';
			$out.= '<canvas class="pad" width="238" height="72"></canvas>';
			$out.= '<input type="hidden" id="output" name="output" class="output">';
			$out.= '</div>';

			$out.= '<script type="text/javascript" language="javascript">
			/*global $ */
            $("#modalSignature").hide();
            $(document).ready(function () {
            "use strict";
                var options = { drawOnly : true },
                    sigpad = $(".sigPad").signaturePad(options),
                    statutButton = $("a[href$=\'action=statut\']");
                statutButton.removeAttr("href");
                statutButton.click(function () {
                    $("#modalSignature").show();
                });

                $("#acceptSignature").click(function () {
                    var ret = sigpad.validateForm(),
                        sign = sigpad.getSignature();
                    if (ret === true && sign !== "") {
                        $.post("'.dol_buildpath("/ultimatepdf/core/ajax/functions.php", 1).'", {
							"action" : "genSignature",
							"id" : "'.$object->id.'",
							"element" : "'.$object->element.'",
							"json" : $("#output").val()
						},
						function () {					
                            var url = "'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=setstatut&statut='.Propal::STATUS_SIGNED.'";
                            location.href=url;						
						});
					}
    			});
				$("#notsigned").click(function() {
					var url = "'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=setstatut&statut='.Propal::STATUS_NOTSIGNED.'";
					location.href=url;
				});
			});
			</script>';

			$out.= '<button id="acceptSignature" type="button">'.$langs->trans("IAcceptTheTermsOfThisAgreement").'</button>';
			
			$out.= '<button id="notsigned" type="button">'.$langs->trans("ThisProposalIsNotSigned").'</button>';

			$out.= '</div>';
			$out.= '</div>';
			$out.= '</aside>';
            if($conf->global->ULTIMATEPDF_DISPLAY_PROPOSAL_ELECTRONIC_SIGNATURE == 1) {
			    echo $out;
			}
		}
		elseif (in_array('interventioncard', $currentcontext) && $object->element == 'fichinter' && $object->statut == Fichinter::STATUS_VALIDATED)
		{
			$out.= '<aside id="modalSignature" class="modal" aria-hidden="true" role="dialog" aria-labelledby="titlemodal">';
			$out.= '<div class="modal-wrapper">';
			$out.= '<div class="sigPad">';
			$out.= '<p class="drawItDesc">'.$langs->trans("DrawYourSignature").'</p>';
			$out.= '<ul class="sigNav">';
			$out.= '<li class="drawIt"><a href="#draw-it" >'.$langs->trans("DrawIt").'</a></li>';
			$out.= '<li class="clearButton"><a href="#clear">'.$langs->trans("Clear").'</a></li></ul>';
			$out.= '<div class="sig sigWrapper">';
			$out.= '<div class="typed"></div>';
			$out.= '<canvas class="pad" width="238" height="72"></canvas>';
			$out.= '<input type="hidden" id="output" name="output" class="output">';
			$out.= '</div>';

			$out.= '<script type="text/javascript" language="javascript">
			$("#modalSignature").hide();
			$(document).ready(function()
			{
				var options = { drawOnly : true };
				var sigpad = $(".sigPad").signaturePad(options);

				var statutButton = $("a[href$=\'action=classifydone\']");
				statutButton.removeAttr("href");
				statutButton.click(function() {
					$("#modalSignature").show();
				});

				$("#acceptSignature").click(function() {
					var ret = sigpad.validateForm();
					var sign = sigpad.getSignature();
					if (ret == true && sign != "") {
						$.post("'.dol_buildpath("/ultimatepdf/core/ajax/functions.php", 1).'", {
							"action" : "genSignature",
							"id" : "'.$object->id.'",
							"element" : "'.$object->element.'",
							"json" : $("#output").val()
						},
						function (result) {
							if (result.status == "ok") {
								var url = "'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=classifydone";
								location.href=url;
							} else {
								alert("ERROR");
							}
						});
					}
    			});
			});
			</script>';

			$out.= '<button id="acceptSignature" type="button">'.$langs->trans("IAcceptTheTermsOfThisAgreement").'</button>';

			$out.= '</div>';
			$out.= '</div>';
			$out.= '</aside>';

			if($conf->global->ULTIMATEPDF_DISPLAY_FICHINTER_ELECTRONIC_SIGNATURE == 1) {
			    echo $out;
			}
		}
		elseif (in_array('ordercard', $currentcontext) && $object->element == 'commande')
		{
			$out.= '<aside id="modalSignature" class="modal" aria-hidden="true" role="dialog" aria-labelledby="titlemodal">';
			$out.= '<div class="modal-wrapper">';
			$out.= '<div class="sigPad">';
			$out.= '<p class="drawItDesc">'.$langs->trans("DrawYourSignature").'</p>';
			$out.= '<ul class="sigNav">';
			$out.= '<li class="typeIt"><a href="#statut=3" class="current">'.$langs->trans("ClassifyShipped").'</a></li>';
			$out.= '<li class="drawIt"><a href="#draw-it" >'.$langs->trans("DrawIt").'</a></li>';
			$out.= '<li class="clearButton"><a href="#clear">'.$langs->trans("Clear").'</a></li></ul>';
			$out.= '<div class="sig sigWrapper">';
			$out.= '<div class="typed"></div>';
			$out.= '<canvas class="pad" width="238" height="72"></canvas>';
			$out.= '<input type="hidden" id="output" name="output" class="output">';
			$out.= '</div>';

			$out.= '<script type="text/javascript" language="javascript">
			$("#modalSignature").hide();
			$(document).ready(function()
			{
				var options = { drawOnly : true };
				var sigpad = $(".sigPad").signaturePad(options);

				var statutButton = $("a[href$=\'action=validate\']");
				statutButton.removeAttr("href");
				statutButton.click(function() {
					$("#modalSignature").show();
				});

				$("#acceptSignature").click(function() {
					var ret = sigpad.validateForm();
					var sign = sigpad.getSignature();
					if (ret == true && sign != "") {
						$.post("'.dol_buildpath("/ultimatepdf/core/ajax/functions.php", 1).'", {
							"action" : "genSignature",
							"id" : "'.$object->id.'",
							"element" : "'.$object->element.'",
							"json" : $("#output").val()
						},
						function (result) {
							if (result.status == "ok") {
								var url = "'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes";
								location.href=url;
							} else {
								alert("ERROR");
							}
						});
					}
    			});
				$("#shipped").click(function() {
					var url = "'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&action=shipped";
					location.href=url;
				});
			});
			</script>';

			$out.= '<button id="acceptSignature" type="button">'.$langs->trans("IAcceptTheTermsOfThisAgreement").'</button>';
			
			$out.= '<button id="shipped" type="button">'.$langs->trans("ClassifyShipped").'</button>';

			$out.= '</div>';
			$out.= '</div>';
			$out.= '</aside>';

			if($conf->global->ULTIMATEPDF_DISPLAY_ORDER_ELECTRONIC_SIGNATURE == 1) {
			    echo $out;
			}
		}

		return 0;
	}
}
