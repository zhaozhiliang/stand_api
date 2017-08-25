<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/**
* restful请求规则定义
*
* get : 获取
* post : 创建
* put : 对已知资源进行完全替换
* delete : 删除
* patch : 对已知资源进行局部更新
*/
$route['aaa/(:num)']['get'] = 'welcome/getinfoaaa/$1';
$route['aaa']['get'] = 'welcome/getaaa';
$route['aaa']['post'] = 'welcome/postaaa';
$route['aaa/(:num)']['put'] = 'welcome/putaaa/$1';
$route['aaa']['delete'] = 'welcome/deleteaaa';
$route['aaa']['patch'] = 'welcome/patchaaa';


//登录和注册,退出
$route['login']['post'] = 'login/login'; //登录或注册
$route['logout']['get'] = 'logout/logout';
$route['validCode']['get'] = 'login/sendCode'; //发送登录验证码


//消息
$route['message']['get'] = 'message/messageList';
$route['message']['post'] = 'message/up';
$route['delMessage']['post'] = 'message/del';   //删除消息

$route['version']['get'] = 'version/getNew'; //获取最新版本信息




