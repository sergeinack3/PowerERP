<?php 
dol_include_once('/core/lib/admin.lib.php');
require_once DOL_DOCUMENT_ROOT.'/user/class/userbankaccount.class.php';

class payrollmod
{
    public function __construct($db)
    {   
        global $langs;

        $this->db =$db;

        $this->payrollmodels = [
            // 'france'            => $langs->trans('payrollFranceModel'),
            'cameroun'          => $langs->trans('payrollCamerounModel'),
            // 'cote_d_ivoire'     => $langs->trans('payrollCote_d_ivoireModel'),
        ];
    }
    
    public function getSelectPayrollModels($slctd='',$name='payrollmodel', $showempty=0, $disabled='')
    {
        global $langs;
        $payrollmodels = $this->payrollmodels;
        $select ='<select class="select_'.$name.'" name="'.$name.'" '.$disabled.'>';
            // if($showempty) $select .='<option value="0"></option>';
            foreach ($payrollmodels as $keyr => $namer) {

                $slctdt = ($keyr == $slctd) ? 'selected' : '';
                $select .='<option value="'.$keyr.'" '.$slctdt.'>'.$namer.'</option>';
            }
        $select .='</select>';

        return $select;
    }

    function employeeinfo($fk_user, $lastday=null)
    {
        global $langs;
        $employee = new User($this->db);
        $employee->fetch($fk_user);


        $account = new UserBankAccount($this->db);
        $account->fetch(0, '', $fk_user);


        $results = array();
        $results['matricule'] = $results['zone'] = $results['categorie'] = $results['echelon'] = $results['cnss'] = $results['niveau'] = $results['anciennete'] = $results['job'] = $results['adresse'] = $results['situafam'] = $results['nbrenf'] = $results['partigr'] = $results['qualif'] = $results['ibanrib'] = '';
        $results['name'] = $employee->lastname.' '.$employee->firstname;
        $results['birth'] = $employee->birth;
        $results['dateemployment'] = $employee->dateemployment;
        if($employee->array_options){
            $dt = $employee->array_options;
            if(isset($dt['options_payrollmodmatricule'])){
                $results['matricule'] = $dt['options_payrollmodmatricule'];
            }
            if(isset($dt['options_payrollmodzone'])){
                $results['zone'] = $dt['options_payrollmodzone'];
            }
            if(isset($dt['options_payrollmodcategorie'])){
                $results['categorie'] = $dt['options_payrollmodcategorie'];
            }
            if(isset($dt['options_payrollmodechelon'])){
                $results['echelon'] = $dt['options_payrollmodechelon'];
            }
            if(isset($dt['options_payrollcnss'])){
                $results['cnss'] = $dt['options_payrollcnss'];
            }
            if(isset($dt['options_payrollniveau'])){
                $results['niveau'] = $dt['options_payrollniveau'];
            }
        }

        if ($account->id > 0)
            $results['ibanrib'] = $account->iban;

        $results['adresse'] = $langs->convToOutputCharset(dol_format_address($employee, 1, "<br>", $langs));


        $results['entree'] = date('d/m/Y');


        if($employee->dateemployment){

            $date1 = strtotime($lastday);  
            $date2 = $employee->dateemployment;  
              
            $diff = abs($date2 - $date1);
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 

     
            $ancie = '';
            $ancie .= $years.' ';

            $yt = strtolower($langs->trans('Years'));
            $ancie .= substr(($yt), 0, 2).'(s)';

            if($months > 0){
                $ancie .= ', '.$months.' ';
                if($months > 1)
                    $ancie .= strtolower($langs->trans('Months'));
                else
                    $ancie .= strtolower($langs->trans('Month'));
            }

            if($days > 0){
                $ancie .= ', '.$days.' ';
                if($days > 1)
                    $ancie .= strtolower($langs->trans('Days'));
                else
                    $ancie .= strtolower($langs->trans('Day'));
            }

            $results['anciennete'] = $ancie;
        }

        if($employee->job)
            $results['job'] = $employee->job;





        // print_r($employee);die;

        return $results;
    }


    public function getExcludedUsers($period)
    {
        $sql = "SELECT * FROM ".MAIN_DB_PREFIX."payrollmod_payrolls WHERE 1>0 ";
        $sql .= "AND period = '".$period."'";
        $resql = $this->db->query($sql);

        $users = array();
        if($resql){
            while ($obj = $this->db->fetch_object($resql)) {
                if($obj->fk_user)
                    $users[] = $obj->fk_user;
            }
        }
        
        return $users;
    }



