<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Products extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->session_expire();
        $this->load->model('ppe_model', 'ppe');
    }

    public function index()
    {
        $this->views('ppe/product/index', array('title' => 'Products'));
    }

    public function getProducts()
    {
        $filters = [];

        if ($this->input->post('category')) {
            $filters['PROD_CATEGORY'] = $this->input->post('category');
        }

        if ($this->input->post('size')) {
            $filters['PROD_SIZES'] = $this->input->post('size');
        }

        if ($this->input->post('PROD_ID')) {
            $filters['PROD_ID'] = $this->input->post('PROD_ID');
        }

        $products = $this->ppe->get_ppe_list($filters);

        echo json_encode($products);
    }


    public function addProduct()
    {
        $this->views('ppe/product/add', array('title' => 'Add Product'));
    }

    public function getType()
    {
        $data = $this->ppe->get_category();
        echo json_encode($data);
    }

    public function insertProduct()
    {
        $productId = $this->ppe->get_id_uniform();
        $maxCode   = $this->ppe->get_max_code();

        $productId   = ($productId && $productId->id !== null) ? $productId->id + 1 : 1;
        $productCode = "PPE" . str_pad($maxCode->CODE + 1, 5, '0', STR_PAD_LEFT);

        $productData = [
            'PROD_ID'       => $productId,
            'PROD_CATEGORY' => $this->input->post('category'),
            'PROD_SIZES'    => $this->input->post('size'),
            'PROD_PRICE'    => $this->input->post('price'),
            'PROD_CODE'     => $productCode,
            'PROD_ALOC'     => 0,
            'PROD_REMAIN'   => $this->input->post('quantity'),
        ];

        $this->ppe->insert_ppe('UNIFORM', $productData);
    }

    public function test_code()
    {
        $data = $this->ppe->get_max_code();
        print_r($data);
    }

    public function updateProduct()
    {
        $PROD_ID = $this->input->post('PROD_ID');
        $REMAIN  = $this->input->post('PROD_REMAIN');
        $data    = [
            'PROD_REMAIN' => $REMAIN,
        ];
        $where   = [
            'PROD_ID' => $PROD_ID
        ];
        $this->ppe->update_ppe('UNIFORM', $where, $data);
    }

    public function updateSize()
    {
        $PROD_ID   = $this->input->post('PROD_ID');
        $PROD_SIZE = $this->input->post('PROD_SIZES');
        $data      = [
            'PROD_SIZES' => $PROD_SIZE,
        ];
        $where     = [
            'PROD_ID' => $PROD_ID
        ];
        $this->ppe->update_ppe('UNIFORM', $where, $data);
    }

    public function updatePrice()
    {
        $PROD_ID    = $this->input->post('PROD_ID');
        $PROD_PRICE = $this->input->post('PROD_PRICE');
        $data       = [
            'PROD_PRICE' => $PROD_PRICE,
        ];
        $where      = [
            'PROD_ID' => $PROD_ID
        ];

        $this->ppe->update_ppe('UNIFORM', $where, $data);

    }

    public function updateTransaction()
    {
        // INSERT INTO UNIFORM_TRANSACTION (TRS_TYPE, TRS_PRODUCT, TRS_QTY, TRS_USER, TRS_DETAIL, REMAINLOG, CATOWNER)

        $PROD_ID = $this->input->post('PROD_ID');
        $REMAIN  = $this->input->post('PROD_REMAIN');
        $REMARK  = $this->input->post('REMARK');
        $QTY     = $this->input->post('QTY');
        $TYPE    = $this->input->post('TYPE');

        $data = [
            'TRS_TYPE'    => $TYPE,
            'TRS_PRODUCT' => $PROD_ID,
            'TRS_QTY'     => $QTY,
            'TRS_USER'    => $_SESSION['user']->SEMPNO,
            'TRS_DETAIL'  => $REMARK,
            'REMAINLOG'   => $REMAIN,
            'CATOWNER'    => '2'
        ];

        $this->ppe->insert_ppe('UNIFORM_TRANSACTION', $data);
    }

    public function updateStatus()
    {
        $PROD_ID = $this->input->post('id');
        $status  = $this->input->post('status');

        $data  = [
            'PROD_STATUS' => $status
        ];
        $where = [
            'PROD_ID' => $PROD_ID
        ];
        $this->ppe->update_ppe('UNIFORM', $where, $data);
    }
}