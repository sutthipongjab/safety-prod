<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authen extends MY_Controller {
    function __construct(){
        parent::__construct();
    }

    public function move(){
        $this->views('layout/move');
    }

    public function setSession(){
        $_SESSION['user']  = (object)$_POST['info'];
        $_SESSION['group']  = (object)$_POST['group'];
        $_SESSION['menu']  = $_POST['menu'];
		$_SESSION['profile-img'] = $_POST['info']['image'];
         if($_SESSION['group'] != null && $_SESSION['group']->GROUP_HOME != null){
            $redir = $_SESSION['group']->GROUP_HOME;
        }else{
            $redir = 'home';
        }
        echo json_encode(['url' => $redir]);
    }

    public function logout(){
        unset($_SESSION['user']);
        unset($_SESSION['group']);
		unset($_SESSION['menu']);
        setcookie($_ENV['APP_NAME'], "", time() - 3600, "/");
        setcookie('safety_session', "", time() - 3600, "/");
		redirect($_ENV['APP_HOST'].'/form/authen/index/'.$this->program);
		// redirect('https://' . $_SERVER['HTTP_HOST'].'/form/authen/pass_logout/'.$this->program);
    }

    public function goOut($url = 'https://portal.mitsubishielevatorasia.co.th/sites/GP/ST/Pages/default.aspx'){
        redirect($url);
    }


}