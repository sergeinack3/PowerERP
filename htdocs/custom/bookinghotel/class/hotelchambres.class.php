<?php 
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php'; 
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

class hotelchambres extends Commonobject{

	public $errors = array();
	public $rowid;
	public $ref;
	public $label;
	public $chambre_category;

	//DoliDBMysqli
	public function __construct($db){ 
		$this->db = $db;
		return 1;
	}

	public function getAllChambresDisponible($debut,$fin,$reservation_id="",$hourstart=0,$minstart=0,$hourend=0,$minend=0){

		$fin2 = $fin;
		$time = strtotime($fin2);
		$time = $time - (1 * 60);
		$fin2 = date("Y-m-d H:i", $time);


		// $fin2 = $fin;
		$sql = "SELECT * FROM ".MAIN_DB_PREFIX."bookinghotel WHERE ";
		$sql .= " (debut between '".$debut."' and '".$fin2."' ";
		$sql .= " OR fin between '".$debut."' and '".$fin2."' ";
		$sql .= " OR (debut < '".$debut."' AND fin > '".$fin."'))";
		if (!empty($reservation_id)) {
			$sql .= " and rowid != ".$reservation_id;
		}

		// $sql = "SELECT * FROM ".MAIN_DB_PREFIX."bookinghotel WHERE ";
		// $sql .= " (debut between '".$debut."' and '".$fin."' ";
		// $sql .= " OR fin between '".$debut."' and '".$fin."' ";
		// $sql .= " OR (debut < '".$debut."' AND fin > '".$fin."'))";
		// if (!empty($reservation_id)) {
		// 	$sql .= " and rowid != ".$reservation_id;
		// }

		// REFUSED
		$sql .= " and reservation_etat != 3";

		// echo $sql;
        $resql = $this->db->query($sql);
    	$NoDispChambres = "";
    	
		if ($resql) {
			$num = $this->db->num_rows($resql);
			$i = 1;
			while ($obj = $this->db->fetch_object($resql)) {

				// if ($debut == $obj->fin && $hourstart >= $obj->hourend && $minstart >= $obj->minend){
				// if ($debut == $obj->fin && 
				// 	(
				// 		($hourstart >= $obj->hourend && $minstart >= $obj->minend) 
				// 		|| 
				// 		($hourstart > $obj->hourend && $minstart <= $obj->minend) 
				// 	)){

				// 	$i++;
				// 	continue;
				// }

				// if ($fin == $obj->debut && 
				// 	(
				// 		($hourend <= $obj->hourstart && $minend <= $obj->minstart) 
				// 		|| 
				// 		($hourend < $obj->hourstart && $minend >= $obj->minstart) 
				// 	)){
				// 	$i++;
				// 	continue;
				// }

				if ($i == $num) {
					$NoDispChambres .= $obj->chambre; 
				}else{
					$NoDispChambres .= $obj->chambre.","; 
				}


				$i++;
			}
			$this->db->free($resql);
    	}
		$NoDispChambres = rtrim($NoDispChambres,", ");
    	return $NoDispChambres;
	}

