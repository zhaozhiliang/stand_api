<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * app接口的签名认证
 * Class CheckSign
 */
class CheckSign {
	
	private $_applist = array();
	private $_CI;
    private $_timeLimit = 10; //超时设置 10s钟
	
	function __construct() {
		$this->_CI =& get_instance();

		$this->_CI->load->config('appkey', TRUE);
		$this->_applist = $this->_CI->config->item('applist', 'appkey');
	}

	public function validSign($params) {
		//应用列表，实际情况会存储到数据库,或配置中

		if(empty($_SERVER['HTTP_SIGN'])){
			exit(json_encode(array('code'=>4001,'msg'=>'auth error1')));
		}


		//获得client传递的签名
		$sign = $_SERVER['HTTP_SIGN'];
		//获得client传递的AK


        //验证时间戳
        if(empty($params['base_timestamp'])){
            exit(json_encode(array('code'=>4002,'msg'=>'基础参数错误1',array('file'=>$_FILES,'post'=>$this->$params))));
        }

        if(empty($params['base_type']) || !in_array($params['base_type'],array('android','h5','ios'))){
            exit(json_encode(array('code'=>4002,'msg'=>'基础参数错误2',array('file'=>$_FILES,'post'=>$this->$params))));
        }
		
		if(empty($params['base_version'])){
            exit(json_encode(array('code'=>4002,'msg'=>'基础参数错误3',array('file'=>$_FILES,'post'=>$this->$params))));
        }

        $base_type = $params['base_type'];

//        if(!empty($_POST['base_timestamp']) && (time() - $_POST['base_timestamp'] > $this->_timeLimit)){
//            exit(json_encode(array('code'=>'402','msg'=>'请求超时')));
//        }

		//获取post参数
		$post = $params;
		if(!empty($post)){
			sort($post,SORT_STRING);
			$post = implode('',$post);
		}else{
			$post = '';
		}

		//根据AK获得SK
		$sk= isset($this->_applist[$params['base_version']][$base_type] ) ? $this->_applist[$params['base_version']][$base_type] : $this->_applist['default'][$base_type];
		//验证签名
		//var_dump($post);
//		var_dump(md5($_SERVER['REQUEST_URI'].$ak.$sk.$post));
//		var_dump($sign);
		//var_dump($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); die;
		if(md5($sk.$post)!= $sign){
			exit(json_encode(array('code'=>4001,'msg'=>'auth error3')));
		}
	}


}

/* End of file Auth.php */
/* Location: ./application/libraries/Auth.php */
