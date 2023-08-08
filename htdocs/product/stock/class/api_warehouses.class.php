<?php
/* Copyright (C) 2016   Laurent Destailleur     <eldy@users.sourceforge.net>
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

 use Luracast\Restler\RestException;

 require_once DOL_DOCUMENT_ROOT.'/product/stock/class/entrepot.class.php';
 require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

/**
 * API class for warehouses
 *
 * @access protected
 * @class  PowerERPApiAccess {@requires user,external}
 */
class Warehouses extends PowerERPApi
{
	/**
	 * @var array   $FIELDS     Mandatory fields, checked when create and update object
	 */
	public static $FIELDS = array(
		'label',
	);

	/**
	 * @var Entrepot $warehouse {@type Entrepot}
	 */
	public $warehouse;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $db, $conf;
		$this->db = $db;
		$this->warehouse = new Entrepot($this->db);
	}

	/**
	 * Get properties of a warehouse object
	 *
	 * Return an array with warehouse informations
	 *
	 * @param 	int 	$id ID of warehouse
	 * @return 	array|mixed data without useless information
	 *
	 * @throws 	RestException
	 */
	public function get($id)
	{
		if (!PowerERPApiAccess::$user->rights->stock->lire) {
			throw new RestException(401);
		}

		$result = $this->warehouse->fetch($id);
		if (!$result) {
			throw new RestException(404, 'warehouse not found');
		}

		if (!PowerERPApi::_checkAccessToResource('warehouse', $this->warehouse->id)) {
			throw new RestException(401, 'Access not allowed for login '.PowerERPApiAccess::$user->login);
		}

		return $this->_cleanObjectDatas($this->warehouse);
	}

	/**
	 * List warehouses
	 *
	 * Get a list of warehouses
	 *
	 * @param string	$sortfield	Sort field
	 * @param string	$sortorder	Sort order
	 * @param int		$limit		Limit for list
	 * @param int		$page		Page number
	 * @param  int    $category   Use this param to filter list by category
	 * @param string    $sqlfilters Other criteria to filter answers separated by a comma. Syntax example "(t.label:like:'WH-%') and (t.date_creation:<:'20160101')"
	 * @return array                Array of warehouse objects
	 *
	 * @throws RestException
	 */
	public function index($sortfield = "t.rowid", $sortorder = 'ASC', $limit = 100, $page = 0, $category = 0, $sqlfilters = '')
	{
		global $db, $conf;

		$obj_ret = array();

		if (!PowerERPApiAccess::$user->rights->stock->lire) {
			throw new RestException(401);
		}

		$sql = "SELECT t.rowid";
		$sql .= " FROM ".$this->db->prefix()."entrepot as t";
		if ($category > 0) {
			$sql .= ", ".$this->db->prefix()."categorie_societe as c";
		}
		$sql .= ' WHERE t.entity IN ('.getEntity('stock').')';
		// Select warehouses of given category
		if ($category > 0) {
			$sql .= " AND c.fk_categorie = ".((int) $category);
			$sql .= " AND c.fk_warehouse = t.rowid ";
		}
		// Add sql filters
		if ($sqlfilters) {
			$errormessage = '';
			if (!PowerERPApi::_checkFilters($sqlfilters, $errormessage)) {
				throw new RestException(503, 'Error when validating parameter sqlfilters -> '.$errormessage);
			}
			$regexstring = '\(([^:\'\(\)]+:[^:\'\(\)]+:[^\(\)]+)\)';
			$sql .= " AND (".preg_replace_callback('/'.$regexstring.'/', 'PowerERPApi::_forge_criteria_callback', $sqlfilters).")";
		}

		$sql .= $this->db->order($sortfield, $sortorder);
		if ($limit) {
			if ($page < 0) {
				$page = 0;
			}
			$offset = $limit * $page;

			$sql .= $this->db->plimit($limit + 1, $offset);
		}

		$result = $this->db->query($sql);
		if ($result) {
			$i = 0;
			$num = $this->db->num_rows($result);
			$min = min($num, ($limit <= 0 ? $num : $limit));
			while ($i < $min) {
				$obj = $this->db->fetch_object($result);
				$warehouse_static = new Entrepot($this->db);
				if ($warehouse_static->fetch($obj->rowid)) {
					$obj_ret[] = $this->_cleanObjectDatas($warehouse_static);
				}
				$i++;
			}
		} else {
			throw new RestException(503, 'Error when retrieve warehouse list : '.$this->db->lasterror());
		}
		if (!count($obj_ret)) {
			throw new RestException(404, 'No warehouse found');
		}
		return $obj_ret;
	}


	/**
	 * Create warehouse object
	 *
	 * @param array $request_data   Request data
	 * @return int  ID of warehouse
	 */
	public function post($request_data = null)
	{
		if (!PowerERPApiAccess::$user->rights->stock->creer) {
			throw new RestException(401);
		}

		// Check mandatory fields
		$result = $this->_validate($request_data);

		foreach ($request_data as $field => $value) {
			$this->warehouse->$field = $value;
		}
		if ($this->warehouse->create(PowerERPApiAccess::$user) < 0) {
			throw new RestException(500, "Error creating warehouse", array_merge(array($this->warehouse->error), $this->warehouse->errors));
		}
		return $this->warehouse->id;
	}

	/**
	 * Update warehouse
	 *
	 * @param int   $id             Id of warehouse to update
	 * @param array $request_data   Datas
	 * @return int
	 */
	public function put($id, $request_data = null)
	{
		if (!PowerERPApiAccess::$user->rights->stock->creer) {
			throw new RestException(401);
		}

		$result = $this->warehouse->fetch($id);
		if (!$result) {
			throw new RestException(404, 'warehouse not found');
		}

		if (!PowerERPApi::_checkAccessToResource('stock', $this->warehouse->id)) {
			throw new RestException(401, 'Access not allowed for login '.PowerERPApiAccess::$user->login);
		}

		foreach ($request_data as $field => $value) {
			if ($field == 'id') {
				continue;
			}
			$this->warehouse->$field = $value;
		}

		if ($this->warehouse->update($id, PowerERPApiAccess::$user)) {
			return $this->get($id);
		}

		return false;
	}

	/**
	 * Delete warehouse
	 *
	 * @param int $id   Warehouse ID
	 * @return array
	 */
	public function delete($id)
	{
		if (!PowerERPApiAccess::$user->rights->stock->supprimer) {
			throw new RestException(401);
		}
		$result = $this->warehouse->fetch($id);
		if (!$result) {
			throw new RestException(404, 'warehouse not found');
		}

		if (!PowerERPApi::_checkAccessToResource('stock', $this->warehouse->id)) {
			throw new RestException(401, 'Access not allowed for login '.PowerERPApiAccess::$user->login);
		}

		if (!$this->warehouse->delete(PowerERPApiAccess::$user)) {
			throw new RestException(401, 'error when delete warehouse');
		}

		return array(
			'success' => array(
				'code' => 200,
				'message' => 'Warehouse deleted'
			)
		);
	}


	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Clean sensible object datas
	 *
	 * @param   Entrepot  $object   Object to clean
	 * @return  Object              Object with cleaned properties
	 */
	protected function _cleanObjectDatas($object)
	{
		// phpcs:enable
		$object = parent::_cleanObjectDatas($object);

		// Remove the subscriptions because they are handled as a subresource.
		//unset($object->subscriptions);

		return $object;
	}


	/**
	 * Validate fields before create or update object
	 *
	 * @param array|null    $data    Data to validate
	 * @return array
	 *
	 * @throws RestException
	 */
	private function _validate($data)
	{
		$warehouse = array();
		foreach (Warehouses::$FIELDS as $field) {
			if (!isset($data[$field])) {
				throw new RestException(400, "$field field missing");
			}
			$warehouse[$field] = $data[$field];
		}
		return $warehouse;
	}
}
