<?php
/* Copyright (C) 2005-2014 Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2005-2014 Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2014      Marcos García		<marcosgdf@gmail.com>
 * Copyright (C) 2017-2019	   Massaoud Bouzenad	<massaoud@dzprod.net>
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
 */

/**
 *  \file       htdocs/gerec/core/triggers/interface_20_modGerec_Gerec.class.php
 *  \ingroup    /gerec/core
 *  \brief      Actions de gerec durant le workflow
 *  
 */
require_once DOL_DOCUMENT_ROOT.'/core/triggers/powererptriggers.class.php';


/**
 *  Class of triggers for demo module
 */
class InterfaceGerec extends PowererpTriggers
{

	public $family = 'product';
	public $picto = 'technic';
	public $description = "Gestionnaire de grille de remises clients";
	public $version = self::VERSION_POWERERP;
	public $name = "Gerec";

	/**
     * Function called when a Powererpr business event is done.
	 * All functions "runTrigger" are triggered if file is inside directory htdocs/core/triggers or htdocs/module/code/triggers (and declared)
     *
     * @param string		$action		Event action code
     * @param Object		$object     Object
     * @param User		    $user       Object user
     * @param Translate 	$langs      Object langs
     * @param conf		    $conf       Object conf
     * @return int         				<0 if KO, 0 if no triggered ran, >0 if OK
     */
    public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
    {
		// Put here code you want to execute when a Powererp business events occurs.
        // Data and type of action are stored into $object and $action
	    # dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id,  LOG_DEBUG);
	    switch ($action) {
		    
		    // Users
		    /*
			// Customer orders
		    case 'ORDER_CREATE':
		    case 'ORDER_CLONE':
		    case 'ORDER_VALIDATE':
		    case 'ORDER_DELETE':
		    case 'ORDER_SENTBYMAIL':
		    case 'ORDER_CLASSIFY_BILLED':
		    */
		    case 'LINEORDER_INSERT':
		    case 'LINEORDER_UPDATE':
		    {
		    	# Application de la grille de remise
		    	# Si une remise a été insérée, on la garde sinon on la détermine et l'applique.
		    	$req = "SELECT cd.subprice, cd.fk_product, cd.fk_commande, cd.tva_tx, cd.qty, cd.remise_percent, cd.total_ht, c.fk_soc FROM ".MAIN_DB_PREFIX."commandedet AS cd LEFT JOIN ".MAIN_DB_PREFIX."commande AS c ON (cd.fk_commande = c.rowid) WHERE cd.rowid = ".$object->id;
		    	$res = $this->db->query($req);

		    	if ($res) {
					$objfd = $this->db->fetch_object($res);
					if ($objfd) {
						$fk_product = (int) $objfd->fk_product;
						$remise 	= (float) $objfd->remise_percent;
						$tva_tx		= (float) $objfd->tva_tx;
						$qty		= (float) $objfd->qty;
						$fk_soc		= (int) $objfd->fk_soc;
						$pu_ht		= (float) $objfd->subprice;
						//$total_ht 	= (float) $objfd->total_ht;
					}
				}

				//die($req);

				if($remise <= 0.01)
				{
					
	                if($remise = $this->fetchRemise($fk_soc, $fk_product, $qty))
	                {
	                	$remise_gtar = $remise['taux'];
	                	$pvht 		 = $remise['pvht'];

						if($pvht > 0)
						{
						# Un nouveau prix unitaire est déclaré
						$pu_ht = $pvht;
						$total_ht = $pu_ht*$qty;
						}

						if($remise_gtar > 0)
						{
						    # Il existe une remise pour ce client pour ce produit
						    #$remise_percent = price2num($remise_gtar);

						    # On calcul donc les nouveaux price, total_ht et total_ttc
						    $price 		= $pu_ht - ($remise_gtar*$pu_ht)/100;
						    //$price 		= price2num($price, 'MU');

						    $total_ht	= $price*$qty;
						    $total_ht 	= price2num($total_ht, 'MT');
						}

						if($tva_tx > 0)
						{
							$total_ttc = $total_ht + ($total_ht*$tva_tx)/100;
							$total_ttc = price2num($total_ttc, 'MU');

							 #$total_ht 	= price2num($total_ht);
							$total_tva  = $total_ttc - $total_ht;
						}

						# On met à jour la ligne insérée:
						$price = ( empty($price) ) ? 0 : $price;// for type compatibility.

						$req = "UPDATE ".MAIN_DB_PREFIX."commandedet SET remise_percent = '{$remise_gtar}', price = '{$price}', subprice = '{$pu_ht}', total_ht = '{$total_ht}', total_tva = '{$total_tva}', total_ttc = '{$total_ttc}' WHERE rowid = ".$object->id;
						$res = $this->db->query($req);
	                }
                } 			

		    }

		    /*
		    case 'LINEORDER_DELETE':

			// Supplier orders
		    case 'ORDER_SUPPLIER_CREATE':
		    case 'ORDER_SUPPLIER_CLONE':
		    case 'ORDER_SUPPLIER_VALIDATE':
		    case 'ORDER_SUPPLIER_DELETE':
		    case 'ORDER_SUPPLIER_APPROVE':
		    case 'ORDER_SUPPLIER_REFUSE':
		    case 'ORDER_SUPPLIER_CANCEL':
		    case 'ORDER_SUPPLIER_SENTBYMAIL':
            case 'ORDER_SUPPLIER_DISPATCH':
		    case 'LINEORDER_SUPPLIER_DISPATCH':
		    case 'LINEORDER_SUPPLIER_CREATE':
		    case 'LINEORDER_SUPPLIER_UPDATE':

			// Proposals
		    case 'PROPAL_CREATE':
		    case 'PROPAL_CLONE':
		    case 'PROPAL_MODIFY':
		    case 'PROPAL_VALIDATE':
		    case 'PROPAL_SENTBYMAIL':
		    case 'PROPAL_CLOSE_SIGNED':
		    case 'PROPAL_CLOSE_REFUSED':
		    case 'PROPAL_DELETE':
		    */
		    case 'LINEPROPAL_INSERT':
		    case 'LINEPROPAL_UPDATE':
		    {
		    	# Application de la grille de remise
		    	# Si une remise a été insérée, on la garde sinon on la détermine et l'applique.
		    	$req = "SELECT pd.subprice, pd.fk_product, pd.fk_propal, pd.tva_tx, pd.qty, pd.remise_percent, pd.total_ht, p.fk_soc FROM ".MAIN_DB_PREFIX."propaldet AS pd LEFT JOIN ".MAIN_DB_PREFIX."propal AS p ON (pd.fk_propal = p.rowid) WHERE pd.rowid = ".$object->id;
		    	$res = $this->db->query($req);

		    	if ($res) {
					$objfd = $this->db->fetch_object($res);
					if ($objfd) {
						$fk_product = (int) $objfd->fk_product;
						$remise 	= (float) $objfd->remise_percent;
						$tva_tx		= (float) $objfd->tva_tx;
						$qty		= (float) $objfd->qty;
						$fk_soc		= (int) $objfd->fk_soc;
						$pu_ht		= (float) $objfd->subprice;
						//$total_ht 	= (float) $objfd->total_ht;
					}
				}

				if($remise <= 0.01)
				{
					 if($remise = $this->fetchRemise($fk_soc, $fk_product, $qty))
					 {
						$remise_gtar = $remise['taux'];
						$pvht 		 = $remise['pvht'];

				        if($pvht > 0)
				        {
				        # Un nouveau prix unitaire est déclaré
				        $pu_ht 		= $pvht;
				        $total_ht 	= $pu_ht*$qty;
				        }

				        if($remise_gtar > 0)
				        {
				            # Il existe une remise pour ce client pour ce produit
				            
				            # On calcul donc les nouveaux price, total_ht et total_ttc
				            $price 		= $pu_ht - ($remise_gtar*$pu_ht)/100;				

				            $total_ht	= $price*$qty;

				            $total_ht 	= price2num($total_ht, 'MT');


				        }

				        if($tva_tx > 0)
				        {
				        	$total_ttc = $total_ht + ($total_ht*$tva_tx)/100;
				        	$total_ttc = price2num($total_ttc);

				        	 #$total_ht 	= price2num($total_ht);
				        	$total_tva  = $total_ttc - $total_ht;
				        }

				           

				        # On met à jour la ligne insérée:
				        $price = ( empty($price) ) ? 0 : $price; // for type compatibility.

				        $req = "UPDATE ".MAIN_DB_PREFIX."propaldet SET remise_percent = '{$remise_gtar}', price = '{$price}', subprice = '{$pu_ht}', total_ht = '{$total_ht}', total_tva = '{$total_tva}', total_ttc = '{$total_ttc}' WHERE rowid = ".$object->id;

				        // die($req);

				        $res = $this->db->query($req);

					    
					}
                } 			

		    }		    


		    /*
		    case 'LINEPROPAL_DELETE':

			// Contracts
		    case 'CONTRACT_CREATE':
		    case 'CONTRACT_ACTIVATE':
		    case 'CONTRACT_CANCEL':
		    case 'CONTRACT_CLOSE':
		    case 'CONTRACT_DELETE':
		    case 'LINECONTRACT_CREATE':
		    case 'LINECONTRACT_UPDATE':
		    case 'LINECONTRACT_DELETE':

			// Bills
		    case 'BILL_CREATE':
		    case 'BILL_CLONE':
		    case 'BILL_MODIFY':
		    case 'BILL_VALIDATE':
		    case 'BILL_UNVALIDATE':
		    case 'BILL_SENTBYMAIL':
		    case 'BILL_CANCEL':
		    case 'BILL_DELETE':
		    case 'BILL_PAYED':
		    */
		    case 'LINEBILL_INSERT':
		    case 'LINEBILL_UPDATE':
		    {
		    	# Application de la grille de remise
		    	# Si une remise a été insérée, on la garde sinon on détermine et applique
		    	$req = "SELECT fd.subprice, fd.fk_product, fd.fk_facture, fd.tva_tx, fd.qty, fd.remise_percent, f.fk_soc FROM ".MAIN_DB_PREFIX."facturedet AS fd LEFT JOIN ".MAIN_DB_PREFIX."facture AS f ON (fd.fk_facture = f.rowid) WHERE fd.rowid = ".$object->id;
		    	$res = $this->db->query($req);

		    	if ($res) {
					$objfd = $this->db->fetch_object($res);
					if ($objfd) {
						$fk_product = $objfd->fk_product;
						$remise 	= $objfd->remise_percent;
						$tva_tx		= $objfd->tva_tx;
						$qty		= $objfd->qty;
						$fk_soc		= $objfd->fk_soc;
						$pu_ht		= $objfd->subprice;
					}
				}

				if($remise <= 0.01)
				{
					if($remise = $this->fetchRemise($fk_soc, $fk_product, $qty))
					{
					$remise_gtar = $remise['taux'];
					$pvht 		 = $remise['pvht'];

					    if($pvht > 0)
					    {
					    # Un nouveau prix unitaire est déclaré
					    $pu_ht = $pvht;
					    $total_ht = $pu_ht*$qty;
					    }

					    if($remise_gtar > 0)
					    {
					        # Il existe une remise pour ce client pour ce produit

					        # On calcul donc les nouveaux price, total_ht et total_ttc
					        $price 		= $pu_ht - ($remise_gtar*$pu_ht)/100;

					        $total_ht	= price2num($price*$qty, 'MT');
					    }

					    if($tva_tx > 0)
					    {
					    	$total_ttc = $total_ht + ($total_ht*$tva_tx)/100;
					    	$total_ttc = price2num($total_ttc, 'MT');

					    	#$total_ht 	= price2num($total_ht);
					    	$total_tva  = $total_ttc - $total_ht;
					    	
					    }

					    # On met à jour la ligne insérée:
					    $price = ( empty($price) ) ? 0 : $price;// for type compatibility.

					    $req = "UPDATE ".MAIN_DB_PREFIX."facturedet SET remise_percent = '{$remise_gtar}', price = '{$price}', subprice = '{$pu_ht}', total_ht = '{$total_ht}', total_tva = '{$total_tva}', total_ttc = '{$total_ttc}' WHERE rowid = ".$object->id;
					    $res = $this->db->query($req);

					}
                } 			

		    }
			    
	    }

        return 1;
	}


