<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'controllers/_file.php';
// require_once APPPATH.'third_party/PHPExcel.php';
class migration extends MY_Controller {
    
    use _File;
    private $title = 'migration-chemical';

    public function __construct(){
		parent::__construct();
        $this->session_expire();
        $this->load->model('gpreport_model', 'gp');
    }
    
    public function index(){
        $data['title'] = $this->title;
        $this->views('chemical/migration',$data);
    }

    public function saveMaster(){
        $data = json_decode($_POST['data'], true);
        $res = array();
        $res['status'] = 0;
        $this->gp->trans_start();
        foreach($data as $d){
            $dUp = array(
                'AMEC_SDS_ID'       => $d['AMEC_SDS_ID'],
                'RECEIVED_SDS_DATE' => $d['RECEIVED_SDS_DATE'] == '1998' ? date('j/n/Y',strtotime('1/1/1998')) : $this->conVdateToDB($d['RECEIVED_SDS_DATE']),
                'EFFECTIVE_DATE'    => $this->conVdateToDB($d['EFFECTIVE_DATE']),
                'PRODUCT_CODE'      => $d['PRODUCT_CODE'],
                'CHEMICAL_NAME'     => $d['CHEMICAL_NAME'],
                'VENDOR'            => $d['VENDOR'],
                'PUR_INCHARGE'      => $d['PUR_INCHARGE'],
                'UN_CLASS'          => $d['UN_CLASS'],
                'REV'               => $d['REV'],
            );
            $exists = $this->gp->customSelect('STY_CHEMICAL',array('AMEC_SDS_ID'=> $d['AMEC_SDS_ID']),'COUNT(*) as COUNT');
            if($exists[0]->COUNT == 0){
                $res[] = $dUp;
                $res['status'] = $this->gp->insert('STY_CHEMICAL', $dUp);
            }

        }
        $this->gp->trans_complete();
        echo json_encode($res);
    }

    public function saveMasterSec(){
        $allData = json_decode($_POST['data'], true);
        $res  = array();
        $res['status'] = 0;
        $this->gp->trans_start();
        foreach($allData as $a){
            $name = $a['filename'];
            $name = substr($name,0,strpos($name,'.')+1);
            foreach($a['data'] as $d){

                $dUp = array(
                    'OWNER'             => $name == 'MP SEC.' ? 'M/P SEC.' : $name,  
                    'OWNERCODE'         => $this->findCode($name),  
                    'AMEC_SDS_ID'       => $d['AMEC_SDS_ID'],
                    'RECEIVED_SDS_DATE' => $this->conVdateToDB($d['RECEIVED_SDS_DATE']),
                    'USED_FOR'          => $d['USED_FOR'],
                    'USED_AREA'         => $d['USED_AREA'],
                    'KEEPING_POINT'     => $d['KEEPING_POINT'],
                    'QTY'               => $d['QTY'] == '-' ? 0 : $d['QTY'],
                    'REC4052'           => $d['REC4052'] == 'OK' ? 1 : 0,
                    'REC4054'           => $d['REC4054'] == 'OK' ? 1 : 0,
                    // 'CLASS'             => $d['CLASS'],
                    // 'REV'               => $d['REV'],
                );
                $existsMaster = $this->gp->customSelect('STY_CHEMICAL',array('AMEC_SDS_ID'=> $d['AMEC_SDS_ID']),'COUNT(*) as COUNT');
                $exists = $this->gp->customSelect('STY_CHEMICAL_SECTION',array('AMEC_SDS_ID' => $dUp['AMEC_SDS_ID'], 'OWNER' => $dUp['OWNER'] ),'COUNT(*) as COUNT');
                if($exists[0]->COUNT == 0 && $existsMaster[0]->COUNT > 0){   
                    $res[] = $dUp;
                    $res['status'] = $this->gp->insert('STY_CHEMICAL_SECTION', $dUp);
                }
            }
            $this->addSectionMaster($name);
        }
        $this->gp->trans_complete();
        // var_dump($data);
        echo json_encode($res);
    }

    private function addSectionMaster($name){
        $exists = $this->gp->customSelect('STY_CHEMICAL_REV',array('OWNER' => $name),'COUNT(*) as COUNT');
        if($exists[0]->COUNT == 0){
            $data = array(
                'OWNER'  => $name,
                'OWNERCODE' => $this->findCode($name),
                'REV'    => '*',
                'TYPE'   => $this->gp->customSelect('STY_TYPE',array('TYPE_CODE' => 'CMS'),'TYPE_ID')[0]->TYPE_ID,    
            );
            $this->gp->insert('STY_CHEMICAL_REV', $data);
        }
    }

    private function findCode($ownerName){
        $ownerCode = '';
        if($ownerName == 'MP SEC.'){
            $ownerName = 'M/P SEC.';
        }
        if (strpos($ownerName, 'DEPT') !== false) {
            // Add your logic here if 'DEPT' is found in $ownerName
            $ownerCode = $this->gp->customSelect('ORGANIZATIONS', array("UPPER(REPLACE(\"SDEPT\", ' ', ''))".'=' => strtoupper(str_replace(' ','',$ownerName))), 'SDEPCODE, SDEPT',1);
            $ownerCode = !empty($ownerCode) ? $ownerCode[0]->SDEPCODE : '' ;
        }else{
            $ownerCode = $this->gp->customSelect('ORGANIZATIONS', array("UPPER(REPLACE(\"SSEC\", ' ', ''))".'=' => strtoupper(str_replace(' ','',$ownerName))), 'SSECCODE, SSEC',1);
            $ownerCode = !empty($ownerCode) ? $ownerCode[0]->SSECCODE : '' ;
        }
        return $ownerCode;
    }
}
    