    /**
     *      Return a string with full address formated for output on documents
     *
     *      @param  Translate             $outputlangs          Output langs object
     *      @param  Societe               $sourcecompany        Source company object
     *      @param  Societe|string|null   $targetcompany        Target company object
     *      @param  Contact|string|null   $targetcontact        Target contact object
     *      @param  int                   $usecontact           Use contact instead of company
     *      @param  string                $mode                 Address type ('source', 'target', 'targetwithdetails', 'targetwithdetails_xxx': target but include also phone/fax/email/url)
     *      @param  Object                $object               Object we want to build document for
     *      @return string                                      String with full address
     */
    function pdf_build_address($outputlangs, $sourcecompany, $targetcompany = '', $targetcontact = '', $usecontact = 0, $mode = 'source', $object = null)
    {
        global $conf, $hookmanager;

        $outputlangs->loadLangs(array("main", "propal", "companies", "bills"));

        if ($mode == 'source' && !is_object($sourcecompany)) return -1;
        if ($mode == 'target' && !is_object($targetcompany)) return -1;

        if (!empty($sourcecompany->state_id) && empty($sourcecompany->state))             $sourcecompany->state = getState($sourcecompany->state_id);
        if (!empty($targetcompany->state_id) && empty($targetcompany->state))             $targetcompany->state = getState($targetcompany->state_id);

        $reshook = 0;
        $stringaddress = '';
        
        if ($mode == 'source')
        {
            $withCountry = 0;
            if (!empty($sourcecompany->country_code) && ($targetcompany->country_code != $sourcecompany->country_code)) $withCountry = 1;

            $stringaddress .= ($stringaddress ? "<br>" : '').$outputlangs->convToOutputCharset(dol_format_address($sourcecompany, $withCountry, ", ", $outputlangs))."<br>";

            // if (empty($conf->global->MAIN_PDF_DISABLESOURCEDETAILS))
            // {
            //     // Phone
            //     if ($sourcecompany->phone) $stringaddress .= ($stringaddress ? "<br>" : '').$outputlangs->transnoentities("PhoneShort").": ".$outputlangs->convToOutputCharset($sourcecompany->phone);
            //     // Fax
            //     if ($sourcecompany->fax) $stringaddress .= ($stringaddress ? ($sourcecompany->phone ? " - " : "<br>") : '').$outputlangs->transnoentities("Fax").": ".$outputlangs->convToOutputCharset($sourcecompany->fax);
            //     // EMail
            //     if ($sourcecompany->email) $stringaddress .= ($stringaddress ? "<br>" : '').$outputlangs->transnoentities("Email").": ".$outputlangs->convToOutputCharset($sourcecompany->email);
            //     // Web
            //     if ($sourcecompany->url) $stringaddress .= ($stringaddress ? "<br>" : '').$outputlangs->transnoentities("Web").": ".$outputlangs->convToOutputCharset($sourcecompany->url);
            // }
            // Intra VAT
            // if (!empty($conf->global->MAIN_TVAINTRA_IN_SOURCE_ADDRESS))
            // {
                if ($sourcecompany->tva_intra) $stringaddress .= ($stringaddress ? "<br>" : '').$outputlangs->transnoentities("VATIntraShort").': '.$outputlangs->convToOutputCharset($sourcecompany->tva_intra);
            // }
            // Professionnal Ids
            $reg = array();
            if ((!empty($conf->global->MAIN_PROFID1_IN_SOURCE_ADDRESS) || 1>0) && !empty($sourcecompany->idprof1))
            {
                $tmp = $outputlangs->transcountrynoentities("ProfId1", $sourcecompany->country_code);
                if (preg_match('/\((.+)\)/', $tmp, $reg)) $tmp = $reg[1];
                $stringaddress .= ($stringaddress ? "<br>" : '').$tmp.': '.$outputlangs->convToOutputCharset($sourcecompany->idprof1);
            }
            if ((!empty($conf->global->MAIN_PROFID2_IN_SOURCE_ADDRESS) || 1>0) && !empty($sourcecompany->idprof2))
            {
                $tmp = $outputlangs->transcountrynoentities("ProfId2", $sourcecompany->country_code);
                if (preg_match('/\((.+)\)/', $tmp, $reg)) $tmp = $reg[1];
                $stringaddress .= ($stringaddress ? "<br>" : '').$tmp.': '.$outputlangs->convToOutputCharset($sourcecompany->idprof2);
            }
            if ((!empty($conf->global->MAIN_PROFID3_IN_SOURCE_ADDRESS) || 1>0) && !empty($sourcecompany->idprof3))
            {
                $tmp = $outputlangs->transcountrynoentities("ProfId3", $sourcecompany->country_code);
                if (preg_match('/\((.+)\)/', $tmp, $reg)) $tmp = $reg[1];
                $stringaddress .= ($stringaddress ? "<br>" : '').$tmp.': '.$outputlangs->convToOutputCharset($sourcecompany->idprof3);
            }
            if ((!empty($conf->global->MAIN_PROFID4_IN_SOURCE_ADDRESS) || 1>0) && !empty($sourcecompany->idprof4))
            {
                $tmp = $outputlangs->transcountrynoentities("ProfId4", $sourcecompany->country_code);
                if (preg_match('/\((.+)\)/', $tmp, $reg)) $tmp = $reg[1];
                $stringaddress .= ($stringaddress ? "<br>" : '').$tmp.': '.$outputlangs->convToOutputCharset($sourcecompany->idprof4);
            }
            if ((!empty($conf->global->MAIN_PROFID5_IN_SOURCE_ADDRESS) || 1>0) && !empty($sourcecompany->idprof5))
            {
                $tmp = $outputlangs->transcountrynoentities("ProfId5", $sourcecompany->country_code);
                if (preg_match('/\((.+)\)/', $tmp, $reg)) $tmp = $reg[1];
                $stringaddress .= ($stringaddress ? "<br>" : '').$tmp.': '.$outputlangs->convToOutputCharset($sourcecompany->idprof5);
            }
            if ((!empty($conf->global->MAIN_PROFID6_IN_SOURCE_ADDRESS) || 1>0) && !empty($sourcecompany->idprof6))
            {
                $tmp = $outputlangs->transcountrynoentities("ProfId6", $sourcecompany->country_code);
                if (preg_match('/\((.+)\)/', $tmp, $reg)) $tmp = $reg[1];
                $stringaddress .= ($stringaddress ? "<br>" : '').$tmp.': '.$outputlangs->convToOutputCharset($sourcecompany->idprof6);
            }
            if (!empty($conf->global->PDF_ADD_MORE_AFTER_SOURCE_ADDRESS)) {
                $stringaddress .= ($stringaddress ? "<br>" : '').$conf->global->PDF_ADD_MORE_AFTER_SOURCE_ADDRESS;
            }
        }

        return $stringaddress;
    }
   


