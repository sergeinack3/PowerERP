<?php
/*	Copyright (C) 2014 Charles-Fr BENKE  <charles.fr@benke.fr>
	détermination d'un prix de vente en fonction des extrafields saisies
 */
class InterfacePrice
{
	var $db;
	/**
	 *   \brief      Constructeur.
	 *   \param      DB      Handler d'acces base
	 */
	function InterfacePrice($DB)
	{
		$this->db = $DB ;
		
		$this->name = preg_replace('/^Interface/i','',get_class($this));
		$this->family = "interfaceprix";
		$this->description = "Triggers pour modifier le prix de vente en fonction de valeurs saisie en extrafields sur la ligne.";
		$this->version = '3.5.1+1.0.1';                        // 'experimental' or 'powererp' or version
	}
	/**
	 *   \brief      Renvoi nom du lot de triggers
	 *   \return     string      Nom du lot de triggers
	 */
	function getName()
	{
	    return $this->name;
	}
	/**
	 *   \brief      Renvoi descriptif du lot de triggers
	 *   \return     string      Descriptif du lot de triggers
	 */
	function getDesc()
	{
	    return $this->description;
	}
	/**
	 *   \brief      Renvoi version du lot de triggers
	 *   \return     string      Version du lot de triggers
	 */
	function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'experimental') return $langs->trans("Experimental");
		elseif ($this->version == 'powererp') return DOL_VERSION;
		elseif ($this->version) return $this->version;
		else return $langs->trans("Unknown");
	}
	/**
	 *      \brief      Fonction appelee lors du declenchement d'un evenement Powererp.
	 *                  D'autres fonctions run_trigger peuvent etre presentes dans includes/triggers
	 *      \param      action      Code de l'evenement
	 *      \param      object      Objet concerne
	 *      \param      user        Objet user
	 *      \param      lang        Objet lang
	 *      \param      conf        Objet conf
	 *      \return     int         <0 si ko, 0 si aucune action faite, >0 si ok
	 */
	function run_trigger($action,$object,$user,$langs,$conf)
	{
		dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);

		include_once (DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php');
		
		if ($action == 'LINEBILL_INSERT' || $action == 'LINEBILL_UPDATE')
		{
			$objecttrigger=new Facture($this->db);
			// en insert on travail sur la ligne en cours, en update sur la ligne ancienne
			// pas de old line sur les factures en update... corrigé depuis la 3.6.1
			if ($action == 'LINEBILL_INSERT')
				$objectline=$object;
			else
			{
				if (DOL_VERSION < "3.6.0")
					$objectline=$object;
				else
					$objectline=$object->oldline;
			}
			
			$objecttrigger->fetch($objectline->fk_facture);
		}
		elseif ($action == 'LINEORDER_INSERT' || $action == 'LINEORDER_UPDATE')
		{
			$objecttrigger=new Commande($this->db);
			if ($action == 'LINEORDER_INSERT')
				$objectline=$object;
			else
				$objectline=$object->oldline;
			$objecttrigger->fetch($objectline->fk_commande);
		}
		elseif ($action == 'LINEPROPAL_INSERT' || $action == 'LINEPROPAL_UPDATE')
		{
			$objecttrigger=new Propal($this->db);
			if ($action == 'LINEPROPAL_INSERT')
				$objectline=$object;
			else
				$objectline=$object->oldline;
			$objecttrigger->fetch($objectline->fk_propal);
		}
		else
			return 0;

		// on récupère le nouveau prix 
		$specialprice=new SpecialPrice($this->db);
		$autreprix=$specialprice->PriceWithExtrafields($objecttrigger, $objectline);

		// si le prix a été changé
		if(! $autreprix == false)
		{
			$pu =price2num($autreprix);
			$price_base_type='HT';

			$remise_percent=$objectline->remise;

			// détermination du prix 
			$tabprice = calcul_price_total($objectline->qty, $pu, $remise_percent ,$objectline->tva_tx,$objectline->localtax1_tx,$objectline->localtax2,0,$price_base_type, $objectline->info_bits,$objectline->product_type);

			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1=$tabprice[9];
			$total_localtax2=$tabprice[10];
			$pu_ht  = $tabprice[3];
			$pu_tva = $tabprice[4];
			$pu_ttc = $tabprice[5];

			$remise = 0;
			if ($remise_percent > 0)
			{
				$remise = round(($pu * $remise_percent / 100),2);
				$price = ($pu - $remise);
			}

			$price    = price2num($price);

			$object->tva_tx				= $objectline->tva_tx;
			$object->remise_percent		= $remise_percent;
			$object->subprice			= ($objecttrigger->type==2?-1:1)*abs($pu);

			$object->total_ht			= ($objecttrigger->type==2?-1:1)*abs($total_ht);
			$object->total_tva			= ($objecttrigger->type==2?-1:1)*abs($total_tva);
			$object->total_localtax1	= ($objecttrigger->type==2?-1:1)*abs($total_localtax1);
			$object->total_localtax2	= ($objecttrigger->type==2?-1:1)*abs($total_localtax2);
			$object->total_ttc			= ($objecttrigger->type==2?-1:1)*abs($total_ttc);

			// on met à jour mais on n'execute pas le trigger (sinon on boucle en MAJ)
			$result=$object->update($user, 1);
			if($result > 0)
				$objecttrigger->update_price();
		}
		return 0;
    }
}
/**
 *      \class      Skeleton_class
 *      \brief      Put here description of your class
 *		\remarks	Put here some comments
 */
class SpecialPrice // extends CommonObject
{
	var $db;	//!< To store db handler

	function SpecialPrice($DB) 
	{
		$this->db = $DB;
		return 1;
	}

	// return new price
	// $Extrafields = les extrafields de la ligne de la piece
	// $objecttrigger = la piece
	// $object = la ligne de la piece
	function PriceWithExtrafields( $mainelement, $object)
	{
		global $conf;

		require_once (DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php');

		$societe = new Societe($this->db);
		$societe->fetch($mainelement->socid);

		// si c'est un produit référencé
		if ($object->fk_product)
		{
			require_once (DOL_DOCUMENT_ROOT."/product/class/product.class.php");		
			$product = new Product($this->db);
			$product->fetch($object->fk_product);

			$price=0;
			$price_level_client=$societe->price_level;

			if ($product->price_base_type == 'TTC')
			{
				if(isset($price_level_client) && $conf->global->PRODUIT_MULTIPRICES)
					$origineprice=price($product->multiprices_ttc[$price_level_client]);
				else
					$origineprice = price($product->price_ttc);
			}
			else
			{
				if(isset($price_level_client) && $conf->global->PRODUIT_MULTIPRICES)
					$origineprice = price($product->multiprices[$price_level_client]);
				else
					$origineprice = price($product->price);
			}
		}
		else	// si c'est un produit saisie
			$origineprice = price($object->subprice);

		$extrafields = new ExtraFields($this->db);		// les extrafields de la ligne de la pièce
		// fetch optionals attributes and labels
		$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
		$res=$object->fetch_optionals($object->rowid, $extralabels);
		$objectvalue=$object->array_options;

		// maintenant on fait sa cuisine pour le calcul du prix
		$val=strval($conf->global->ExtraPriceFormula);
		eval( $val);

		// ensuite la formule selon les extrafields
		return $newprice;
	}
}
?>