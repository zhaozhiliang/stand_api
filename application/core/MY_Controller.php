<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Controller extends CI_Controller
{
    protected $_requestParams = null;
    /**
     * 构造函数
     */
    public function __construct() {

        parent::__construct();  // 调用父类的构造函数


        //$this->load->config('api'); // 加载API配置文件
        $this->load->library('checkSign');  // 加载参数验证类库
        //$this->load->helper('common');  // 加载公共辅助函数

        $this->_init_params();

        //正式环境去掉 flag
        if(isset($this->_requestParams['flag']) && $this->_requestParams['flag'] == '1'){ //不验证签名
        }else{
            $this->checksign->validSign($this->_requestParams);
        }
        // 验证请求的签名
    }


    public function response($code, $msg = '', $data =  null) {
        $response_data = array('code' => $code);
        $response_data['msg'] = $msg;
        $data = self::changeNull($data);
        $response_data['data'] = (isset($data) && $data !== "") ? $data : new stdClass();
//        $response_data['data'] = $data;

        $response_str = json_encode($response_data);
        //$response_str = $this->changeNull($response_str);


        echo $response_str;
        exit;


    }



    /**
     * 把null 转变为 ''
     * @param $vars
     * @param null $from
     * @param string $to
     * @return array|string
     */
    public static function changeNull($vars,$from=null,$to='') {
        if (is_array($vars)) {
            $result = array();
            foreach ($vars as $key => $value) {
                $result[$key] = self::changeNull($value,$from,$to);
            }
        } else {
            $result = ($vars === null || strtolower($vars) == 'null') ? '' : $vars;
        }
        return $result;
    }

    private function _init_params(){
        $requestMethod = strtolower($this->input->server('REQUEST_METHOD'));
        switch ($requestMethod) {
            case 'get':
                $paramsData = $this->input->get();
                break;
            case 'post':
                $paramsData = $this->input->post();
                break;
            default:
                $paramsData = json_decode($this->input->raw_input_stream, true);
                break;
        }
//        var_dump($paramsData);
        $this->_requestParams = $this->security->xss_clean($paramsData);
    }

    /**
     *
     * 方法说明：解析接口返回的数据
     *
     * @author zhangxing
     * 2016年12月2日 上午11:16:57
     */
    public function deCode($json){
        return $res = json_decode($json,true);
    }

}

/**
 * Class Logined_Controller
 * 需要登录的接口都需要继承这个类
 */
class Needlogin_Controller extends Api_Controller{

    /**
     * Logined_Controller constructor.
     * 构造函数
     */
    public function __construct()
    {
        parent::__construct();

        $uid = $this->session->userdata('uid');

        if(empty($uid)){  //日志仅为调试，正式可去掉

            $this->response(4003,'该接口需要登录后才能访问');
        }

    }
}