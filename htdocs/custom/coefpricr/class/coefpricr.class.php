<?php
/*	Copyright (C) 2016 		Charlene BENKE  <charlie@patas-monkey.com>
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
 *	\file	   /coefpricr/class/coefpricr_indice.class.php
 *	\ingroup	member
 *	\brief	  File of class to manage coefpricr indices
 */
class Coefpricr // extends CommonObject
{
	var $db;						//!< To store db handler

	function __construct($db)
	{
		$this->db = $db;
		return 1;
	}

	// return new price
	// $object = le produit
	function PriceWithExtrafields( $object)
	{
		global $conf;

		require_once (DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php');

		if ($object->price_base_type == 'TTC')
			$origineprice = price($object->price_ttc);
		else
			$origineprice = price($object->price);

		// par d�faut le nouveau prix c'est l'ancien prix
		$newprice = $origineprice;

		$extrafieldsproduct = new ExtraFields($this->db);	// les extrafields du produit
		// fetch optionals attributes and labels
		$extralabels=$extrafieldsproduct->fetch_name_optionals_label($object->table_element);
		$res=$object->fetch_optionals($object->id, $extralabels);
		$productextravalue=$object->array_options;

		$val.=strval($conf->global->CoefPricRFormula);
		eval($val);

		// ensuite la formule selon les extrafields
		return $newprice;
	}
}
?>