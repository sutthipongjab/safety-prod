<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ppe_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->wf = $this->load->database('WEBFORM', true);
    }

    // Function to get all PPE records
    public function get_all_ppe()
    {
        $query = $this->db->get('ppe');
        return $query->result();
    }

    // Function to get a single PPE record by ID
    public function get_ppe_by_id($id)
    {
        $query = $this->db->get_where('ppe', array('id' => $id));
        return $query->row();
    }

    // Function to insert a new PPE record
    public function insert_ppe($table, $data)
    {
        return $this->db->insert($table, $data);
    }

    // Function to update an existing PPE record
    public function update_ppe($table, $where, $data)
    {
        $this->db->where($where);
        return $this->db->update($table, $data);
    }

    // Function to delete a PPE record
    public function delete_ppe($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('ppe');
    }

    public function get_category()
    {
        // $this->db->order_by('PCAT_ID', 'asc');
        $this->db->where('CATOWNER', '2');
        // $this->db->where('CATANNUAL', '1');
        $this->db->order_by('CATSEQ', 'asc');
        $query = $this->db->get('UNIFORM_CATEGORY');
        return $query->result();
    }

    public function get_category_by_id($id)
    {
        $this->db->where('CATID', $id);
        $query = $this->db->get('UNIFORM_CATEGORY');
        return $query->row();
    }

    public function get_id_list()
    {
        $this->db->select_max('PL_ID', 'id');
        $query = $this->db->get('PPE_LIST');
        return $query->row();
    }

    public function get_id_uniform()
    {
        $this->db->select_max('PROD_ID', 'id');
        $query = $this->db->get('UNIFORM');
        return $query->row();
    }

    public function get_id_cat()
    {
        $this->db->select_max('CATID', 'id');
        $query = $this->db->get('UNIFORM_CATEGORY');
        return $query->row();
    }

    public function get_id_request()
    {
        $this->db->select_max('URD_ID', 'id');
        $query = $this->db->get('UNIFORM_REQUEST_DETAIL');
        return $query->row();
    }

    public function get_max_code()
    {
        $sql   = "SELECT MAX(TO_NUMBER(REGEXP_SUBSTR(PROD_CODE, '[0-9]+$'))) AS code
                FROM uniform
                WHERE PROD_CODE LIKE 'PPE%'";
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function get_uniform($id)
    {
        $this->db->select('*');
        $this->db->from('UNIFORM');
        $this->db->where('PROD_ID', $id);
        // $this->db->where('PROD_STATUS', '1');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_uniform_by_cat($CATID)
    {
        $this->db->select('*');
        $this->db->from('UNIFORM');
        $this->db->where('PROD_CATEGORY', $CATID);
        $this->db->order_by('PROD_ID', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ppe_list($q = null)
    {
        $this->db->select('*');
        $this->db->from('PPE_LIST_VIEW');
        if ($q != null) {
            $this->db->where($q);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ppe_list_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('PPE_LIST_VIEW');
        $this->db->where('PROD_CATEGORY', $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ppe_category()
    {
        $this->db->select('*');
        $this->db->from('PPE_CATEGORY');
        $this->db->order_by('PCAT_ID', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_request_type()
    {
        $this->db->select('*');
        $this->db->from('UNIFORM_REQUEST_TYPE');
        $this->db->where('RT_STATUS', '2');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_data_user($empno)
    {
        $this->db->select('*');
        $this->db->from('AMECUSERALL');
        $this->db->where('SEMPNO', $empno);
        $this->db->where('CSTATUS', '1');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_request($nfrmno, $vorgno, $cyear)
    {
        $this->db->select("pw.*,TO_CHAR(CREATE_DATE, 'DD/MM/YYYY') AS CREATE_DATE");
        $this->db->from('PPE_WAITING pw');
        $this->db->order_by('CSTEPST', 'ASC');
        $this->db->order_by('NRUNNO', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    // public function get_history()
    // {
    //     $this->db->select('*');
    //     $this->db->from('PPE_HISTORY');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    public function get_history($q = null)
    {
        $this->db->select("A.*,B.*,C.*,D.*,TO_CHAR(A.TRS_DATE, 'YYYY-MM-DD') AS TRS_DATE , TO_CHAR(TRS_DATE, 'YYYY-MM-DD HH24:MI') AS TRS_SHOWDATE", FALSE);
        $this->db->from('UNIFORM_TRANSACTION A');
        $this->db->join('UNIFORM B', 'B.PROD_ID = A.TRS_PRODUCT');
        $this->db->join('UNIFORM_CATEGORY C', 'C.CATID = B.PROD_CATEGORY');
        $this->db->join('AMECUSERALL D', 'D.SEMPNO = A.TRS_USER');
        $this->db->where('C.CATOWNER', '2');
        if ($q != null) {
            $this->db->where($q);
        }
        // if ($date_s != null && $date_e != null) {
        //     $this->db->where("TO_CHAR(TRS_DATE, 'YYYY-MM-DD') >= ", $date_s);
        //     $this->db->where("TO_CHAR(TRS_DATE, 'YYYY-MM-DD') <= ", $date_e);
        // }

        $query = $this->db->get();
        return $query->result();
    }

    public function get_request($nfrmno, $vorgno, $cyear, $cyear2, $nrunno)
    {
        $this->db->select('*');
        $this->db->from('UNIFORM_VIEW');
        $this->db->where('NFRMNO', $nfrmno);
        $this->db->where('VORGNO', $vorgno);
        $this->db->where('CYEAR', $cyear);
        $this->db->where('CYEAR2', $cyear2);
        $this->db->where('NRUNNO', $nrunno);
        $this->db->order_by('NRUNNO', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function getEmpFlow($nfrmno, $vorgno, $cyear, $cyear2, $nrunno, $step, $cstepst)
    {
        $this->wf->select('*');
        $this->wf->from('FLOW');
        $this->wf->where('NFRMNO', $nfrmno);
        $this->wf->where('VORGNO', $vorgno);
        $this->wf->where('CYEAR', $cyear);
        $this->wf->where('CYEAR2', $cyear2);
        $this->wf->where('CSTEPNO', $step);
        $this->wf->where('NRUNNO', $nrunno);
        $this->wf->where('CSTEPST', $cstepst);

        // $query = $this->wf->get_compiled_select();
        // echo $query;

        return $this->wf->get()->result();
    }

    public function updateRemarkFlow($nfrmno, $vorgno, $cyear, $cyear2, $nrunno, $remark)
    {
        $this->wf->set('VREMARK', $remark);
        $this->wf->where('NFRMNO', $nfrmno);
        $this->wf->where('VORGNO', $vorgno);
        $this->wf->where('CYEAR', $cyear);
        $this->wf->where('CYEAR2', $cyear2);
        $this->wf->where('NRUNNO', $nrunno);
        $this->wf->where('CSTEPNO', '19');

        // $this->wf->where('CSTEPST', $cstepst);

        return $this->wf->update('FLOW');
    }
}
?>