    public function getDataToSend2($link, $token, $decodejsn = true){
        $ch = curl_init();
        $link = trim($link, '/');
        curl_setopt($ch, CURLOPT_URL, $link.'/api/index.php/powererpmobileapi/'.$toget);
        // print_r($result);die();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Dolapikey: '.$token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        // if (curl_errno($ch)) return -1;
        if($decodejsn) $result = json_decode($result, true);
        // print_r($result);die();
        curl_close($ch);
        return $result;
    }

}
class payrollmodcls extends Commonobject{ 
    
    public function __construct($db){ 
        $this->db = $db;
        return 1;
    }

    public function fetch()
    {
        global $conf, $mysoc, $user, $langs;
        $langs->load('payrollmod@payrollmod');

        $name = '';
        $mail = $mysoc->email; $name = $mysoc->name; $link = dol_buildpath('/',2);
        if(!$name) $name = "(U) ".$user->lastname.' '.$user->firstname;


        if (!powererp_get_const($this->db,'PAYROLLMOD_CURRENT_D_MODULE',0))
            powererp_set_const($this->db,'PAYROLLMOD_CURRENT_D_MODULE',date('Y-m-d'),'chaine',0,'',0);
        if (!powererp_get_const($this->db,'PAYROLLMOD_EDITEUR_MODULE',0))
            powererp_set_const($this->db,'PAYROLLMOD_EDITEUR_MODULE','https://www.'.$langs->trans('payrollmodeditormod'),'chaine',0,'',0);
        if (!powererp_get_const($this->db,'PAYROLLMOD_MODULEID_MODULE',0))
            powererp_set_const($this->db,'PAYROLLMOD_MODULEID_MODULE',$langs->trans('payrollmodnummod'),'chaine',0,'',0);


        $_day   = powererp_get_const($this->db,'PAYROLLMOD_CURRENT_D_MODULE',0);
        $_link  = powererp_get_const($this->db,'PAYROLLMOD_EDITEUR_MODULE',0);
        $_mod   = powererp_get_const($this->db,'PAYROLLMOD_MODULEID_MODULE',0);


        if($_day &&  $_day <= date('Y-m-d') && !empty($_link) && !empty($_mod) && !empty($link)){
        // if(!empty($_link) && !empty($_mod) && !empty($link)){
            $par = "?mod=".urlencode($_mod)."&link=".urlencode($link)."&name=".urlencode($name)."&email=".urlencode($mail);
            $url = $_link.'/dsadmin/module/registeruse'.$par;

            require_once DOL_DOCUMENT_ROOT.'/core/lib/geturl.lib.php';
            $result = getURLContent($url);
            $response = json_decode($result['content']);

            if($response && $response->actif == 0){
                powererp_set_const($this->db,'PAYROLLMOD_MODULES_ID', 1, 'chaine',0,'',0);
                $sql = "DELETE FROM `".MAIN_DB_PREFIX."const` WHERE `value` like '%payrollmod%'";
                $resql = $this->db->query($sql);
                unActivateModule("modpayrollmod");
            }

            powererp_set_const($this->db,'PAYROLLMOD_CURRENT_D_MODULE', date("Y-m-d", time() + 86400), 'chaine',0,'',0);

        }


        return 1;
    } 
} 
?>