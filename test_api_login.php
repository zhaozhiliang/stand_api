<?php
$GLOBALS['config']= array(
    'sk'=>'ca5e7816d16c1eda23db4xxx'
);
$host = 'www.api.cn';
//$host = 'http://api.test.yi-play.com';


//restful 方式请求

$ret = request_api_post('/login',array(
    'mobile'=>'13146105128',//'17710264320',   //婷婷
    'base_timestamp'=>time(),
    'base_type'=>'ios',
    'base_version'=>'v00',
	'code'=>'888888',//'118585'
	'meid'=>'5jsx2qslmm7xyx',
	'mobile_type'=>'xiao.mi'
	)
);


/*
$ret = request_api_post('/login',array(
    'mobile'=>'13146105128',
    'base_timestamp'=>time(),
    'base_type'=>'ios',
	'code'=>'888888',//'118585'
	'meid'=>'5jsx2qslmm7xyx',
	'mobile_type'=>'xiao.mi'
	)
);
*/


//$ret = request_api('/app/applist','');
//$ret = request_api('/login/login','');


function request_api_post($url,$params=''){
	global $host;
    $request_url=  $host.$url;
    //$request_url='z.sale.com'.$url;  //注意不要加斜杠

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$request_url);

    if($params){
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));

    }
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    //签名header

    if(!empty($params)){
        sort($params,SORT_STRING);
        $post = implode('',$params);
    }else{
        $post = '';
    }


	
    $headers=array(
        'sign:'.md5($GLOBALS['config']['sk'].$post)
    );


	
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
    curl_setopt($ch,CURLOPT_TIMEOUT,3);

    parse_url($url);

    $txt = curl_exec($ch);
	echo '---------<hr/>';
    var_dump($txt);
	echo '---------<hr/>';

    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);

    $json = json_decode($txt,true);

    if(!$json){

        exit('接口返回格式错误');

    }

    if(curl_errno($ch)){

        exit(curl_error($ch));

    }


}


function request_api_get($url,$params=''){
	global $host;
    $request_url=  $host.$url;
    //$request_url='z.sale.com'.$url;  //注意不要加斜杠

    $ch = curl_init();
	
	if(!empty($params)){
		$request_url .= '?'.http_build_query($params);
	}
    curl_setopt($ch,CURLOPT_URL,$request_url);

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    //签名header
	if(!empty($params)){
        sort($params,SORT_STRING);
        $post = implode('',$params);
    }else{
        $post = '';
    }
    $headers=array(
        'sign:'.md5($request_url.$GLOBALS['config']['ak'].$GLOBALS['config']['sk'].$post),
        'ak:'.$GLOBALS['config']['ak']
    );


    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
    curl_setopt($ch,CURLOPT_TIMEOUT,3);

    parse_url($url);

    $txt = curl_exec($ch);
	echo '---------';
    var_dump($txt);
	echo '++++++++++++++';

    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    var_dump($errno);
    var_dump($error);
    var_dump($info);
	echo  '----------';

    $json = json_decode($txt,true);

    if(!$json){

        exit('接口返回格式错误');

    }

    if(curl_errno($ch)){

        exit(curl_error($ch));

    }


}

