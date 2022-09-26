<?php
/* Copyright (C) 2014-2016	Charlene BENKE	<charlie@patas-monkey.com>
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
 *	  \file	   management/class/managementprojet.class.php
 *	  \ingroup	management
 *	  \brief	pour gérer la transfert en facturation du projet
 */

require_once DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php";
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';


/**
 *	  \brief	  Class to manage coefpricr feature
 *	\remarks	Initialy built by build_class_from_table on 2008-09-10 12:41
 */
class Productcoefpricr extends Product
{

	function getHistoryPrice($nb=5)
	{

		$sql = "SELECT price, price_ttc, date_price";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_price";
		$sql.= " WHERE fk_product =".$this->id;
		$sql.= " AND price_level=1";
		$sql.= $this->db->order('date_price','desc');
		$sql.= $this->db->plimit($nb + 1);

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$pricehistory = array();
			$nump = $this->db->num_rows($resql);

			if ($nump)
			{
				$nbprice = 0;
				while ($nbprice < min($nump, $nb))
				{
					$obj = $this->db->fetch_object($resql);

					$pricehistory[$nbprice]['date'] = $obj->date_price;
					$pricehistory[$nbprice]['price'] = $obj->price;
					$pricehistory[$nbprice]['price_ttc'] = $obj->price_ttc;
					$nbprice++;
				}
			}
		}
		else
			print $this->db->error();
		return $pricehistory;
	}
}
?>
