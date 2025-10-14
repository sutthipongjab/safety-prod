<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends MY_Controller {
    function __construct(){
        parent::__construct();
    }

    public function page_missing() {
        $this->output->set_status_header('404');
        // echo site_url();
        // echo "<br>";
        // echo base_url();
        // echo "<br>";
        // echo root_url();
        // echo "<br>";
        // echo current_url();
        // echo "<br>";
        // echo uri_string();
        // echo "<br>";
        // echo index_page();
        // echo "<br>";
        // echo anchor();
        // echo "<br>";
        // echo anchor_popup();
        // echo "<br>";
        // echo prep_url();
        // echo "<br>";
        $data['heading'] = "404 Page Not Found";
        $data['message'] = "The page you requested was not found.";
        $data['image']   = root_url() . "cdn/theme/assets/image/dribbble_1.gif";
        // $data['image']   = $this->config->item('base_uri') . "cdn/theme/assets/image/dribbble_1.gif";
        $this->load->view('errors/html/error_404', $data);
    }

    public function internal_error() {
        $this->output->set_status_header('500');
        $data['heading'] = "500 Internal Server Error";
        $data['message'] = "The server encountered an internal error or misconfiguration and was unable to complete your request.";
        $data['errorCode'] = '500';
        // $this->load->view('errors/html/error_404', $data);

        $this->views('errors/html/error_template', $data);
    }


}