	/**
     * Function to retrieve discounts values
     *
     * @param int			$fk_soc			customer ID
     * @param int			$fk_product     Product ID
     * @param int 			$qty 			Product Qty
     *
     * @return Array 						$pvht and $remise
     */
	private function fetchRemise ($fk_soc, $fk_product, $qty)
	{
		// On récupère les catégories éventuelles de ce produit:
        $sql = "SELECT fk_categorie FROM  " . MAIN_DB_PREFIX . "categorie_product";
        $sql .= " WHERE fk_product='" . $fk_product . "'";
        $result = $this->db->query($sql);
        if ($result) {
        	$num = $this->db->num_rows($result);
			$i = 0;
			while($i < $num)
			{
				$objp = $this->db->fetch_object($result);
            	if ($objp) {
            		$fk_categorie = $objp->fk_categorie;
            		if (! empty($clause_categorie))
            		{
            			$clause_categorie .= ', '; 	
            		}
            		$clause_categorie .= $fk_categorie;        	
                }
				$i++;
			}	                    
        }

        // On récupère les eventuelles catégories de ce client:
        $sql = "SELECT fk_categorie FROM  " . MAIN_DB_PREFIX . "categorie_societe";
        $sql .= " WHERE fk_soc = '" . $fk_soc . "'";
        $result = $this->db->query($sql);
        if ($result) {
        	$num = $this->db->num_rows($result);
			$i = 0;
			while($i < $num)
			{
				$objp = $this->db->fetch_object($result);
            	if ($objp) {
            		$fk_cat_soc = $objp->fk_categorie;
            		if (! empty($clause_cat_soc))
            		{
            			$clause_cat_soc .= ', '; 	
            		}
            		$clause_cat_soc .= $fk_cat_soc;        	
                }
				$i++;
			}	                    
        }

        // On recherche si il existe une remise de prix de ce produit (ou sa cat.) pour cette societe (ou sa cat.)
		$sql = "SELECT fk_grille, remise, pvht FROM " . MAIN_DB_PREFIX . "product_gremises_det ";
		$sql .= " WHERE ((fk_product='" . $fk_product . "' AND fk_product > 0)";
			if(! empty($clause_categorie))
				$sql .= " OR (fk_categorie IN (".$clause_categorie.") AND fk_categorie > 0))";
			else
				$sql .= ")";
        $sql .= " AND fk_grille IN (SELECT fk_grille FROM ".MAIN_DB_PREFIX."product_gremises_soc WHERE fk_soc = '{$fk_soc}'";
        	if(! empty($clause_cat_soc))
        		$sql .= " OR fk_cat_soc IN (".$clause_cat_soc."))";
        	else
        		$sql .=")";
		
		$sql .= "  AND seuil <= ".$qty;
		# Prise en compte du statu actif ou pas de la grille
		$sql .= " AND (SELECT status FROM ".MAIN_DB_PREFIX."product_gremises WHERE rowid = fk_grille) > 0";
        $sql .= " ORDER BY remise DESC";

        $result = $this->db->query($sql);

        # En suivant on récupère la plus grande remise et le plus petit pvht
        if ($result) {

        	/*
            $objp = $this->db->fetch_object($result);
            if ($objp) {
                $taux = (float) $objp->remise;                
            }
            */

            $num = $this->db->num_rows($result);
			$i = 0;
			$pvht_min = 0;
			while($i < $num)
			{
				
				$objp = $this->db->fetch_object($result);
            	if ($objp) {
            		# On ne récupère que la première occurence de remise
            		if($i == 0) $taux = (float) $objp->remise;

            		# Boucle: pour récupérer le plus petit prix unitaire  éventuellement renseigné
                	$pvht = (float) $objp->pvht;

                	$pvht_min = ($pvht <= $pvht_min OR $pvht_min == 0) ? $pvht : $pvht_min;
                }

				$i++;
			}

			if($taux > 0 OR $pvht_min > 0)
            	return Array('taux'=>$taux, 'pvht'=>$pvht_min);
            else
            	return FALSE;
        }

        return FALSE;

	}

}
