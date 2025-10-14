<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'models/Gpreport_model.php';
class inspection_model extends Gpreport_model {
    public function __construct(){
        parent::__construct();

    }

    public function getAllSection(){
        $this->db->distinct()
                 ->select('SSEC, SSECCODE')
                 ->from('ORGANIZATIONS')
                 ->where('CSTATUS','1')
                 ->not_like('SSEC','No Section')
                 ->order_by('SSEC ASC');
        return $this->db->get()->result();
    }

    public function getST(){
        return $this->db->from('ORGANIZATIONS')
                    ->where('SDEPCODE', '020601')
                    ->get()
                    ->result();
    }

    public function getSem($seccode, $poscode = '30'){
        return $this->db->select('B.*')
                        ->from('SEQUENCEORG A')
                        ->join('AMECUSERALL B', 'B.SEMPNO = A.EMPNO')
                        ->where('A.VORGNO',$seccode)
                        ->where('A.SPOSCODE', $poscode)
                        ->get()->result();
    }

    public function getClass($code){
        return $this->db->from('STY_TYPE')
                        ->where('TYPE_CODE', $code)
                        ->order_by('TYPE_NO','ASC')
                        ->get()->result();
    }

    public function getItems($type=""){
        $ctype = ($type == "" ? "2" : $type );
        return $this->db->select('A.*, B.TYPE_NAME')
                        ->from('STY_ITEMS A')
                        ->join('STY_TYPE B','A.ITEMS_TYPE = B.TYPE_ID')
                        ->where ('ITEMS_TYPE',$ctype )
                        ->order_by('ITEMS_ID', 'ASC')
                        ->get()->result();
    }

    public function getTypeID($typeCode){
        return $this->db->from('STY_TYPE')
                        ->where('TYPE_CODE', $typeCode)
                        ->get()->result();
    }

    public function getPatrol($condition=''){
        foreach($condition as $key => $value){
            if(!empty($value)){
                $this->set_where($key, $value);
            }
        }
        return $this->db->from('STY_PATROL_INSPECTION')
                        ->get()
                        ->result();
    }

