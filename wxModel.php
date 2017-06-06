<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/3
 * Time: 20:29
 */
include './vendor/autoload.php';


class wechatCallbackapiTest
{

    //初始化
    public function __construct( $appId, $appSecret )
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

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
        file_put_contents( './get.txt', "================".date('Y-m-d H:i:s', time()).PHP_EOL.$postStr.PHP_EOL, FILE_APPEND );

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
                if(substr($keyWord, 0, 6) == "天气") {

                        $city = substr($keyWord, 6, strlen($keyWord));
                        $str = json_decode($this->getWeather($city), 1);
                        $str = $str['result']['today'];
                        $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                <FuncFlag>0</FuncFlag>
                                </xml>";
                        if ($str == null) {
                            $content = "请在天气+城市名";
                        } else {
                            $content = "====天气预报====\r\n";
                            $content .= "城市:" . $city . "\r\n";
                            $content .= "当天温度:" . $str['temperature'] . ";\r\n";
                            $content .= "当天天气:" . $str['weather'] . ";\r\n";
                            $content .= "当天风力:" . $str['wind'] . ";\r\n";
                            $content .= "当天:" . $str['week'] . ";\r\n";
                            $content .= "建议着装:" . $str['dressing_advice'] . ";\r\n";
                        }

                        $msgType = "text";

                        $time = time();

                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);

                        echo $resultStr;

                        exit;
                    }
                    elseif ($keyWord == "名字")
                    {
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
    //$data 自定义的菜单数据
    //可以传入json格式也可以是数组格式
    public function createMenu( $data )
    {
        //首先获取Access_token
        $token = $this->getAccessToken();

        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token;

        if( is_array( $data ) ){
            $data = json_encode( $data );
        }

        $res = $this->toCurl( $url, $data );
//        dump($res);
    }


    /*
     * curl
     *
     * $url 为必填  curl提交的url地址
     * $data 默认为空,data不为空则为post方式
     *
     * $result 返回值  返回一个数组
     * */
    public function toCurl( $url,$data =  null )
    {
        $curl = curl_init(); //初始化 curl
        curl_setopt( $curl, CURLOPT_URL, $url ); //设置提交的url
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );  //设置curl_exec的结果返回
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER,false );
        if( $data ){
            //data有数据,就改为post
            curl_setopt($curl, CURLOPT_POST, true );
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec( $curl ); //执行curl
        curl_close( $curl );//关闭curl会话
        //把json数据转为arr
        $result = json_decode( $response, true );
        return $result;
    }

    //获取Token;
    public function getAccessToken(){
        //判断AccessToken文件是否存在
        //不存在就创建
        if( !file_exists( './AccessToken.txt' ) ){
            touch('./AccessToken.txt');
        }
        //获取AccessToken文件中的内容
        $tokenJson = @file_get_contents('./AccessToken.txt');
        //文件中有内容
        if( $tokenJson ){
            //获取文件中存储的时间戳,计算如果超过7000秒,就重新获取Token
            $tokenArr = json_decode( $tokenJson, true );
            $cacheToken = $tokenArr[ 'token' ];
            $expireTime = $tokenArr['time'];
            $diff = time()-$expireTime;
            //相差时间小于7000秒就从文件读取
            if( $diff < 7000  ){
                return $cacheToken;
            }else{
                //超过7000秒,就再次从微信服务器获取
                $token =  $this->_getToken();
                return $token;
            }
        }else{
            //文件中没有内容 就直接获取
           $token = $this->_getToken();
            return $token;
        }
    }

    //获取token的辅助方法
    //获取accecc_token,并写入文件
    private function _getToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appId."&secret=".$this->appSecret;
        $res = $this->toCurl( $url );
        //把获取到的json格式的数据转为arr,然后提取token
        $access_token = $res['access_token'];
        //把token值保存到文件中
        $arr = [ 'token'=> $access_token, 'time'=> time() ];
        $json = json_encode( $arr );
        file_put_contents( './AccessToken.txt', $json);
        return $access_token;
    }

    public function getWeather($city)
    {
        $appkey = "3d92eb3623d5cc1ec6c85f596cc58054";
        $url = "http://v.juhe.cn/weather/index?format=2&cityname=".$city."&key=".$appkey;
        return $this->toCurl($url);
    }

}