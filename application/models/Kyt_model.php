<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class Kyt_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();

    }

    public function getSectionEmp($emp)
    {
        $sql = "SELECT DISTINCT SSECCODE , SSEC FROM ORGANIZATIONS WHERE sseccode <> '00' AND CSTATUS = '1' AND
        (
           sseccode IN (SELECT VORGNO  FROM WEBFORM.ORGPOS WHERE vempno = '$emp')
           OR SDEPCODE IN (SELECT VORGNO  FROM WEBFORM.ORGPOS WHERE vempno = '$emp')
           OR SDIVCODE IN (SELECT VORGNO  FROM WEBFORM.ORGPOS WHERE vempno = '$emp') 
        ) ORDER BY SSEC";
        return $this->db->query($sql)->result();
    }

    public function getKytFrm($cond)
    {
        if(!empty($cond)){
            foreach($cond as $key => $value){
                $this->set_where($key, $value);
            }
        }
        return $this->db->select('KYT.* , ITM.ITEMS_NAME , SEC.SSEC , REQ.SNAME REQ , HEAD.SNAME AS HEAD ')
        ->from('STY_KYTFRM KYT')
        ->join('STY_ITEMS ITM','KYT.ITEMS_ID = ITM.ITEMS_ID')
        ->join('AMEC.PSECTION SEC','KYT.SECCODE = SEC.SSECCODE')
        ->join('AMECUSERALL REQ','KYT.EMPNORISK = REQ.SEMPNO')
        ->join('AMECUSERALL HEAD','KYT.EMPNOHEAD = HEAD.SEMPNO')
        ->get()->result();
    }

    public function getKytFrmWaitApv($apv)
    {
        return $this->db->select('KYT.* , ITM.ITEMS_NAME , SEC.SSEC , REQ.SNAME REQ , HEAD.SNAME AS HEAD ')
        ->from('STY_KYTFRM KYT')
        ->join('STY_ITEMS ITM','KYT.ITEMS_ID = ITM.ITEMS_ID')
        ->join('AMEC.PSECTION SEC','KYT.SECCODE = SEC.SSECCODE')
        ->join('AMECUSERALL REQ','KYT.EMPNORISK = REQ.SEMPNO')
        ->join('AMECUSERALL HEAD','KYT.EMPNOHEAD = HEAD.SEMPNO')
        ->join('FLOW F','KYT.NFRMNO = F.NFRMNO AND KYT.VORGNO = F.VORGNO AND KYT.CYEAR = F.CYEAR AND KYT.CYEAR2 = F.CYEAR2 AND KYT.NRUNNO = F.NRUNNO')
        ->where("(VAPVNO = '".$apv."' or VREPNO = '".$apv."') AND CSTEPST = '3'",null,false)
        ->get()->result();
    }

    public function getItems(){
        return $this->db->select('A.*, B.TYPE_NAME')
                        ->from('STY_ITEMS A')
                        ->join('STY_TYPE B','A.ITEMS_TYPE = B.TYPE_ID')
                        // ->where ('ITEMS_TYPE',2)
                        ->order_by('ITEMS_ID', 'ASC')
                        ->get()->result();
    }

    public function getType(){
        return $this->db->from('STY_TYPE')
                        ->where('TYPE_NO', 99)
                        ->order_by('TYPE_ID','ASC')
                        ->get()->result();
    }

    // follow and report
    public function getFrmRun(){
        return $this->db->from('STY_KYT_RUNNING')
                        ->order_by('NRUNNO', 'ASC')
                        ->get()->result();
    }

    // report 
    public function totalRisk($fyear, $cst=''){
        if($cst != ''){
            $this->db->where('CST', $cst);
        }else{
            $this->db->where_in('CST', array('1', '2'));
            // $this->db->where('CST', '2');
        }
        $this->db->from('FORM F')
                 ->join('STY_KYTFRM K', 'F.NFRMNO = K.NFRMNO AND F.VORGNO = K.VORGNO AND F.CYEAR = K.CYEAR AND F.CYEAR2 = K.CYEAR2 AND F.NRUNNO = K.NRUNNO')
                 ->where("(
                            TO_CHAR(DREQDATE, 'MM') >= '04' AND  TO_CHAR(DREQDATE, 'YYYY') = '$fyear' 
                            or
                            TO_CHAR(DREQDATE, 'MM') < '04'  AND 
                            (CASE 
                                WHEN TO_CHAR(DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(DREQDATE, 'YYYY')) - 1
                                ELSE TO_NUMBER(TO_CHAR(DREQDATE, 'YYYY'))
                            END) = '$fyear'
                        ) AND F.CST IN ('1','2')",null,false);
        return $this->db->get()->result();
    }

    public function topten($fyear){
        $this->db->select('I.ITEMS_TYPE, K.ITEMS_ID, I.ITEMS_NAME, COUNT(K.ITEMS_ID) AS AMOUNT')
                 ->from('STY_KYTFRM K')
                 ->join('FORM F', 'F.NFRMNO = K.NFRMNO AND F.VORGNO = K.VORGNO AND F.CYEAR = K.CYEAR AND F.CYEAR2 = K.CYEAR2 AND F.NRUNNO = K.NRUNNO')
                 ->join('STY_ITEMS I', 'I.ITEMS_ID = K.ITEMS_ID')
                 ->where("(
                            TO_CHAR(DREQDATE, 'MM') >= '04' AND  TO_CHAR(DREQDATE, 'YYYY') = '$fyear' 
                            or
                            TO_CHAR(DREQDATE, 'MM') < '04'  AND 
                            (CASE 
                                WHEN TO_CHAR(DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(DREQDATE, 'YYYY')) - 1
                                ELSE TO_NUMBER(TO_CHAR(DREQDATE, 'YYYY'))
                            END) = '$fyear'
                ) AND F.CST IN ('1','2')",null,false)
                 ->group_by('I.ITEMS_TYPE, K.ITEMS_ID, I.ITEMS_NAME')
                 ->order_by('AMOUNT DESC')
                 ->limit(10);
        return $this->db->get()->result();
    }

    public function monthly($fyear, $month){
        $this->db->select('F.DREQDATE, COUNT(F.DREQDATE) AS AMOUNT')
                 ->from('STY_KYTFRM K')
                 ->join('FORM F', 'F.NFRMNO = K.NFRMNO AND F.VORGNO = K.VORGNO AND F.CYEAR = K.CYEAR AND F.CYEAR2 = K.CYEAR2 AND F.NRUNNO = K.NRUNNO')
                 ->where("(
                            TO_CHAR(DREQDATE, 'MM') >= '04' AND  TO_CHAR(DREQDATE, 'YYYY') = '$fyear' 
                            or
                            TO_CHAR(DREQDATE, 'MM') < '04'  AND 
                            (CASE 
                                WHEN TO_CHAR(DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(DREQDATE, 'YYYY')) - 1
                                ELSE TO_NUMBER(TO_CHAR(DREQDATE, 'YYYY'))
                            END) = '$fyear'
                            ) AND TO_CHAR(DREQDATE, 'MM') = '$month' AND F.CST IN ('1','2')",null,false)
                //  ->where("TO_CHAR(DREQDATE, 'MM')" , $month)
                 ->group_by('F.DREQDATE')
                 ->order_by('AMOUNT DESC');
        return $this->db->get()->result();
    }

    public function monSection($fyear, $month){
        $this->db->select('O.SSECCODE, O.SSEC, F.DREQDATE, COUNT(K.SECCODE) AS AMOUNT')
                 ->from('STY_KYTFRM K')
                 ->join("FORM F" , "F.NFRMNO = K.NFRMNO AND F.VORGNO = K.VORGNO AND F.CYEAR = K.CYEAR AND F.CYEAR2 = K.CYEAR2 AND F.NRUNNO = K.NRUNNO
                        AND(
                                TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
                                or
                                TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
                                (CASE 
                                    WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
                                    ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
                                END) = '$fyear'
                            )  
                        AND TO_CHAR(F.DREQDATE, 'MM') = '$month'
                        AND F.CST IN ('1','2') ",null,false)
                 ->join('ORGANIZATIONS O', 'O.SSECCODE = K.SECCODE', 'right')
                 ->where(" O.CSTATUS = '1' AND O.SSECCODE != '00'")
                 ->group_by('O.SSECCODE, O.SSEC, F.DREQDATE')
                 ->order_by('AMOUNT DESC');
        return $this->db->get()->result();
    }

    public function monCategory($fyear, $month){
        $this->db->select('I.ITEMS_NAME, I.ITEMS_ID, F.DREQDATE, COUNT(K.ITEMS_ID) AS AMOUNT')
                 ->from('STY_KYTFRM K')
                 ->join("FORM F" , "F.NFRMNO = K.NFRMNO AND F.VORGNO = K.VORGNO AND F.CYEAR = K.CYEAR AND F.CYEAR2 = K.CYEAR2 AND F.NRUNNO = K.NRUNNO
                        AND(
                                TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
                                or
                                TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
                                (CASE 
                                    WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
                                    ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
                                END) = '$fyear'
                            )  
                        AND TO_CHAR(F.DREQDATE, 'MM') = '$month'
                        AND F.CST IN ('1','2') ",null,false)
                 ->join('STY_ITEMS I', 'K.ITEMS_ID = I.ITEMS_ID')
                 ->where('I.ITEMS_TYPE = 6')
                 ->group_by('I.ITEMS_ID, I.ITEMS_NAME, F.DREQDATE')
                 ->order_by('AMOUNT DESC, I.ITEMS_ID ASC');
        return $this->db->get()->result();
    }

    public function yearSection($fyear){
        $this->db->select("O.SSECCODE, O.SSEC, TO_CHAR(F.DREQDATE, 'MON') AS MON, COUNT(K.SECCODE) AS AMOUNT")
                 ->from('STY_KYTFRM K')
                 ->join("FORM F" , "F.NFRMNO = K.NFRMNO AND F.VORGNO = K.VORGNO AND F.CYEAR = K.CYEAR AND F.CYEAR2 = K.CYEAR2 AND F.NRUNNO = K.NRUNNO
                        AND(
                                TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
                                or
                                TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
                                (CASE 
                                    WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
                                    ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
                                END) = '$fyear'
                            )  
                        AND F.CST IN ('1','2') ",null,false)
                 ->join('ORGANIZATIONS O', 'O.SSECCODE = K.SECCODE', 'right')
                 ->where(" O.CSTATUS = '1' AND O.SSECCODE != '00'")
                 ->group_by("O.SSECCODE, O.SSEC, TO_CHAR(F.DREQDATE, 'MON')")
                 ->order_by('AMOUNT DESC');
        return $this->db->get()->result();
    }

    public function yearCategory($fyear){
        $this->db->select("I.ITEMS_NAME, I.ITEMS_ID, TO_CHAR(F.DREQDATE, 'MON') AS MON, COUNT(K.ITEMS_ID) AS AMOUNT")
                 ->from('STY_KYTFRM K')
                 ->join("FORM F" , "F.NFRMNO = K.NFRMNO AND F.VORGNO = K.VORGNO AND F.CYEAR = K.CYEAR AND F.CYEAR2 = K.CYEAR2 AND F.NRUNNO = K.NRUNNO
                        AND(
                                TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
                                or
                                TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
                                (CASE 
                                    WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
                                    ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
                                END) = '$fyear'
                            )  
                        AND F.CST IN ('1','2') ",null,false)
                 ->join('STY_ITEMS I', 'K.ITEMS_ID = I.ITEMS_ID', 'right')
                 ->where("I.ITEMS_TYPE = 6")
                 ->group_by("I.ITEMS_NAME, I.ITEMS_ID, TO_CHAR(F.DREQDATE, 'MON')")
                 ->order_by('AMOUNT DESC, I.ITEMS_ID ASC');
        return $this->db->get()->result();
    }

    public function getReport($fyear){
        $sql = "
        SELECT SDEPT, SSEC, SNAME, NVL(APR,'') AS APR, NVL(MAY,'') AS MAY, NVL(JUN,'') AS JUN, NVL(JUL,'') AS JUL, NVL(AUG,'') AS AUG, NVL(SEP,'') AS SEP,
        NVL(OCT,'') AS OCT, NVL(NOV,'') AS NOV, NVL(DEC,'') AS DEC, NVL(JAN,'') AS JAN, NVL(FEB,'') AS FEB, NVL(MAR,'') AS MAR FROM 
        (
        SELECT O.SDEPT, O.SSEC, A.SNAME, TO_CHAR(F.DREQDATE, 'MON') AS MONTH, COUNT(TO_CHAR(F.DREQDATE, 'MON')) AS AMOUNT FROM STY_KYTFRM K
        JOIN FORM F ON F.NFRMNO = K.NFRMNO AND F.VORGNO = K.VORGNO AND F.CYEAR = K.CYEAR AND F.CYEAR2 = K.CYEAR2 AND F.NRUNNO = K.NRUNNO 
        AND( TO_CHAR(F.DREQDATE, 'MM') >= '04' AND TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' or TO_CHAR(F.DREQDATE, 'MM') < '04' 
            AND (CASE WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1 ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) END) = '$fyear' ) 
        AND F.CST IN ('1','2')
        JOIN ORGANIZATIONS O ON K.SECCODE = O.SSECCODE
        JOIN AMECUSERALL A ON K.EMPNOHEAD = A.SEMPNO
        GROUP BY O.SDEPT, O.SSEC, A.SNAME, TO_CHAR(F.DREQDATE, 'MON')
        ORDER BY O.SDEPT ASC, O.SSEC ASC
        )

        PIVOT(
                SUM(AMOUNT) FOR MONTH IN 
                ('APR' AS APR,'MAY' AS MAY,'JUN' AS JUN,'JUL' AS JUL,'AUG' AS AUG,'SEP' AS SEP,'OCT' AS OCT,'NOV' AS NOV,'DEC' AS DEC, 'JAN' AS JAN,'FEB' AS FEB,'MAR' AS MAR)
        )";
        return $this->db->query($sql)->result();
    }

    public function getKytReport($condition=''){
        $sql = "SELECT ROWNUM AS NO , A.* FROM (SELECT * FROM STY_KYT_REPORT  WHERE CST in ('1','2')  $condition ORDER BY DREQDATE) A";
        return $this->db->query($sql)->result();

    }

}