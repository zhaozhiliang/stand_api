<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends Needlogin_Controller {
//class User extends CI_Controller {
    private $_codeTime = 60; //60分钟，短信验证码有效期
    private $_numOfPage = 20; //每页显示20条

    public function __construct()
    {
        parent::__construct();
        //$this->load->model('message_model');

    }

    public function index(){
    }

    /**
     * 消息列表
     */
    public function messageList(){
        $uid = $_SESSION['uid'];
        $page = isset($this->_requestParams['page']) && (int)$this->_requestParams['page'] >0 ? (int)$this->_requestParams['page'] : 1;
        $limit = isset($this->_requestParams['limit']) && (int)$this->_requestParams['limit'] >0 ? (int)$this->_requestParams['limit'] : $this->_numOfPage;

        $offset = ($page-1)*$limit;

        //$res = $this->message_model->getList(array('uid'=>$uid),$offset,$limit,'');

		$res = '这是消息列表';

        if($res !== FALSE){
            $this->response(200,'',$res);
        }else{
            $this->response(4004,'执行失败');
        }
    }

    /**
     *  修改消息
     */
    public function up()
    {

        $message_id = isset($this->_requestParams['message_id']) ? $this->_requestParams['message_id'] : null;
        $post_type = isset($this->_requestParams['post_type']) ? $this->_requestParams['post_type'] : null;

        if(empty($message_id)){
            $this->response(4002,'参数错误');
        }
        if(!empty($post_type) && $post_type == 'delete'){
            $this->delete($message_id);
        }

        $this->response(4002,'post类型不正确');
    }

    /**
     * 删除消息
     * @param $message_id
     */
    public function del(){
        $uid = $_SESSION['uid'];
        $msg_id = $this->_requestParams['msg_id'];
        if(empty($msg_id)){
            $this->response(4002,'参数错误1');
        }
        $msg = $this->message_model->getInfoById($msg_id);
        if(empty($msg) ||  $msg['uid'] != $uid){
            $this->response(4002,'参数错误2');
        }

        $res = $this->message_model->updateById($msg_id,array('is_del'=>1));
        if($res){
            $this->response(200);
        }else{
            $this->response(4004);//执行失败
        }
    }


    public function test(){
        $this->load->model('alioss_model');
        $res = $this->alioss_model->uploadFile("./head.jpg");
        echo 'res:';
        echo '<pre>';
        var_dump($res);
        echo '</pre>';
    }




}