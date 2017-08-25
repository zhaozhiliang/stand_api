<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends Needlogin_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        $this->logout();
    }

    public function logout()
    {
        $_SESSION=array();

        session_destroy();
        //$this->session->sess_destroy();
        $this->response(200,'logout success !',array());
    }
}
