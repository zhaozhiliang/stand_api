<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Version extends Api_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('version_model');

    }

    public function index(){
    }

    /**
     * 消息列表
     */
    public function getNew(){
        $base_type = $this->_requestParams['base_type'];
        switch($base_type){
            case 'ios':
                $app_type = 1; break;
            case 'android' :
                $app_type = 2; break;
            default :
                $this->response(4002,'参数错误');
        }

        $res = $this->version_model->getNew($app_type);

        if($res !== FALSE){
            unset($res['status']);
            $this->response(200,'',$res);
        }else{
            $this->response(4004,'执行失败');
        }
    }


}