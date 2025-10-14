<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News_model extends CI_Model {
    protected $db;
    public function __construct(){
        parent::__construct();
        $this->db = $this->load->database('gpreport', true);
        $this->load->library('Format', 'format');
    }

    function insertNews($data){
        $list = array('NEWS_START', 'NEWS_END', 'NEWS_ADDDATE', 'NEWS_UPDATEDATE');
        $data['NEWS_ID'] = $this->generate_news_id();
        $this->setData($data, $list);
        $this->db->insert('NEWS');
        return $data['NEWS_ID'];
    }

    function updateNews($data, $q){
        $list = array('NEWS_START', 'NEWS_END', 'NEWS_ADDDATE', 'NEWS_UPDATEDATE');
        $this->setData($data, $list);
        $this->db->where($q);
        return $this->db->update('NEWS');
    }

    function deleteNews($q){
        $this->db->where($q);
        return $this->db->delete('NEWS');
    }

    private function generate_news_id(){
        $data = $this->db->select('NVL(MAX(NEWS_ID),0) AS ID')
            ->get('NEWS')->result();
        return $data[0]->ID+1;
    }

    private function generate_file_id(){
        $data = $this->db->select('NVL(MAX(FILE_ID),0) AS ID')
            ->get('NEWS_FILES')->result();
        return $data[0]->ID+1;
    }

    function getNews($q = ''){
        if($q) $this->db->where($q);
        return $this->db->from('NEWS')
            ->join('AMECUSERALL', 'NEWS_ADDBY = SEMPNO')
			->order_by('NEWS_ADDDATE', 'DESC')
            ->get()
            ->result();
    }

    function insertFile($data){
        $id = $this->generate_file_id();
        $data['FILE_ID'] = $id;
        return $this->db->insert('NEWS_FILES', $data);
    }

    function deleteFile($q){
        $this->db->where($q);
        return $this->db->delete('NEWS_FILES');
    }

    function getFiles($q = ''){
        if($q) $this->db->where($q);
        return $this->db->from('NEWS_FILES')
            ->get()
            ->result();
    }

    function setData($data, $list = array()){
        if($list){
            foreach ($data as $key => $value) {
                if(in_array($key, $list)){
                    $this->db->set($key, "to_date('".$value."','YYYY-MM-DD HH24:MI:SS')", false);
                }else{
                    $this->db->set($key, $value);
                }
            }
        }else{
            $this->db->set($data);
        }
    }

}