	public function select_all_hotelchambres($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr='',$disabld='0',$multiple=true,$NoDispChambres="",$selectedChmbrs="",$getbyowncategory=true,$withfilter=true,$groupcbycategory=true,$typechambr=''){
	    global $conf;

	    // die();
	    $id = (!empty($id)) ? $id : $name;
	    $nodatarole = '';
        if ($conf->use_javascript_ajax)
	    {
	        include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	        $comboenhancement = ajax_combobox('select_'.$id);
	        $html.=$comboenhancement;
	        $nodatarole=($comboenhancement?' data-role="none"':'');
	    }
	    
	    $form           = new Form($this->db);
		$product        = new Product($this->db);
		$category       = new Categorie($this->db);

        $this->db->begin();
        $sql2 = "SELECT ctgp.fk_categorie, ctgp.fk_product, prdcs.ref ";
        $sql2 .= " FROM ".MAIN_DB_PREFIX."categorie_product as ctgp";
        $sql2 .= " JOIN ".MAIN_DB_PREFIX."product as prdcs";
        $sql2 .= " ON ctgp.fk_product = prdcs.rowid";
        if($typechambr) $sql2 .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_extrafields as pe ON pe.fk_object=prdcs.rowid';
        $sql2 .= " WHERE  1>0 ";
        if($typechambr) $sql2 .= " AND pe.type='".$typechambr."'";

        if ($withfilter) {
        	$sql2 .= " AND fk_categorie in (SELECT rowid from ".MAIN_DB_PREFIX."categorie ";
    	}


        $slcted_categories = $conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER;

        if ($withfilter) {
	        if (!empty($slcted_categories)) {
	        	$sql2 .= " where rowid in (".$slcted_categories.")";
	        }else{
	        	$sql2 .= " where 1<0";
	        }

	    	$sql2 .= ")";


	        if (!empty($NoDispChambres)) {
	        	$sql2 .= " and ctgp.fk_product not in (".$NoDispChambres.")";
	        }
        }

        $sql2 .= " ORDER BY prdcs.ref ASC";

        // echo $sql2;

        $resql2 = $this->db->query($sql2);
        $arraychambres2 = array();
    	// die($sql2);

        if ($resql2) {
            $num = $this->db->num_rows($resql2);
            while ($obj = $this->db->fetch_object($resql2)) {
                $product->fetch($obj->fk_product);
                $category->fetch($obj->fk_categorie);
                if($groupcbycategory)
                	$arraychambres2[$category->label][$obj->fk_product] = $product->ref;
                else
                	$arraychambres2['All'][$obj->fk_product] = $product->ref;
                
               
            }
            $this->db->free($resql2);
        }
        // print_r($arraychambres2);
        // die();
        $html = '<span id="select_onechambre">';

        $chng = '';
        $sname = $name;
       	$mult = '';
        if ($multiple){
        	// $chng = 'onchange="newchambreselected(this)"';
        	$sname = $name.'[]';
        	$mult = 'multiple';
        }
        
        $html .= '<select '.$chng.' '.$attr.' class="flat" name="'.$sname.'" '.$mult.' id="select_'.$id.'" '.$nodatarole.' required>';
        // if ($multiple)
        // 	$html .= '<option value="-1">Choisir une ou plus</option>';
        // else
        	$html .= '<option value="-1">&nbsp;</option>';

        // print_r($arraychambres2);

        foreach ($arraychambres2 as $categories => $chambres) {
        	asort($chambres);
        	// print_r($chambres);

        	if($groupcbycategory)
            	$html .= '<optgroup label="'.$categories.'">';
            foreach ($chambres as $key => $value) {
            	$product->fetch($key);
            	if (!$multiple){
            		if ($key == $selected){
                		$html .= '<option value="'.$key.'" selected>'.$product->ref.' - '.$product->label.'</option>';
            		}
                	else
                		$html .= '<option value="'.$key.'">'.$product->ref.' - '.$product->label.'</option>';
            	}else{
                	$html .= '<option value="'.$key.'">'.$product->ref.' - '.$product->label.'</option>';
            	}
            }
            if($groupcbycategory)
            	$html .= '</optgroup>';
        }
        $html .= '</select>';

        $html .= '</span>';

        // if ($multiple)
        // 	$html .= '<span id="multi_chambre">'.$form->multiselectarray('chambre', $arraychambres, $arraychambres, null, null, null,null, "calc(100% - 188px)","required").'</span>';


        return $html;




	 //    $moreforfilter = '';
	 //    $nodatarole = '';
	 //    $id = (!empty($id)) ? $id : $name;
	 //    if ($conf->use_javascript_ajax)
	 //    {
	 //        include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	 //        $comboenhancement = ajax_combobox('select_'.$id);
	 //        $moreforfilter.=$comboenhancement;
	 //        $nodatarole=($comboenhancement?' data-role="none"':'');
	 //    }

	 //    $moreforfilter.='<select required width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'" '.$nodatarole.'>';
	 //    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

		// $this->db->begin();
		  //   	$sql = "SELECT ".$val.",".$opt." FROM ".MAIN_DB_PREFIX."product ";
		  //   	$sql .= " where fk_product_type = 1 ORDER BY rowid DESC";
		  //   	$resql = $this->db->query($sql);

				// if ($resql) {
				// 	$num = $this->db->num_rows($resql);

				// 	while ($obj = $this->db->fetch_object($resql)) {
				// 		$moreforfilter.='<option value="'.$obj->$val.'"';
			 //            if ($obj->$val == $selected) $moreforfilter.=' selected';
			 //            $moreforfilter.='>'.$obj->$opt.'</option>';
				// 	}
				// 	$this->db->free($resql);
				// }

			 //    $moreforfilter.='</select>';
			 //    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
			 //    return $moreforfilter;
	}
	public function select_all_categories_a_reservee($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr='',$disabld='0',$multiple=true,$Dispcategories="",$selectedChmbrs="",$getbyowncategory=true){
	    global $conf;
	    $id = (!empty($id)) ? $id : $name;
	    $nodatarole = '';
        if ($conf->use_javascript_ajax)
	    {
	        include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	        $comboenhancement = ajax_combobox('select_'.$id);
	        $html.=$comboenhancement;
	        $nodatarole=($comboenhancement?' data-role="none"':'');
	    }
	    
	    $form           = new Form($this->db);
		$category       = new Categorie($this->db);

		// print_r($Dispcategories);
        $this->db->begin();
        $sql2 = "SELECT * FROM ".MAIN_DB_PREFIX."categorie ";
        if(!empty($Dispcategories))
        $sql2 .= " WHERE rowid in (".$Dispcategories.")";
        $sql2 .= " ORDER BY label ASC";

        // echo $sql2;
        $resql2 = $this->db->query($sql2);
        $arraychambres2 = array();

        if ($resql2) {
            $num = $this->db->num_rows($resql2);
            while ($obj = $this->db->fetch_object($resql2)) {
                $category->fetch($obj->rowid);
                $arraychambres2[$obj->rowid] = $category->label;
            }
            $this->db->free($resql2);
        }
        // print_r($arraychambres2);
        $html = '<span id="select_onecategory">';

        $chng = '';
        $sname = $name;
       	$mult = '';
        if ($multiple){
        	$sname = $name.'[]';
        	$mult = 'multiple';
        }
        
        $html .= '<select '.$chng.' '.$attr.' class="flat" name="'.$sname.'" '.$mult.' id="select_'.$id.'" '.$nodatarole.'>';

        $html .= '<option value="-1">&nbsp;</option>';

        foreach ($arraychambres2 as $key => $value) {
        	if (!$multiple){
        		if ($key == $selected){
            		$html .= '<option value="'.$key.'" selected>'.$value.'</option>';
        		}
            	else
            		$html .= '<option value="'.$key.'">'.$value.'</option>';
        	}else{
            	$html .= '<option value="'.$key.'">'.$value.'</option>';
        	}
        }
        $html .= '</select>';

        $html .= '</span>';

        return $html;
	}
	public function select_with_filter($selected=0,$name='select_',$showempty=1,$val="rowid",$opt="label",$id='',$attr=''){
	    global $conf;
	    $moreforfilter = '';
	    $nodatarole = '';
	    $id = (!empty($id)) ? $id : $name;
	    if ($conf->use_javascript_ajax)
	    {
	        include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
	        $comboenhancement = ajax_combobox('select_'.$id);
	        $moreforfilter.=$comboenhancement;
	        $nodatarole=($comboenhancement?' data-role="none"':'');
	    }

	    $moreforfilter.='<select width="100%" '.$attr.' class="flat" id="select_'.$id.'" name="'.$name.'" '.$nodatarole.'>';
	    if ($showempty) $moreforfilter.='<option value="0">&nbsp;</option>';

		$this->db->begin();
    	$sql = "SELECT ".$val.",".$opt." FROM ".MAIN_DB_PREFIX.get_class($this)."  ORDER BY rowid DESC";

    	$resql = $this->db->query($sql);

		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$moreforfilter.='<option value="'.$obj->$val.'"';
	            if ($obj->$val == $selected) $moreforfilter.=' selected';
	            $moreforfilter.='>'.$obj->$opt.'</option>';
			}
			$this->db->free($resql);
		}

	    $moreforfilter.='</select>';
	    $moreforfilter.='<style>#s2id_select_'.$name.'{ width: 100% !important;}</style>';
	    return $moreforfilter;
	}

	public function getNomUrl($withpicto = 0,  $id = null, $ref = null)
    {
        global $langs;

        $result	= '';
        $setRef = (null !== $ref) ? $ref : '';
        $id  	= ($id  ?: '');
        $label  = $langs->trans("Show").': '. $setRef;

        $link 	 = '<a href="'.dol_buildpath('/bookinghotel/'.get_class($this).'/card.php?id='. $id,2 ).'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
        $linkend ='</a>';
        $picto   = 'elemt@lchambreion';

        if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
        if ($withpicto && $withpicto != 2) $result.=' ';
        if ($withpicto != 2) $result.=$link.$setRef.$linkend;
        $result = $link."<div class='icon-accessoire mainvmenu'></div>  ".$setRef.$linkend;
        return $result;
    }

	public function fetchAll($sortorder = '', $sortfield = '', $limit = 0, $offset = 0, $filter = '', $filtermode = 'AND',$dashboards=true)
	{
		global $conf;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT pro.rowid, pro.ref, pro.fk_product_type, categoryprod.fk_product, categoryprod.fk_categorie, c1.rowid as rowidcategory, c1.fk_parent, pe.type as typep ";
		$sql .= " FROM ".MAIN_DB_PREFIX ."product pro";
		$sql .= " JOIN ".MAIN_DB_PREFIX ."categorie_product categoryprod";
		$sql .= " ON pro.rowid = categoryprod.fk_product";
		$sql .= " JOIN ".MAIN_DB_PREFIX ."categorie c1";
		$sql .= " ON categoryprod.fk_categorie = c1.rowid";

		if ($dashboards){
        	$sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'product_extrafields as pe ON pe.fk_object=pro.rowid';
		}

		// $filter .= " AND pro.fk_product_type = 1";

		// 	$filter .= " AND c1.fk_parent = 891";

		$slcted_categories = $conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER;
        if (!empty($slcted_categories)) {
        	$filter .= " AND c1.rowid in (".$slcted_categories.")";
        }

		$slcted_categories_no_tb = $conf->global->BOOKINGHOTEL_GESTION_CATEGORIES_A_RESERVER_NON_TB;
        if (!empty($slcted_categories_no_tb)) {
        	$filter .= " AND c1.rowid not in (".$slcted_categories_no_tb.")";
        }
		// if ($dashboards)
		// 	$filter .= " AND c1.fk_parent = 891";


        $sql .= " WHERE  1>0 ";


		if (!empty($filter)) {
			$sql .= $filter;
		}

		$sortfield = "pe.type";
		$sortorder = "DESC";
		// echo $sql;

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield, $sortorder);
		}

        // die($sql);

		if (!empty($limit)) {
			if($offset==1)
				$sql .= " limit ".$limit;
			else
				$sql .= " limit ".$offset.",".$limit;				
		}
		// die($sql);
		$this->rows = array();
		$resql = $this->db->query($sql);
		// print_r($this->db->fetch_object($resql));
		// echo "$resql";
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new stdClass;
				$line->rowid 	= $obj->rowid; 
				$line->ref 	=  $obj->ref;
				$line->typep 	=  $obj->typep;
				$line->fk_categorie 	=  $obj->fk_categorie;
				$this->rows[] 	= $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
		}

	}

	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT * FROM ' . MAIN_DB_PREFIX .get_class($this). ' WHERE rowid = ' . $id;

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			
			if ($numrows) {
				$obj 			= $this->db->fetch_object($resql);
				$this->rowid 	= $obj->rowid;
				$this->ref 	=  $obj->ref; 
				$this->label 	=  $obj->label;
				// $this->chambre_category 	=  $obj->chambre_category;
			}

			$this->db->free($resql);

			if ($numrows) {
				return 1 ;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return -1;
		}
	}

}

?>