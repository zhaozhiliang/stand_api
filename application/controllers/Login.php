<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Api_Controller {
    private $_codeTime = 15;  //验证码有效时间15分

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
    }

    /**
     * Index Page for this controller.
     */
    public function login()
    {
//        $this->load->model('user_model');
//        $this->load->model('login_log_model');

        $mobile = $this->_requestParams['mobile'];
        $code = $this->_requestParams['code'];
        $meid = $this->_requestParams['meid']; //手机唯一标示
        $mobile_type = $this->_requestParams['mobile_type']; //手机机型

        if(empty($mobile) || empty($code) || empty($meid)){
            $this->response(4002,'参数错误');
        }

        if(!preg_match('/^1\d{10}$/', $mobile)){
            $this->response(4002,'手机号格式错误');
        }

        //验证码验证，包括超级验证码，普通验证码
        if('13146105128' == $mobile && '888888' == $code){
            //验证通过
        }else{
            $this->response(4002,'验证码或手机号错误');
        }


        $this->load->library('session');
        $this->session->set_userdata(array(
            'uid'=>9,
            'name'=>'亮',
            'mobile'=>'13146105128',
            'avatar'=>'5.jpg',
            'sex'=>'1'
        ));

        //          session_start();
        $token = session_id();


        //单点处理
        // $this->one_dot($res['uid']);

        $this->response(200,'',array('session_id'=>$token,'is_first'=>0,'info'=>array(
            'uid'=>9,
            'name'=>'亮',
            'mobile'=>'13146105128',
            'avatar'=>'5.jpg',
            'sex'=>'1'
        ),'alert_list'=>array())); //msg_list todo

    }

    public function test(){
        $this->load->library('sms');

        $mobile = '13146105128';

        $sendCode = 112233;
        $returnCode = $this->sms->api_login_code($mobile, $sendCode);   //正式 打开 todo;
        var_dump($returnCode);
    }

    public  function validCode($mobile,$code){
        //查看是否有超级验证码
        $this->load->model('power_code_model');
        $power_res = $this->power_code_model->getLast();

        $errno = 0;  //错误号，0无错误
        $power_sign = 0;   //超级验证，默认不通过
        if(!empty($power_res)){
            if($power_res['start_time'] + $power_res['use_minute']* 60 >time() && time() > $power_res['start_time'] && $power_res['status'] == 0){
                if($power_res['code'] == $code){
                    //通过
                    $power_sign = 1; //超级验证通过
                }
            }
        }


        $normal_sign = 0; //普通验证，默认不通过
        //发送成功将验证码信息存入redis
        $this->load->driver('cache');
        $key = "login_code_".$mobile."_".ENVIRONMENT;

        $redis_code_json = $this->cache->redis->get($key); //
//        var_dump($_POST);
//        var_dump($redis_code_json);
//        die;

        //  todo; 暂时不验证
        if(empty($redis_code_json)){
            $errno = -1; //验证码不存在，在redis中
        }else{
            $redis_code = json_decode($redis_code_json,true);

            if($redis_code['code'] != $code){
                $errno = -2; //验证码不对，和redis比较
            }
            if( $redis_code['code'] == $code && time() - $redis_code['addtime'] > $this->_codeTime*60){
                $errno = -3; //验证码，失效了
            }

            if($redis_code['code'] == $code && time() - $redis_code['addtime'] < $this->_codeTime*60){
                $normal_sign = 1;
            }
        }

        $final = array();
        if($power_sign || $normal_sign){
            $final['status'] = true;
        }else{
            $final['status'] = false;
        }
        $final['type'] = 0; //wu
        if($power_sign == 1){
            $final['type'] = 1; //超级验证
        }
        if($normal_sign == 1){
            $final['type'] = 2; //普通验证
        }
        $final['errno'] = $errno;

        return $final;

    }



    /**
     * 单点处理
     */
    private function  one_dot($uid){
        //查看最后一个登陆信息（当前正登陆）
        $res_prev = $this->login_log_model->getPrev($uid);
        if(!empty($res_prev)){
            //极光 --todo;
            //比较上一个登陆手机，是否和当地手机一样，如果一样，不推送；如果不一样推送
        }

        return true;
    }

    /**
     * @param $arr
     * uid, add_time, meid,mobile_type
     */
    private function login_log($arr){

        return $this->login_log_model->insert($arr);
    }

    /**
     * 发送验证码
     */
    public function sendCode(){
        $this->load->library('sms');

        $mobile = $this->_requestParams['mobile'];

        if(empty($mobile) || !preg_match('/^1\d{10}$/', $mobile)){
            $this->response(4002,'手机号格式错误');
        }

        $sendCode = (string)rand(100000, 999999);
        $returnCode = $this->sms->api_login_code($mobile, $sendCode);   //正式 打开 todo;
//        echo '------';
//        var_dump($returnCode);
//        echo '------';

        //发送成功将验证码信息存入redis
        $this->load->driver('cache');
        $key = "login_code_".$mobile."_".ENVIRONMENT;
        $code_info = json_encode(array(
            'code'=>$sendCode,
            'addtime'=>time()
            ));

        $this->cache->redis->save($key,$code_info,60*15); //60分钟

        $this->response(200,'');

    }
}