<?php
define("TOKEN", "showking");//自己定义的token 就是个通信的私钥

require_once 'wxModel.php';

//=========================
////这里是正式公众号!
//$appId= 'wx0f57a95d244904a6' ;
//$appSecret = '90ef5c49bffe3750dfe5fb33caa5ae3b';

//====================
//测试使用配置
$appId= 'wx402a31a201a2c9b9' ;

$appSecret = 'd4624c36b6795d1d99dcf0547af5443d'; //测试用的
//====================

$wechatObj = new wechatCallbackapiTest( $appId, $appSecret );

$wechatObj->responseMsg();

$data  = <<<EOT
{
    "button":[
        {
        "name":"测试1",
        "sub_button":[
            {
            "type":"click",
            "name":"子菜单名1",
            "key":"name1"
            },
            {
            "type":"click",
            "name":"子菜单名2",
            "key":"name2"
            }
            ]
         },
        {
        "type":"view",
        "name":"aboutus",
        "url":"http://www.baidu.com"
        }, 
        {
        "name":"测试1",
        "sub_button":[
            {
            "type":"click",
            "name":"子菜单名1",
            "key":"name1"
            },
            {
            "type":"click",
            "name":"子菜单名2",
            "key":"name2"
            }
            ]
         }
    ]
}
EOT;

$wechatObj->createMenu( $data );
