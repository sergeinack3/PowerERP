<?php
/* Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2008      Raphael Bertrand     <raphael.bertrand@resultic.fr>
 * Copyright (C) 2010      Juanjo Menent	    <jmenent@2byte.es>
 * Copyright (C) 2011-2020 Philippe Grand		<philippe.grand@atoo-net.com>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file       core/modules/commande/doc/pdf_ultimate_order.modules.php
 *	\ingroup    commande
 *	\brief      File of Class to generate PDF order from pdf_ultimate_order model 
 */

require_once DOL_DOCUMENT_ROOT.'/core/modules/commande/modules_commande.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
dol_include_once('/ultimatepdf/lib/ultimatepdf.lib.php');


/**
 *	Class for generate the commands with ultimate_order model
 */
class pdf_ultimate_order extends ModelePDFCommandes
{
	/**
     * @var DoliDb Database handler
     */
    public $db;
	
	/**
     * @var string model name
     */
    public $name;
	
	/**
     * @var string model description (short text)
     */
    public $description;
	
	 /**
     * @var int 	Save the name of generated file as the main doc when generating a doc with this template
     */
    public $update_main_doc_field;
	
	/**
     * @var string document type
     */
    public $type;
	
	/**
     * @var array() Minimum version of PHP required by module.
	 * e.g.: PHP ≥ 5.5 = array(5, 5)
     */
	public $phpmin = array(5, 5); 
	
	/**
     * PowerERP version of the loaded document
     * @public string
     */
	public $version = 'PowerERP';
	
	/**
     * @var int page_largeur
     */
    public $page_largeur;
	
	/**
     * @var int page_hauteur
     */
    public $page_hauteur;
	
	/**
     * @var array format
     */
    public $format;
	
	/**
     * @var int marge_gauche
     */
	public $marge_gauche;
	
	/**
     * @var int marge_droite
     */
	public $marge_droite;
	
	/**
     * @var int marge_haute
     */
	public $marge_haute;
	
	/**
     * @var int marge_basse
     */
	public $marge_basse;
	
	/**
     * @var array style
     */
	public $style;
	
	/**
	 * @var
	 */
	public $roundradius;
	
	/**
     * @var string logo_height
     */
	public $logo_height;	
	
	/**
     * @var int number column width
     */
	public $number_width;
	
	/**
     * @var int description column width
     */
	public $desc_width;
	
	/**
     * @var int vat column width
     */
	public $tva_width;
	
	/**
     * @var int up column width
     */
	public $up_width;
	
	/**
     * @var int up after column width
     */
	public $upafter_width;
	
	/**
     * @var int qty column width
     */
	public $qty_width;
	
	/**
     * @var int weight column width
     */
	public $weight_width;
	
	/**
     * @var int discount column width
     */
	public $discount_width;

	/**
	 * Issuer
	 * @var Societe
	 */
	public $emetteur;
	
	/**
     * @var boolean is there a message or not
     */
	private $messageErrBarcodeSet;


	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $conf, $langs, $mysoc;
		
		// Translations
		$langs->loadLangs(array("main", "bills", "products", "ultimatepdf@ultimatepdf"));

		$this->db = $db;
		$this->name = "ultimate_order";
		$this->description = $langs->trans('PDFUltimate_orderDescription');
		$this->update_main_doc_field = 1;		// Save the name of generated file as the main doc when generating a doc with this template
		$_SESSION['ultimatepdf_model'] = true;

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray=pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = $formatarray['height'];
		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=isset($conf->global->ULTIMATE_PDF_MARGIN_LEFT)?$conf->global->ULTIMATE_PDF_MARGIN_LEFT:10;
		$this->marge_droite=isset($conf->global->ULTIMATE_PDF_MARGIN_RIGHT)?$conf->global->ULTIMATE_PDF_MARGIN_RIGHT:10;
		$this->marge_haute =isset($conf->global->ULTIMATE_PDF_MARGIN_TOP)?$conf->global->ULTIMATE_PDF_MARGIN_TOP:10;
		$this->marge_basse =isset($conf->global->ULTIMATE_PDF_MARGIN_BOTTOM)?$conf->global->ULTIMATE_PDF_MARGIN_BOTTOM:10;

		$this->option_logo = 1;                    // Display logo
		$this->option_tva = 1;                     // Manage the vat option FACTURE_TVAOPTION
		$this->option_modereg = 1;                 // Display payment mode
		$this->option_condreg = 1;                 // Display payment terms
		$this->option_codeproduitservice = 1;      // Display product-service code
		$this->option_multilang = 1;               // Available in several languages
		$this->option_escompte = 0;                // Displays if there has been a discount
		$this->option_credit_note = 0;             // Support credit notes
		$this->option_freetext = 1;				   // Support add of a personalised text
		$this->option_draft_watermark = 1;		   // Support add of a watermark on drafts

		$this->franchise =! $mysoc->tva_assuj;
		