    public function getPatrols($dateStart, $dateEnd){
        return $this->db->distinct()
                        ->select('S.FORMNO, S.NFRMNO, S.VORGNO, S.CYEAR, S.CYEAR2, S.NRUNNO, S.PA_SECTION, S.OWNER_SECTION, S.PA_OWNER, S.STNAME, S.SNAME,
S.SSEC, S.SDEPT, S.SDIV, S.PA_DATE, S.PA_AUDIT, F.CST')
                        ->from('STY_PATROL_INSPECTION S')
                        ->join('FORM F', 'S.NFRMNO = F.NFRMNO AND S.VORGNO =F.VORGNO AND S.CYEAR = F.CYEAR AND S.CYEAR2 = F.CYEAR2 AND S.NRUNNO = F.NRUNNO')
                        ->where('PA_DATE >=',"TO_DATE('$dateStart 00:00:00', 'DD/MM/YYYY HH24:MI:SS')", false)
                        ->where('PA_DATE <=',"TO_DATE('$dateEnd 23:59:59', 'DD/MM/YYYY HH24:MI:SS')", false)
                        ->order_by('NRUNNO ASC')
                        ->get()->result();
    }

    // อันเก่าอันแรกสุด
    // public function getEmployee($seccode){
    //     return $this->db->select('B.*')
    //                     ->from('SEQUENCEORG A')
    //                     ->join('AMECUSERALL B', 'B.SEMPNO = A.EMPNO')
    //                     ->where('A.VORGNO',$seccode)
    //                     ->where("(A.SPOSCODE >= '32' AND A.SPOSCODE < '60')", null, false)
    //                     ->get()->result();
    // }

    // 2025-07-14 แก้ไขให้หาพนักงานที่อยู่ภายใต้ทั้งหมด หากเป็น department อันเก่าจะไม่เจอใครเพราะหาตาม VORGNO
    public function getEmployee($empno){
        return $this->db->query("
            SELECT DISTINCT B.* FROM 
            (
                SELECT * FROM SEQUENCEORG
                START WITH HEADNO = '$empno' CONNECT BY PRIOR EMPNO = HEADNO AND PRIOR CCO = CCO1
            ) A
            JOIN AMECUSERALL B ON A.EMPNO = B.SEMPNO
            WHERE B.SEMPNO != '$empno'  AND B.CSTATUS = 1
        ")->result();
    }



    public function compClass($fyear){
        // $this->db->select("T.TYPE_NAME, TO_CHAR(P.PA_DATECREATE, 'MON') AS MON, COUNT(T.TYPE_NAME) AS AMOUNT")
        //          ->from('STY_PATROL P')
        //          ->join("FORM F" , "F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO
        //                 AND(
        //                         TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
        //                         or
        //                         TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
        //                         (CASE 
        //                             WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
        //                             ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
        //                         END) = '$fyear'
        //                     )  
        //                     AND F.CST IN ('1','2') ",null,false)
        //          ->join('STY_TYPE T', 'P.PA_CLASS = T.TYPE_ID ')
        //          ->where("T.TYPE_NO IN (1,2)")
        //          ->group_by("T.TYPE_NAME, TO_CHAR(P.PA_DATECREATE, 'MON') ")
        //          ->order_by('MON ASC');
        // return $this->db->get()->result();
        $this->db->select("T.TYPE_NAME, TO_CHAR(P.PA_DATE, 'MON') AS MON, COUNT(T.TYPE_NAME) AS AMOUNT")
                 ->from('STY_PATROL P')
                 ->join("FORM F" , "F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO
                        AND F.CST = '2' ",null,false)
                 ->join('STY_TYPE T', 'P.PA_CLASS = T.TYPE_ID ')
                 ->where("T.TYPE_NO IN (1,2) AND(
                            TO_CHAR(P.PA_DATE, 'MM') >= '04' AND  TO_CHAR(P.PA_DATE, 'YYYY') = '$fyear' 
                            or
                            TO_CHAR(P.PA_DATE, 'MM') < '04'  AND 
                            (CASE 
                                WHEN TO_CHAR(P.PA_DATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) - 1
                                ELSE TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY'))
                            END) = '$fyear'
                        )  ")
                 ->group_by("T.TYPE_NAME, TO_CHAR(P.PA_DATE, 'MON') ")
                 ->order_by('MON ASC');
        return $this->db->get()->result();
    }

    public function getDataDept($month, $fyear){
        // $this->db->select("SDEPT, T.TYPE_NAME, COUNT(T.TYPE_NAME) AS AMOUNT")
        //          ->from('STY_PATROL P')
        //          ->join("FORM F" , "F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO
        //             AND(
        //                     TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
        //                     or
        //                     TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
        //                     (CASE 
        //                         WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
        //                         ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
        //                     END) = '$fyear'
        //                 )  
        //                 AND F.CST IN ('1','2') 
        //                 AND TO_CHAR(F.DREQDATE, 'MM') = '$month'",null,false)
        //          ->join('STY_TYPE T', 'P.PA_CLASS = T.TYPE_ID ')
        //          ->join('ORGANIZATIONS O', 'O.SSECCODE = P.PA_SECTION')
        //          ->where("T.TYPE_NO IN (1,2) AND O.CSTATUS = '1' AND O.SSECCODE != '00'")
        //          ->group_by("SDEPT, T.TYPE_NAME")
        //          ->order_by('SDEPT');
        // return $this->db->get()->result();
        $this->db->select("SDEPT, T.TYPE_NAME, COUNT(T.TYPE_NAME) AS AMOUNT")
                 ->from('STY_PATROL P')
                 ->join("FORM F" , "F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO
                        AND F.CST = '2'",null,false)
                 ->join('STY_TYPE T', 'P.PA_CLASS = T.TYPE_ID ')
                 ->join('ORGANIZATIONS O', 'O.SSECCODE = P.PA_SECTION')
                 ->where("T.TYPE_NO IN (1,2) AND O.CSTATUS = '1' AND O.SSECCODE != '00' 
                                    AND(
                            TO_CHAR(P.PA_DATE, 'MM') >= '04' AND  TO_CHAR(P.PA_DATE, 'YYYY') = '$fyear' 
                            or
                            TO_CHAR(P.PA_DATE, 'MM') < '04'  AND 
                            (CASE 
                                WHEN TO_CHAR(P.PA_DATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) - 1
                                ELSE TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY'))
                            END) = '$fyear'
                        ) AND TO_CHAR(P.PA_DATE, 'MM') = '$month'")
                 ->group_by("SDEPT, T.TYPE_NAME")
                 ->order_by('SDEPT');
        return $this->db->get()->result();
    }

    public function getClassByMonth($month, $fyear, $class=''){
        // $sql = "
        //         SELECT I.ITEMS_ID, I.ITEMS_NAME, PT.CLASS, COUNT(PT.CLASS) AS AMOUNT  FROM STY_ITEMS I
        //         JOIN STY_TYPE TI ON I.ITEMS_TYPE = TI.TYPE_ID AND TI.TYPE_ID = 2
        //         LEFT JOIN 
        //         ( 
        //             SELECT DREQDATE, TYPE_NAME AS CLASS, CST, PA_ITEMS FROM STY_PATROL P
        //             JOIN STY_TYPE TC ON P.PA_CLASS = TC.TYPE_ID 
        //             JOIN FORM F ON F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO
        //                 AND(
        //                     TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
        //                     or
        //                     TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
        //                     (CASE 
        //                         WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
        //                         ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
        //                     END) = '$fyear'
        //                 )  
        //                 AND F.CST IN ('1','2') 
        //                 AND TO_CHAR(F.DREQDATE, 'MM') = '$month'
        //                 WHERE TYPE_NAME = '$class'
        //                 ORDER BY TYPE_NAME ASC
        //         ) PT ON PT.PA_ITEMS = I.ITEMS_ID
        //         GROUP BY I.ITEMS_ID, I.ITEMS_NAME, PT.CLASS
        //         ORDER BY I.ITEMS_ID";
        // return  $this->db->query($sql)->result();
        $sql = "
                SELECT I.ITEMS_ID, I.ITEMS_NAME, PT.CLASS, COUNT(PT.CLASS) AS AMOUNT FROM STY_ITEMS I  
                JOIN STY_TYPE TI ON I.ITEMS_TYPE = TI.TYPE_ID AND TI.TYPE_ID = 2 
                LEFT JOIN 
                ( 
                    SELECT DREQDATE, TYPE_NAME AS CLASS, CST, PA_ITEMS FROM STY_PATROL P-- ON P.PA_ITEMS = I.ITEMS_ID AND P.PA_CLASS = 3 
                    JOIN STY_TYPE TC ON P.PA_CLASS = TC.TYPE_ID -- AND TC.TYPE_NO = 1 
                    JOIN FORM F ON F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO AND F.CST = '2'
                    WHERE TYPE_NAME = '$class' 
                    AND( TO_CHAR(P.PA_DATE, 'MM') >= '04' AND TO_CHAR(P.PA_DATE, 'YYYY') = '$fyear' or TO_CHAR(P.PA_DATE, 'MM') < '04' 
                    AND (CASE WHEN TO_CHAR(P.PA_DATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) - 1 ELSE TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) END) = '$fyear' ) 
                    AND TO_CHAR(P.PA_DATE, 'MM') = '$month' 
                    ORDER BY TYPE_NAME ASC 
                ) PT ON PT.PA_ITEMS = I.ITEMS_ID 
                GROUP BY I.ITEMS_ID, I.ITEMS_NAME, PT.CLASS 
                ORDER BY I.ITEMS_ID";
        return  $this->db->query($sql)->result();
    }

    public function getDataSec($month, $fyear, $section){
        // $sql = "
        //         SELECT SSEC, TYPE_NO, CLASS, COUNT(CLASS) AS AMOUNT FROM ORGANIZATIONS O
        //         LEFT JOIN 
        //         (
        //             SELECT PA_SECTION, T.TYPE_NO, T.TYPE_NAME AS CLASS FROM STY_PATROL P
        //             JOIN FORM F ON F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO
        //             AND(
        //                 TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
        //                 or
        //                 TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
        //                 (CASE 
        //                     WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
        //                     ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
        //                 END) = '$fyear'
        //             )  
        //             AND F.CST IN ('1','2') 
        //             AND TO_CHAR(F.DREQDATE, 'MM') = '$month'
        //             JOIN STY_TYPE T ON P.PA_CLASS = T.TYPE_ID AND T.TYPE_NO IN (1,2)
        //         ) PA ON O.SSECCODE = PA.PA_SECTION

        //         WHERE SDEPT = '$section'
        //         AND CSTATUS = '1' AND SSECCODE != '00' 
        //         GROUP BY SSEC, CLASS, TYPE_NO
        //         ORDER BY SSEC ASC, CLASS ASC";
        // return $this->db->query($sql)->result();
        $sql = "
                SELECT SSEC, TYPE_NO, CLASS, COUNT(CLASS) AS AMOUNT FROM ORGANIZATIONS O
                LEFT JOIN 
                (
                    SELECT PA_SECTION, T.TYPE_NO, T.TYPE_NAME AS CLASS FROM STY_PATROL P
                    JOIN FORM F ON F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO
                    AND F.CST = '2' 
                    JOIN STY_TYPE T ON P.PA_CLASS = T.TYPE_ID AND T.TYPE_NO IN (1,2)
                    WHERE ( TO_CHAR(P.PA_DATE, 'MM') >= '04' AND TO_CHAR(P.PA_DATE, 'YYYY') = '$fyear' or TO_CHAR(P.PA_DATE, 'MM') < '04' 
                    AND (CASE WHEN TO_CHAR(P.PA_DATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) - 1 ELSE TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) END) = '$fyear' ) 
                    AND TO_CHAR(P.PA_DATE, 'MM') = '$month' 
                ) PA ON O.SSECCODE = PA.PA_SECTION

                WHERE SDEPT = '$section'
                AND CSTATUS = '1' AND SSECCODE != '00' 
                GROUP BY SSEC, CLASS, TYPE_NO
                ORDER BY SSEC ASC, CLASS ASC";
        return $this->db->query($sql)->result();
    }

    public function getYearlySec($month, $fyear, $section){
        // $this->db->select("SSEC,T.TYPE_NAME AS CLASS, TO_CHAR(P.PA_DATECREATE, 'MON') AS MON, COUNT(T.TYPE_NAME) AS AMOUNT")
        //          ->from('STY_PATROL P')
        //          ->join("FORM F", "F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO
        //                 AND(
        //                     TO_CHAR(F.DREQDATE, 'MM') >= '04' AND  TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' 
        //                     or
        //                     TO_CHAR(F.DREQDATE, 'MM') < '04'  AND 
        //                     (CASE 
        //                         WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1
        //                         ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY'))
        //                     END) = '$fyear'
        //                 )  
        //                 AND F.CST IN ('1','2')",null,false)
        //          ->join('STY_TYPE T', 'P.PA_CLASS = T.TYPE_ID AND T.TYPE_NO IN (1,2)')
        //          ->join('ORGANIZATIONS O', 'O.SSECCODE = P.PA_SECTION')
        //          ->where("UPPER(SSEC) = '$section'")
        //          ->group_by("SSEC, T.TYPE_NAME, TO_CHAR(P.PA_DATECREATE, 'MON')")
        //          ->order_by('MON ASC');
        // return $this->db->get()->result();
        $this->db->select("SSEC,T.TYPE_NAME AS CLASS, TO_CHAR(P.PA_DATECREATE, 'MON') AS MON, COUNT(T.TYPE_NAME) AS AMOUNT")
                 ->from('STY_PATROL P')
                 ->join("FORM F", "F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO 
                        AND F.CST = '2'",null,false)
                 ->join('STY_TYPE T', 'P.PA_CLASS = T.TYPE_ID AND T.TYPE_NO IN (1,2)')
                 ->join('ORGANIZATIONS O', 'O.SSECCODE = P.PA_SECTION')
                 ->where("UPPER(SSEC) = '$section'
                        AND( TO_CHAR(P.PA_DATE, 'MM') >= '04' AND TO_CHAR(P.PA_DATE, 'YYYY') = '$fyear' or TO_CHAR(P.PA_DATE, 'MM') < '04' 
                        AND (CASE WHEN TO_CHAR(P.PA_DATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) - 1 ELSE TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) END) = '$fyear' ) ")
                 ->group_by("SSEC, T.TYPE_NAME, TO_CHAR(P.PA_DATECREATE, 'MON')")
                 ->order_by('MON ASC');
        return $this->db->get()->result();
    }

    public function getCatByMonth($month, $fyear, $section, $class = ''){
        $where = $class != '' ? "AND TYPE_NAME = '$class'" : $class;
        // $sql = "
        //     SELECT I.ITEMS_ID, I.ITEMS_NAME, COUNT(CLASS) AS AMOUNT
        //     FROM STY_ITEMS I 
        //     JOIN STY_TYPE TI ON I.ITEMS_TYPE = TI.TYPE_ID AND TI.TYPE_ID = 2 
        //     LEFT JOIN 
        //     ( 
        //         SELECT DREQDATE, TYPE_NAME AS CLASS, CST, PA_ITEMS FROM STY_PATROL P
        //         JOIN STY_TYPE TC ON P.PA_CLASS = TC.TYPE_ID AND TC.TYPE_NO IN (1,2) 
        //         JOIN FORM F ON F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO 
        //         AND( TO_CHAR(F.DREQDATE, 'MM') >= '04' AND TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' or TO_CHAR(F.DREQDATE, 'MM') < '04' 
        //         AND (CASE WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1 ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) END) = '$fyear' ) 
        //         AND F.CST IN ('1','2') 
        //         AND TO_CHAR(F.DREQDATE, 'MM') = '$month' 
        //         JOIN ORGANIZATIONS O ON O.SSECCODE = P.PA_SECTION
        //         WHERE UPPER(SSEC) = '$section' $where
        //         ORDER BY TYPE_NAME ASC 
        //     ) PT ON PT.PA_ITEMS = I.ITEMS_ID 
        //     GROUP BY I.ITEMS_ID, I.ITEMS_NAME
        //     ORDER BY I.ITEMS_ID";
        $sql = "
            SELECT I.ITEMS_ID, I.ITEMS_NAME, COUNT(CLASS) AS AMOUNT
            FROM STY_ITEMS I 
            JOIN STY_TYPE TI ON I.ITEMS_TYPE = TI.TYPE_ID AND TI.TYPE_ID = 2 
            LEFT JOIN 
            ( 
                SELECT DREQDATE, TYPE_NAME AS CLASS, CST, PA_ITEMS FROM STY_PATROL P-- ON P.PA_ITEMS = I.ITEMS_ID AND P.PA_CLASS = 3 
                --SELECT * FROM STY_PATROL P
                JOIN STY_TYPE TC ON P.PA_CLASS = TC.TYPE_ID AND TC.TYPE_NO IN (1,2) -- AND TC.TYPE_NO = 1 
                JOIN FORM F ON F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO 
                
                AND F.CST = '2'
            
                JOIN ORGANIZATIONS O ON O.SSECCODE = P.PA_SECTION
                WHERE UPPER(SSEC) = '$section' $where
                AND( TO_CHAR(P.PA_DATE, 'MM') >= '04' AND TO_CHAR(P.PA_DATE, 'YYYY') = '$fyear' or TO_CHAR(P.PA_DATE, 'MM') < '04' 
                AND (CASE WHEN TO_CHAR(P.PA_DATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) - 1 ELSE TO_NUMBER(TO_CHAR(P.PA_DATE, 'YYYY')) END) = '$fyear' ) 
                AND TO_CHAR(P.PA_DATE, 'MM') = '$month' 
                ORDER BY TYPE_NAME ASC 
            ) PT ON PT.PA_ITEMS = I.ITEMS_ID 

            GROUP BY I.ITEMS_ID, I.ITEMS_NAME
            ORDER BY I.ITEMS_ID
        ";
        // $where = $class != '' ? "WHERE CLASS = '$class'" : $class;
        // $sql = "
        //     SELECT I.ITEMS_ID, I.ITEMS_NAME, COUNT(CLASS) AS AMOUNT
        //     FROM STY_ITEMS I 
        //     JOIN STY_TYPE TI ON I.ITEMS_TYPE = TI.TYPE_ID AND TI.TYPE_ID = 2 
        //     LEFT JOIN 
        //     ( 
        //         SELECT DREQDATE, TYPE_NAME AS CLASS, CST, PA_ITEMS FROM STY_PATROL P
        //         JOIN STY_TYPE TC ON P.PA_CLASS = TC.TYPE_ID AND TC.TYPE_NO IN (1,2) 
        //         JOIN FORM F ON F.NFRMNO = P.NFRMNO AND F.VORGNO = P.VORGNO AND F.CYEAR = P.CYEAR AND F.CYEAR2 = P.CYEAR2 AND F.NRUNNO = P.NRUNNO 
        //         AND( TO_CHAR(F.DREQDATE, 'MM') >= '04' AND TO_CHAR(F.DREQDATE, 'YYYY') = '$fyear' or TO_CHAR(F.DREQDATE, 'MM') < '04' 
        //         AND (CASE WHEN TO_CHAR(F.DREQDATE, 'MM') < '04' THEN TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) - 1 ELSE TO_NUMBER(TO_CHAR(F.DREQDATE, 'YYYY')) END) = '$fyear' ) 
        //         AND F.CST IN ('1','2') 
        //         AND TO_CHAR(F.DREQDATE, 'MM') = '$month' 
        //         JOIN ORGANIZATIONS O ON O.SSECCODE = P.PA_SECTION
        //         WHERE UPPER(SSEC) = '$section' 
        //         ORDER BY TYPE_NAME ASC 
        //     ) PT ON PT.PA_ITEMS = I.ITEMS_ID 
        //     $where
        //     GROUP BY I.ITEMS_ID, I.ITEMS_NAME
        //     ORDER BY I.ITEMS_ID";
            return $this->db->query($sql)->result();
    }

    public function getPatrolReport($condition=''){
        
        $sql = "
        SELECT I.ITEMS_ID, I.ITEMS_NAME, I.ITEMS_ENAME, P.OWNER_SECTION, P.CLASS, COUNT(P.OWNER_SECTION) AS AMOUNT FROM STY_ITEMS I 
        LEFT JOIN 
            (
                SELECT P.ITEMS_ID, P.OWNER_SECTION, P.CLASS FROM STY_PATROL_INSPECTION P 
                JOIN SEQUENCEORG S ON P.PA_OWNER = S.EMPNO AND S.SPOSCODE = '30'
                JOIN ORGANIZATIONS O ON S.VORGNO = O.SSECCODE
                WHERE CST = '2' $condition
            )P ON I.ITEMS_ID = P.ITEMS_ID   		
        WHERE I.ITEMS_TYPE = 2
        GROUP BY I.ITEMS_ID, I.ITEMS_NAME, I.ITEMS_ENAME, P.OWNER_SECTION, P.CLASS
        ORDER BY P.OWNER_SECTION ASC, I.ITEMS_ID ASC, CLASS ASC";
        return $this->db->query($sql)->result();
        // foreach($condition as $key => $value){
        //     if(!empty($value)){
        //         $this->set_where($key, $value);
        //     }
        // }
        // return $this->db->select('I.ITEMS_ID, I.ITEMS_NAME, I.ITEMS_ENAME, P.OWNER_SECTION, P.STNAME, P.CLASS, COUNT(I.ITEMS_ID) AS AMOUNT')
        //                 ->from('STY_ITEMS I ')
        //                 ->join('STY_PATROL_INSPECTION P', 'I.ITEMS_ID = P.ITEMS_ID', 'left')
        //                 ->where('ITEMS_TYPE = 2')
        //                 ->group_by('I.ITEMS_ID, I.ITEMS_NAME, I.ITEMS_ENAME, P.OWNER_SECTION, P.STNAME, P.CLASS')
        //                 ->order_by('P.OWNER_SECTION ASC, I.ITEMS_ID ASC, CLASS ASC')
        //                 ->get()
        //                 ->result();
    }
}

