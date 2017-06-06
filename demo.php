
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/2
 * Time: 15:25
 */
include 'wxModel.php';
include './vendor/autoload.php';

$appId= 'wxf344ebfe858e6669' ;

$appSecret = 'a600083b67235751dde0f1452d4beeca'; //测试用的

$wechatObj = new wechatCallbackapiTest( $appId, $appSecret );

//session_start();
//$_SESSION['access_token'] = null;
//$_SESSION['expire_time'] = null;
//$wechatObj->getAccessToken();
dump($wechatObj->getWeather('汉中'));