		$bordercolor = array('0', '63', '127');
		$roundradius = isset($conf->global->ULTIMATE_SET_RADIUS) ? $conf->global->ULTIMATE_SET_RADIUS : 2;
		$dashdotted = isset($conf->global->ULTIMATE_DASH_DOTTED) ? $conf->global->ULTIMATE_DASH_DOTTED : '';
		if(! empty($conf->global->ULTIMATE_BORDERCOLOR_COLOR))
		{
			$bordercolor = html2rgb($conf->global->ULTIMATE_BORDERCOLOR_COLOR);
			if(! empty($conf->global->ULTIMATE_DASH_DOTTED))
			{
				$dashdotted = $conf->global->ULTIMATE_DASH_DOTTED;
			}
			$this->border_style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => $dashdotted , 'color' => $bordercolor);
		}

		// Get source company
		$this->emetteur = $mysoc;
		if (empty($this->emetteur->country_code)) $this->emetteur->country_code = substr($langs->defaultlang, - 2);    // By default, if was not defined
		
		$this->tabTitleHeight = 8; // default height

		$this->tva=array();
		$this->localtax1 = array();
		$this->localtax2 = array();
		$this->atleastoneratenotnull = 0;
		$this->atleastonediscount = 0;
		$this->atleastoneref = 0;
	}

	/**
     *  Function to build pdf onto disk
     *
     *  @param		Object		$object				Object to generate
     *  @param		Translate	$outputlangs		Lang output object
     *  @param		string		$srctemplatepath	Full path of source filename for generator using a template file
     *  @param		int			$hidedetails		Do not show line details
     *  @param		int			$hidedesc			Do not show desc
     *  @param		int			$hideref			Do not show ref
     *  @return     int             				1=OK, 0=KO
	 */
	public function write_file($object, $outputlangs, $srctemplatepath = '', $hidedetails = 0, $hidedesc = 0, $hideref = 0)
	{
		global $user, $langs, $conf, $mysoc, $db, $hookmanager, $nblignes;
		
		$textcolor = array('25', '25', '25');
		if (! empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor = html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}
		if (!empty($conf->global->ULTIMATE_SET_RADIUS))
		{
			$roundradius = $conf->global->ULTIMATE_SET_RADIUS;
		}
		if(! empty($conf->global->ULTIMATE_BORDERCOLOR_COLOR))
		{
			$bordercolor = html2rgb($conf->global->ULTIMATE_BORDERCOLOR_COLOR);
		}
		if (! empty($conf->global->ULTIMATE_DASH_DOTTED))
		{
			$dashdotted = $conf->global->ULTIMATE_DASH_DOTTED;
		}
		$this->border_style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => $dashdotted , 'color' => $bordercolor);

		if (! is_object($outputlangs)) $outputlangs = $langs;
		
		// Translations
		$outputlangs->loadLangs(array("main", "dict", "companies", "bills", "products", "orders", "deliveries", "errors", "ultimatepdf@ultimatepdf"));
		
		$nblignes = count($object->lines);
		
		// Loop on each lines to detect if there is at least one image to show
		$realpatharray=array();
		if (! empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_PICTURE))
		{			
			$objphoto = new Product($this->db);

			for ($i = 0 ; $i < $nblignes ; $i++)
			{
				if (empty($object->lines[$i]->fk_product)) continue;

				$objphoto->fetch($object->lines[$i]->fk_product);

				if (! empty($conf->global->PRODUCT_USE_OLD_PATH_FOR_PHOTO))
				{
					$pdir[0] = get_exdir($objphoto->id,2,0,0,$objphoto,'product') . $objphoto->id ."/photos/";
					$pdir[1] = dol_sanitizeFileName($objphoto->ref).'/';
				}
				else
				{
					$pdir[0] = dol_sanitizeFileName($objphoto->ref).'/';				// default
					$pdir[1] = get_exdir($objphoto->id,2,0,0,$objphoto,'product') . $objphoto->id ."/photos/";	// alternative
				}

				$arephoto = false;
				foreach ($pdir as $midir)
				{
					if (! $arephoto)
					{
						$dir = $conf->product->dir_output.'/'.$midir;

						foreach ($objphoto->liste_photos($dir,1) as $key => $obj)
						{
							if (empty($conf->global->CAT_HIGH_QUALITY_IMAGES))		// If CAT_HIGH_QUALITY_IMAGES not defined, we use thumb if defined and then original photo
							{
								if ($obj['photo_vignette'])
								{
									$filename= $obj['photo_vignette'];
								}
								else
								{
									$filename=$obj['photo'];
								}
							}
							else
							{
								$filename=$obj['photo'];
							}

							$realpath = $dir.$filename;
							$arephoto = true;
						}
					}
				}				
				if ($realpath && $arephoto) $realpatharray[$i] = $realpath;
			}
		}

		if ($conf->commande->dir_output)
		{
            $object->fetch_thirdparty();

            $deja_regle = 0;

            // Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->commande->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->commande->dir_output . "/" . $objectref;
				$file = $dir . "/" . $objectref . ".pdf";
			}

			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
					return 0;
				}
			}

			if (file_exists($dir))
			{	
				// Add pdfgeneration hook
				if (! is_object($hookmanager))
				{
					include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
					$hookmanager=new HookManager($this->db);
				}
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters = array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
				global $action;
				$reshook = $hookmanager->executeHooks('beforePDFCreation', $parameters, $object, $action);    // Note that $action and $object may have been modified by some hooks
				
				// Create pdf instance
                $pdf = pdf_getInstance($this->format);
				$default_font_size = pdf_getPDFFontSize($outputlangs);  // Must be after pdf_getInstance
				
				if (! empty($conf->global->ULTIMATE_DISPLAY_ORDER_AGREEMENT_BLOCK))
				{
					$heightforinfotot = 60;	// Height reserved to output the info and total part
				}
				else
				{
					$heightforinfotot = 50;
				}
		        $heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:12);	// Height reserved to output the free text on last page
	             $heightforfooter = $this->marge_basse + 12;	// Height reserved to output the footer (value include bottom margin)
				 $pdf->SetAutoPageBreak(1,0);

                if (class_exists('TCPDF'))
                {
                    $pdf->setPrintHeader(false);
                    $pdf->setPrintFooter(false);
                }
                $pdf->SetFont(pdf_getPDFFont($outputlangs));
				
				// Set path to the background PDF File
                if (! empty($conf->global->MAIN_ADD_PDF_BACKGROUND))
                {
                    $pagecount = $pdf->setSourceFile($conf->mycompany->dir_output.'/'.$conf->global->MAIN_ADD_PDF_BACKGROUND);
                    $tplidx = $pdf->importPage(1);
                }

				$pdf->Open();
				$pagenb = 0;
				$pdf->SetDrawColor(128,128,128);

				$pdf->SetTitle($outputlangs->convToOutputCharset($object->ref));
				$pdf->SetSubject($outputlangs->transnoentities("PdfOrderTitle"));
				$pdf->SetCreator("PowerERP ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("PdfOrderTitle")." ".$outputlangs->convToOutputCharset($object->thirdparty->name));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right
				
				// Positionne $this->atleastoneref si on a au moins une ref 
				for ($i = 0 ; $i < $nblignes ; $i++)
				{
					if ($object->lines[$i]->product_ref)
					{
						$this->atleastoneref++;
					}
				}
				
				// Does we have at least one line with discount $this->atleastonediscount
				foreach ($object->lines as $line) 
				{
				   if ($line->remise_percent)
				   {
	                    $this->atleastonediscount = true;
	                    break;
	               }
	            }

				// New page
				$pdf->AddPage();
				if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				$pagenb++;

				$top_shift = $this->_pagehead($pdf, $object, 1, $outputlangs);
				$pdf->SetFont('', '', $default_font_size - 2);
				$pdf->MultiCell(0, 3, '');		// Set interline to 3
				$pdf->SetTextColorArray($textcolor);

				//catch logo height
				// Other Logo
				if (! empty ($conf->global->ULTIMATE_DESIGN) && ! empty ($conf->global->ULTIMATE_OTHERLOGO_FILE))
				{
					$id = $conf->global->ULTIMATE_DESIGN;
					$upload_dir	= $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/';	
					$filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview.*\.png)$', $sortfield, 0, 1);
					$otherlogo = $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/'.$filearray[0]['name'];
				}		
				if (is_readable($otherlogo) && ! empty($filearray))
				{
					$logo_height = max(pdf_getUltimateHeightForOtherLogo($otherlogo, true), 20);					
				}
				else
				{
					// MyCompany logo
					$logo_height = max(pdf_getUltimateHeightForLogo($conf->global->ULTIMATE_LOGO_HEIGHT, true), 20);
				}

				//Set $hautcadre
				if (($conf->global->ULTIMATE_PDF_ORDER_ADDALSOTARGETDETAILS == 1) || (! empty($conf->global->MAIN_INFO_SOCIETE_NOTE) && !empty($this->emetteur->note_private)) || (! empty($conf->global->MAIN_INFO_TVAINTRA) && !empty($this->emetteur->tva_intra)))
				{
					$hautcadre = 48;
				}
				else
				{
					$hautcadre = 40;
				}
				
				$tab_top = $this->marge_haute + $logo_height + $hautcadre + $top_shift + 15;
				$tab_top_newpage = (empty($conf->global->ULTIMATE_ORDERS_PDF_DONOTREPEAT_HEAD) ? $this->marge_haute + $logo_height + $top_shift + 10 : 10);			
				
				$tab_height = 130 - $top_shift;
				$tab_height_newpage = 150;
				if (empty($conf->global->ULTIMATE_ORDERS_PDF_DONOTREPEAT_HEAD)) $tab_height_newpage -= $top_shift;
				$tab_width = $this->page_largeur - $this->marge_gauche - $this->marge_droite;
				if ($roundradius == 0) 
				{
					$roundradius = 0.1; //RoundedRect don't support $roundradius to be 0
				}
				
				// Incoterm
				$height_incoterms = 0;
				if ($conf->incoterm->enabled)
				{
					$nexY = $pdf->GetY();
					$tab_top = $nexY + 5;
					$desc_incoterms = $object->getIncotermsForPDF();
					if ($desc_incoterms)
					{
						$pdf->SetFont('', '', $default_font_size - 2);
						$pdf->writeHTMLCell($tab_width, 3, $this->marge_gauche + 1, $tab_top + 1, dol_htmlentitiesbr($desc_incoterms), 0, 1);
						
						$height_incoterms = 4;
						
						// Rect prend une longueur en 3eme param
						$pdf->SetDrawColor(192, 192, 192);
						$pdf->RoundedRect($this->marge_gauche, $tab_top, $tab_width, $height_incoterms + 1, $roundradius, $round_corner = '1111', 'S', $this->border_style, $bgcolor);
						$nexY = $pdf->GetY();
						$tab_top = $nexY;
					}
				}

				// Affiche notes
				$notetoshow=empty($object->note_public)?'':$object->note_public;
				if (! empty($conf->global->MAIN_ADD_SALE_REP_SIGNATURE_IN_ORDER_NOTE))
				{
					// Get first sale rep
					if (is_object($object->thirdparty))
					{
						$salereparray=$object->thirdparty->getSalesRepresentatives($user);
						$salerepobj=new User($this->db);
						$salerepobj->fetch($salereparray[0]['id']);
						if (! empty($salerepobj->signature)) $notetoshow=dol_concatdesc($notetoshow, $salerepobj->signature);
					}
				}
				
				$pagenb = $pdf->getPage();
				if ($notetoshow && empty($conf->global->MAIN_INFO_SOCIETE_NOTE))
				{
					$pageposbeforenote = $pagenb;
					$nexY = $pdf->GetY();
					$tab_top = $nexY + 6;
					if($desc_incoterms)
					{
						$tab_top -= 2;
					}
					
					$substitutionarray = pdf_getSubstitutionArray($outputlangs, null, $object);
					complete_substitutions_array($substitutionarray, $outputlangs, $object);
					$notetoshow = make_substitutions($notetoshow, $substitutionarray, $outputlangs);
					
					$pdf->startTransaction();
					
					$pdf->SetFont('', '', $default_font_size - 1);   // Dans boucle pour gerer multi-page
					$pdf->writeHTMLCell($tab_width, 3, $this->marge_gauche+1, $tab_top, dol_htmlentitiesbr($notetoshow), 0, 1);
					// Description
				    $pageposafternote = $pdf->getPage();
				    $posyafter = $pdf->GetY();
					$nexY = $pdf->GetY();
					$height_note = $nexY - $tab_top;

					// Rect prend une longueur en 3eme et 4eme param
					$pdf->SetDrawColor(192, 192, 192);					
					$pdf->RoundedRect($this->marge_gauche, $tab_top - 1, $tab_width, $height_note + 1, $roundradius, $round_corner = '1111', 'S', $this->border_style, $bgcolor);

					if ($pageposafternote > $pageposbeforenote)
				    {
				        $pdf->rollbackTransaction(true);

				        // prepair pages to receive notes
				        while ($pagenb < $pageposafternote) 
						{
				            $pdf->AddPage();
				            $pagenb++;
				            if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				            if (empty($conf->global->ULTIMATE_ORDERS_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
				            // $this->_pagefoot($pdf,$object,$outputlangs,1);
				            $pdf->setTopMargin($tab_top_newpage);
				            // The only function to edit the bottom margin of current page to set it.
				            $pdf->setPageOrientation('', 1, $heightforfooter + $heightforfreetext);
				        }

				        // back to start
				        $pdf->setPage($pageposbeforenote);
				        $pdf->setPageOrientation('', 1, $heightforfooter + $heightforfreetext);
				        $pdf->SetFont('','', $default_font_size - 1);
				        $pdf->writeHTMLCell($tab_width, 3, $this->marge_gauche+1, $tab_top, dol_htmlentitiesbr($notetoshow), 0, 1);
				        $pageposafternote=$pdf->getPage();

				        $posyafter = $pdf->GetY();
						$nexY = $pdf->GetY();

				        if ($posyafter > ($this->page_hauteur - ($heightforfooter+$heightforfreetext+20)))	// There is no space left for total+free text
				        {
				            $pdf->AddPage('','',true);
				            $pagenb++;
				            $pageposafternote++;
				            $pdf->setPage($pageposafternote);
				            $pdf->setTopMargin($tab_top_newpage);
				            // The only function to edit the bottom margin of current page to set it.
				            $pdf->setPageOrientation('', 1, $heightforfooter + $heightforfreetext);
				            //$posyafter = $tab_top_newpage;
				        }

				        // apply note frame to previus pages
				        $i = $pageposbeforenote;
				        while ($i < $pageposafternote) 
						{
				            $pdf->setPage($i);

				            $pdf->SetDrawColor(128,128,128);
				            // Draw note frame
				            if ($i > $pageposbeforenote)
							{
				                $height_note = $this->page_hauteur - ($tab_top_newpage + $heightforfooter);
								$pdf->RoundedRect($this->marge_gauche, $tab_top_newpage-1, $tab_width, $height_note+1, $roundradius, $round_corner = '1111', 'S', array());
				            }
				            else
							{
				                $height_note = $this->page_hauteur - ($tab_top + $heightforfooter);
								$pdf->RoundedRect($this->marge_gauche, $tab_top-1, $tab_width, $height_note+1, $roundradius, $round_corner = '1111', 'S', array());
				            }

				            // Add footer
				            $pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.
				            $this->_pagefoot($pdf,$object,$outputlangs,1);

				            $i++;
				        }

				        // apply note frame to last page
				        $pdf->setPage($pageposafternote);
				        if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				        if (empty($conf->global->ULTIMATE_ORDERS_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
				        $height_note=$posyafter-$tab_top_newpage;
				        $pdf->RoundedRect($this->marge_gauche, $tab_top_newpage-1, $tab_width, $height_note+1, $roundradius, $round_corner = '1111', 'S', array());
				    }
				    else // No pagebreak
				    {
				        $pdf->commitTransaction();
				        $posyafter = $pdf->GetY();
				        $height_note=$posyafter-$tab_top;
				        $pdf->RoundedRect($this->marge_gauche, $tab_top-1, $tab_width, $height_note+1, $roundradius, $round_corner = '1111', 'S', array());

				        if($posyafter > ($this->page_hauteur - ($heightforfooter+$heightforfreetext+20)) )
				        {
				            // not enough space, need to add page
				            $pdf->AddPage('','',true);
				            $pagenb++;
				            $pageposafternote++;
				            $pdf->setPage($pageposafternote);
				            if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				            if (empty($conf->global->ULTIMATE_ORDERS_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);

				            $posyafter = $tab_top_newpage;
				        }
				    }

				    $tab_height = $tab_height - $height_note;
				    $tab_top = $posyafter + 12;

				}
				else
				{
					$height_note=0;
				}
				
				// Use new auto column system
				$this->prepareArrayColumnField($object, $outputlangs, $hidedetails, $hidedesc, $hideref);
				
				// Simulation de tableau pour connaitre la hauteur de la ligne de titre
				$pdf->startTransaction();
				$this->pdfTabTitles($pdf, $tab_top, $tab_height, $outputlangs, $hidetop);
				$pdf->rollbackTransaction(true);
				$nexY = $pdf->GetY();
				$tab_top = $nexY;
				if($desc_incoterms)
				{
					$tab_top += $height_incoterms + 6;
				}
				if($height_note)
				{
					$tab_top += $height_note + 2;
				}
				if(! $height_note && ! $desc_incoterms)
				{
					$tab_top = $nexY + 16;
				}
				if( $height_note && $desc_incoterms)
				{
					$tab_top -= 10;
				}
				
				$iniY = $tab_top + $this->tabTitleHeight + 2;
				$curY = $tab_top + $this->tabTitleHeight + 2;
				if (empty($conf->global->MAIN_PDF_DISABLE_COL_HEAD_TITLE))
				{
					$nexY = $tab_top + $this->tabTitleHeight - 8;
				}
				else
				{
					$nexY = $tab_top + $this->tabTitleHeight - 2;
				}

				// Loop on each lines
				$pageposbeforeprintlines = $pdf->getPage();
				$pagenb = $pageposbeforeprintlines;
				$line_number = 1;
				for ($i = 0 ; $i < $nblignes ; $i++)
                {
                    $curY = $nexY;
                    $pdf->SetFont('', '', $default_font_size - 2);   // Into loop to work with multipage
					$pdf->SetTextColorArray($textcolor);
					$barcode = null;
					if (! empty($object->lines[$i]->fk_product))
					{
						$product = new Product($db);
						$result = $product->fetch($object->lines[$i]->fk_product, '', '','');
						$product->fetch_barcode();
					}
					//Barcode style
					$styleBc = array(
						'position' => '',
						'align' => '',
						'stretch' => false,
						'fitwidth' => true,
						'cellfitalign' => '',
						'border' => false,
						'hpadding' => 'auto',
						'vpadding' => 'auto',
						'fgcolor' => array(0,0,0),
						'bgcolor' => false, //array(255,255,255),
						'text' => true,
						'font' => 'helvetica',
						'fontsize' => 8,
						'stretchtext' => 4
						);
					
					// Define size of image if we need it
					$imglinesize = array();
					if (! empty($realpatharray[$i])) $imglinesize = pdf_getSizeForImage($realpatharray[$i]);

					$pdf->setTopMargin($tab_top_newpage);
					//If we aren't on last lines footer space needed is on $heightforfooter
					if ($i != $nblignes-1)
					{
						$bMargin = $heightforfooter;
					}
					else 
					{
						//We are on last item, need to check all footer (freetext, ...)
						$bMargin = $heightforfooter + $heightforfreetext + $heightforinfotot;
					}
					$pdf->setPageOrientation('', 1, $bMargin);	// The only function to edit the bottom margin of current page to set it.
					$pageposbefore = $pdf->getPage();

                    $showpricebeforepagebreak=1;
					$posYAfterImage=0;
					$posYStartDescription=0;
					$posYAfterDescription=0;
					$posYafterRef=0;
					
					if ($this->getColumnStatus('picture'))
	                {
						// We start with Photo of product line
						if (isset($imglinesize['width']) && isset($imglinesize['height']) && ($curY + $imglinesize['height']) > ($this->page_hauteur-$bMargin))	// If photo too high, we moved completely on new page
						{
							$pdf->AddPage('','',true);
							if (! empty($tplidx)) $pdf->useTemplate($tplidx);
							$pdf->setPage($pageposbefore+1);
							
							$curY = $tab_top_newpage;
							$showpricebeforepagebreak=0;
						}
						
						$picture = false;						
						if (isset($imglinesize['width']) && isset($imglinesize['height']))
						{
							$curX = $this->getColumnContentXStart('picture')-1;
							$pdf->Image($realpatharray[$i], $curX, $curY, $imglinesize['width'], $imglinesize['height'], '', '', '', 2, 300);	// Use 300 dpi
							// $pdf->Image does not increase value return by getY, so we save it manually
							$posYAfterImage = $curY+$imglinesize['height'];
							$picture = true;							
						}
					}
					
					if ($picture) 
					{
						$nexY = $posYAfterImage;
					}
					
					if ($this->getColumnStatus('vat') == true) 
					{
						$vatorprice = $this->getColumnContentXStart('vat');
					}
					elseif ($this->getColumnStatus('subprice') == true)
					{
						$vatorprice = $this->getColumnContentXStart('subprice');
					}
					elseif ($this->getColumnStatus('discount') == true)
					{
						$vatorprice = $this->getColumnContentXStart('discount');
					}
					elseif ($this->getColumnStatus('upafter') == true)
					{
						$vatorprice = $this->getColumnContentXStart('upafter');
					}
					else
					{
						$vatorprice = $this->getColumnContentXStart('qty');
					}
					
					// Description of product line
					if ($conf->milestone->enabled && $object->lines[$i]->product_type == 9)
					{
						$curX = $this->getColumnContentXStart('desc') + 1.5;
						$text_length = ($picture ? $this->getColumnContentXStart('picture') - 2 : $vatorprice - 2);
					}
					else
					{
						$curX = $this->getColumnContentXStart('desc');						
						$text_length = ($picture ? $this->getColumnContentXStart('picture') : $vatorprice);
					}
					
					if ($this->getColumnStatus('desc'))
					{
						$pdf->startTransaction();
						if ($conf->global->ULTIMATE_DOCUMENTS_WITH_REF == "yes" && $this->atleastoneref == true) 
						{
							$hideref = 1;
						}
						else
						{
							$hideref = (GETPOST('hideref', 'int') ? GETPOST('hideref', 'int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_REF) ? 1 : 0));
						}
						$hidedetails = (GETPOST('hidedetails', 'int') ? GETPOST('hidedetails', 'int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DETAILS) ? 1 : 0));
						$hidedesc = (GETPOST('hidedesc', 'int') ? GETPOST('hidedesc', 'int') : (! empty($conf->global->MAIN_GENERATE_DOCUMENTS_HIDE_DESC) ?  1 : 0));

						$pageposbeforedesc = $pdf->getPage();
						$posYStartDescription = $curY;
						pdf_writelinedesc($pdf, $object, $i, $outputlangs, $text_length-$curX, 3, $curX, $curY, $hideref, $hidedesc);
						$posYAfterDescription=$pdf->GetY();
						$pageposafter = $pdf->getPage();
						
						if (!empty($product->barcode) && !empty($product->barcode_type_code) && $object->lines[$i]->product_type != 9 && $conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_PRODUCTS_BARCODE == 1)
						{
							// dysplay product barcode
							//function get_ean13_key(string $digits)
							$digits = $product->barcode;						
							$code = get_ean13_key($digits);					
							$pdf->write1DBarcode($product->barcode.$code, $product->barcode_type_code, $curX, $posYAfterDescription, '', 12, 0.4, $styleBc, 'L');
							$posYAfterDescription = $pdf->GetY();
							$pageposafter = $pdf->getPage();
						}										
						
						if ($pageposafter > $pageposbefore)	// There is a pagebreak
						{
							$posYAfterImage = $tab_top_newpage+$imglinesize['height'];
							$pdf->rollbackTransaction(true);
							$pageposbeforedesc = $pdf->getPage();
							$pageposafter=$pageposbefore;
							$posYStartDescription = $curY;
							$pdf->setPageOrientation('', 1, $heightforfooter);	// The only function to edit the bottom margin of current page to set it.
							pdf_writelinedesc($pdf, $object, $i, $outputlangs, $text_length-$curX, 3, $curX+1, $curY, $hideref, $hidedesc);
							$posYAfterDescription = $pdf->GetY();
							$pageposafter = $pdf->getPage();
							if (!empty($product->barcode) && !empty($product->barcode_type_code) && $object->lines[$i]->product_type != 9 && $conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_PRODUCTS_BARCODE == 1)
							{
								// dysplay product barcode
								//function get_ean13_key(string $digits)
								$digits = $product->barcode;						
								$code = get_ean13_key($digits);					
								$pdf->write1DBarcode($product->barcode.$code, $product->barcode_type_code, $curX, $posYAfterDescription, '', 12, 0.4, $styleBc, 'L');
								$posYAfterDescription = $pdf->GetY();
								$pageposafter = $pdf->getPage();
							}

							if ($posYAfterDescription > ($this->page_hauteur - $bMargin))	// There is no space left for total+free text
							{
								if ($i == ($nblignes-1))	// No more lines, and no space left to show total, so we create a new page
								{
									$pdf->AddPage('','',true);
									if (! empty($tplidx)) $pdf->useTemplate($tplidx);
									if (empty($conf->global->ULTIMATE_ORDERS_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
									$pdf->setPage($pageposafter+1);
								}
							}
							else
							{
								// We found a page break
								$showpricebeforepagebreak=1;
							}
						}
						else	// No pagebreak
						{
							$pdf->commitTransaction();
						}
						$posYAfterDescription=$pdf->GetY();
					}
					$nexY = max($pdf->GetY(), $posYAfterImage);
												
					$pageposafter=$pdf->getPage();

					$pdf->setPage($pageposbefore);
					$pdf->setTopMargin($this->marge_haute);
					$pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.		
					
					// We suppose that a too long description or photo were moved completely on next page
					if ($pageposafter>$pageposbefore && empty($showpricebeforepagebreak)) 
					{
						$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
					}
					if ($pageposafterRef>$pageposbefore && $posYafterRef < $posYStartRef)
					{
						$pdf->setPage($pageposbefore); $showpricebeforepagebreak=1;
					}
					if ($nexY>$curY && $pageposafter>$pageposbefore)	
					{
						$pdf->setPage($pageposafter); $curY = $tab_top_newpage+1;
					}
					if ($pageposbeforedesc<$pageposafterdesc)
					{
						$pdf->setPage($pageposbeforedesc); $curY = $posYStartDescription;
					}

					$pdf->SetFont('','',  $default_font_size - 2);   // On repositionne la police par defaut
					//test extrafields on line
					/*$object->lines[$i]->fetch_optionals($object->lines[$i]->id);
					$posxdate=$object->lines[$i]->array_options['options_confirm'];
					$pdf->SetXY($this->posxdate, $curY);
					$pdf->MultiCell($this->posxtva-$this->posxdate-0.8, 3, dol_print_date($posxdate,"day",false,$outputlangs,true), 0, 'C');*/
					
					if (($pageposafter>$pageposbefore) && ($pageposbeforedesc<$pageposafterdesc))
					{
						$pdf->setPage($pageposbefore); $curY = $posYStartDescription+1;
					}
					if ($posYStartDescription>$posYAfterDescription && $pageposafter>$pageposbefore)
					{
						$pdf->setPage($pageposbefore); $curY = $posYStartDescription;
					}
					if ($curY+4>($this->page_hauteur - $heightforfooter))	
					{			
						$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
					}

					//Line numbering
					if (! empty($conf->global->ULTIMATE_ORDERS_WITH_LINE_NUMBER))
					{
						// Numbering
						if ($this->getColumnStatus('num') && array_key_exists($i,$object->lines) && $object->lines[$i]->product_type != 9)
						{
							$this->printStdColumnContent($pdf, $curY, 'num', $line_number);
							//$nexY = max($pdf->GetY(),$nexY);
							$line_number++;
						}
					}
					
					//  Column reference
					if ($conf->global->ULTIMATE_DOCUMENTS_WITH_REF == "yes" && $this->atleastoneref == true)
					{
						if ($this->getColumnStatus('ref'))
						{
							$productRef = pdf_getlineref($object, $i, $outputlangs, $hidedetails);
							$this->printStdColumnContent($pdf, $curY, 'ref', $productRef);
							//$nexY = max($pdf->GetY(), $nexY);
						}
					}
					
					// VAT Rate
					if ($conf->global->ULTIMATE_GENERATE_DOCUMENTS_WITHOUT_VAT == "no" && empty($conf->global->ULTIMATE_SHOW_HIDE_VAT_COLUMN) && empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
					{
						// VAT Rate
						if ($this->getColumnStatus('vat'))
						{
							$vat_rate = pdf_getlinevatrate($object, $i, $outputlangs, $hidedetails);
							$this->printStdColumnContent($pdf, $curY, 'vat', $vat_rate);
							//$nexY = max($pdf->GetY(),$nexY);
						}
					}

					// Unit price before discount
					if (empty($conf->global->ULTIMATE_SHOW_HIDE_PUHT) && empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
					{
						if ($this->getColumnStatus('subprice'))
						{
							$up_excl_tax = pdf_getlineupexcltax($object, $i, $outputlangs, $hidedetails);
							$this->printStdColumnContent($pdf, $curY, 'subprice', $up_excl_tax);
							//$nexY = max($pdf->GetY(),$nexY);
						}
					}
					
					// Discount on line  
					if ($this->atleastonediscount && !empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_DISCOUNT))
					{					
						if ($this->getColumnStatus('discount') && $object->lines[$i]->remise_percent)
						{
							$remise_percent = pdf_getlineremisepercent($object, $i, $outputlangs, $hidedetails);
							$this->printStdColumnContent($pdf, $curY, 'discount', $remise_percent);
							//$nexY = max($pdf->GetY(),$nexY);
						}
					}
					
					// Unit price after discount
					if ($this->atleastonediscount && !empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_PUAFTER)) 
					{
						if ($remise_percent == dol_print_reduction(100, $langs))
						{
							$up_after = price(0);
							$this->printStdColumnContent($pdf, $curY, 'upafter', $up_after);
							//$nexY = max($pdf->GetY(),$nexY);
						}
						else
						{
							if ($this->getColumnStatus('upafter') && $object->lines[$i]->remise_percent > 0)
							{
								$up_after = price(price2num($up_excl_tax,'MU')* price2num(1 - price2num($remise_percent,'MU')/100,'MU'));
								$this->printStdColumnContent($pdf, $curY, 'upafter', $up_after);
								//$nexY = max($pdf->GetY(),$nexY);
							}
						}
					}
					
					// Quantity
					if (empty($conf->global->ULTIMATE_SHOW_HIDE_QTY))
					{
						if ($this->getColumnStatus('qty'))
						{
							$qty = pdf_getlineqty($object, $i, $outputlangs, $hidedetails);
							$this->printStdColumnContent($pdf, $curY, 'qty', $qty);
							//$nexY = max($pdf->GetY(),$nexY);
						}
					}
					
					// Weight
					if (!empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_WEIGHT_COLUMN))
					{
						if ($this->getColumnStatus('weight'))
						{
							$weight = pdf_getlineweight($object, $i, $outputlangs, $hidedetails);
							$this->printStdColumnContent($pdf, $curY, 'weight', $weight);
							//$nexY = max($pdf->GetY(),$nexY);
						}						
					}
					
					// Unit
					if($conf->global->PRODUCT_USE_UNITS)
					{
						if ($this->getColumnStatus('unit') && $qty == 0 && $object->lines[$i]->product_type != 9)
						{
							$unit = $object->lines[$i]->getLabelOfUnit('short');
							$this->printStdColumnContent($pdf, $curY, 'unit', $unit);
						}
						else
						{
							$unit = pdf_getlineunit($object, $i, $outputlangs, $hidedetails, $hookmanager);
							$this->printStdColumnContent($pdf, $curY, 'unit', $unit);
							//$nexY = max($pdf->GetY(),$nexY);
						}
					}

                     if (! empty($conf->global->ULTIMATE_SHOW_LINE_TTTC))
					 {
						// Total TTC line
						$hidedetails = ((! empty($conf->global->ULTIMATE_SHOW_HIDE_THT)) || (! empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))? 1 : 0);
						$total_incl_tax = pdf_getlinetotalwithtax($object, $i, $outputlangs, $hidedetails);

						if ($this->getColumnStatus('totalincltax'))
						{
							$this->printStdColumnContent($pdf, $curY, 'totalincltax', $total_incl_tax);
						}
					}
					else
					{
						// Total HT line
						$hidedetails = ((! empty($conf->global->ULTIMATE_SHOW_HIDE_THT)) || (! empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))? 1 : 0);
						$total_excl_tax = pdf_getlinetotalexcltax($object, $i, $outputlangs, $hidedetails);
						
						if ($this->getColumnStatus('totalexcltax'))
						{
							$this->printStdColumnContent($pdf, $curY, 'totalexcltax', $total_excl_tax);
						}
					}
					
					$parameters=array(
					    'object' => $object,
					    'i' => $i,
					    'pdf' =>& $pdf,
					    'curY' =>& $curY,
					    'nexY' =>& $nexY,
					    'outputlangs' => $outputlangs,
					    'hidedetails' => $hidedetails
					);
					$reshook=$hookmanager->executeHooks('printPDFline',$parameters,$this);    // Note that $object may have been modified by hook

					// Collecte des totaux par valeur de tva dans $this->tva["taux"]=total_tva
					if ($conf->multicurrency->enabled && $object->multicurrency_tx != 1) $tvaligne=$object->lines[$i]->multicurrency_total_tva;
					else $tvaligne=$object->lines[$i]->total_tva;
					
					$localtax1ligne=$object->lines[$i]->total_localtax1;
					$localtax2ligne=$object->lines[$i]->total_localtax2;
					$localtax1_rate=$object->lines[$i]->localtax1_tx;
					$localtax2_rate=$object->lines[$i]->localtax2_tx;
					$localtax1_type=$object->lines[$i]->localtax1_type;
					$localtax2_type=$object->lines[$i]->localtax2_type;

					if ($object->remise_percent) $tvaligne-=($tvaligne*$object->remise_percent)/100;
					if ($object->remise_percent) $localtax1ligne-=($localtax1ligne*$object->remise_percent)/100;
					if ($object->remise_percent) $localtax2ligne-=($localtax2ligne*$object->remise_percent)/100;

					$vatrate=(string) $object->lines[$i]->tva_tx;

					// Retrieve type from database for backward compatibility with old records
					if ((! isset($localtax1_type) || $localtax1_type=='' || ! isset($localtax2_type) || $localtax2_type=='') // if tax type not defined
					&& (! empty($localtax1_rate) || ! empty($localtax2_rate))) // and there is local tax
					{
						$localtaxtmp_array=getLocalTaxesFromRate($vatrate,0,$object->thirdparty,$mysoc);
						$localtax1_type = $localtaxtmp_array[0];
						$localtax2_type = $localtaxtmp_array[2];
					}

				    // retrieve global local tax
					if ($localtax1_type && $localtax1ligne != 0)
						$this->localtax1[$localtax1_type][$localtax1_rate]+=$localtax1ligne;
					if ($localtax2_type && $localtax2ligne != 0)
						$this->localtax2[$localtax2_type][$localtax2_rate]+=$localtax2ligne;

					if (($object->lines[$i]->info_bits & 0x01) == 0x01) $vatrate.='*';
					if (! isset($this->tva[$vatrate])) 				$this->tva[$vatrate]=0;
					$this->tva[$vatrate] += $tvaligne;
					
					if ($posYAfterImage > $posYAfterDescription) $nexY = $posYAfterImage;

					// Add line
					if (! empty($conf->global->ULTIMATE_ORDER_PDF_DASH_BETWEEN_LINES) && $i < ($nblignes - 1) && $object->lines[$i]->product_type != 9)
					{
						$pdf->setPage($pageposafter);
						$pdf->SetLineStyle(array('dash'=>'1, 1', 'color'=>array(70, 70, 70)));
						if ($conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_PRODUCTS_BARCODE == 1 && !empty($product->barcode))
						{
							$pdf->line($this->marge_gauche, $nexY+4, $this->page_largeur - $this->marge_droite, $nexY+4);
						}
						else
						{
							$pdf->line($this->marge_gauche, $nexY+1, $this->page_largeur - $this->marge_droite, $nexY+1);
						}
						$pdf->SetLineStyle(array('dash'=>0));
					}

					if ($conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_PRODUCTS_BARCODE == 1) 
					{
						$nexY+=5;    // Passe espace entre les lignes
					}
					else
					{
						$nexY+=2;    // Passe espace entre les lignes
					}

					// Detect if some page were added automatically and output _tableau for past pages
					while ($pagenb < $pageposafter)
					{
						$pdf->setPage($pagenb);
						if ($pagenb == $pageposbeforeprintlines)
						{
							$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, $hidetop, 1, $object->multicurrency_code);
						}
						else
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code);
						}
						$this->_pagefoot($pdf,$object,$outputlangs,1);
						$pagenb++;
						$pdf->setPage($pagenb);
						$pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.
						if (empty($conf->global->ULTIMATE_ORDERS_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
					}
					if (isset($object->lines[$i+1]->pagebreak) && $object->lines[$i+1]->pagebreak)
					{
						if ($pagenb == $pageposafter && $pagenb != 1)
						{
							$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, $hidetop, 1, $object->multicurrency_code);
						}
						else
						{
							$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code);
						}

						$this->_pagefoot($pdf,$object,$outputlangs,1);
						// New page
						$pdf->AddPage();
						if (! empty($tplidx)) $pdf->useTemplate($tplidx);
						$pagenb++;
						if (empty($conf->global->ULTIMATE_ORDERS_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
					}
				}

				// Show square
				if ($pagenb == $pageposbeforeprintlines)
				{
					$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, $hidetop, 0, $object->multicurrency_code);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}
				else
				{
					$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0, $object->multicurrency_code);
					$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				}
				

				// Affiche zone infos
				$posy=$this->_tableau_info($pdf, $object, $bottomlasttab, $outputlangs);

				// Affiche zone totaux
				$posy=$this->_tableau_tot($pdf, $object, $deja_regle, $bottomlasttab, $outputlangs);
				
				// Affiche zone agreement
				$posy=$this->_agreement($pdf, $object, $posy, $outputlangs);

				// Affiche zone versements
				/*if ($deja_regle)
				{
					$posy=$this->_tableau_versements($pdf, $object, $posy, $outputlangs);
				}*/

				// Pied de page
				$this->_pagefoot($pdf,$object,$outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();
				
				// Add PDF to be merged
				if (!empty($conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_MERGED_PDF))
				{
					dol_include_once ( '/ultimatepdf/class/documentmergedpdf.class.php' );
					
					$already_merged=array();
					
					if (! empty ($object->id) && !(in_array($object->id, $already_merged))) 
					{					
						// Find the desire PDF
						$filetomerge = new DocumentMergedPdf($this->db);					
						$filetomerge->fetch_by_element($object);							
						$already_merged[]= $object->id;
							
						// If PDF is selected and file is not empty
						if (count ($filetomerge->lines) > 0) 
						{
							foreach ($filetomerge->lines as $linefile) 
							{									
								if (! empty($linefile->id) && ! empty($linefile->file_name)) 
								{
									if (! empty ($conf->commande->enabled))
									$filetomerge_dir = $conf->commande->dir_output.'/'. dol_sanitizeFileName($object->ref);
									
									$infile = $filetomerge_dir.'/'.$linefile->file_name;
									dol_syslog(get_class($this).'::$upload_dir='. $filetomerge_dir, LOG_DEBUG);
									// If file really exists
									if (is_file($infile)) 
									{													
										$count = $pdf->setSourceFile($infile);
										// import all page
										for($i = 1; $i <= $count; $i ++) 
										{
											// New page
											$pdf->AddPage();
											$tplIdx = $pdf->importPage($i);
											$pdf->useTemplate($tplIdx, 0, 0, $this->page_largeur);
											if (method_exists($pdf, 'AliasNbPages'))
												$pdf->AliasNbPages();
										}
									}
								}
							}
						}
					}
				}

				$pdf->Close();

				$pdf->Output($file,'F');

				// Add pdfgeneration hook
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters=array('file'=>$file,'object'=>$object,'outputlangs'=>$outputlangs);
				global $action;
				$reshook=$hookmanager->executeHooks('afterPDFCreation', $parameters, $this, $action);    // Note that $action and $object may have been modified by some hooks

				if (! empty($conf->global->MAIN_UMASK))
					@chmod($file, octdec($conf->global->MAIN_UMASK));
				
				$this->result = array('fullpath'=>$file);
				
				return 1;   // Pas d'erreur
			}
			else
			{
				$this->error=$langs->transnoentities("ErrorCanNotCreateDir",$dir);
				return 0;
			}
		}
		else
		{
			$this->error=$langs->transnoentities("ErrorConstantNotDefined","COMMANDE_OUTPUTDIR");
			return 0;
		}
		$this->error=$langs->transnoentities("ErrorUnknown");
		
		unset($_SESSION['ultimatepdf_model']);
		
		return 0;   // Erreur par defaut
	}

	/**
	 *  Show payments table
     *
	 *  @param	PDF			&$pdf     		Object PDF
	 *  @param  Object		$object			Object order
	 *	@param	int			$posy			Position y in PDF
	 *	@param	Translate	$outputlangs	Object langs for output
	 *	@return int							<0 if KO, >0 if OK
	 */
	function _tableau_versements(&$pdf, $object, $posy, $outputlangs)
	{

	}


	/**
	 *   Show miscellaneous information (payment mode, payment term, ...)
	 *
	 *   @param		TCPDF		&$pdf     		Object PDF
	 *   @param		Object		$object			Object to show
	 *   @param		int			$posy			Y
	 *   @param		Translate	$outputlangs	Langs object
	 *   @return	void
	 */
	function _tableau_info(&$pdf, $object, $posy, $outputlangs)
	{
		global $conf;
		
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		$textcolor = array('25','25','25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor =  html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}

		$pdf->SetFont('','', $default_font_size - 1);

        // If France, show VAT mention if not applicable
		if ($this->emetteur->country_code == 'FR' && $this->franchise == 1)
		{
			$pdf->SetFont('','B', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$pdf->MultiCell(100, 3, $outputlangs->transnoentities("VATIsNotUsedForInvoice"), 0, 'L', 0);

			$posy=$pdf->GetY()+4;
		}
		
		$widthrecbox=($this->page_largeur-$this->marge_gauche-$this->marge_droite-4)/2;
		
		// Show planed date of delivery
        if (! empty($object->date_livraison))
		{
            $outputlangs->load("sendings");
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre = '<strong>'.$outputlangs->transnoentities("DateDeliveryPlanned").'</strong>'.' : ';
			$dlp=dol_print_date($object->date_livraison,"daytext",false,$outputlangs,true);
			$pdf->writeHTMLCell($widthrecbox, 4, $this->marge_gauche, $posy, $titre .' '.$dlp, 0, 0, false, true, 'L', true);

            $posy=$pdf->GetY()+4;
		}
        elseif ($object->availability_code || $object->availability)    // Show availability conditions
		{
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetTextColorArray($textcolor);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre = '<strong>'.$outputlangs->transnoentities("AvailabilityPeriod").'</strong>'.' : ';
			$lib_availability=$outputlangs->transnoentities("AvailabilityType".$object->availability_code)!=('AvailabilityType'.$object->availability_code)?$outputlangs->transnoentities("AvailabilityType".$object->availability_code):$outputlangs->convToOutputCharset($object->availability);
			$lib_availability=str_replace('\n',"\n",$lib_availability);
			$pdf->writeHTMLCell($widthrecbox, 4, $this->marge_gauche, $posy, $titre .' '.$lib_availability, 0, 0, false, true, 'L', true);

			$posy=$pdf->GetY()+4;
		}
		
		// Show payments conditions
		if ($object->cond_reglement_code || $object->cond_reglement)
		{
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre = '<strong>'.$outputlangs->transnoentities("PaymentConditions").'</strong>'.' : ';		
			$lib_condition_paiement=$outputlangs->transnoentities("PaymentCondition".$object->cond_reglement_code)!=('PaymentCondition'.$object->cond_reglement_code)?$outputlangs->transnoentities("PaymentCondition".$object->cond_reglement_code):$outputlangs->convToOutputCharset($object->cond_reglement_doc);
			$lib_condition_paiement=str_replace('\n',"\n",$lib_condition_paiement);
			$pdf->writeHTMLCell($widthrecbox, 4,$this->marge_gauche,$posy, $titre.' '.$lib_condition_paiement, 0, 0, false, true, 'L', true);
	        
			$posy=$pdf->GetY()+7;
		}

      	// Show payment mode
        if ($object->mode_reglement_code
        && $object->mode_reglement_code != 'CHQ'
        && $object->mode_reglement_code != 'VIR')
        {
	        $pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre = '<strong>'.$outputlangs->transnoentities("PaymentMode").'</strong>'.' : ';
			$lib_mode_reg=$outputlangs->transnoentities("PaymentType".$object->mode_reglement_code)!=('PaymentType'.$object->mode_reglement_code)?$outputlangs->transnoentities("PaymentType".$object->mode_reglement_code):$outputlangs->convToOutputCharset($object->mode_reglement);
			$pdf->writeHTMLCell($widthrecbox, 4,$this->marge_gauche,$posy, $titre.' '.$lib_mode_reg, 0, 0, false, true, 'L', true);

			$posy=$pdf->GetY()+4;
        }
		
		// Auto-liquidation régime de la sous-traitance
		if (! empty($conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_AUTO_LIQUIDATION))
		{
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($this->marge_gauche, $posy);
			$titre1 = '<strong>'.$outputlangs->transnoentities("AutoLiquidation1").'</strong>';			
			$titre2 = $outputlangs->transnoentities("AutoLiquidation2");
			$pdf->writeHTMLCell($widthrecbox, 4, $this->marge_gauche, $posy, $titre1 .' '.$titre2, 0, 0, false, true, 'L', true);
			
			$posy=$pdf->GetY()+7;
		}
		
		// Example using extrafields
		$title_key=(empty($object->array_options['options_newline']))?'':($object->array_options['options_newline']);
		$extrafields = new ExtraFields ( $this->db );
		$extralabels = $extrafields->fetch_name_optionals_label ( $object->table_element, true );
		if (is_array ( $extralabels ) && key_exists ( 'newline', $extralabels ) && !empty($title_key)) {
			$pdf->SetXY($this->marge_gauche, $posy);
			$title = $extrafields->showOutputField ( 'newline', $title_key );
			$pdf->writeHTMLCell($widthrecbox, 4, $this->marge_gauche, $posy, $title, 0, 0, false, true, 'L', true);
			
			$posy=$pdf->GetY()+7;
		}

		// Show payment mode CHQ
        if (empty($object->mode_reglement_code) || $object->mode_reglement_code == 'CHQ')
        {
        	// Si mode reglement non force ou si force a CHQ
	        if (! empty($conf->global->FACTURE_CHQ_NUMBER))
	        {
				$diffsizetitle=(empty($conf->global->PDF_DIFFSIZE_TITLE)?3:$conf->global->PDF_DIFFSIZE_TITLE);
				
	            if ($conf->global->FACTURE_CHQ_NUMBER > 0)
	            {					
	                $account = new Account($this->db);
	                $account->fetch($conf->global->FACTURE_CHQ_NUMBER);

	                $pdf->SetXY($this->marge_gauche, $posy);
	                $pdf->SetFont('','B', $default_font_size - $diffsizetitle);
	                $pdf->MultiCell($widthrecbox, 3, $outputlangs->transnoentities('PaymentByChequeOrderedTo',$account->proprio),0,'L',0);
		            $posy=$pdf->GetY()+1;

		            if (empty($conf->global->MAIN_PDF_HIDE_CHQ_ADDRESS))
		            {
		                $pdf->SetXY($this->marge_gauche, $posy);
		                $pdf->SetFont('','', $default_font_size - $diffsizetitle);
		                $pdf->MultiCell($widthrecbox, 3, $outputlangs->convToOutputCharset($account->owner_address), 0, 'L', 0);
			            $posy=$pdf->GetY()+2;
		            }
	            }
	            if ($conf->global->FACTURE_CHQ_NUMBER == -1)
	            {
	                $pdf->SetXY($this->marge_gauche, $posy);
	                $pdf->SetFont('','B', $default_font_size - $diffsizetitle);
	                $pdf->MultiCell($widthrecbox, 3, $outputlangs->transnoentities('PaymentByChequeOrderedToShort').' '.$outputlangs->convToOutputCharset($this->emetteur->name).' '.$outputlangs->transnoentities('SendTo').':',0,'L',0);
		            $posy=$pdf->GetY()+1;

		            if (empty($conf->global->MAIN_PDF_HIDE_CHQ_ADDRESS))
		            {
			            $pdf->SetXY($this->marge_gauche, $posy);
		                $pdf->SetFont('','', $default_font_size - $diffsizetitle);
		                $pdf->MultiCell($widthrecbox, 3, $outputlangs->convToOutputCharset($this->emetteur->getFullAddress()), 0, 'L', 0);
			            $posy=$pdf->GetY()+2;
		            }
	            }
	        }
		}

        // If payment mode not forced or forced to VIR, show payment with BAN
        if (empty($object->mode_reglement_code) || $object->mode_reglement_code == 'VIR')
        {
        	if (! empty($object->fk_account) || ! empty($object->fk_bank) || ! empty($conf->global->FACTURE_RIB_NUMBER))
			{
				$bankid=(empty($object->fk_account)?$conf->global->FACTURE_RIB_NUMBER:$object->fk_account);
				if (! empty($object->fk_bank)) $bankid=$object->fk_bank;   // For backward compatibility when object->fk_account is forced with object->fk_bank
				$account = new Account($this->db);
				$account->fetch($bankid);

				$curx=$this->marge_gauche;
				$cury=$posy;

				$posy=pdf_bank($pdf,$outputlangs,$curx,$cury,$account,0,$default_font_size);

				$posy+=2;
			}
        }
		return $posy;
	}

	/**
	 *	Show total to pay
	 *
	 *	@param	PDF			&$pdf           Object PDF
	 *	@param  Facture		$object         Object invoice
	 *	@param  int			$deja_regle     Montant deja regle
	 *	@param	int			$posy			Position depart
	 *	@param	Translate	$outputlangs	Objet langs
	 *	@return int							Position pour suite
	 */
	function _tableau_tot(&$pdf, $object, $deja_regle, $posy, $outputlangs)
	{
	   global $conf, $mysoc, $langs;

		$default_font_size = pdf_getPDFFontSize($outputlangs);
		
		if (!empty($conf->global->ULTIMATE_DASH_DOTTED))
		{
			$dashdotted = $conf->global->ULTIMATE_DASH_DOTTED;
		}
		if(!empty($conf->global->ULTIMATE_BORDERCOLOR_COLOR))
		{
			$bordercolor = html2rgb($conf->global->ULTIMATE_BORDERCOLOR_COLOR);
		}
		$bgcolor = array('170','212','255');
		if (!empty($conf->global->ULTIMATE_BGCOLOR_COLOR))
		{
			$bgcolor =  html2rgb($conf->global->ULTIMATE_BGCOLOR_COLOR);
		}
		$opacity = 0.5;
		if (!empty($conf->global->ULTIMATE_BGCOLOR_OPACITY))
		{
			$opacity =  $conf->global->ULTIMATE_BGCOLOR_OPACITY;
		}
		if (!empty($conf->global->ULTIMATE_SET_RADIUS))
		{
			$roundradius = $conf->global->ULTIMATE_SET_RADIUS;
		}
		$textcolor = array('25','25','25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor =  html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}
		$this->border_style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' =>$dashdotted , 'color' => $bordercolor);

		$tab2_top = $posy;
		$tab2_hl = 4;
		$pdf->SetFont('', '', $default_font_size - 1);

		// Tableau total
		$col1x = $this->page_largeur/2 +2; $col2x = 150; 
		if ($this->page_largeur < 210) // To work with US executive format
		{
			$col2x-=20;
		}
		$largcol2 = ($this->page_largeur - $this->marge_droite - $col2x);
		
		$widthrecbox=($this->page_largeur-$this->marge_gauche-$this->marge_droite-4)/2;
		$deltax=$this->marge_gauche+$widthrecbox+4;
		if ($roundradius == 0) 
		{
			$roundradius = 0.1; //RoundedRect don't support $roundradius to be 0
		}
		$pdf->SetAlpha($opacity);	
		$pdf->RoundedRect($deltax, $tab2_top, $widthrecbox, 22, $roundradius, $round_corner = '1111', 'FD', $this->border_style, $bgcolor); 
		$pdf->SetAlpha(1);

		$useborder=0;
		$index = 0;

		// Total HT
		if (empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
		{
			$pdf->SetFillColor(255,255,255);
			$pdf->SetXY ($col1x, $tab2_top + 0);
			$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("TotalHT"), 0, 'L', 1);
			
			$total_ht = ($conf->multicurrency->enabled && $object->multicurrency_tx != 1 ? $object->multicurrency_total_ht : $object->total_ht);
			$pdf->SetXY ($col2x, $tab2_top + 0);
			$this->_setFontForMulticurrencyCode($pdf, $object, $outputlangs);
			$pdf->MultiCell($largcol2, $tab2_hl, price($total_ht + (! empty($object->remise)?$object->remise:0), 0, $outputlangs, 0, -1, -1, (! empty($object->multicurrency_code) ? $object->multicurrency_code : $conf->currency)), 0, 'R', 1);
			$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
		}

		// Show VAT by rates and total
		$pdf->SetFillColor(248,248,248);
		
		$total_ttc = ($conf->multicurrency->enabled && $object->multicurrency_tx != 1) ? $object->multicurrency_total_ttc : $object->total_ttc;

		$this->atleastoneratenotnull=0;
		if ($conf->global->ULTIMATE_GENERATE_DOCUMENTS_WITHOUT_VAT == "no")
		{
			$tvaisnull=((! empty($this->tva) && count($this->tva) == 1 && isset($this->tva['0.000']) && is_float($this->tva['0.000'])) ? true : false);
			if (! empty($conf->global->ULTIMATE_GENERATE_DOCUMENTS_WITHOUT_VAT_ISNULL) && $tvaisnull)
			{
				// Nothing to do
			}
			else
			{
				//Local tax 1 before VAT
				foreach( $this->localtax1 as $localtax_type => $localtax_rate )
				{
					if (in_array((string) $localtax_type, array('1','3','5'))) continue;
					
					foreach( $localtax_rate as $tvakey => $tvaval )
					{
						if ($tvakey!=0)    // On affiche pas taux 0
						{
							$index++;
							$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);

							$tvacompl='';
							if (preg_match('/\*/',$tvakey))
							{
								$tvakey=str_replace('*','',$tvakey);
								$tvacompl = " (".$outputlangs->transnoentities("NonPercuRecuperable").")";
							}
							$totalvat = $outputlangs->transcountrynoentities("TotalLT1",$mysoc->country_code).' ';
							if (empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
							{
								$totalvat.=vatrate(abs($tvakey),1).$tvacompl;
								$pdf->MultiCell($col2x-$col1x, $tab2_hl, $totalvat, 0, 'L', 1);

								$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
								$this->_setFontForMulticurrencyCode($pdf, $object, $outputlangs);
								$pdf->MultiCell($largcol2, $tab2_hl, price($tvaval, 0, $outputlangs, 1, -1, -1, $object->multicurrency_code), 0, 'R', 1);
								$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
							}
						}
					}
				}

				//Local tax 2  before VAT
				foreach( $this->localtax2 as $localtax_type => $localtax_rate )
				{
					if (in_array((string) $localtax_type, array('1','3','5'))) continue;
					
					foreach( $localtax_rate as $tvakey => $tvaval )
					{
						if ($tvakey!=0)    // On affiche pas taux 0
						{
							$index++;
							$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);

							$tvacompl='';
							if (preg_match('/\*/',$tvakey))
							{
								$tvakey=str_replace('*','',$tvakey);
								$tvacompl = " (".$outputlangs->transnoentities("NonPercuRecuperable").")";
							}
							$totalvat = $outputlangs->transcountrynoentities("TotalLT2",$mysoc->country_code).' ';
							$totalvat.=vatrate(abs($tvakey),1).$tvacompl;
							if (empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
							{
								$pdf->MultiCell($col2x-$col1x, $tab2_hl, $totalvat, 0, 'L', 1);

								$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
								$this->_setFontForMulticurrencyCode($pdf, $object, $outputlangs);
								$pdf->MultiCell($largcol2, $tab2_hl, price($tvaval, 0, $outputlangs, 1, -1, -1, $object->multicurrency_code), 0, 'R', 1);
								$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
							}
						}
					}
				}
				
				// Situations totals migth be wrong on huge amounts
				if ($object->situation_cycle_ref && $object->situation_counter > 1) {

					$sum_pdf_tva = 0;
					foreach($this->tva as $tvakey => $tvaval){
						$sum_pdf_tva+=$tvaval; // sum VAT amounts to compare to object
					}

					if($sum_pdf_tva!=$object->total_tva) { // apply coef to recover the VAT object amount (the good one)
						$coef_fix_tva = $object->total_tva / $sum_pdf_tva;

						foreach($this->tva as $tvakey => $tvaval) {
							$this->tva[$tvakey]=$tvaval * $coef_fix_tva;
						}
					}
				}
				
				// VAT
				foreach($this->tva as $tvakey => $tvaval)
				{
					if ($tvakey > 0)    // On affiche pas taux 0
					{
						$this->atleastoneratenotnull++;

						$index++;
						$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);

						$tvacompl='';
						if (preg_match('/\*/',$tvakey))
						{
							$tvakey=str_replace('*','',$tvakey);
							$tvacompl = " (".$outputlangs->transnoentities("NonPercuRecuperable").")";
						}
						$totalvat =$outputlangs->transnoentities("TotalVAT").' ';
						$totalvat.=vatrate($tvakey,1).$tvacompl;
						if (empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
						{
							$pdf->MultiCell($col2x-$col1x, $tab2_hl, $totalvat, 0, 'L', 1);

							$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
							$this->_setFontForMulticurrencyCode($pdf, $object, $outputlangs);
							$pdf->MultiCell($largcol2, $tab2_hl, price($tvaval, 0, $outputlangs, 1, -1, -1, $object->multicurrency_code), 0, 'R', 1);
							$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
						}
					}
				}

				//Local tax 1 after VAT
				foreach( $this->localtax1 as $localtax_type => $localtax_rate )
				{
					if (in_array((string) $localtax_type, array('2','4','6'))) continue;

					foreach( $localtax_rate as $tvakey => $tvaval )
					{
						if ($tvakey != 0)    // On affiche pas taux 0
						{
							$index++;
							$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);

							$tvacompl='';
							if (preg_match('/\*/',$tvakey))
							{
								$tvakey=str_replace('*','',$tvakey);
								$tvacompl = " (".$outputlangs->transnoentities("NonPercuRecuperable").")";
							}
							$totalvat = $outputlangs->transcountrynoentities("TotalLT1",$mysoc->country_code).' ';
							
							$totalvat.=vatrate(abs($tvakey),1).$tvacompl;
							if (empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
							{
								$pdf->MultiCell($col2x-$col1x, $tab2_hl, $totalvat, 0, 'L', 1);
								$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
								$this->_setFontForMulticurrencyCode($pdf, $object, $outputlangs);
								$pdf->MultiCell($largcol2, $tab2_hl, price($tvaval, 0, $outputlangs, 1, -1, -1, $object->multicurrency_code), 0, 'R', 1);
								$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
							}
						}
					}
				}

				//Local tax 2  after VAT
				foreach( $this->localtax2 as $localtax_type => $localtax_rate )
				{
					if (in_array((string) $localtax_type, array('2','4','6'))) continue;

					foreach( $localtax_rate as $tvakey => $tvaval )
					{
						if ($tvakey != 0)    // On affiche pas taux 0
						{
							$index++;
							$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);

							$tvacompl='';
							if (preg_match('/\*/',$tvakey))
							{
								$tvakey=str_replace('*','',$tvakey);
								$tvacompl = " (".$outputlangs->transnoentities("NonPercuRecuperable").")";
							}
							$totalvat = $outputlangs->transcountrynoentities("TotalLT2",$mysoc->country_code).' ';
								
							$totalvat.=vatrate($tvakey,1).$tvacompl;
							if (empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
							{
								$pdf->MultiCell($col2x-$col1x, $tab2_hl, $totalvat, 0, 'L', 1);

								$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
								$this->_setFontForMulticurrencyCode($pdf, $object, $outputlangs);
								$pdf->MultiCell($largcol2, $tab2_hl, price($tvaval, 0, $outputlangs, 1, -1, -1, $object->multicurrency_code), 0, 'R', 1);
								$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
							}
						}
					}
				}

				// Total TTC
				if (empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
				{
					$index++;
					$pdf->SetXY ($col1x, $tab2_top + $tab2_hl * $index);
					$pdf->SetTextColorArray($textcolor);
					$pdf->SetFont('','B',$default_font_size );
					$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("TotalTTC"), $useborder, 'L', 1);
					
					//$total_ttc = ($conf->multicurrency->enabled && $object->multiccurency_tx != 1) ? $object->multicurrency_total_ttc : $object->total_ttc;
					$pdf->SetXY ($col2x, $tab2_top + $tab2_hl * $index);
					$this->_setFontForMulticurrencyCode($pdf, $object, $outputlangs);
					$pdf->MultiCell($largcol2, $tab2_hl, price($total_ttc, 0, $outputlangs, 1, -1, -1, $object->multicurrency_code), $useborder, 'R', 1);
					$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
				}
			}
		}
		else
		{
			// Total TTC without VAT
			if (empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
			{
				$index++;
				$pdf->SetXY ($col1x, $tab2_top + 0);
				$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("TotalHT"), 0, 'L', 1);
				$pdf->SetXY ($col1x, $tab2_top + $tab2_hl * $index);
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','B',$default_font_size );
				$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("TotalTTC"), $useborder, 'L', 1);

				$pdf->SetXY ($col2x, $tab2_top + $tab2_hl * $index);
				$this->_setFontForMulticurrencyCode($pdf, $object, $outputlangs);
				$pdf->MultiCell($largcol2, $tab2_hl, price($total_ht + (! empty($object->remise)?$object->remise:0),0,$outputlangs), 0, 'R', 1);
				$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
			}			
		}			
		$pdf->SetTextColorArray($textcolor);

		$creditnoteamount=0;
        $depositsamount=0;
		$resteapayer = price2num($total_ttc - $deja_regle - $creditnoteamount - $depositsamount, 'MT');
		if (! empty($object->paye)) $resteapayer=0;

		if ($deja_regle > 0)
		{
			// Already paid + Deposits
			$index++;

			$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);
			$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("AlreadyPaid"), 0, 'L', 0);
			$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
			$pdf->MultiCell($largcol2, $tab2_hl, price($deja_regle, 0, $outputlangs), 0, 'R', 0);

			$index++;
			$pdf->SetTextColor(0,0,60);
			$pdf->SetFillColor(224,224,224);
			$pdf->SetXY($col1x, $tab2_top + $tab2_hl * $index);
			$pdf->MultiCell($col2x-$col1x, $tab2_hl, $outputlangs->transnoentities("RemainderToPay"), $useborder, 'L', 1);

			$pdf->SetXY($col2x, $tab2_top + $tab2_hl * $index);
			$pdf->MultiCell($largcol2, $tab2_hl, price($resteapayer, 0, $outputlangs), $useborder, 'R', 1);

			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->SetTextColorArray($textcolor);
		}

		$index++;
		return ($tab2_top + ($tab2_hl * $index));
	}
	
	/**
	 *	Show good for agreement
	 *
	 *	@param	PDF			&$pdf           Object PDF
	 *  @param	Object		$object			Object to show
	 *	@param	int			$posy			Position depart
	 *	@param	Translate	$outputlangs	Objet langs
	 *	@return int							Position pour suite
	 */
	function _agreement(&$pdf, $object, $posy, $outputlangs)
	{
		global $conf,$langs;
		
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		if (!empty($conf->global->ULTIMATE_SET_RADIUS))
		{
			$roundradius = $conf->global->ULTIMATE_SET_RADIUS;
		}
		if (!empty($conf->global->ULTIMATE_DASH_DOTTED))
		{
			$dashdotted = $conf->global->ULTIMATE_DASH_DOTTED;
		}
		if(!empty($conf->global->ULTIMATE_BORDERCOLOR_COLOR))
		{
			$bordercolor = html2rgb($conf->global->ULTIMATE_BORDERCOLOR_COLOR);
		}
		$textcolor = array('25','25','25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor =  html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}
		$this->border_style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' =>$dashdotted , 'color' => $bordercolor);
		$widthrecbox=($this->page_largeur-$this->marge_gauche-$this->marge_droite-4)/2;
		
		if (! empty($conf->global->ULTIMATE_DISPLAY_ORDER_AGREEMENT_BLOCK))
	    {
			$heightforfreetext= (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT)?$conf->global->MAIN_PDF_FREETEXT_HEIGHT:12);	// Height reserved to output the free text on last page
			$heightforfooter = $this->marge_basse + 12;	// Height reserved to output the footer (value include bottom margin)
			$heightforinfotot = 35;	// Height reserved to output the info and total part
			$deltay=$this->page_hauteur-$heightforfreetext-$heightforfooter-$heightforinfotot;	
			$posy=max($posy,$deltay);
			$deltax=$this->marge_gauche+$widthrecbox+4;
			if ($roundradius == 0) 
			{
				$roundradius = 0.1; //RoundedRect don't support $roundradius to be 0
			}
			$pdf->RoundedRect($deltax, $posy, $widthrecbox, 40, $roundradius, $round_corner = '1111', 'S', $this->border_style, array());
			$pdf->SetFont('','B', $default_font_size - 1);
			$pdf->SetXY($deltax, $posy);
			$titre = $outputlangs->transnoentities('DocORDER1'); 
			$pdf->MultiCell(80, 5, $titre, 0, 'L',0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($deltax, $posy+5);
			$pdf->SetFont('','I', $default_font_size - 2);
			$pdf->MultiCell(90, 3, $outputlangs->transnoentities('DocORDER2'),0,'L',0);
			$pdf->SetXY($deltax, $posy+12);
			
			if ( !empty($conf->global->ULTIMATEPDF_DISPLAY_ORDER_ELECTRONIC_SIGNATURE))
			{
				$pdf->SetFont('','I', $default_font_size - 2);
				$text = $outputlangs->transnoentities('MadeTo') .' '. $object->thirdparty->town .' '. $outputlangs->transnoentities('The').' '. dol_print_date(dol_now(), 'daytext');
				$pdf->MultiCell(80, 3, $text, 0, 'L', 0);
				$posy=$pdf->GetY()+4;			
				$sigdir = $conf->ultimatepdf->dir_output.'/orders/temp';
				dol_mkdir($sigdir);
				$signature = $sigdir .'/'.$object->id.'_signature.png';
				if (is_readable($signature)) {
					// insert signature image 
					$height = max(pdf_getHeightForLogo($signature), 15);
					$pdf->Image($signature, $deltax+18, $posy, 0, $height); // width=0 (auto)
				} 
				else 
				{
					$pdf->SetTextColor(200, 0, 0);
					$pdf->SetXY($deltax, $posy);
					$pdf->SetFont('', '', $default_font_size - 2);
					$pdf->MultiCell($widthrecbox, 3, $outputlangs->transnoentities("ErrorFileNotFound", $signature), 0, 'L');
					$pdf->SetTextColorArray($textcolor);
				}
			}
			else
			{
				$pdf->MultiCell(80, 3, $outputlangs->transnoentities('DocORDER3'), 0, 'L', 0);
			
				$posy=$pdf->GetY()+4;			
				$pdf->SetXY($deltax, $posy);
				$pdf->SetFont('','I', $default_font_size - 2);
				$pdf->MultiCell(80, 3, $outputlangs->transnoentities('DocORDER4'), 0, 'L', 0);
				$posy=$pdf->getY();
			}

			return $posy;
		}
	}

	/**
	 *   Show table for lines
	 *
	 *   @param		PDF			&$pdf     		Object PDF
	 *   @param		string		$tab_top		Top position of table
	 *   @param		string		$tab_height		Height of table (rectangle)
	 *   @param		int			$nexY			Y (not used)
	 *   @param		Translate	$outputlangs	Langs object
	 *   @param		int			$hidetop		1=Hide top bar of array and title, 0=Hide nothing, -1=Hide only title
	 *   @param		int			$hidebottom		Hide bottom bar of array
	 *   @return	void
	 */
	function _tableau(&$pdf, $tab_top, $tab_height, $nexY, $outputlangs, $hidetop=0, $hidebottom=0)
	{
		global $conf, $langs;
		
		$outputlangs->load("ultimatepdf@ultimatepdf");

		// Force to disable hidetop and hidebottom
		$hidebottom=0;
		if ($hidetop) $hidetop=-1;

		$default_font_size = pdf_getPDFFontSize($outputlangs);
		$opacity = 0.5;
		if (!empty($conf->global->ULTIMATE_BGCOLOR_OPACITY))
		{
			$opacity =  $conf->global->ULTIMATE_BGCOLOR_OPACITY;
		}
		if (!empty($conf->global->ULTIMATE_SET_RADIUS))
		{
			$roundradius = $conf->global->ULTIMATE_SET_RADIUS;
		}
		if (!empty($conf->global->ULTIMATE_DASH_DOTTED))
		{
			$dashdotted = $conf->global->ULTIMATE_DASH_DOTTED;
		}
		$bgcolor = array('170','212','255');
		if (!empty($conf->global->ULTIMATE_BGCOLOR_COLOR))
		{
			$bgcolor =  html2rgb($conf->global->ULTIMATE_BGCOLOR_COLOR);
		}
		if(!empty($conf->global->ULTIMATE_BORDERCOLOR_COLOR))
		{
			$bordercolor = html2rgb($conf->global->ULTIMATE_BORDERCOLOR_COLOR);
		}
		$textcolor = array('25','25','25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor =  html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}
		$this->border_style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => $dashdotted , 'color' => $bordercolor);

		// Amount in (at tab_top - 1)
		$pdf->SetTextColorArray($textcolor);
		$pdf->SetFillColorArray($bgcolor);
		$pdf->SetFont('','', $default_font_size - 2);
		if ($roundradius == 0) 
		{
			$roundradius = 0.1; //RoundedRect don't support $roundradius to be 0
		}
		$pdf->SetAlpha($opacity);
		$pdf->RoundedRect($this->marge_gauche, $tab_top-8, $this->page_largeur-$this->marge_gauche-$this->marge_droite, 8, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
		$pdf->SetAlpha(1);
		//title line
		$pdf->RoundedRect($this->marge_gauche, $tab_top, $this->page_largeur-$this->marge_gauche-$this->marge_droite, $tab_height, $roundradius, $round_corner = '0110', 'S', $this->border_style, $bgcolor);
		
		$this->pdfTabTitles($pdf, $tab_top-8, $tab_height+8, $outputlangs, $hidetop);
	}

	/**
	 *  Show top header of page.
	 *
	 *  @param	PDF			&$pdf     		Object PDF
	 *  @param  Object		$object     	Object to show
	 *  @param  int	    	$showaddress    0=no, 1=yes
	 *  @param  Translate	$outputlangs	Object lang for output
	 *  @param	string		$titlekey		Translation key to show as title of document
	 *  @return	void
	 */
	protected function _pagehead(&$pdf, $object, $showaddress, $outputlangs, $titlekey="")
	{
		global $conf, $langs, $hookmanager;
		
		// Translations
		$outputlangs->loadLangs(array("main", "dict", "companies", "bills", "propal", "orders", "deliveries", "commercial", "ultimatepdf@ultimatepdf"));
		
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		if (!empty($conf->global->ULTIMATE_DASH_DOTTED))
		{
			$dashdotted = $conf->global->ULTIMATE_DASH_DOTTED;
		}
		$bgcolor = array('170','212','255');
		if (!empty($conf->global->ULTIMATE_BGCOLOR_COLOR))
		{
			$bgcolor =  html2rgb($conf->global->ULTIMATE_BGCOLOR_COLOR);
		}
		if (!empty($conf->global->ULTIMATE_SENDER_STYLE))
		{
			$senderstyle = $conf->global->ULTIMATE_SENDER_STYLE;
		}
		if (!empty($conf->global->ULTIMATE_RECEIPT_STYLE))
		{
			$receiptstyle = $conf->global->ULTIMATE_RECEIPT_STYLE;
		}
		if(!empty($conf->global->ULTIMATE_BORDERCOLOR_COLOR))
		{
			$bordercolor = html2rgb($conf->global->ULTIMATE_BORDERCOLOR_COLOR);
		}
		$opacity = 0.5;
		if (!empty($conf->global->ULTIMATE_BGCOLOR_OPACITY))
		{
			$opacity =  $conf->global->ULTIMATE_BGCOLOR_OPACITY;
		}
		if (!empty($conf->global->ULTIMATE_SET_RADIUS))
		{
			$roundradius = $conf->global->ULTIMATE_SET_RADIUS;
		}
		$textcolor = array('25', '25', '25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor =  html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}
		$qrcodecolor = array('25', '25', '25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$qrcodecolor =  html2rgb($conf->global->ULTIMATE_QRCODECOLOR_COLOR);
		}	

		//affiche repere de pliage	
		if (! empty($conf->global->MAIN_DISPLAY_ORDERS_FOLD_MARK))
		{
			$pdf->Line(0, ($this->page_hauteur) / 3, 3, ($this->page_hauteur) / 3);
		}

		pdf_pagehead($pdf, $outputlangs, $this->page_hauteur);

		//Affiche le filigrane brouillon - Print Draft Watermark
		if($object->statut == 0 && (! empty($conf->global->COMMANDE_DRAFT_WATERMARK)) )
		{
            pdf_watermark($pdf, $outputlangs, $this->page_hauteur, $this->page_largeur, 'mm', $conf->global->COMMANDE_DRAFT_WATERMARK);
		}

		$pdf->SetTextColorArray($textcolor);
		$pdf->SetFont('', 'B', $default_font_size + 3);

		$posy = $this->marge_haute;
		$posx = $this->page_largeur - $this->marge_droite - 100;
		$main_page = $this->page_largeur - $this->marge_gauche - $this->marge_droite;

		$pdf->SetXY($this->marge_gauche, $posy);

		// Other Logo
		$id = $conf->global->ULTIMATE_DESIGN;
        $upload_dir	= $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/';	
        $filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview.*\.png)$', 'name', 0, 1);
        $otherlogo = $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/'.$filearray[0][name];
       
        if (! empty ($conf->global->ULTIMATE_DESIGN) && ! empty ($conf->global->ULTIMATE_OTHERLOGO_FILE) && is_readable($otherlogo) && ! empty($filearray))
        {
            $logo_height = max(pdf_getUltimateHeightForOtherLogo($otherlogo, true), 20);
            $pdf->Image($otherlogo, $this->marge_gauche, $posy, 0, $logo_height);	// width=0 (auto)
        }
		else
		{
			// Logo					
			if (empty($conf->global->PDF_DISABLE_MYCOMPANY_LOGO))
			{
				if ($this->emetteur->logo)
				{						
					$logodir = $conf->mycompany->dir_output;
					if (! empty($conf->mycompany->multidir_output[$object->entity])) $logodir = $conf->mycompany->multidir_output[$object->entity];
					if (empty($conf->global->MAIN_PDF_USE_LARGE_LOGO))
					{
						$logo = $logodir.'/logos/thumbs/'.$this->emetteur->logo_small;
					}
					else 
					{
						$logo = $logodir.'/logos/'.$this->emetteur->logo;
					}
					if (is_readable($logo))
					{
						$logo_height = max(pdf_getUltimateHeightForLogo($logo, true), 20);
						$pdf->Image($logo, $this->marge_gauche, $posy, 0, $logo_height);	// width=0 (auto)
					}
					else
					{
						$pdf->SetTextColor(200, 0, 0);
						$pdf->SetFont('', 'B', $default_font_size - 2);
						$pdf->RoundedRect($this->marge_gauche, $this->marge_haute, 100, 20, $roundradius, $round_corner = '1111', $senderstyle, $this->border_style, $bgcolor);
						$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorLogoFileNotFound", $logo), 0, 'L');
						$pdf->MultiCell(100, 3, $outputlangs->transnoentities("ErrorGoToGlobalSetup"), 0, 'L');
					}
				}
				else
				{
					$pdf->RoundedRect($this->marge_gauche, $this->marge_haute, 100, 20, $roundradius, $round_corner = '1111', $senderstyle, $this->border_style, $bgcolor);
					$text = $this->emetteur->name;
					$pdf->MultiCell(100, 4, $outputlangs->convToOutputCharset($text), 0, 'L');
					$logo_height = 20;
				}
			}
		}
		
		//Display Thirdparty barcode at top				
		if (! empty($conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_TOP_BARCODE))
		{
			$result=$object->thirdparty->fetch_barcode();
			$barcode=$object->thirdparty->barcode;	
			$posxbarcode=$this->page_largeur*2/3;
			$posybarcode=$posy-$this->marge_haute;
			$pdf->SetXY($posxbarcode,$posy-$this->marge_haute);
			$styleBc = array(
				'position' => '',
				'align' => 'R',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'hpadding' => 'auto',
				'vpadding' => 'auto',
				'fgcolor' => array(0,0,0),
				'bgcolor' => false, //array(255,255,255),
				'text' => true,
				'font' => 'helvetica',
				'fontsize' => 8,
				'stretchtext' => 4
				);
			if ($barcode <= 0)
			{
				$error++;
				if (empty($this->messageErrBarcodeSet)) 
				{
					setEventMessages($outputlangs->trans("BarCodeDataForThirdpartyMissing"), null, 'errors');
					$this->messageErrBarcodeSet=true;
				}
			}	
			else
			{
				// barcode_type_code
				$pdf->write1DBarcode($barcode, $object->thirdparty->barcode_type_code, $posxbarcode, $posybarcode, '', 12, 0.4, $styleBc, 'R');
			}
		}
		
		if ($logo_height<=30) 
		{
			$heightQRcode=$logo_height;
		}
		else 
		{
			$heightQRcode=30;
		}
		$posxQRcode=$this->page_largeur/2 +15;		
		// set style for QRcode
		$styleQr = array(
		'border' => false,
		'vpadding' => 'auto',
		'hpadding' => 'auto',
		'fgcolor' => $qrcodecolor,
		'bgcolor' => false, //array(255,255,255)
		'module_width' => 1, // width of a single module in points
		'module_height' => 1 // height of a single module in points
		);
		// Order link QRcode
		if (! empty($conf->global->ULTIMATEPDF_GENERATE_ORDERLINK_WITH_TOP_QRCODE))
		{
			$code = pdf_codeOrderLink(); //get order link
			$pdf->write2DBarcode($code, 'QRCODE,L', $posxQRcode, $posy, $heightQRcode, $heightQRcode, $styleQr, 'N');
		}
		// ThirdParty QRcode
		if (! empty($conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_TOP_QRCODE))
		{
			$code = pdf_codeContents(); //get order link
			$pdf->write2DBarcode($code, 'QRCODE,L', $posxQRcode, $posy, $heightQRcode, $heightQRcode, $styleQr, 'N');
		}
		// My Company QR-code
		if (! empty($conf->global->ULTIMATEPDF_GENERATE_ORDERS_WITH_MYCOMP_QRCODE))
		{
			$code = pdf_mycompCodeContents();
			$pdf->write2DBarcode($code, 'QRCODE,L', $posxQRcode, $posy, $heightQRcode, $heightQRcode, $styleQr, 'N');
		}
		
		// Example using extrafields for new title of document
		$title_key=(empty($object->array_options['options_newtitle']))?'':($object->array_options['options_newtitle']);	
		$extrafields = new ExtraFields($this->db);
		$extralabels = $extrafields->fetch_name_optionals_label ($object->table_element, true);
		if (is_array($extralabels ) && key_exists('newtitle', $extralabels) && !empty($title_key)) 
		{
			$titlekey = $extrafields->showOutputField ('newtitle', $title_key);
		}

		$pdf->SetFont('','B', $default_font_size + 3);
		$pdf->SetXY($posx, $posy);
		$pdf->SetTextColorArray($textcolor);
		$standardtitle = $outputlangs->transnoentities("PdfOrderTitle");
		$title=(empty($outputlangs->transnoentities($titlekey))?$standardtitle:$outputlangs->transnoentities($titlekey));
		$pdf->MultiCell(100, 4, $title, '', 'R');
		
		$pdf->SetFont('','B', $default_font_size + 2);
		
		$posy=$pdf->getY();
		$pdf->SetXY($posx, $posy);
		$pdf->SetTextColorArray($textcolor);
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Ref")." : " . $outputlangs->convToOutputCharset($object->ref), '', 'R');

		$posy=$pdf->getY();
		$pdf->SetFont('','', $default_font_size - 1);
		$pdf->SetXY($posx, $posy);
		$pdf->SetTextColorArray($textcolor);
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("OrderDate")." : " . dol_print_date($object->date,"%d %b %Y",false,$outputlangs,true), '', 'R');
		
		$posy=$pdf->getY();
		
		if (! empty($conf->global->ULTIMATE_ORDERS_PDF_SHOW_PROJECT))
		{
			$object->fetch_projet();
			if (! empty($object->project->ref))
			{
				$langs->load('projects');
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx, $posy);
				$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Project") ." : ".(empty($object->project->ref)?'':$object->projet->ref), 0, 'R');
			}			
		}
		
		// Example using extrafields
		$title_key=(empty($object->array_options['options_codesupplier']))?'':($object->array_options['options_codesupplier']);	
		$extrafields = new ExtraFields ( $this->db );
		$extralabels = $extrafields->fetch_name_optionals_label ( $object->table_element, true );
		if (is_array ( $extralabels ) && key_exists ( 'codesupplier', $extralabels ) && !empty($title_key)) 
		{
			$title = $extrafields->showOutputField ( 'codesupplier', $title_key );
			$posy=$pdf->getY();
			$pdf->SetXY($posx, $posy);
			$pdf->SetTextColorArray($textcolor);
			$pdf->MultiCell(100, 5, $title, 0, 'R');
		}

		$pdf->SetXY($posx, $posy);
		
		// Show list of linked objects
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size);
		$posy = $pdf->getY();
		
		if($logo_height>=20 && $logo_height<=25)
		{
			$delta = 10;
		}
		elseif ($logo_height>25 && $logo_height<=30) 
		{
			$delta = 5;
		}
		elseif ($logo_height>30 && $logo_height<=35) 
		{
			$delta = 4;
		}
		else 
		{
			$delta = 2;
		}	
		
		if ($showaddress)
		{
			// Sender properties
			$carac_emetteur = pdf_element_build_address($outputlangs, $this->emetteur, $object->thirdparty, '', 0, 'source', $object);

			// Show sender
			$posy=$logo_height+$this->marge_haute+$delta;	
			$posx=$this->marge_gauche;
			if (($conf->global->ULTIMATE_PDF_ORDER_ADDALSOTARGETDETAILS == 1) || (! empty($conf->global->MAIN_INFO_SOCIETE_NOTE) && !empty($this->emetteur->note_private)) || (! empty($conf->global->MAIN_INFO_TVAINTRA) && !empty($this->emetteur->tva_intra)))
			{
				$hautcadre=48;
			}
			else
			{
				$hautcadre=40;
			}
			$widthrecbox=$conf->global->ULTIMATE_WIDTH_RECBOX-2;
			if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx=$this->page_largeur-$this->marge_droite-$widthrecbox;  
			
			if ($roundradius == 0) 
			{
				$roundradius = 0.1; //RoundedRect don't support $roundradius to be 0
			}
			$this->border_style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => $dashdotted , 'color' => $bordercolor);
			// Show sender frame
			$pdf->SetTextColorArray($textcolor);
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($posx, $posy, $widthrecbox, $hautcadre, $roundradius, $round_corner = '1111', $senderstyle, $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
			
			// Show sender name
	        $pdf->SetXY($posx + 2, $posy + 3);
			$pdf->SetFont('', 'B', $default_font_size - 1);
			if (!empty($conf->global->ULTIMATE_PDF_ALIAS_COMPANY))
			{
				$pdf->MultiCell($widthrecbox-5, 4, $outputlangs->convToOutputCharset($conf->global->ULTIMATE_PDF_ALIAS_COMPANY), 0, 'L');
			}
			else
			{
				$pdf->MultiCell($widthrecbox-5, 4, $outputlangs->convToOutputCharset($this->emetteur->name), 0, 'L');
			}
				
			$posy = $pdf->getY();
			
			// Show sender information
			$pdf->SetFont('','', $default_font_size - 1);
			$pdf->SetXY($posx+2,$posy);
			$pdf->writeHTMLCell($widthrecbox-5, 4, $posx+2, $posy, $carac_emetteur, 0, 2, 0, true, 'L', true);
			$posy=$pdf->getY();
			
			// Show private note from societe
			if (! empty($conf->global->MAIN_INFO_SOCIETE_NOTE) && ! empty($this->emetteur->note_private))
    		{
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2,$posy);
				$pdf->writeHTMLCell($widthrecbox-5, 8, $posx+2, $posy+2, dol_string_nohtmltag($this->emetteur->note_private), 0, 1, 0, true, 'L', true);
			}
			
			// If SHIPPING and BILLING contact defined, we use it
			if ($object->getIdContact('external','BILLING') && $object->getIdContact('external','SHIPPING'))
			{
				// If BILLING contact defined on invoice, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','BILLING');
				if (count($arrayidcontact) > 0);
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				// Recipient address
				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, $object->contact, $usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient=$this->page_largeur-$this->marge_droite-$this->marge_gauche-$conf->global->ULTIMATE_WIDTH_RECBOX;
				$posy=$logo_height+$this->marge_haute+$delta;
				$posx=$this->page_largeur-$this->marge_droite-$widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx=$this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show invoice address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient/2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx-$widthrecboxrecipient/2, $posy-4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("BillAddress"), 0, 'R');

				// Show recipient name
				$pdf->SetXY($posx+2, $posy+2);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);	
				
				// If SHIPPING contact defined on invoice, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','SHIPPING');
			
				if (count($arrayidcontact) > 0)
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}

				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, $object->contact, $usecontact, 'target', $object);
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show shipping address
				$posy=$logo_height+$this->marge_haute+$delta;
				$posx=$posx+$widthrecboxrecipient/2;
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient/2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx-$widthrecboxrecipient/2, $posy-4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("DeliveryAddress"), 0, 'R');	
				
				// Show recipient name
				$pdf->SetXY($posx+2, $posy+2);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();

				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);
			}
			// If SHIPPING and CUSTOMER contact defined, we use it
			elseif ($object->getIdContact('external','CUSTOMER') && $object->getIdContact('external','SHIPPING'))
			{
				// If CUSTOMER contact defined on invoice, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','CUSTOMER');
				if (count($arrayidcontact) > 0)
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				// Recipient address
				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, $object->contact, $usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient=$this->page_largeur-$this->marge_droite-$this->marge_gauche-$conf->global->ULTIMATE_WIDTH_RECBOX-2;
				$posy=$logo_height+$this->marge_haute+$delta;
				$posx=$this->page_largeur-$this->marge_droite-$widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx=$this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show invoice address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient/2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx-$widthrecboxrecipient/2, $posy-4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("TypeContact_commande_external_CUSTOMER"), 0, 'R');

				// Show recipient name
				$pdf->SetXY($posx+2, $posy+2);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);	
				
				// If SHIPPING contact defined on invoice, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','SHIPPING');
			
				if (count($arrayidcontact) > 0)
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}

				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, $object->contact, $usecontact, 'target', $object);
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show shipping address
				$posy=$logo_height+$this->marge_haute+$delta;
				$posx=$posx+$widthrecboxrecipient/2;
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient/2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx-$widthrecboxrecipient/2, $posy-4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("DeliveryAddress"), 0, 'R');	
				
				// Show recipient name
				$pdf->SetXY($posx+2, $posy+2);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();

				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);
			}
			// If BILLING and CUSTOMER contact defined, we use it
			elseif ($object->getIdContact('external','CUSTOMER') && $object->getIdContact('external','BILLING'))
			{

				// If CUSTOMER contact defined on invoice, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','CUSTOMER');
				if (count($arrayidcontact) > 0)
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				// Recipient address
				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, $object->contact, $usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient=$this->page_largeur-$this->marge_droite-$this->marge_gauche-$conf->global->ULTIMATE_WIDTH_RECBOX-2;
				$posy=$logo_height+$this->marge_haute+$delta;
				$posx=$this->page_largeur-$this->marge_droite-$widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx=$this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show invoice address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient/2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx-$widthrecboxrecipient/2, $posy-4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("TypeContact_commande_external_CUSTOMER"), 0, 'R');

				// Show recipient name
				$pdf->SetXY($posx+2, $posy+2);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);	
				
				// If BILLING contact defined on invoice, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','BILLING');
			
				if (count($arrayidcontact) > 0)
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}

				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, $object->contact, $usecontact, 'target', $object);
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show billing address
				$posy=$logo_height+$this->marge_haute+$delta;
				$posx=$posx+$widthrecboxrecipient/2;
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient/2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx-$widthrecboxrecipient/2, $posy-4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("BillAddress"), 0, 'R');	
				
				// Show recipient name
				$pdf->SetXY($posx+2, $posy+2);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();

				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);
			}
			elseif ($object->getIdContact('external','BILLING'))
			{
				// If BILLING contact defined on invoice, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','BILLING');
				if (count($arrayidcontact) > 0)
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);
				

				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, $object->contact, $usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient=$this->page_largeur-$this->marge_droite-$this->marge_gauche-$conf->global->ULTIMATE_WIDTH_RECBOX-2;
				$posy=$logo_height+$this->marge_haute+$delta;		
				$posx=$this->page_largeur-$this->marge_droite-$widthrecboxrecipient;	
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx=$this->marge_gauche;
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show billing address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx, $posy-4);		
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("BillAddress"), 0, 'R');
				
				$posy = $pdf->getY();
				
				// Show recipient name
				$pdf->SetXY($posx+2, $posy+3);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2,$posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);

			}
			elseif ($object->getIdContact('external','SHIPPING'))
			{			
				// If SHIPPING contact defined, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','SHIPPING');
				if (count($arrayidcontact) > 0)
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}
				
				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);

				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, $object->contact, $usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient=$this->page_largeur-$this->marge_droite-$this->marge_gauche-$conf->global->ULTIMATE_WIDTH_RECBOX-2;
				$posy=$logo_height+$this->marge_haute+$delta;	
				$posx=$this->page_largeur-$this->marge_droite-$widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx=$this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);
				
				// Show shipping address
				$pdf->SetXY($posx, $posy-4);
				$pdf->SetAlpha($opacity);				
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->MultiCell($widthrecboxrecipient,4, $outputlangs->transnoentities("DeliveryAddress"), 0, 'R');

				// Show recipient name
				$pdf->SetXY($posx+2, $posy+3);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2,$posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);			
			}
			elseif ($object->getIdContact('external','CUSTOMER'))
			{
				// If CUSTOMER contact defined on order, we use it
				$usecontact=false;
				$arrayidcontact=$object->getIdContact('external','CUSTOMER');
				if (count($arrayidcontact) > 0)
				{
					$usecontact=true;
					$result=$object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && !empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);

				$carac_client=pdf_element_build_address($outputlangs,$this->emetteur,$object->thirdparty,($usecontact?$object->contact:''),$usecontact,'target', $object);

				// Show recipient
				$widthrecboxrecipient=$this->page_largeur-$this->marge_droite-$this->marge_gauche-$conf->global->ULTIMATE_WIDTH_RECBOX-2;
				$posy=$logo_height+$this->marge_haute+$delta;		
				$posx=$this->page_largeur-$this->marge_droite-$widthrecboxrecipient;	
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx=$this->marge_gauche;
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show Contact_commande_external_CUSTOMER address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx, $posy-4);		
				$pdf->MultiCell($widthrecboxrecipient,4, $outputlangs->transnoentities("TypeContact_commande_external_CUSTOMER"), 0, 'R');
				
				// Show recipient name
				$pdf->SetXY($posx+2, $posy+3);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);

			}
			else
			{
				$thirdparty = $object->thirdparty;
				// Recipient name
				$carac_client_name= pdfBuildThirdpartyName($thirdparty, $outputlangs);
				// Recipient address
				$carac_client=pdf_element_build_address($outputlangs, $this->emetteur, $object->thirdparty, '', 0, 'target', $object);

				// Show recipient
				$widthrecboxrecipient=$this->page_largeur-$this->marge_droite-$this->marge_gauche-$conf->global->ULTIMATE_WIDTH_RECBOX-2;
				$posy=$logo_height+$this->marge_haute+$delta;
				$posx=$this->page_largeur-$this->marge_droite-$widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx=$this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);
				
				// Show shipping address
				$pdf->SetXY($posx, $posy-4);	
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);

				// Show recipient name
				$pdf->SetXY($posx+2, $posy+3);
				$pdf->SetFont('','B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient-5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('','', $default_font_size - 1);
				$pdf->SetXY($posx+2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient-5, 4, $posx+2, $posy, $carac_client, 0, 2, 0, true, 'L', true);			
			}				

			// Other informations

			$pdf->SetFillColor(255,255,255);

			// Availability Period
			$width=$main_page/5 -1.5;
			$RoundedRectHeight = $this->marge_haute+$logo_height+$hautcadre+$delta+2;
			$pdf->SetAlpha($opacity);			
			$pdf->RoundedRect($this->marge_gauche, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
	        $pdf->SetFont('','B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche,$RoundedRectHeight+0.5);
	        $pdf->SetTextColorArray($textcolor);
			$text='<div style="line-height:90%;">'.$outputlangs->transnoentities("AvailabilityPeriod").'</div>';
	        $pdf->writeHTMLCell($width, 5,$this->marge_gauche,$RoundedRectHeight+0.5, $text, 0, 0, false, true, 'C', true);

	        if (! empty($object->availability_id))
	        {
				$form = new Form($this->db);
				$form->load_cache_availability();
	        	$pdf->SetFont('','', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche,$RoundedRectHeight+6);
	        	$pdf->SetTextColorArray($textcolor);
	        	$availability=$form->cache_availability[$object->availability_id]['label'];
				$pdf->writeHTMLCell($width, 6, $this->marge_gauche, $RoundedRectHeight+6, $availability, 0, 0, false, true, 'C', true);
	        }
			else
			{
				$pdf->MultiCell($width, 6, '', '0', 'C');
			}

	        // Delivery date
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($this->marge_gauche+$width+2, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
	        $pdf->SetFont('','B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche+$width+2,$RoundedRectHeight);
	        $pdf->SetTextColorArray($textcolor);
	        $pdf->MultiCell($width, 5, $outputlangs->transnoentities("DeliveryDate"), 0, 'C', false);

	        if (! empty($object->date_livraison))
	        {
	        	$pdf->SetFont('','', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche+$width+2,$RoundedRectHeight+6);
	        	$pdf->SetTextColorArray($textcolor);
				$pdf->SetFillColor(255,255,255);
	        	$pdf->MultiCell($width, 6, dol_print_date($object->date_livraison,"day",false,$outputlangs,true), '0', 'C');
	        }
			else
			{
				$pdf->SetFont('','', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche+$width+2,$RoundedRectHeight+6);
	        	$pdf->SetTextColorArray($textcolor);
				$pdf->SetFillColor(255,255,255);
				$pdf->MultiCell($width, 6, '', '0', 'C');
			}

	        // Commercial Interlocutor
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($this->marge_gauche+$width*2+4, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
	        $pdf->SetFont('','B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche+$width*2+4,$RoundedRectHeight+0.5);
	        $pdf->SetTextColorArray($textcolor);
			$text='<div style="line-height:90%;">'.$outputlangs->transnoentities("SalesRepresentative").'</div>';
	        $pdf->writeHTMLCell($width, 5,$this->marge_gauche+$width*2+4,$RoundedRectHeight+0.5, $text, 0, 0, false, true, 'C', true);
	        
	        $contact_id = $object->getIdContact('internal','SALESREPFOLL');

	        if (! empty($contact_id))
	        {
	        	$object->fetch_user($contact_id[0]);
	        	$pdf->SetFont('','', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche+$width*2+4,$RoundedRectHeight+6);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 5, $object->user->firstname.' '.$object->user->lastname, 0, 'C', false);
				$pdf->SetXY($this->marge_gauche+$width*2+4,$RoundedRectHeight+9);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 7, $object->user->office_phone, '0', 'C');
	        }
	        else if ($object->user_author_id)
	        {
	        	$object->fetch_user($object->user_author_id);
	        	$pdf->SetFont('','', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche+$width*2+4,$RoundedRectHeight+6);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 6, $object->user->firstname.' '.$object->user->lastname, '0', 'C');
				$pdf->SetXY($this->marge_gauche+$width*2+4,$RoundedRectHeight+9);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 7, $object->user->office_phone, '0', 'C');
	        }
			else
			{
				$pdf->SetFont('','', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche+$width*2+4,$RoundedRectHeight+6);
	        	$pdf->SetTextColorArray($textcolor);
				$pdf->SetFillColor(255,255,255);
				$pdf->MultiCell($width, 6, '', '0', 'C');
			}

	        // Customer code
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($this->marge_gauche+$width*3+6, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
	        $pdf->SetFont('','B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche+$width*3+6,$RoundedRectHeight);
	        $pdf->SetTextColorArray($textcolor);
	        $pdf->MultiCell($width, 5, $outputlangs->transnoentities("CustomerCode"), 0, 'C', false);

	        if ($object->thirdparty->code_client)
	        {
	        	$pdf->SetFont('','', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche+$width*3+6,$RoundedRectHeight+6);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 6, $outputlangs->transnoentities($object->thirdparty->code_client), '0', 'C');
	        }
			else
			{
				$pdf->SetFont('','', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche+$width*3+6,$RoundedRectHeight+6);
	        	$pdf->SetTextColorArray($textcolor);
				$pdf->SetFillColor(255,255,255);
				$pdf->MultiCell($width, 6, '', '0', 'C');
			}
			
			// Customer ref
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($this->marge_gauche+$width*4+8, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
	        $pdf->SetFont('','B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche+$width*4+8,$RoundedRectHeight);
	        $pdf->SetTextColorArray($textcolor);
	        $pdf->MultiCell($width, 5, $outputlangs->transnoentities("RefCustomer"), 0, 'C', false);
	        
			if ($object->ref_client)
			{
				$pdf->SetFont('','', $default_font_size - 2);
				$pdf->SetXY($this->marge_gauche+$width*4+8,$RoundedRectHeight+6);
				$pdf->SetTextColorArray($textcolor);
				$pdf->MultiCell($width, 6, $object->ref_client, '0', 'C');	
			}
		}
		$pdf->SetTextColorArray($textcolor);
	}

	/**
	 *   	Show footer of page. Need this->emetteur object
     *
	 *   	@param	PDF			&$pdf     			PDF
	 * 		@param	Object		$object				Object to show
	 *      @param	Translate	$outputlangs		Object lang for output
	 *      @param	int			$hidefreetext		1=Hide free text
	 *      @return	int								Return height of bottom margin including footer text
	 */
	protected function _pagefoot(&$pdf,$object,$outputlangs,$hidefreetext=0)
	{
		global $conf;
		$showdetails=$conf->global->MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS;
		return pdf_ultimatepagefoot($pdf, $outputlangs, 'ORDER_FREE_TEXT', $this->emetteur, $this->marge_basse, $this->marge_gauche, $this->page_hauteur, $object, $showdetails, $hidefreetext, $footertextcolor);
	}
	
	function _setFontForMulticurrencyCode(&$pdf, $object, $outputlangs)
	{
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		if ($object->multicurrency_code == 'SAR' || $object->multicurrency_code == 'IRR' || $object->multicurrency_code == 'OMR' || $object->multicurrency_code == 'QAR' || $object->multicurrency_code == 'YER')
		{
			$pdf->SetFont('MarkaziText', '', $default_font_size + 2);
		}
		elseif ($object->multicurrency_code == 'RUB' || $object->multicurrency_code == 'PLN' || $object->multicurrency_code == 'BYR')
		{
			$pdf->SetFont('DejaVuSans', '', $default_font_size);
		}
		else
		{
			$pdf->SetFont($conf->global->MAIN_PDF_FORCE_FONT, '', $default_font_size );
		}
	}
	
	/**
	 *   	Define Array Column Field
	 *
	 *   	@param	object			$object    		common object
	 *   	@param	Translate		$outputlangs    langs
	 *      @param	int				$hidedetails	Do not show line details
	 *      @param	int				$hidedesc		Do not show desc
	 *      @param	int				$hideref		Do not show ref
	 *      @return	null
	 */
    public function defineColumnField($object,$outputlangs,$hidedetails=0,$hidedesc=0,$hideref=0)
    {
	    global $conf, $hookmanager;

	    // Default field style for content
	    $this->defaultContentsFieldsStyle = array(
	        'align' => 'R', // R,C,L
	        'padding' => array(0.5,0.5,0.5,0.5), // Like css 0 => top , 1 => right, 2 => bottom, 3 => left
	    );

	    // Default field style for content
	    $this->defaultTitlesFieldsStyle = array(
	        'align' => 'C', // R,C,L
	        'padding' => array(0.5,0,0.5,0), // Like css 0 => top , 1 => right, 2 => bottom, 3 => left
	    );

	    /*
	     * For exemple
	     $this->cols['theColKey'] = array(
	     'rank' => $rank, // int : use for ordering columns
	     'width' => 20, // the column width in mm
	     'title' => array(
	     'textkey' => 'yourLangKey', // if there is no label, yourLangKey will be translated to replace label
	     'label' => ' ', // the final label : used fore final generated text
	     'align' => 'L', // text alignement :  R,C,L
	     'padding' => array(0.5,0.5,0.5,0.5), // Like css 0 => top , 1 => right, 2 => bottom, 3 => left
	     ),
	     'content' => array(
	     'align' => 'L', // text alignement :  R,C,L
	     'padding' => array(0.5,0.5,0.5,0.5), // Like css 0 => top , 1 => right, 2 => bottom, 3 => left
	     ),
	     );
	     */
		 
		  $rank=0; // do not use negative rank
		  $this->cols['num'] = array(
	        'rank' => $rank,
	        'width' => (empty($conf->global->ULTIMATE_DOCUMENTS_WITH_NUMBERING_WIDTH)?10:$conf->global->ULTIMATE_DOCUMENTS_WITH_NUMBERING_WIDTH), // in mm 
	        'status' => false,
	        'title' => array(
	            'textkey' => 'Numbering', // use lang key is usefull in somme case with module
	            'align' => 'C',
	            // 'textkey' => 'yourLangKey', // if there is no label, yourLangKey will be translated to replace label
	            // 'label' => ' ', // the final label
	            'padding' => array(0.5,0.5,0.5,0.5), // Like css 0 => top , 1 => right, 2 => bottom, 3 => left
	        ),
	        'content' => array(
	            'align' => 'C',
	        ),
	    );
		if (!empty($conf->global->ULTIMATE_ORDERS_WITH_LINE_NUMBER))
	    {
	        $this->cols['num']['status'] = true;
	    }
		 
		  $rank = $rank + 10; // do not use negative rank
		  $this->cols['ref'] = array(
	        'rank' => $rank,
	        'width' => (empty($conf->global->ULTIMATE_DOCUMENTS_WITH_REF_WIDTH)?16:$conf->global->ULTIMATE_DOCUMENTS_WITH_REF_WIDTH), // in mm 
	        'status' => false,
	        'title' => array(
	            'textkey' => 'RefShort', // use lang key is usefull in somme case with module
	            'align' => 'C',
	            // 'textkey' => 'yourLangKey', // if there is no label, yourLangKey will be translated to replace label
	            // 'label' => ' ', // the final label
	            'padding' => array(0.5,0.5,0.5,0.5), // Like css 0 => top , 1 => right, 2 => bottom, 3 => left
	        ),
	        'content' => array(
	            'align' => 'L',
	        ),
		   'border-left' => false, // remove left line separator
	    );
		
		if ($conf->global->ULTIMATE_DOCUMENTS_WITH_REF == "yes" && $this->atleastoneref==true)
		{
			$this->cols['ref']['status'] = true;
		}
		if (!empty($conf->global->ULTIMATE_ORDERS_WITH_LINE_NUMBER))
	    {
	        $this->cols['ref']['border-left'] = true;
	    }

	    $rank = $rank + 10; // do not use negative rank
	    $this->cols['desc'] = array(
	        'rank' => $rank,
	        'width' => false, // only for desc
	        'status' => true,
	        'title' => array(
	            'textkey' => 'Designation', // use lang key is usefull in somme case with module
	            'align' => 'C',
	            // 'textkey' => 'yourLangKey', // if there is no label, yourLangKey will be translated to replace label
	            // 'label' => ' ', // the final label
	            'padding' => array(0.5,0.5,0.5,0.5), // Like css 0 => top , 1 => right, 2 => bottom, 3 => left
	        ),
	        'content' => array(
	            'align' => 'L',
	        ),
			'border-left' => false, // remove left line separator
	    );
		
		if (!empty($conf->global->ULTIMATE_ORDERS_WITH_LINE_NUMBER) || ($conf->global->ULTIMATE_DOCUMENTS_WITH_REF == "yes" && $this->atleastoneref==true))
	    {
	        $this->cols['desc']['border-left'] = true;
	    }
		
		$rank = $rank + 10;
		$this->cols['picture'] = array(
			'rank' => $rank,
			'width' => (empty($conf->global->MAIN_DOCUMENTS_WITH_PICTURE_WIDTH)?20:$conf->global->MAIN_DOCUMENTS_WITH_PICTURE_WIDTH), // in mm
			'status' => false,
			'title' => array(
				'textkey' => 'Picture',
				'label' => ' '
			),
			'content' => array(
				'padding' => array(0,0,0,0), // Like css 0 => top , 1 => right, 2 => bottom, 3 => left
			),
			'border-left' => false, // remove left line separator
		);
		
	    if ($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_PICTURE == 1)
		{
			$this->cols['picture']['status'] = true;
		}
		
		$rank = $rank + 10;
		$this->cols['vat'] = array(
			'rank' => $rank,
			'status' => false,
			'width' => (empty($conf->global->ULTIMATE_DOCUMENTS_WITH_TVA_WIDTH)?14:$conf->global->ULTIMATE_DOCUMENTS_WITH_TVA_WIDTH), // in mm  
			'title' => array(
				'textkey' => 'VAT'
			),
			'content' => array(
	            'align' => 'R',
	        ),
			'border-left' => true, // add left line separator
		);
		
	    if(($conf->global->ULTIMATE_GENERATE_DOCUMENTS_WITHOUT_VAT == 0) && ($conf->global->ULTIMATE_SHOW_HIDE_VAT_COLUMN == 0))
	    {
	        $this->cols['vat']['status'] = true;
	    }

	    $rank = $rank + 10;
	    $this->cols['subprice'] = array(
	        'rank' => $rank,
	        'width' => (empty($conf->global->ULTIMATE_DOCUMENTS_WITH_UP_WIDTH)?19:$conf->global->ULTIMATE_DOCUMENTS_WITH_UP_WIDTH), // in mm 
	        'status' => false,
	        'title' => array(
	            'textkey' => 'PriceUHT'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => true, // add left line separator
	    );
		
		if (!empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_PRICEUHT))
	    {
	        $this->cols['subprice']['status'] = true;
	    }
		
		$rank = $rank + 10;
	    $this->cols['discount'] = array(
	        'rank' => $rank,
	        'width' => (empty($conf->global->ULTIMATE_DOCUMENTS_WITH_DISCOUNT_WIDTH)?13:$conf->global->ULTIMATE_DOCUMENTS_WITH_DISCOUNT_WIDTH), // in mm
	        'status' => false,
	        'title' => array(
	            'textkey' => 'ReductionShort'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => true, // add left line separator
	    );
		
	    if (!empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_DISCOUNT) && $this->atleastonediscount)
		{
	        $this->cols['discount']['status'] = true;
	    }
		
		$rank = $rank + 10;
	    $this->cols['upafter'] = array(
	        'rank' => $rank,
	        'width' => (empty($conf->global->ULTIMATE_DOCUMENTS_WITH_UP_WIDTH)?19:$conf->global->ULTIMATE_DOCUMENTS_WITH_UP_WIDTH), // in mm 
	        'status' => false,
	        'title' => array(
	            'textkey' => 'PuAfter'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => true, // add left line separator
	    );
		if ($this->atleastonediscount && !empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_PUAFTER))
	    {
	        $this->cols['upafter']['status'] = true;
	    }

	    $rank = $rank + 10;
	    $this->cols['qty'] = array(
	        'rank' => $rank,
	        'width' => (empty($conf->global->ULTIMATE_DOCUMENTS_WITH_QTY_WIDTH)?16:$conf->global->ULTIMATE_DOCUMENTS_WITH_QTY_WIDTH), // in mm 
	        'status' => false,
	        'title' => array(
	            'textkey' => 'Qty'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => true, // add left line separator
	    );
		if (!empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_QTY))
	    {
	        $this->cols['qty']['status'] = true;
	    }
		
		$rank = $rank + 10;
	    $this->cols['weight'] = array(
	        'rank' => $rank,
	        'width' => 12, // in mm
	        'status' => false,
	        'title' => array(
	            'textkey' => 'Weight'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => true, // add left line separator
	    );

	    if(!empty($conf->global->ULTIMATE_GENERATE_ORDERS_WITH_WEIGHT_COLUMN))
	    {
	        $this->cols['weight']['status'] = true;
	    }

	    $rank = $rank + 10;
	    $this->cols['progress'] = array(
	        'rank' => $rank,
	        'width' => 19, // in mm
	        'status' => false,
	        'title' => array(
	            'textkey' => 'Progress'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => false, // add left line separator
	    );

	    if($this->situationinvoice)
	    {
	        $this->cols['progress']['status'] = true;
	    }

	    $rank = $rank + 10;
	    $this->cols['unit'] = array(
	        'rank' => $rank,
	        'width' => (empty($conf->global->ULTIMATE_DOCUMENTS_WITH_UNIT_WIDTH)?11:$conf->global->ULTIMATE_DOCUMENTS_WITH_UNIT_WIDTH), // in mm 
	        'status' => false,
	        'title' => array(
	            'textkey' => 'Unit'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => true, // add left line separator
	    );
		
	    if($conf->global->PRODUCT_USE_UNITS)
		{
	        $this->cols['unit']['status'] = true;
	    }

	    $rank = $rank + 10;
	    $this->cols['totalexcltax'] = array(
	        'rank' => $rank,
	        'width' => 26, // in mm
	        'status' => false,
	        'title' => array(
	            'textkey' => 'TotalHT'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => true, // add left line separator
	    );
		
		if(!$conf->global->ULTIMATE_SHOW_LINE_TTTC)
		{
	        $this->cols['totalexcltax']['status'] = true;
	    }
		
		$rank = $rank + 10;
	    $this->cols['totalincltax'] = array(
	        'rank' => $rank,
	        'width' => 26, // in mm
	        'status' => false,
	        'title' => array(
	            'textkey' => 'TotalTTC'
	        ),
			'content' => array(
	            'align' => 'R',
	        ),
	        'border-left' => true, // add left line separator
	    );
		
		if($conf->global->ULTIMATE_SHOW_LINE_TTTC)
		{
	        $this->cols['totalincltax']['status'] = true;
	    }
		
		$parameters=array(
	        'object' => $object,
	        'outputlangs' => $outputlangs,
	        'hidedetails' => $hidedetails,
	        'hidedesc' => $hidedesc,
	        'hideref' => $hideref
	    );
		
		$reshook=$hookmanager->executeHooks('defineColumnField',$parameters,$this);    // Note that $object may have been modified by hook
	    if ($reshook < 0)
	    {
	        setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
	    }
	    elseif (empty($reshook))
	    {
	        $this->cols = array_replace($this->cols, $hookmanager->resArray); // array_replace is used to preserve keys
	    }
	    else
	    {
	        $this->cols = $hookmanager->resArray;
	    }
	}
}

?>