<?php
header("Content-type: text/html; charset=utf-8");
ini_set('display_errors',1);
error_reporting(E_ALL);
$GLOBALS['config']= array(
    'sk'=>'ca5e7816d16c1eda23db4xxx'
);

$host = 'www.api.cn';

$cookie = 'yiplay_session='.'e2a61af269633c3f743d5eac0220aa721b556d0f';

/*
$ret = request_api_post('/willFriend',array(
	'base_type'=>'android',
	'token'=>'3b55096541a4ddc46373c0f499f8ed5f6f66f71f',//'peomp50g4au72lgbj8mqt2tmd3' --婷婷；；；；1s010t949btuggcircba08hau7--捏捏
	'base_timestamp'=>'1488373794944',
	'mobile_str'=>''
	)
);
*/



/*
$ret = request_api_get('/message',array(
    'base_timestamp'=>'1488373794944',
    'base_type'=>'ios',
    'base_version'=>'v100'
	)
);
*/



$ret = request_api_get('/logout',array(
        'base_timestamp'=>'1488373794944',
        'base_type'=>'ios',
        'base_version'=>'v100'
    )
);



function request_api_post($url,$params=''){
	global $host;
	global $cookie;
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
	curl_setopt($ch, CURLOPT_COOKIE,$cookie);

    parse_url($url);

    $txt = curl_exec($ch);
    var_dump($txt);
	echo '<hr/>++++++++++++++';

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


function request_api_get($url,$params=''){
	global $host;
	global $cookie;
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
        'sign:'.md5($GLOBALS['config']['sk'].$post)
    );
	

    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
    curl_setopt($ch,CURLOPT_TIMEOUT,3);
	curl_setopt($ch, CURLOPT_COOKIE,$cookie);

    parse_url($url);

    $txt = curl_exec($ch);
    #var_dump($txt);
    echo $txt;
	echo '<hr/><pre>++++++++++++++';

    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $info = curl_getinfo($ch);
    var_dump($errno);
    var_dump($error);
    var_dump($info);
	echo  '</pre>----------';

    $json = json_decode($txt,true);

    if(!$json){

        exit('接口返回格式错误');

    }

    if(curl_errno($ch)){

        exit(curl_error($ch));

    }


}

