<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/_file.php';
class Category extends MY_Controller
{

    use _File;
    public function __construct()
    {
        parent::__construct();
        $this->session_expire();
        // Load any required models, libraries, etc.
        $this->load->model('ppe_model', 'ppe');
        $this->upload_path = "//amecnas/AMECWEB/File/" . ($this->_servername() == 'amecweb' ? 'production' : 'development') . "/safety/image/ppe/";
    }

    public function index()
    {
        // Load a view or perform some action
        $data['categories'] = $this->ppe->get_category();
        $data['title']      = 'Categories';

        $this->views('ppe/category/index', $data);
    }

    public function get_categories()
    {
        // Return a JSON response with all categories
        $data = $this->ppe->get_category();
        foreach ($data as $key => $item) {
            if ($item->CATIMAGE != null) {
                $item->CATIMAGE = $this->conVBase64($this->upload_path . $item->CATIMAGE);
            } else {
                $item->CATIMAGE = null;
            }
            // $item->PIC = $this->conVBase64($this->upload_path . $item->PCAT_PIC);
        }
        echo json_encode($data);
    }

    public function get_categoly_id()
    {
        $id   = $this->input->post('id');
        $data = $this->ppe->get_category_by_id($id);

        if ($data->CATIMAGE != null) {
            $data->CATIMAGE = $this->conVBase64($this->upload_path . $data->CATIMAGE);
        } else {
            $data->CATIMAGE = null;
        }
        // $item->PIC = $this->conVBase64($this->upload_path . $item->PCAT_PIC);

        echo json_encode($data);
    }

    public function insert_category()
    {
        // Insert a new category
        $id = $this->ppe->get_id_cat();
        if ($id === null || $id->id === null) {
            $id = 1;
        } else {
            $id = $id->id + 1;
        }
        $data = array(
            'CATID'    => $id,
            'CATNAME'  => trim($this->input->post('name')),
            'CATDESC'  => trim($this->input->post('description')),
            'CATUNIT'  => trim($this->input->post('unit')),
            'CATMETER' => trim($this->input->post('meter')),
            'CATSEQ'   => trim($this->input->post('seq')),
            'CATOWNER' => '2',
        );

        $file = $this->uploadfile($_FILES['file']);
        if ($file['status'] == 'success') {
            $data['CATIMAGE'] = $file['file_name'];
        }
        $this->ppe->insert_ppe('UNIFORM_CATEGORY', $data);
    }

    public function update_category()
    {
        $name = $this->input->post('name');
        $unit = $this->input->post('unit');
        $id   = $this->input->post('id');
        $desc = $this->input->post('description');
        $remark = $this->input->post('remark');
        $data = [
            'CATNAME' => $name,
            'CATUNIT' => $unit,
            'CATDESC' => $desc,
            'CATREMARK' => $remark
        ];

        $file = $this->uploadfile($_FILES['file']);

        if ($file['status'] == 'success') {
            $data['CATIMAGE'] = $file['file_name'];
        }

        $where = [
            'CATID' => $id
        ];

        $this->ppe->update_ppe('UNIFORM_CATEGORY', $where, $data);

    }

    public function update_category_status()
    {
        $id     = $this->input->post('id');
        $status = $this->input->post('status');
        $data   = [
            'CATANNUAL' => $status
        ];
        $where  = [
            'CATID' => $id
        ];

        $this->ppe->update_ppe('UNIFORM_CATEGORY', $where, $data);
    }

    public function create()
    {
        // Handle form submission for creating a new category
        if ($this->input->post()) {
            $this->ppe->insert_category($this->input->post());
            redirect('admin/category');
        } else {
            $this->load->view('admin/category/create');
        }
    }

    public function edit($id)
    {
        // Handle form submission for editing an existing category
        if ($this->input->post()) {
            $this->ppe->update_category($id, $this->input->post());
            redirect('admin/category');
        } else {
            $data['category'] = $this->ppe->get_category($id);
            $this->load->view('admin/category/edit', $data);
        }
    }

    public function delete($id)
    {
        // Handle deletion of a category
        $this->ppe->delete_category($id);
        redirect('admin/category');
    }

    public function get_ppe_cat()
    {
        $data = $this->ppe->get_ppe_category();
        echo json_encode($data);
    }
}
?>