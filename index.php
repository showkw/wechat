<?php
define("TOKEN", "showking");//自己定义的token 就是个通信的私钥
$wechatObj = new wechatCallbackapiTest();

if ($_GET['echostr'])
{
    $wechatObj->valid();
}
else
{
    $wechatObj->responseMsg();
    $wechatObj->getAccessToken();
}

class wechatCallbackapiTest
{

    public $appid= 'wx0f57a95d244904a6' ;
    public $appsecret = '90ef5c49bffe3750dfe5fb33caa5ae3b';
   /*
    * 接口配置信息
    *  */
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    /*
     * 微信发送消息,开发者服务器接受xml消息,进行业务处理**/
    public function responseMsg()
    {
        //接收消息XML
        $postStr = file_get_contents('php://input');
        file_put_contents( './get.txt', "================".data('Y-m-d H:i:s', time()).PHP_EOL.$postStr.PHP_EOL, FILE_APPEND );

        if (!empty($postStr)){

            libxml_disable_entity_loader(true);
           //接收微信服务起发送过来的xml数据
            //分为: 事件. 消息.按照MsgType分
            //把xml转为对象
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            $toUsername = $postObj->ToUserName;

            $fromUsername = $postObj->FromUserName;

            $msgType = trim($postObj->MsgType);

            if( $msgType == 'text' ) {

                $keyWord = $postObj->Content;
                //判断关键字回复消息

                if ($keyWord == "名字") {

                    $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                <FuncFlag>0</FuncFlag>
                                </xml>";

                    $msgType = "text";

                    $time = time();

                    $content = "煞笔";

                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);

                    echo $resultStr;

                    exit;
                }
                elseif( $keyWord == '图文' )
                {
                    $arr  = array(
                        array(
                            'url' => 'http://m.huanqiu.com/r/MV8wXzEwNzgxOTQ1XzEzOF8xNDk2Mzg0OTQw?tt_group_id=6426922363829469441',
                            'picUrl' => 'http://a1.huanqiu.cn/images/9a8a2b05c871c1fa0130483bd5597254.jpg',
                            'title' => '日媒:一名日本籍男子涉嫌违法行为5月在辽宁省被拘',
                            'description' => '日本NHK电视台6月2日报道称,一名60多岁的日本籍男子5月下旬在辽宁省被中国治安部门拘留。'
                        ),
                        array(
                            'url' => 'http://www.toutiao.com/a6411085805339312385/',
                            'picUrl' => 'http://p3.pstatp.com/large/24490003902fe3721f94',
                            'title' => '男子强奸杀害3名少女 今早被执行注射死刑',
                            'description' => '“流浪恶魔”江望兵流窜多省作案，杀害三少女被判处死刑。6月2日，江望兵在潜山县被执行了注射死刑。'
                        ),
                        array(
                            'url' => 'http://www.toutiao.com/a6426694681564135682/',
                            'picUrl' => 'http://p3.pstatp.com/large/242e0000e5840b675032',
                            'title' => '阿里顺丰互相拉黑 国家邮政局深夜发话了',
                            'description' => '针对菜鸟网络与顺丰速运互相关闭互通数据接口，国家邮政局1日深夜发声，对此事高度重视，及时与当事双方高层进行沟通，切实维护市场秩序和消费者合法权益。'
                        )
                    );
                    $textTpl = <<<EOT
                    <xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>
EOT;
                    $str = '';
                    foreach( $arr as $v )
                    {
                       $str .= "<item>";
                       $str .= "<Title><![CDATA[".$v['title']."]]></Title>";
                       $str .= "<Description><![CDATA[".$v['description']."]]></Description>";
                       $str .= "<PicUrl><![CDATA[". $v['picUrl'] ."]]></PicUrl>";
                       $str .= "<Url><![CDATA[".$v['url']."]]></Url>";
                       $str .= "</item>";
                    }
                    $str .= "</Articles></xml>";
                    $textTpl .= $str;
                    $nums = count($arr);
                    $time = time();
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $nums);
                    echo $resultStr;
                }

//            $msgType = $postObj->MsgType;
                //要发送的xml消息模板:文本消息
            }
            if( $msgType == 'event' ){
                //获取事件推送消息
                $event = $postObj->Event;
                //判断是什么事件
                if( $event == 'subscribe' ){
                    $eventKey = @$postObj->EventKey;
                    file_put_contents( './get.txt', "EventKey:".$eventKey.PHP_EOL, FILE_APPEND);
                    if( $eventKey == '' ){
                        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
                        $msgType = "text";
                        $time = time();
                        $content = "欢迎来到ShowKing公众号!";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);
                        echo $resultStr;
                    }else{}
                }
            }
        }else {
            echo '咋不说话呢';
            exit;
        }
    }

    /*
     * 检查开发者服务器接入是否正常
     * */
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token =TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }


    //创建自定义菜单
    public function createMenu()
    {
        //首先获取Access_token
        $token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token;

    }


    /*
     * curl类
     * $url 为必填  curl提交的url地址
     * $isPost 可选  POST方式提交时,可设置不为0的任何字符
     * $data   可选, POST方式提交有数据时填写
     * */
    public function toCurl( $url, $isPost = 0, $data = null )
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,0 );
        if( $isPost != 0 ){
            if( $data == null ){
                curl_setopt( $ch, CURLOPT_POST, 1);
            }else{
                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
            }
        }
        $res = curl_exec( $ch );
        curl_close( $ch );
        return $res;
    }



    public function getAccessToken(){
        session_start();
        $diff = time() - $_SESSION['expire_time'];
        if( $_SESSION['access_token'] && $diff < 7000 ){
            return $_SESSION['access_token'];
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $res = $this->toCurl( $url );
            $access_token = json_decode( $res, true)['access_token'];
            file_put_contents( './get.txt','TOKEN:'.$access_token.PHP_EOL, FILE_APPEND );
            return $access_token;
        }
    }

}
?>