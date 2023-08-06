<?php
/* Copyright (C) 2003		Rodolphe Quiedeville		<rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010	Laurent Destailleur			<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012	Regis Houssin				<regis.houssin@capnetworks.com>
 * Copyright (C) 2008		Raphael Bertrand (Resultic)	<raphael.bertrand@resultic.fr>
 * Copyright (C) 2011		Fabrice CHERRIER
 * Copyright (C) 2013-2020  Philippe Grand	            <philippe.grand@atoo-net.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/core/modules/contract/doc/pdf_ultimate_contract.modules.php
 *	\ingroup    ficheinter
 *	\brief      Fichier de la classe permettant de generer les contrats au modele Ultimate_contract
 */
require_once DOL_DOCUMENT_ROOT.'/core/modules/contract/modules_contract.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
dol_include_once('/ultimatepdf/lib/ultimatepdf.lib.php');


/**
 *	Class to build contracts documents with model ultimatecontract
 */
class pdf_ultimate_contract extends ModelePDFContract
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
     * @var string logo_height
     */
	public $logo_height;

	/**
	* Issuer
	* @var Societe
	*/
	public $emetteur;
	
	/**
	* Recipient
	* @var Societe
	*/
	public $recipient;
	
	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		global $conf, $langs, $mysoc;

		$this->db = $db;
		$this->name = 'ultimate_contract';
		$this->description = $langs->trans("StandardContractsTemplate");
		$_SESSION['ultimatepdf_model'] = true;

		// Dimension page pour format A4
		$this->type = 'pdf';
		$formatarray = pdf_getFormat();
		$this->page_largeur = $formatarray['width'];
		$this->page_hauteur = $formatarray['height'];
		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=isset($conf->global->ULTIMATE_PDF_MARGIN_LEFT)?$conf->global->ULTIMATE_PDF_MARGIN_LEFT:10;
		$this->marge_droite=isset($conf->global->ULTIMATE_PDF_MARGIN_RIGHT)?$conf->global->ULTIMATE_PDF_MARGIN_RIGHT:10;
		$this->marge_haute =isset($conf->global->ULTIMATE_PDF_MARGIN_TOP)?$conf->global->ULTIMATE_PDF_MARGIN_TOP:10;
		$this->marge_basse =isset($conf->global->ULTIMATE_PDF_MARGIN_BOTTOM)?$conf->global->ULTIMATE_PDF_MARGIN_BOTTOM:10;

		$this->option_logo = 1;                    // Affiche logo
		$this->option_tva = 0;                     // Gere option tva FACTURE_TVAOPTION
		$this->option_modereg = 0;                 // Affiche mode reglement
		$this->option_condreg = 0;                 // Affiche conditions reglement
		$this->option_codeproduitservice = 0;      // Affiche code produit-service
		$this->option_multilang = 1;               // Dispo en plusieurs langues
		$this->option_draft_watermark = 1;		   // Support add of a watermark on drafts
		
		$bordercolor = array('0', '63', '127');
		$dashdotted = isset($conf->global->ULTIMATE_DASH_DOTTED)?$conf->global->ULTIMATE_DASH_DOTTED : '';
		if(!empty($conf->global->ULTIMATE_BORDERCOLOR_COLOR))
		{
			$bordercolor = html2rgb($conf->global->ULTIMATE_BORDERCOLOR_COLOR);
			if(!empty($conf->global->ULTIMATE_DASH_DOTTED))
			{
				$dashdotted = $conf->global->ULTIMATE_DASH_DOTTED;
			}
			$this->border_style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => $dashdotted , 'color' => $bordercolor);
		}
		
		// Get source company
		$this->emetteur = $mysoc;
		if (empty($this->emetteur->country_code)) $this->emetteur->country_code=substr($langs->defaultlang, -2);    // By default, if not defined

		// Define position of columns
		$this->posxdesc=$this->marge_gauche+1;
		
		$this->tabTitleHeight = 8; // default height;
		
		$this->tva=array();
		$this->localtax1=array();
		$this->localtax2=array();
		$this->atleastoneratenotnull=0;
		$this->atleastonediscount=0;

	}

	/**
     *  Function to build pdf onto disk
     *
     *  @param		CommonObject	$object				Id of object to generate
     *  @param		object			$outputlangs		Lang output object
     *  @param		string			$srctemplatepath	Full path of source filename for generator using a template file
     *  @param		int				$hidedetails		Do not show line details
     *  @param		int				$hidedesc			Do not show desc
     *  @param		int				$hideref			Do not show ref
     *  @return     int             					1=OK, 0=KO
	 */
	public function write_file($object, $outputlangs, $srctemplatepath = '', $hidedetails = 0, $hidedesc = 0, $hideref = 0)
	{
		global $user, $langs, $conf, $mysoc, $db, $hookmanager, $nblines;
		
		$textcolor = array('25', '25', '25');
		if (! empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor =  html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}
		if (! empty($conf->global->ULTIMATE_SET_RADIUS))
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
		// For backward compatibility with FPDF, force output charset to ISO, because FPDF expect text to be encoded in ISO
		if (! empty($conf->global->MAIN_USE_FPDF)) $outputlangs->charset_output = 'ISO-8859-1';
		
		// Load traductions files requiredby by page
		$outputlangs->loadLangs(array("main", "dict", "companies", "contracts", "ultimatepdf@ultimatepdf"));
		
		$nblines = count($object->lines);

		$hidetop = 0;
		if(!empty($conf->global->MAIN_PDF_DISABLE_COL_HEAD_TITLE)){
		    $hidetop = $conf->global->MAIN_PDF_DISABLE_COL_HEAD_TITLE;
		}

		if ($conf->contrat->dir_output)
		{
            $object->fetch_thirdparty();

			// Definition of $dir and $file
			if ($object->specimen)
			{
				$dir = $conf->contrat->dir_output;
				$file = $dir . "/SPECIMEN.pdf";
			}
			else
			{
				$objectref = dol_sanitizeFileName($object->ref);
				$dir = $conf->contrat->dir_output . "/" . $objectref;
				$file = $dir . "/" . $objectref . ".pdf";
			}

			if (! file_exists($dir))
			{
				if (dol_mkdir($dir) < 0)
				{
					$this->error=$outputlangs->trans("ErrorCanNotCreateDir",$dir);
					return 0;
				}
			}

			if (file_exists($dir))
			{
				// Add pdfgeneration hook
				if (! is_object($hookmanager))
				{
					include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
					$hookmanager = new HookManager($this->db);
				}
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters = array('file'=>$file, 'object'=>$object, 'outputlangs'=>$outputlangs);
				global $action;
				$reshook = $hookmanager->executeHooks('beforePDFCreation', $parameters, $object, $action);    // Note that $action and $object may have been modified by some hooks
				
				// Create pdf instance
                $pdf = pdf_getInstance($this->format);
                $default_font_size = pdf_getPDFFontSize($outputlangs);	// Must be after pdf_getInstance
                
				if (! empty($conf->global->ULTIMATE_DISPLAY_CONTRACT_AGREEMENT_BLOCK))
				{
					$heightforinfotot = 60;	// Height reserved to output the info and total part
				}
				else
				{
					$heightforinfotot = 50;
				}
		        $heightforfreetext = (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT) ? $conf->global->MAIN_PDF_FREETEXT_HEIGHT : 12);	// Height reserved to output the free text on last page
	            $heightforfooter = $this->marge_basse + (empty($conf->global->MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS) ? 12 : 22);	// Height reserved to output the footer (value include bottom margin)
                $pdf->SetAutoPageBreak(1,0);

                if (class_exists('TCPDF'))
                {
                    $pdf->setPrintHeader(false);
                    $pdf->setPrintFooter(false);
                }
                $pdf->SetFont(pdf_getPDFFont($outputlangs));
                // Set path to the background PDF File
                if (empty($conf->global->MAIN_DISABLE_FPDI) && ! empty($conf->global->MAIN_ADD_PDF_BACKGROUND))
                {
                    $pagecount = $pdf->setSourceFile($conf->mycompany->dir_output.'/'.$conf->global->MAIN_ADD_PDF_BACKGROUND);
                    $tplidx = $pdf->importPage(1);
                }

				$pdf->Open();
				$pagenb = 0;
				$pdf->SetDrawColor(128, 128, 128);

				$pdf->SetTitle($outputlangs->convToOutputCharset($object->ref));
				$pdf->SetSubject($outputlangs->transnoentities("ContractCard"));
				$pdf->SetCreator("PowerERP ".DOL_VERSION);
				$pdf->SetAuthor($outputlangs->convToOutputCharset($user->getFullName($outputlangs)));
				$pdf->SetKeyWords($outputlangs->convToOutputCharset($object->ref)." ".$outputlangs->transnoentities("ContractCard"));
				if (! empty($conf->global->MAIN_DISABLE_PDF_COMPRESSION)) $pdf->SetCompression(false);

				$pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right
				
				// Positionne $this->atleastoneref si on a au moins une ref
				for ($i = 0 ; $i < $nblines ; $i++)
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
				if (! empty ($conf->global->ULTIMATE_DESIGN) && ! empty ($conf->global->ULTIMATE_OTHERLOGO_FILE))
				{
					$id = $conf->global->ULTIMATE_DESIGN;
					$upload_dir	= $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/';	
					$filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview.*\.png)$', $sortfield, 0, 1);
					$otherlogo = $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/'.$filearray[0]['name'];
				}		
				if (is_readable($otherlogo) && ! empty($filearray))
				{
					$logo_height = max(pdf_getUltimateHeightForLogo($otherlogo, true), 20);					
				}
				else
				{
					// MyCompany logo
					$logo_height = max(pdf_getUltimateHeightForLogo($conf->global->ULTIMATE_LOGO_HEIGHT, true), 20);
				}

				//Set $hautcadre
				if (($conf->global->ULTIMATE_PDF_CONTRACT_ADDALSOTARGETDETAILS == 1) || (! empty($conf->global->MAIN_INFO_SOCIETE_NOTE)) || (! empty($this->emetteur->note_private)))
				{
					$hautcadre = 52;
				}
				else
				{
					$hautcadre = 48;
				}

				$tab_top = $this->marge_haute + $logo_height + $hautcadre + $top_shift + 15;
				$tab_top_newpage = (empty($conf->global->ULTIMATE_CONTRACT_PDF_DONOTREPEAT_HEAD) ? $this->marge_haute + $logo_height + $top_shift + 10 : 10);
				
				$tab_height = 130 - $top_shift;
				$tab_height_newpage = 150;
				if (empty($conf->global->ULTIMATE_CONTRACT_PDF_DONOTREPEAT_HEAD)) $tab_height_newpage -= $top_shift;
				$tab_width = $this->page_largeur - $this->marge_gauche - $this->marge_droite;
				if ($roundradius == 0) 
				{
					$roundradius = 0.1; //RoundedRect don't support $roundradius to be 0
				}

				// Display notes
				$notetoshow = empty($object->note_public) ? '' : $object->note_public;
				if (! empty($conf->global->MAIN_ADD_SALE_REP_SIGNATURE_IN_CONTRACT_NOTE))
				{
					// Get first sale rep
					if (is_object($object->thirdparty))
					{
						$salereparray = $object->thirdparty->getSalesRepresentatives($user);
						$salerepobj = new User($this->db);
						$salerepobj->fetch($salereparray[0]['id']);
						if (! empty($salerepobj->signature)) $notetoshow = dol_concatdesc($notetoshow, $salerepobj->signature);
					}
				}
				if (! empty($conf->global->MAIN_ADD_CREATOR_IN_NOTE) && $object->user_author_id > 0)
				{
				    $tmpuser = new User($this->db);
				    $tmpuser->fetch($object->user_author_id);
				    $notetoshow .= $langs->trans("CaseFollowedBy").' '.$tmpuser->getFullName($langs);
				    if ($tmpuser->email) $notetoshow .= ',  Mail: '.$tmpuser->email;
				    if ($tmpuser->office_phone) $notetoshow .= ', Tel: '.$tmpuser->office_phone;
				}

				$pagenb = $pdf->getPage();
				if ($notetoshow && empty($conf->global->MAIN_PUBLIC_NOTE_IN_ADDRESS))
				{
					$pageposbeforenote = $pagenb;
					$nexY = $pdf->GetY();
					$tab_top = $nexY;
					
					$substitutionarray = pdf_getSubstitutionArray($outputlangs, null, $object);
					complete_substitutions_array($substitutionarray, $outputlangs, $object);
					$notetoshow = make_substitutions($notetoshow, $substitutionarray, $outputlangs);
					
					$pdf->startTransaction();
					
					$pdf->SetFont('', '', $default_font_size - 1);   // Dans boucle pour gerer multi-page
					$pdf->writeHTMLCell($tab_width, 3, $this->marge_gauche + 1, $tab_top, dol_htmlentitiesbr($notetoshow), 0, 1);
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
				            if (empty($conf->global->ULTIMATE_CONTRACT_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
				            // $this->_pagefoot($pdf,$object,$outputlangs,1);
				            $pdf->setTopMargin($tab_top_newpage);
				            // The only function to edit the bottom margin of current page to set it.
				            $pdf->setPageOrientation('', 1, $heightforfooter);
				        }

				        // back to start
				        $pdf->setPage($pageposbeforenote);
				        $pdf->setPageOrientation('', 1, $heightforfooter);
				        $pdf->SetFont('', '', $default_font_size - 1);
				        $pdf->writeHTMLCell($tab_width, 3, $this->marge_gauche + 1, $tab_top, dol_htmlentitiesbr($notetoshow), 0, 1);
				        $pageposafternote = $pdf->getPage();

				        $posyafter = $pdf->GetY();
						$nexY = $pdf->GetY();

				        if ($posyafter > ($this->page_hauteur - $heightforfooter))	// There is no space left for total+free text
				        {
				            $pdf->AddPage('', '', true);
				            $pagenb++;
				            $pageposafternote++;
				            $pdf->setPage($pageposafternote);
				            $pdf->setTopMargin($tab_top_newpage);
				            // The only function to edit the bottom margin of current page to set it.
				            $pdf->setPageOrientation('', 1, $heightforfooter);
				            //$posyafter = $tab_top_newpage;
				        }

				        // apply note frame to previus pages
				        $i = $pageposbeforenote;
				        while ($i < $pageposafternote) 
						{
				            $pdf->setPage($i);

				            $pdf->SetDrawColor(128, 128, 128);
				            // Draw note frame
				            if ($i > $pageposbeforenote)
							{
				                $height_note = $this->page_hauteur - ($tab_top_newpage + $heightforfooter);
								$pdf->RoundedRect($this->marge_gauche, $tab_top_newpage - 1, $tab_width, $height_note + 1, $roundradius, $round_corner = '1111', $this->border_style, $bgcolor);
				            }
				            else
							{
				                $height_note = $this->page_hauteur - ($tab_top + $heightforfooter);
								$pdf->RoundedRect($this->marge_gauche, $tab_top - 1, $tab_width, $height_note + 1, $roundradius, $round_corner = '1111', 'S', $this->border_style, $bgcolor);
				            }

				            // Add footer
				            $pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.
				            $this->_pagefoot($pdf, $object, $outputlangs, 1);

				            $i++;
				        }

				        // apply note frame to last page
				        $pdf->setPage($pageposafternote);
				        if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				        if (empty($conf->global->ULTIMATE_CONTRACT_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
				        $height_note = $posyafter - $tab_top_newpage;
				        $pdf->RoundedRect($this->marge_gauche, $tab_top_newpage - 1, $tab_width, $height_note + 1, $roundradius, $round_corner = '1111', 'S', $this->border_style, $bgcolor);
				    }
				    else // No pagebreak
				    {
				        $pdf->commitTransaction();
				        $posyafter = $pdf->GetY();
				        $height_note = $posyafter - $tab_top;
				        $pdf->RoundedRect($this->marge_gauche, $tab_top - 1, $tab_width, $height_note + 1, $roundradius, $round_corner = '1111', 'S', $this->border_style, $bgcolor);

				        if($posyafter > ($this->page_hauteur - ($heightforfooter + $heightforfreetext + 20)) )
				        {
				            // not enough space, need to add page
				            $pdf->AddPage('', '', true);
				            $pagenb++;
				            $pageposafternote++;
				            $pdf->setPage($pageposafternote);
				            if (! empty($tplidx)) $pdf->useTemplate($tplidx);
				            if (empty($conf->global->ULTIMATE_CONTRACT_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);

				            $posyafter = $tab_top_newpage;
				        }
				    }

				    $tab_height = $tab_height - $height_note;
				    $tab_top = $posyafter + 10;
				}
				else
				{
					$height_note = 0;
				}

				// Use new auto column system
				$this->prepareArrayColumnField($object, $outputlangs, $hidedetails, $hidedesc, $hideref);
				
				// Simulation de tableau pour connaitre la hauteur de la ligne de titre
				$pdf->startTransaction();
				$this->pdfTabTitles($pdf, $tab_top, $tab_height, $outputlangs, $hidetop);
				$pdf->rollbackTransaction(true);
				$nexY = $pdf->GetY();
				$tab_top = $nexY;
				if($height_note)
				{
					$tab_top += $height_note + 8;
				}
				if(! $height_note)
				{
					$tab_top = $nexY + 6;
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
				for ($i = 0; $i < $nblines; $i++)
				{
					$objectligne = $object->lines[$i];

					$valide = $objectligne->id ? $objectligne->fetch($objectligne->id) : 0;

					if ($valide > 0 || $object->specimen)
					{
						$curX = $this->getColumnContentXStart('desc')-1;
						$text_length = ($this->getColumnStatus('vat') ? $this->getColumnContentXStart('vat')-2:$this->getColumnContentXStart('subprice')-2);
						$curY = $nexY;
						$pdf->SetFont('', '', $default_font_size - 1);   // Into loop to work with multipage
						$pdf->SetTextColorArray($textcolor);
						$barcode = null;
						if (! empty($object->lines[$i]->fk_product))
						{
							$product = new Product($db);
							$result = $product->fetch($object->lines[$i]->fk_product, '', '', '');
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

						$pdf->setTopMargin($tab_top_newpage);
						//If we aren't on last lines footer space needed is on $heightforfooter
						if ($i != $nblines - 1)
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
						
						$showpricebeforepagebreak = 1;
						$posYStartDescription = 0;
						$posYAfterDescription = 0;
						
						// Description of contract line
						
						if ($objectligne->date_ouverture_prevue) 
						{
							$datei = dol_print_date($objectligne->date_ouverture_prevue, 'day', false, $outputlangs, true);
						} 
						else 
						{
							$datei = $langs->trans("Unknown");
						}

						if ($objectligne->date_fin_validite) 
						{
							$durationi = convertSecondToTime($objectligne->date_fin_validite - $objectligne->date_ouverture_prevue, 'allwithouthour');
							$datee = dol_print_date($objectligne->date_fin_validite, 'day', false, $outputlangs, true);
						} 
						else 
						{
							$durationi = $langs->trans("Unknown");
							$datee = $langs->trans("Unknown");
						}

						if ($objectligne->date_ouverture) 
						{
							$daters = dol_print_date($objectligne->date_ouverture, 'day', false, $outputlangs, true);
						} 
						else 
						{
							$daters = $langs->trans("Unknown");
						}

						if ($objectligne->date_cloture) 
						{
							$datere = dol_print_date($objectligne->date_cloture, 'day', false, $outputlangs, true);
						} 
						else 
						{
							$datere = $langs->trans("Unknown");
						}

						$txtpredefinedservice='';
                        $txtpredefinedservice = $objectligne->product_label;
                        if ($objectligne->product_label)
                        {
                        	$txtpredefinedservice .= ' - ';
                        	$txtpredefinedservice .= $objectligne->product_label;
                        }

						$desc = dol_htmlentitiesbr($objectligne->desc,1);   // Desc (not empty for free lines)
						
						$txt = '';
						$txt .= $outputlangs->transnoentities("DateStartPlannedShort")." : <strong>".$datei."</strong> - ".$outputlangs->transnoentities("DateEndPlanned")." : <strong>".$datee.'</strong>';
						$txt .= '<br>';
                        $txt .= $outputlangs->transnoentities("DateStartRealShort")." : <strong>".$daters.'</strong>';
						if ($objectligne->date_cloture) $txt .= " - ".$outputlangs->transnoentities("DateEndRealShort")." : '<strong>'".$datere.'</strong>';

						$pdf->startTransaction();
						$pageposbeforedesc = $pdf->getPage();
						$posYStartDescription = $curY;
						$pdf->writeHTMLCell($text_length - $curX, 8, $curX, $curY, dol_concatdesc($txtpredefinedservice, dol_concatdesc($txt, $desc)), 0, 1, false, true);
						$posYAfterDescription = $pdf->GetY();
						$pageposafter = $pdf->getPage();

						if (! empty($product->barcode) && ! empty($product->barcode_type_code) && $object->lines[$i]->product_type != 9 && $conf->global->ULTIMATEPDF_GENERATE_CONTRACT_WITH_PRODUCTS_BARCODE == 1)
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
							$pdf->rollbackTransaction(true);
							$pageposbeforedesc = $pdf->getPage();
							$pageposafter = $pageposbefore;
							$posYStartDescription = $curY;
							$pdf->setPageOrientation('', 1, $heightforfooter);	// The only function to edit the bottom margin of current page to set it.
							$pdf->writeHTMLCell($text_length - $curX, 8, $curX, $curY, dol_concatdesc($txtpredefinedservice, dol_concatdesc($txt, $desc)), 0, 1, false, true);
							$posYAfterDescription = $pdf->GetY();
							$pageposafter=$pdf->getPage();

							if (! empty($product->barcode) && ! empty($product->barcode_type_code) && $object->lines[$i]->product_type != 9 && $conf->global->ULTIMATEPDF_GENERATE_CONTRACT_WITH_PRODUCTS_BARCODE == 1)
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
								if ($i == ($nblines - 1))	// No more lines, and no space left to show total, so we create a new page
								{
									$pdf->AddPage('', '', true);
									if (! empty($tplidx)) $pdf->useTemplate($tplidx);
									$pdf->setPage($pageposafter + 1);
								}
							}
							else
							{
								// We found a page break
								$showpricebeforepagebreak = 1;
							}
						}
						else	// No pagebreak
						{
							$pdf->commitTransaction();
						}
						$posYAfterDescription = $pdf->GetY();

						$nexY = $pdf->GetY();

						$pageposafter = $pdf->getPage();

						$pdf->setPage($pageposbefore);
						$pdf->setTopMargin($this->marge_haute);
						$pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.

						// We suppose that a too long description is moved completely on next page
						if ($pageposafter>$pageposbefore && empty($showpricebeforepagebreak)) 
						{
							$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
						}
						if ($nexY>$curY && $pageposafter>$pageposbefore)	
						{
							$pdf->setPage($pageposafter); $curY = $tab_top_newpage+1;
						}
						if ($pageposbeforedesc<$pageposafterdesc)
						{
							$pdf->setPage($pageposbeforedesc); $curY = $posYStartDescription;
						}

						$pdf->SetFont('', '', $default_font_size - 2);   // On repositionne la police par defaut
						
						if ($posYStartDescription>$posYAfterDescription && $pageposafter>$pageposbefore)
						{
							$pdf->setPage($pageposbefore); $curY = $posYStartDescription+1;
						}
						if ($curY+4>($this->page_hauteur - $heightforfooter))	
						{			
							$pdf->setPage($pageposafter); $curY = $tab_top_newpage;
						}
						
						//Line numbering
						if (! empty($conf->global->ULTIMATE_CONTRACT_WITH_LINE_NUMBER))
						{
							// Numbering
							if ($this->getColumnStatus('num') && array_key_exists($i, $object->lines))
							{
								$this->printStdColumnContent($pdf, $curY, 'num', $line_number);
								//$nexY = max($pdf->GetY(),$nexY);
								$line_number++;
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
								$subprice = $object->lines[$i]->subprice;
								$up_excl_tax = price($subprice, 0, $outputlangs);							
								$this->printStdColumnContent($pdf, $curY, 'subprice', $up_excl_tax);
								//$nexY = max($pdf->GetY(),$nexY);
							}
						}
						
						// Discount on line                	
						if ($this->atleastonediscount && !empty($conf->global->ULTIMATE_GENERATE_CONTRACTS_WITH_DISCOUNT))
						{					
							if ($this->getColumnStatus('discount') && $object->lines[$i]->remise_percent)
							{
								$remise_percent = pdf_getlineremisepercent($object, $i, $outputlangs, $hidedetails);
								$this->printStdColumnContent($pdf, $curY, 'discount', $remise_percent);
								//$nexY = max($pdf->GetY(),$nexY);
							}
						}
						
						// Unit price after discount					
						if ($this->atleastonediscount && !empty($conf->global->ULTIMATE_GENERATE_CONTRACTS_WITH_PUAFTER)) 
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
						
						if (!empty($conf->global->ULTIMATE_SHOW_LINE_TTTC))
						{
							// Total TTC line
							if ($this->getColumnStatus('totalincltax') && empty($conf->global->ULTIMATE_SHOW_HIDE_THT) && empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
							{
								$subprice = $object->lines[$i]->total_ttc;
								$total_incl_tax = price(price2num($subprice,'MU'), 0, $outputlangs);
								$this->printStdColumnContent($pdf, $curY, 'totalincltax', $total_incl_tax);
								//$nexY = max($pdf->GetY(),$nexY);
							}
						}
						else
						{				
							// Total HT line
							if ($this->getColumnStatus('totalexcltax') && empty($conf->global->ULTIMATE_SHOW_HIDE_THT) && empty($conf->global->ULTIMATE_SHOW_HIDE_ALL_PRICES))
							{
								$subprice = $object->lines[$i]->total_ht;
								$total_excl_tax = price(price2num($subprice,'MU'), 0, $outputlangs);
								$this->printStdColumnContent($pdf, $curY, 'totalexcltax', $total_excl_tax);
								//$nexY = max($pdf->GetY(),$nexY);
							}
						}
						
						if ($posYAfterImage > $posYAfterDescription) $nexY = $posYAfterImage;
						
						// Add line
						if (! empty($conf->global->ULTIMATE_CONTRACT_PDF_DASH_BETWEEN_LINES) && $i < ($nblines - 1))
						{
							$pdf->setPage($pageposafter);
							$pdf->SetLineStyle(array('dash'=>'1, 1', 'color'=>array(70, 70, 70)));
							if ($conf->global->ULTIMATEPDF_GENERATE_CONTRACT_WITH_PRODUCTS_BARCODE == 1 && !empty($product->barcode))
							{
								$pdf->line($this->marge_gauche, $nexY + 4, $this->page_largeur - $this->marge_droite, $nexY + 4);
							}
							else
							{
								$pdf->line($this->marge_gauche, $nexY + 1, $this->page_largeur - $this->marge_droite, $nexY + 1);
							}
							$pdf->SetLineStyle(array('dash'=>0));
						}
						
						if ($conf->global->ULTIMATEPDF_GENERATE_CONTRACT_WITH_PRODUCTS_BARCODE == 1) 
						{
							$nexY += 5;    // Passe espace entre les lignes. C'est la bonne valeur pour gérer les codes-barres.
						}
						else
						{
							$nexY += 2;    // Passe espace entre les lignes
						}  
						
						// Detect if some page were added automatically and output _tableau for past pages
						while ($pagenb < $pageposafter)
						{
							$pdf->setPage($pagenb);
							if ($pagenb == $pageposbeforeprintlines)
							{
								$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code);
							}
							else
							{
								$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code);
							}
							$this->_pagefoot($pdf, $object, $outputlangs, 1);
							$pagenb++;
							$pdf->setPage($pagenb);
							$pdf->setPageOrientation('', 1, 0);	// The only function to edit the bottom margin of current page to set it.
							if (empty($conf->global->ULTIMATE_CONTRACT_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
						}
						if (isset($object->lines[$i+1]->pagebreak) && $object->lines[$i+1]->pagebreak)
						{
							if ($pagenb == $pageposafter)
							{
								$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code);
							}
							else
							{
								$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforfooter, 0, $outputlangs, 0, 1, $object->multicurrency_code);
							}
							$this->_pagefoot($pdf,$object,$outputlangs,1);
							// New page
							$pdf->AddPage();
							if (! empty($tplidx)) $pdf->useTemplate($tplidx);
							$pagenb++;
							if (empty($conf->global->ULTIMATE_CONTRACT_PDF_DONOTREPEAT_HEAD)) $this->_pagehead($pdf, $object, 0, $outputlangs);
						}
					}
				}

				// Show square
				if ($pagenb == $pageposbeforeprintlines)
				{
					$this->_tableau($pdf, $tab_top, $this->page_hauteur - $tab_top - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0, $object->multicurrency_code);
				}
				else
				{
					$this->_tableau($pdf, $tab_top_newpage, $this->page_hauteur - $tab_top_newpage - $heightforinfotot - $heightforfreetext - $heightforfooter, 0, $outputlangs, 0, 0, $object->multicurrency_code);
				}
				$bottomlasttab=$this->page_hauteur - $heightforinfotot - $heightforfreetext - $heightforfooter + 1;
				
				// Affiche zone agreement
				$posy = $this->_agreement($pdf, $object, $posy, $outputlangs);
				
				// Pied de page
				$this->_pagefoot($pdf,$object,$outputlangs);
				if (method_exists($pdf,'AliasNbPages')) $pdf->AliasNbPages();
				
				// Add PDF to be merged
				if (! empty($conf->global->ULTIMATEPDF_GENERATE_CONTRACTS_WITH_MERGED_PDF))
				{
					dol_include_once ( '/ultimatepdf/class/documentmergedpdf.class.php' );
					
					$already_merged = array();

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
									if (! empty ($conf->contrat->enabled))
									$filetomerge_dir = $conf->contrat->dir_output. '/' . dol_sanitizeFileName($object->ref);
									
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
				if (! is_object($hookmanager))
				{
					include_once DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php';
					$hookmanager=new HookManager($this->db);
				}
				$hookmanager->initHooks(array('pdfgeneration'));
				$parameters = array('file'=>$file, 'object'=>$object, 'outputlangs'=>$outputlangs);
				global $action;
				$reshook=$hookmanager->executeHooks('afterPDFCreation', $parameters, $this, $action);    // Note that $action and $object may have been modified by some hooks

				if (! empty($conf->global->MAIN_UMASK))
				@chmod($file, octdec($conf->global->MAIN_UMASK));

				return 1;
			}
			else
			{
				$this->error = $langs->trans("ErrorCanNotCreateDir", $dir);
				return 0;
			}
		}
		else
		{
			$this->error = $langs->trans("ErrorConstantNotDefined", "CONTRACT_OUTPUTDIR");
			return 0;
		}
		$this->error = $langs->trans("ErrorUnknown");
		
		unset($_SESSION['ultimatepdf_model']);
		
		return 0;   // Erreur par defaut
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
		global $conf;
		
		$default_font_size = pdf_getPDFFontSize($outputlangs);
		$textcolor = array('25', '25', '25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor =  html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}
		$widthrecbox = ($this->page_largeur - $this->marge_gauche - $this->marge_droite - 4) / 2;
		
		if (! empty($conf->global->ULTIMATE_DISPLAY_CONTRACT_AGREEMENT_BLOCK))
	    {
			$heightforfreetext = (isset($conf->global->MAIN_PDF_FREETEXT_HEIGHT) ? $conf->global->MAIN_PDF_FREETEXT_HEIGHT : 12);	// Height reserved to output the free text on last page
			$heightforfooter = $this->marge_basse + 12;	// Height reserved to output the footer (value include bottom margin)
			$heightforinfotot = 35;	// Height reserved to output the info and total part
			$deltay = $this->page_hauteur - $heightforfreetext - $heightforfooter - $heightforinfotot;	
			$cury = max($cury, $deltay);
			$deltax = $this->marge_gauche;

			$pdf->RoundedRect($deltax, $cury, $widthrecbox, 40, 2, $round_corner = '1111', 'S', $this->style, array());
			$pdf->SetFont('','B', $default_font_size - 1);
			$pdf->SetXY($deltax, $cury);
			$titre = $outputlangs->transnoentities("ContactNameAndSignature", $this->emetteur->name);
			$pdf->MultiCell(80, 5, $titre, 0, 'L',0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($deltax, $cury+5);
			$pdf->SetFont('','I', $default_font_size - 2);
			$pdf->MultiCell(90, 3, "",0,'L',0);
			$pdf->SetXY($deltax, $cury+12);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->MultiCell(80, 3, $outputlangs->transnoentities("DocORDER3"), 0, 'L', 0);
			$pdf->SetXY($deltax, $cury+17);
			$pdf->SetFont('','I', $default_font_size - 2);
			$pdf->MultiCell(80, 3, $outputlangs->transnoentities("DocORDER5"), 0, 'L', 0);
			
			$posy = max($posy, $deltay);
			$deltax = $this->marge_gauche + $widthrecbox + 4;
			
			// Recipient name
			// On peut utiliser le nom de la societe du contact
			if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
			{
				$thirdparty = $object->contact;
			} 
			else 
			{
				$thirdparty = $object->thirdparty;
			}
			
			$pdf->RoundedRect($deltax, $posy, $widthrecbox, 40, 2, $round_corner = '1111', 'S', $this->style, array());
			$pdf->SetFont('','B', $default_font_size - 1);
			$pdf->SetXY($deltax, $posy);
			$titre = $outputlangs->transnoentities("ContactNameAndSignature", $object->thirdparty->nom); 
			$pdf->MultiCell(80, 5, $titre, 0, 'L',0);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->SetXY($deltax, $posy+5);
			$pdf->SetFont('','I', $default_font_size - 2);
			$pdf->MultiCell(90, 3, $outputlangs->transnoentities('DocORDER2'),0,'L',0);
			$pdf->SetXY($deltax, $posy+12);
			$pdf->SetFont('','', $default_font_size - 2);
			$pdf->MultiCell(80, 3, $outputlangs->transnoentities('DocORDER3'), 0, 'L', 0);
			$pdf->SetXY($deltax, $posy+17);
			$pdf->SetFont('','I', $default_font_size - 2);
			$pdf->MultiCell(80, 3, $outputlangs->transnoentities('DocORDER4'), 0, 'L', 0);

			return $posy;
		}
	}

	/**
	 *   Show table for lines
	 *
	 *   @param		PDF			&$pdf     		Object PDF
	 *   @param		string		$tab_top		Top position of table
	 *   @param		string		$tab_height		Height of table (rectangle)
	 *   @param		int			$nexY			Y
	 *   @param		Translate	$outputlangs	Langs object
	 *   @param		int			$hidetop		Hide top bar of array
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
		
		$pdf->SetTextColorArray($textcolor);
		$pdf->SetFillColorArray($bgcolor);
		$pdf->SetFont('','', $default_font_size - 2);	
		if ($roundradius == 0) 
		{
			$roundradius = 0.1; //RoundedRect don't support $roundradius to be 0
		}
		// Output RoundedRect
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
	 *  @return	void
	 */
	function _pagehead(&$pdf, $object, $showaddress, $outputlangs)
	{
		global $conf,$langs,$hookmanager;
		
		// Translations
		$outputlangs->loadLangs(array("main", "dict", "bills", "companies", "contracts", "ultimatepdf@ultimatepdf"));
		
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
		$textcolor = array('25','25','25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$textcolor =  html2rgb($conf->global->ULTIMATE_TEXTCOLOR_COLOR);
		}
		$qrcodecolor = array('25','25','25');
		if (!empty($conf->global->ULTIMATE_TEXTCOLOR_COLOR))
		{
			$qrcodecolor =  html2rgb($conf->global->ULTIMATE_QRCODECOLOR_COLOR);
		}
		
		$main_page = $this->page_largeur-$this->marge_gauche-$this->marge_droite;	

		pdf_pagehead($pdf,$outputlangs,$this->page_hauteur);

		//Affiche le filigrane brouillon - Print Draft Watermark
		if($object->statut==0 && (! empty($conf->global->CONTRACT_DRAFT_WATERMARK)) )
		{
			pdf_watermark($pdf,$outputlangs,$this->page_hauteur,$this->page_largeur,'mm',$conf->global->CONTRACT_DRAFT_WATERMARK);
		}

		//Prepare la suite
		$pdf->SetTextColorArray($textcolor);
		$pdf->SetFont('', 'B', $default_font_size + 1);

		$posx = $this->page_largeur - $this->marge_droite - 100;
		$posy = $this->marge_haute;

		$pdf->SetXY($this->marge_gauche, $posy);

		// Other Logo
		$id = $conf->global->ULTIMATE_DESIGN;
		$upload_dir	= $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/';	
		$filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview.*\.png)$', 'name', 0, 1);
		$otherlogo = $conf->ultimatepdf->dir_output.'/otherlogo/'.$id.'/'.$filearray[0]['name'];
		
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
		if (! empty($conf->global->ULTIMATEPDF_GENERATE_CONTRACT_WITH_TOP_BARCODE))
		{
			$result = $object->thirdparty->fetch_barcode();
			$barcode = $object->thirdparty->barcode;	
			$posxbarcode = $this->page_largeur * 2/3;
			$posybarcode = $posy - $this->marge_haute;
			$pdf->SetXY($posxbarcode, $posy - $this->marge_haute);
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
				if (empty($this->messageErrBarcodeSet)) 
				{
					setEventMessages($outputlangs->trans("BarCodeDataForThirdpartyMissing"), null, 'errors');
					$this->messageErrBarcodeSet = true;
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
			$heightQRcode = $logo_height;
		}
		else 
		{
			$heightQRcode = 30;
		}
		$posxQRcode = $this->page_largeur / 2;		
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
		if (! empty($conf->global->ULTIMATEPDF_GENERATE_CONTRACT_WITH_TOP_QRCODE))
		{
			$code = pdf_codeContents(); //get order link
			$pdf->write2DBarcode($code, 'QRCODE,L', $posxQRcode, $posy, $heightQRcode, $heightQRcode, $styleQr, 'N');
		}
		// My Company QR-code
		if (! empty($conf->global->ULTIMATEPDF_GENERATE_CONTRACT_WITH_MYCOMP_QRCODE))
		{
			$code = pdf_mycompCodeContents();
			$pdf->write2DBarcode($code, 'QRCODE,L', $posxQRcode, $posy, $heightQRcode, $heightQRcode, $styleQr, 'N');
		}
		
		//Document name
		$pdf->SetFont('', 'B', $default_font_size + 3);
		$pdf->SetXY($posx, $posy);
		$pdf->SetTextColorArray($textcolor);
		$title = $outputlangs->transnoentities("ContractCard");
		$pdf->MultiCell(100, 4, $title, '', 'R');

		$pdf->SetFont('', 'B', $default_font_size + 2);

		$posy = $pdf->getY();
		$pdf->SetXY($posx, $posy);
		$pdf->SetTextColorArray($textcolor);
		$pdf->MultiCell(100, 4, $outputlangs->transnoentities("Ref")." : " . $outputlangs->convToOutputCharset($object->ref), '', 'R');

		$posy = $pdf->getY();
		
		$top_shift = 0;
		// Show list of linked objects
		$current_y = $pdf->getY();
		$posy = pdf_writeLinkedObjects($pdf, $object, $outputlangs, $posx, $posy, 100, 3, 'R', $default_font_size);
		if ($current_y < $pdf->getY())
		{
			$top_shift = $pdf->getY() - $current_y;
		}
		
		$posy=$pdf->getY();	

		if ($showaddress)
		{
			// Sender properties
			$carac_emetteur .= pdf_element_build_address($outputlangs, $this->emetteur, $object->thirdparty, '', 0, 'source', $object);

			// Show sender
			$posy = $logo_height + $this->marge_haute + $top_shift;	
			$posx = $this->marge_gauche;
			if (($conf->global->ULTIMATE_PDF_CONTRACT_ADDALSOTARGETDETAILS == 1) || (! empty($conf->global->MAIN_INFO_SOCIETE_NOTE) && !empty($this->emetteur->note_private)) || (! empty($conf->global->MAIN_INFO_TVAINTRA) && !empty($this->emetteur->tva_intra)))
			{
				$hautcadre = 52;
			}
			else
			{
				$hautcadre = 48;
			}
			$widthrecbox = $conf->global->ULTIMATE_WIDTH_RECBOX;
			if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx = $this->page_largeur - $this->marge_droite - $widthrecbox;  
			
			if ($roundradius == 0) 
			{
				$roundradius = 0.1; //RoundedRect don't support $roundradius to be 0
			}
			$this->border_style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => $dashdotted , 'color' => $bordercolor);
			// Show sender frame
			$pdf->SetTextColorArray($textcolor);
			$pdf->SetFont('', '', $default_font_size - 1);
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($posx, $posy, $widthrecbox, $hautcadre, $roundradius, $round_corner = '1111', $senderstyle, $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);

			// Show sender name
	        $pdf->SetXY($posx + 2, $posy + 3);
			$pdf->SetFont('', 'B', $default_font_size - 1);
			if (! empty($conf->global->ULTIMATE_PDF_ALIAS_COMPANY))
			{
				$pdf->MultiCell($widthrecbox - 5, 4, $outputlangs->convToOutputCharset($conf->global->ULTIMATE_PDF_ALIAS_COMPANY), 0, 'L');
			}
			else
			{
				$pdf->MultiCell($widthrecbox - 5, 4, $outputlangs->convToOutputCharset($this->emetteur->name), 0, 'L');
			}
				
			$posy = $pdf->getY();

			// Show sender information
			$pdf->SetFont('', '', $default_font_size - 1);
			$pdf->SetXY($posx + 2, $posy);
			$pdf->writeHTMLCell($widthrecbox - 5, 4, $posx + 2, $posy, $carac_emetteur, 0, 2, 0, true, 'L', true);
			$posy = $pdf->getY();
			
			// Show private note from societe
			if (! empty($conf->global->MAIN_INFO_SOCIETE_NOTE) && ! empty($this->emetteur->note_private))
    		{
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecbox - 5, 8, $posx + 2, $posy + 2, dol_string_nohtmltag($this->emetteur->note_private), 0, 1, 0, true, 'L', true);
			}
			
			// If CUSTOMER and BILLING contact defined, we use it
			if ($arrayidcontact = $object->getIdContact('external', 'BILLING') && $object->getIdContact('external', 'CUSTOMER'))
			{
				// If BILLING contact defined on invoice, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external','BILLING');
				if (count($arrayidcontact) > 0);
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
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

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				// Recipient address
				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact ? $object->contact : ''),$usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient = $this->page_largeur - $this->marge_droite - $this->marge_gauche - $conf->global->ULTIMATE_WIDTH_RECBOX - 2;
				$posy = $logo_height + $this->marge_haute + $top_shift;
				$posx = $this->page_largeur - $this->marge_droite - $widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx = $this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('','', $default_font_size - 2);

				// Show invoice address
				$posy = $logo_height + $this->marge_haute + $top_shift;
				$posx = $posx + $widthrecboxrecipient / 2;
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient / 2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx-$widthrecboxrecipient / 2, $posy - 4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("BillAddress"), 0, 'R');

				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 2);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);	
				
				// If CUSTOMER contact defined on invoice, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external', 'CUSTOMER');
			
				if (count($arrayidcontact) > 0)
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
				}

				// On peut utiliser le nom de la societe du contact
				if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact?$object->contact : ''),$usecontact, 'target', $object);
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);

				// Show shipping address
				$posy = $logo_height + $this->marge_haute + $top_shift;
				$posx = $posx + $widthrecboxrecipient / 2;
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient / 2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx - $widthrecboxrecipient / 2, $posy - 4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("TypeContact_contrat_external_CUSTOMER"), 0, 'R');	
				
				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 2);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();

				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);
			}
			// If CUSTOMER and SALESREPSIGN contact defined, we use it
			elseif ($arrayidcontact = $object->getIdContact('external', 'CUSTOMER') && $object->getIdContact('external', 'SALESREPSIGN'))
			{
				// If CUSTOMER contact defined on invoice, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external', 'CUSTOMER');
				if (count($arrayidcontact) > 0);
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				// Recipient address
				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact ? $object->contact : ''),$usecontact,'target', $object);

				// Show recipient
				$widthrecboxrecipient = $this->page_largeur - $this->marge_droite - $this->marge_gauche - $conf->global->ULTIMATE_WIDTH_RECBOX - 2;
				$posy = $logo_height + $this->marge_haute + $top_shift;
				$posx = $this->page_largeur - $this->marge_droite - $widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx = $this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);

				// Show invoice address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient / 2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx - $widthrecboxrecipient / 2, $posy - 4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("TypeContact_contrat_external_CUSTOMER"), 0, 'R');

				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 2);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);	
				
				// If SALESREPSIGN contact defined on invoice, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external', 'SALESREPSIGN');
			
				if (count($arrayidcontact) > 0)
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
				}

				// On peut utiliser le nom de la societe du contact
				if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact ? $object->contact : ''),$usecontact, 'target', $object);
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);

				// Show shipping address
				$posy = $logo_height + $this->marge_haute + $top_shift;
				$posx = $posx + $widthrecboxrecipient / 2;
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient / 2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx-$widthrecboxrecipient / 2, $posy - 4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("TypeContact_contrat_external_SALESREPSIGN"), 0, 'R');	
				
				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 2);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();

				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);
			}
			// If BILLING and SALESREPSIGN contact defined, we use it
			elseif ($arrayidcontact = $object->getIdContact('external', 'BILLING') && $object->getIdContact('external', 'SALESREPSIGN'))
			{
				// If BILLING contact defined on invoice, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external', 'BILLING');
				if (count($arrayidcontact) > 0);
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				// Recipient address
				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact?$object->contact : ''),$usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient = $this->page_largeur - $this->marge_droite - $this->marge_gauche - $conf->global->ULTIMATE_WIDTH_RECBOX-2;
				$posy = $logo_height + $this->marge_haute  +$top_shift;
				$posx = $this->page_largeur - $this->marge_droite - $widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx = $this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);

				// Show invoice address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient / 2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx - $widthrecboxrecipient / 2, $posy - 4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("BillAddress"), 0, 'R');

				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 2);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);	
				
				// If SALESREPSIGN contact defined on invoice, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external', 'SALESREPSIGN');
			
				if (count($arrayidcontact) > 0)
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
				}

				// On peut utiliser le nom de la societe du contact
				if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);
				
				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact?$object->contact : ''),$usecontact, 'target', $object);
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);

				// Show shipping address
				$posy = $logo_height + $this->marge_haute + $top_shift;
				$posx = $posx + $widthrecboxrecipient / 2;
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient / 2, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx - $widthrecboxrecipient / 2, $posy - 4);
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("TypeContact_contrat_external_SALESREPSIGN"), 0, 'R');	
				
				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 2);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();

				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);
			}
			elseif ($arrayidcontact = $object->getIdContact('external', 'BILLING'))
			{
				// If BILLING contact defined on invoice, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external', 'BILLING');
				if (count($arrayidcontact) > 0)
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);

				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact ? $object->contact : ''),$usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient = $this->page_largeur - $this->marge_droite - $this->marge_gauche - $conf->global->ULTIMATE_WIDTH_RECBOX - 2;
				$posy = $logo_height + $this->marge_haute + $top_shift;		
				$posx = $this->page_largeur - $this->marge_droite - $widthrecboxrecipient;	
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx = $this->marge_gauche;
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);

				// Show billing address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx, $posy - 4);		
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("BillAddress"), 0, 'R');
				
				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 3);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);

			}
			elseif ($arrayidcontact = $object->getIdContact('external', 'CUSTOMER'))
			{
				// If CUSTOMER contact defined on order, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external', 'CUSTOMER');
				if (count($arrayidcontact) > 0)
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);

				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact ? $object->contact : ''),$usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient = $this->page_largeur - $this->marge_droite - $this->marge_gauche - $conf->global->ULTIMATE_WIDTH_RECBOX - 2;
				$posy = $logo_height + $this->marge_haute + $top_shift;		
				$posx = $this->page_largeur - $this->marge_droite - $widthrecboxrecipient;	
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx = $this->marge_gauche;
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);

				// Show Contact_commande_external_CUSTOMER address
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx, $posy - 4);		
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("TypeContact_contrat_external_CUSTOMER"), 0, 'R');
				
				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 3);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);

			}
			elseif ($arrayidcontact = $object->getIdContact('external', 'SALESREPSIGN'))
			{
				// If SALESREPSIGN contact defined on order, we use it
				$usecontact = false;
				$arrayidcontact = $object->getIdContact('external', 'SALESREPSIGN');
				if (count($arrayidcontact) > 0)
				{
					$usecontact = true;
					$result = $object->fetch_contact($arrayidcontact[0]);
				}

				// Recipient name
				// On peut utiliser le nom de la societe du contact
				if ($usecontact && ! empty($conf->global->MAIN_USE_COMPANY_NAME_OF_CONTACT)) 
				{
					$thirdparty = $object->contact;
				} 
				else 
				{
					$thirdparty = $object->thirdparty;
				}

				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);

				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $thirdparty, ($usecontact ? $object->contact : ''),$usecontact, 'target', $object);

				// Show recipient
				$widthrecboxrecipient = $this->page_largeur - $this->marge_droite - $this->marge_gauche - $conf->global->ULTIMATE_WIDTH_RECBOX - 2;
				$posy = $logo_height + $this->marge_haute + $top_shift;		
				$posx = $this->page_largeur - $this->marge_droite - $widthrecboxrecipient;	
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx = $this->marge_gauche;
				
				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);

				// Show SALESREPSIGN address
				$pdf->SetXY($posx, $posy);
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetXY($posx, $posy - 4);		
				$pdf->MultiCell($widthrecboxrecipient, 4, $outputlangs->transnoentities("TypeContact_contrat_external_SALESREPSIGN"), 0, 'R');
				
				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 3);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);

			}
			else
			{
				$thirdparty = $object->thirdparty;
				// Recipient name
				$carac_client_name = pdfBuildThirdpartyName($thirdparty, $outputlangs);
				// Recipient address
				$carac_client = pdf_element_build_address($outputlangs, $this->emetteur, $object->thirdparty, '', 0, 'target', $object);

				// Show recipient
				$widthrecboxrecipient = $this->page_largeur - $this->marge_droite - $this->marge_gauche  -$conf->global->ULTIMATE_WIDTH_RECBOX - 2;
				$posy = $logo_height + $this->marge_haute + $top_shift;
				$posx = $this->page_largeur - $this->marge_droite - $widthrecboxrecipient;
				if (! empty($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT) && ($conf->global->ULTIMATE_INVERT_SENDER_RECIPIENT != "no")) $posx = $this->marge_gauche;

				// Show recipient frame
				$pdf->SetTextColorArray($textcolor);
				$pdf->SetFont('', '', $default_font_size - 2);
				
				// Show shipping address
				$pdf->SetXY($posx, $posy - 4);	
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($posx, $posy, $widthrecboxrecipient, $hautcadre, $roundradius, $round_corner = '1111', $receiptstyle, $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);

				// Show recipient name
				$pdf->SetXY($posx + 2, $posy + 3);
				$pdf->SetFont('', 'B', $default_font_size);
				$pdf->MultiCell($widthrecboxrecipient - 5, 4, $carac_client_name, 0, 'L');
				
				$posy = $pdf->getY();
				
				// Show recipient information
				$pdf->SetFont('', '', $default_font_size - 1);
				$pdf->SetXY($posx + 2, $posy);
				$pdf->writeHTMLCell($widthrecboxrecipient - 5, 4, $posx + 2, $posy, $carac_client, 0, 2, 0, true, 'L', true);				
			}
			
			 // Other informations

	        $pdf->SetFillColor(255,255,255);

			// Date contrat
			if (! empty($conf->global->ULTIMATE_CONTRACTS_PDF_SHOW_PROJECT))
			{
				$width = $main_page / 5 - 1.5;
			}
			else
			{
				$width = $main_page / 4 - 1.5;
			}
			$RoundedRectHeight = $this->marge_haute + $logo_height + $hautcadre + $top_shift + 2;
			$pdf->SetAlpha($opacity);			
			$pdf->RoundedRect($this->marge_gauche, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
	        $pdf->SetFont('', 'B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche, $RoundedRectHeight);
	        $pdf->SetTextColorArray($textcolor);
	        $pdf->MultiCell($width, 5, $outputlangs->transnoentities("Date"), 0, 'C', false);

	        $pdf->SetFont('', '', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche, $RoundedRectHeight+6);
	        $pdf->SetTextColorArray($textcolor);
	        $pdf->MultiCell($width, 6, dol_print_date($object->date_creation, "day", false, $outputlangs, true), '0', 'C');			

	        // Commercial Interlocutor
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($this->marge_gauche + $width + 2, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
	        $pdf->SetFont('', 'B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche + $width + 4,$RoundedRectHeight);
	        $pdf->SetTextColorArray($textcolor);
	        $pdf->MultiCell($width, 5, $outputlangs->transnoentities("Contact"), 0, 'C', false);

	        $contact_id = $object->getIdContact('internal','SALESREPFOLL');

	        if (! empty($contact_id))
	        {
	        	$object->fetch_user($contact_id[0]);
	        	$pdf->SetFont('', '', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche + $width + 2, $RoundedRectHeight + 6);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 7, $object->user->firstname.' '.$object->user->lastname, '0', 'C');
				$pdf->SetXY($this->marge_gauche + $width + 2, $RoundedRectHeight + 9);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 7, $object->user->office_phone, '0', 'C');
	        }
			
	        else if ($object->user_author_id)
	        {
	        	$object->fetch_user($object->user_author_id);
	        	$pdf->SetFont('', '', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche + $width + 2, $RoundedRectHeight + 6);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 7, $object->user->firstname.' '.$object->user->lastname, '0', 'C');
				$pdf->SetXY($this->marge_gauche + $width + 2, $RoundedRectHeight + 9);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 7, $object->user->office_phone, '0', 'C');
	        }
			else
			{
				$pdf->SetFont('', '', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche + $width + 2, $RoundedRectHeight + 6);
	        	$pdf->SetTextColorArray($textcolor);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->MultiCell($width, 6, '', '0', 'C');
			}

	        // Customer code
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($this->marge_gauche + $width * 2 + 4, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
			$pdf->SetAlpha(1);
	        $pdf->SetFont('', 'B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche + $width * 2 + 4,$RoundedRectHeight);
	        $pdf->SetTextColorArray($textcolor);
	        $pdf->MultiCell($width, 5, $outputlangs->transnoentities("CustomerCode"), 0, 'C', false);

	        if ($object->thirdparty->code_client)
	        {
	        	$pdf->SetFont('', '', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche + $width * 2 + 4, $RoundedRectHeight + 6);
	        	$pdf->SetTextColorArray($textcolor);
	        	$pdf->MultiCell($width, 6, $outputlangs->transnoentities($object->thirdparty->code_client), '0', 'C');
	        }
			else
			{
				$pdf->SetFont('', '', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche + $width * 2 + 4, $RoundedRectHeight + 6);
	        	$pdf->SetTextColorArray($textcolor);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->MultiCell($width, 6, '', '0', 'C');
			}
			
			// Customer ref
			$pdf->SetAlpha($opacity);
			$pdf->RoundedRect($this->marge_gauche + $width * 3 + 6, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor); 
			$pdf->SetAlpha(1);
	        $pdf->SetFont('', 'B', $default_font_size - 2);
	        $pdf->SetXY($this->marge_gauche + $width * 3 + 6, $RoundedRectHeight);
	        $pdf->SetTextColorArray($textcolor);
	        $pdf->MultiCell($width, 5, $outputlangs->transnoentities("RefCustomer"), 0, 'C', false);
	        
			if ($object->ref_customer)
			{
				$pdf->SetFont('', '', $default_font_size - 2);
				$pdf->SetXY($this->marge_gauche + $width * 3 + 6, $RoundedRectHeight + 6);
				$pdf->SetTextColorArray($textcolor);
				$pdf->MultiCell($width, 6, $object->ref_customer, '0', 'C');	
			}
			else
			{
				$pdf->SetFont('', '', $default_font_size - 2);
	        	$pdf->SetXY($this->marge_gauche + $width * 3 + 6, $RoundedRectHeight + 6);
	        	$pdf->SetTextColorArray($textcolor);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->MultiCell($width, 6, '', '0', 'C');
			}

			// Project ref
			if (! empty($conf->global->ULTIMATE_CONTRACTS_PDF_SHOW_PROJECT))
			{
				$pdf->SetAlpha($opacity);
				$pdf->RoundedRect($this->marge_gauche + $width * 4 + 8, $RoundedRectHeight, $width, 6, $roundradius, $round_corner = '1001', 'FD', $this->border_style, $bgcolor);
				$pdf->SetAlpha(1);
				$pdf->SetFont('', 'B', $default_font_size - 2);
				$pdf->SetXY($this->marge_gauche + $width * 4 + 8, $RoundedRectHeight);
				$pdf->SetTextColorArray($textcolor);
				$pdf->MultiCell($width, 5, $outputlangs->transnoentities("Project"), 0, 'C', false);
			
				$object->fetch_projet();
				if (! empty($object->project->ref))
				{
					$langs->load('projects');
					$pdf->SetTextColorArray($textcolor);
					$pdf->SetFont('', '', $default_font_size - 2);
					$pdf->SetXY($this->marge_gauche + $width * 4 + 8, $RoundedRectHeight + 6);
					$pdf->MultiCell($width, 6, (empty($object->project->ref) ? '' : $object->projet->ref), 0, 'C');
				}			
			}
		
		$posy=$pdf->getY();
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
	 *      @return	void
	 */
	function _pagefoot(&$pdf, $object, $outputlangs, $hidefreetext = 0)
	{
		global $conf;
		$showdetails = $conf->global->MAIN_GENERATE_DOCUMENTS_SHOW_FOOT_DETAILS;
		return pdf_ultimatepagefoot($pdf, $outputlangs, 'CONTRACT_FREE_TEXT', $this->emetteur, $this->marge_basse, $this->marge_gauche, $this->page_hauteur, $object, $showdetails, $hidefreetext, $footertextcolor);
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
		if (!empty($conf->global->ULTIMATE_CONTRACT_WITH_LINE_NUMBER))
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
		if (!empty($conf->global->ULTIMATE_CONTRACT_WITH_LINE_NUMBER))
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
		
		if (!empty($conf->global->ULTIMATE_CONTRACT_WITH_LINE_NUMBER) || $conf->global->ULTIMATE_DOCUMENTS_WITH_REF == "yes")
	    {
	        $this->cols['desc']['border-left'] = true;
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
	            'align' => 'R'
	        ),
	        'border-left' => true, // add left line separator
	    );
		
		if(!empty($conf->global->ULTIMATE_GENERATE_CONTRACTS_WITH_PRICEUHT))
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
	            'align' => 'R'
	        ),
	        'border-left' => true, // add left line separator
	    );
		
	    if ($this->atleastonediscount && !empty($conf->global->ULTIMATE_GENERATE_CONTRACTS_WITH_DISCOUNT))
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
	            'align' => 'R'
	        ),
	        'border-left' => true, // add left line separator
	    );
		
		if ($this->atleastonediscount && !empty($conf->global->ULTIMATE_GENERATE_CONTRACTS_WITH_PUAFTER))
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
	            'align' => 'R'
	        ),
	        'border-left' => true, // add left line separator
	    );
		
		if (!empty($conf->global->ULTIMATE_GENERATE_CONTRACTS_WITH_QTY))
		{
	        $this->cols['qty']['status'] = true;
	    }

	    $rank = $rank + 10;
	    $this->cols['totalexcltax'] = array(
	        'rank' => $rank,
	        'width' => 20, // in mm
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
	        'width' => 20, // in mm
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
