<?php

namespace app\mini\controller;

use think\controller;

class Weixinapi extends controller {

    //appid
    private static $appid = 'wx07c5256fbb3a1db6';
    //appsecret
    private static $appsecret = '0433c84fd7994cce46f3379d5488ad0d';

    //获取code后跳转的地址
    const authRedirectUri = '';

    /**
     * 获取微信用户code接口地址
     */
    const API_WEIXIN = "https://api.weixin.qq.com/sns/oauth2/access_token?";
    //登录地址
    const loginUrl = "";

    /**
     * 获取access_token
     *
     * access_token是公众号的全局唯一票据，公众号调用各接口时都需使用access_token。
     * 正常情况下access_token有效期为7200秒，重复获取将导致上次获取的access_token失效。
     * 由于获取access_token的api调用次数非常有限，建议开发者全局存储与更新access_token，频繁刷新access_token会导致api调用受限，影响自身业务。
     * 公众号可以使用AppID和AppSecret调用本接口来获取access_token。
     * AppID和AppSecret可在开发模式中获得（需要已经成为开发者，且帐号没有异常状态）。注意调用所有微信接口时均需使用https协议。
     * @return bool
     */
    public function getAccessToken() {

        $access_token = '';

        $hostdir = $_SERVER['DOCUMENT_ROOT'];
        if (!is_dir($hostdir . "/token/")) {
            mkdir($hostdir . "/token/", 0777, true);
        }
        $is_local = preg_match('/(127\.0\.0\.1)|(192.168\.8\.\d)/i', $_SERVER['SERVER_ADDR']);

        if (!$is_local) {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/token/access_token.json"))
                fopen($_SERVER['DOCUMENT_ROOT'] . "/token/access_token.json", "w");

            $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/token/access_token.json"));
            if ($data == null || $data->expire_time < time()) {

                $data = (object) array();

                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::$appid . "&secret=" . self::$appsecret;
                $res = file_get_contents($url);
                $result = json_decode($res, true);
                $access_token = $result['access_token'];

                if ($access_token) {

                    $data->expire_time = time() + 7000;
                    $data->access_token = $access_token;
                    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/token/access_token.json", "w");
                    fwrite($fp, json_encode($data));
                    fclose($fp);
                }
            } else {
                $access_token = $data->access_token;
            }
        }

        return $access_token;
    }

    /**
     * 生成被动响应消息模版,默认文本消息
     * 参数  是否必须  描述
     * ToUserName  是  接收方帐号（收到的OpenID）
     * FromUserName  是  开发者微信号
     * CreateTime  是  消息创建时间 （整型）
     * MsgType  是  text
     * Content  是  回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
     * @param $ToUserName
     * @param $FromUserName
     * @param $Content
     * @param $type  text/news/image
     * @return string
     */
    public function replyTextTemplate($ToUserName, $FromUserName, $Content, $type = "text") {
        if ($type == "text") {
            $xml = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
            return sprintf($xml, $ToUserName, $FromUserName, time(), $type, $Content);
        } elseif ($type == "image") {
            $xml = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Image>
            <MediaId><![CDATA[%s]]></MediaId>
            </Image>
            </xml>";
            return sprintf($xml, $ToUserName, $FromUserName, time(), $type, $Content);
        } elseif ($type == "news") {
            $count = count($Content);
            $newsTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>";

            $string = "";
            foreach ($Content as $k => $v) {
                $string .= sprintf($newsTpl, $v['title'], $v['description'], $v['picurl'], $v['url']);
            }
            $newsTpl = '<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <ArticleCount><![CDATA[%s]]></ArticleCount>
                            <Articles>' . $string . '</Articles>
						</xml>';

            return sprintf($newsTpl, $ToUserName, $FromUserName, time(), $type, $count);
        }
    }

    /**
     * post 数据到指定地址
     * @param $url      地址
     * @param $postdata 需要post的数据
     * @return mixed
     */
    public function sendData($url, $postdata) {
        $opts = array(
            'http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context = stream_context_create($opts);
        return json_decode(file_get_contents($url, false, $context), true);
    }

    //创建二维码ticket 
    public function getQrcodeCreate($scene_id = 0, $token) {

        $ret = array();
        if (!is_numeric($scene_id)) {
            return $ret;
        }
        $token = $token == '' ? $this->getAccessToken() : $token;
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s";
        $url = sprintf($url, $token);
        $data = array(
            'action_name' => 'QR_LIMIT_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_id' => $scene_id,
                ),
            ),
        );

        $postdata = json_encode($data);

        $res = $this->sendData($url, $postdata);

        return $res;
    }

    /**
     * @description 返回微信二维码地址
     * 
     */
    public function getQrcode($scene_id) {
        if (!$scene_id) {
            return '';
        }
        $token = $this->getAccessToken();

        //返回用于获取二维码的ticket
        $ticketData = $this->getQrcodeCreate($scene_id, $token);

        if (!isset($ticketData['ticket']) || !$ticketData['ticket']) {
            return false;
        }
        $ticketurl = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticketData['ticket']);
        return $ticketurl;
    }

    /**
     * 当用户主动发消息给公众号的时候（包括发送信息、点击自定义菜单click事件、订阅事件、扫描二维码事件、支付成功事件、用户维权），微信将会把消息数据推送给开发者，开发者在一段时间内（目前修改为48小时）可以调用客服消息接口，通过POST一个JSON数据包来发送消息给普通用户，在48小时内不限制发送次数。此接口主要用于客服等有人工消息处理环节的功能，方便开发者为用户提供更加优质的服务。
     *
     * 接口调用请求说明
     *
     * http请求方式: POST
     * https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN
     *
     * 发送文本消息
     *
     * 参数  是否必须  说明
     * access_token  是  调用接口凭证
     * touser  是  普通用户openid
     * msgtype  是  消息类型，text
     * content  是  文本消息内容
     *
     * 发送图文消息
     * 图文消息条数限制在10条以内，注意，如果图文数超过10，则将会无响应。
     *
     * {
     * "touser":"OPENID",
     * "msgtype":"news",
     * "news":{
     * "articles": [
     * {
     * "title":"Happy Day",
     * "description":"Is Really A Happy Day",
     * "url":"URL",
     * "picurl":"PIC_URL"
     * },
     * {
     * "title":"Happy Day",
     * "description":"Is Really A Happy Day",
     * "url":"URL",
     * "picurl":"PIC_URL"
     * }
     * ]
     * }
     * }
     * 参数  是否必须  说明
     * access_token  是  调用接口凭证
     * touser  是  普通用户openid
     * msgtype  是  消息类型，news
     * title  否  标题
     * description  否  描述
     * url  否  点击后跳转的链接
     * picurl  否  图文消息的图片链接，支持JPG、PNG格式，较好的效果为大图640*320，小图80*80
     * @param        $touser
     * @param        $content
     * @param string $type
     * @return bool|string
     * @license http://mp.weixin.qq.com/wiki/index.php?title=%E5%8F%91%E9%80%81%E5%AE%A2%E6%9C%8D%E6%B6%88%E6%81%AF#.E5.8F.91.E9.80.81.E5.9B.BE.E6.96.87.E6.B6.88.E6.81.AF
     */
    public function pushServerMsg($touser, $content, $type = 'text') {
        if (!$touser || !$content) {
            return false;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s";
        $data['touser'] = $touser;
        $data['msgtype'] = $type;
        switch ($type) {
            case 'image':
                $data['image']['media_id'] = $content;
                break;
            case 'voice':
                $data['voice']['media_id'] = $content;
                break;
            case 'news'://发送图文消息（点击跳转到外链） 图文消息条数限制在8条以内，注意，如果图文数超过8，则将会无响应。 
                $data['news']['articles'] = $content;
                break;
            case 'mpnews'://发送图文消息（点击跳转到图文消息页面） 图文消息条数限制在8条以内，注意，如果图文数超过8，则将会无响应。 
                $data['mpnews'] = $content;
                break;
            default:
                $data['text']['content'] = str_replace("<br>", "\r\n", $content);
        }
        $url = sprintf($url, $this->getAccessToken());

        $postdata = json_encode($data, JSON_UNESCAPED_UNICODE);

        $res = $this->sendData($url, $postdata);

        return $res;
    }

    /**
     * 在确保微信公众账号拥有授权作用域（scope参数）的权限的前提下（服务号获得高级接口后，默认带有scope参数中的snsapi_base和snsapi_userinfo），引导关注者打开如下页面：
     *
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
     * 若提示“该链接无法访问”，请检查参数是否填写错误，是否拥有scope参数对应的授权作用域权限。
     * 参考链接(请在微信客户端中打开此链接体验)
     * Scope为snsapi_base
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx520c15f417810387&redirect_uri=http%3A%2F%2Fchong.qq.com%2Fphp%2Findex.php%3Fd%3D%26c%3DwxAdapter%26m%3DmobileDeal%26showwxpaytitle%3D1%26vb2ctag%3D4_2030_5_1194_60&response_type=code&scope=snsapi_base&state=123#wechat_redirect
     * Scope为snsapi_userinfo
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxf0e81c3bee622d60&redirect_uri=http%3A%2F%2Fnba.bluewebgame.com%2Foauth_response.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
     * 参数说明
     *
     * 参数  是否必须  说明
     * appid  是  公众号的唯一标识
     * redirect_uri  是  授权后重定向的回调链接地址，请使用urlencode对链接进行处理
     * response_type  是  返回类型，请填写code
     * scope  是  应用授权作用域，snsapi_base （不弹出授权页面，直接跳转，只能获取用户openid），snsapi_userinfo （弹出授权页面，可通过openid拿到昵称、性别、所在地。并且，即使在未关注的情况下，只要用户授权，也能获取其信息）
     * state  否  重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值
     * #wechat_redirect  是  无论直接打开还是做页面302重定向时候，必须带此参数
     * 用户同意授权后
     *
     * 如果用户同意授权，页面将跳转至 redirect_uri/?code=CODE&state=STATE。若用户禁止授权，则重定向后不会带上code参数，仅会带上state参数redirect_uri?state=STATE
     *
     * code说明 ：
     * code作为换取access_token的票据，每次用户授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。
     * @param $scope
     * @param $state
     * @param $redirect_uri
     */
    public function getAuthCode($scope, $redirect_uri = '', $state = '', $skip = TRUE) {
        if (!$redirect_uri) {
            return false;
        }
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect";
        switch ($scope) {
            case 'snsapi_userinfo':
                $url = sprintf($url, self::$appid, urlencode($redirect_uri), 'snsapi_userinfo', $state);
                break;
            default:
                $url = sprintf($url, self::$appid, urlencode($redirect_uri), 'snsapi_base', $state);
        }
//        $this->addErrorLog(__METHOD__ . PHP_EOL . $url);
        if ($skip) {
            ob_start();
            header('Location: ' . $url);
            ob_end_flush();
            exit();
        } else {
            return $url;
        }
    }

    /**
     * 通过code换取网页授权access_token
     * 首先请注意，这里通过code换取的网页授权access_token,与基础支持中的access_token不同。公众号可通过下述接口来获取网页授权access_token。如果网页授权的作用域为snsapi_base，则本步骤中获取到网页授权access_token的同时，也获取到了openid，snsapi_base式的网页授权流程即到此为止。
     *
     * 请求方法
     *
     * 获取code后，请求以下链接获取access_token：
     * https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code
     * 参数说明
     *
     * 参数  是否必须  说明
     * appid  是  公众号的唯一标识
     * secret  是  公众号的appsecret
     * code  是  填写第一步获取的code参数
     * grant_type  是  填写为authorization_code
     * @param $code
     * @return bool|mixed
     * 正确时返回的JSON数据包如下：
     *
     * {
     * "access_token":"ACCESS_TOKEN",
     * "expires_in":7200,
     * "refresh_token":"REFRESH_TOKEN",
     * "openid":"OPENID",
     * "scope":"SCOPE"
     * }
     * 参数  描述
     * access_token  网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
     * expires_in  access_token接口调用凭证超时时间，单位（秒）
     * refresh_token  用户刷新access_token
     * openid  用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
     * scope  用户授权的作用域，使用逗号（,）分隔
     */
    public function getAuthAccessToken($code) {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code";
        $jsonData = file_get_contents(sprintf($url, self::$appid, self::$appsecret, $code));
        if (empty($jsonData) || !($data = json_decode($jsonData, true)) && json_last_error() != JSON_ERROR_NONE) {
            //$this->addErrorLog(__METHOD__ . PHP_EOL . json_encode($data));
            return false;
        }

        return $data;
    }

    /**
     * 第三步：刷新access_token（如果需要）
     * 由于access_token拥有较短的有效期，当access_token超时后，可以使用refresh_token进行刷新，refresh_token拥有较长的有效期（7天、30天、60天、90天），当refresh_token失效的后，需要用户重新授权。
     *
     * 请求方法
     *
     * 获取第二步的refresh_token后，请求以下链接获取access_token：
     * https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN
     * 参数  是否必须  说明
     * appid  是  公众号的唯一标识
     * grant_type  是  填写为refresh_token
     * refresh_token  是  填写通过access_token获取到的refresh_token参数
     * @param $refresh_token
     * @return bool|mixed|string
     * 正确时返回的JSON数据包如下：
     *
     * {
     * "access_token":"ACCESS_TOKEN",
     * "expires_in":7200,
     * "refresh_token":"REFRESH_TOKEN",
     * "openid":"OPENID",
     * "scope":"SCOPE"
     * }
     * 参数  描述
     * access_token  网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
     * expires_in  access_token接口调用凭证超时时间，单位（秒）
     * refresh_token  用户刷新access_token
     * openid  用户唯一标识
     * scope  用户授权的作用域，使用逗号（,）分隔
     * 错误时微信会返回JSON数据包如下（示例为Code无效错误）:
     *
     * {"errcode":40029,"errmsg":"invalid code"}
     */
    public function getAuthRefreshAccessToken($refresh_token) {
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s";
        $jsonData = file_get_contents(sprintf($url, self::$appid, $refresh_token));
        if (empty($data) || !($data = json_decode($jsonData, true)) && json_last_error() != JSON_ERROR_NONE) {
            //$this->addErrorLog(__METHOD__ . PHP_EOL . json_encode($data));
            return false;
        }
        return $data;
    }

    //获取用户基本信息（包括UnionID机制）
    public function getUserInfo($openid) {
        if (!$openid) {
            return false;
        }
        $data = array();

        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $this->getAccessToken() . '&openid=' . $openid . '&lang=zh_CN';
        return $this->sendData($url, json_encode($data));
    }

    /**
     * 目前自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。请注意，创建自定义菜单后，由于微信客户端缓存，需要24小时微信客户端才会展现出来。建议测试时可以尝试取消关注公众账号后再次关注，则可以看到创建后的效果。
     * 目前自定义菜单接口可实现两种类型按钮，如下：
     *
     * click：
     * 用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event    的结构给开发者（参考消息接口指南），并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；
     * view：
     * 用户点击view类型按钮后，微信客户端将会打开开发者在按钮中填写的url值    （即网页链接），达到打开网页的目的，建议与网页授权获取用户基本信息接口结合，获得用户的登入个人信息。
     * 生成用户菜单
     *  {
     * "button":[
     * {
     * "type":"click",
     * "name":"今日歌曲",
     * "key":"V1001_TODAY_MUSIC"
     * },
     * {
     * "type":"click",
     * "name":"歌手简介",
     * "key":"V1001_TODAY_SINGER"
     * },
     * {
     * "name":"菜单",
     * "sub_button":[
     * {
     * "type":"view",
     * "name":"搜索",
     * "url":"http://www.soso.com/"
     * },
     * {
     * "type":"view",
     * "name":"视频",
     * "url":"http://v.qq.com/"
     * },
     * }]
     * }
     * 参数说明
     *
     * 参数  是否必须  说明
     * button  是  一级菜单数组，个数应为1~3个
     * sub_button  否  二级菜单数组，个数应为1~5个
     * type  是  菜单的响应动作类型，目前有click、view两种类型
     * name  是  菜单标题，不超过16个字节，子菜单不超过40个字节
     * key  click类型必须  菜单KEY值，用于消息接口推送，不超过128字节
     * url  view类型必须  网页链接，用户点击菜单可打开链接，不超过256字节
     *
     * 返回结果
     *
     * 正确时的返回JSON数据包如下：
     *
     * {"errcode":0,"errmsg":"ok"}
     * 错误时的返回JSON数据包如下（示例为无效菜单名长度）：
     *
     * {"errcode":40018,"errmsg":"invalid button name size"}
     * @param $data
     */
    function menuManage($action = 'get', $data = null) {
        switch ($action) {
            case 'delete':
                $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s";
                break;
            case 'save':
                if (isset($data['menu']['button'])) {
                    $data = $data['menu'];
                } elseif (!isset($data['button'])) {
                    $data = array('button' => $data);
                }
                $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s";
                break;
            case 'menu_info':
                $url = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=%s";
                break;
            default:
                $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=%s";
                break;
        }
        $url = sprintf($url, $this->getAccessToken());
        $postdata = $this->chinaCharTransition(json_encode($data));
//        $postdata = json_encode($data);
        return $this->sendData($url, $postdata);
    }

    /**
     *
     * @param $data
     * @return mixed
     */
    public function chinaCharTransition($data) {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'self::replace_unicode_escape_sequence', $data);
//        preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function('$matches', 'return iconv("UCS-2BE","UTF-8",pack("H*", $matches[1]));'), $str); 
    }

    /**
     * UTF-16BE 转utf-8
     * @param $match
     * @return string
     */
    function replace_unicode_escape_sequence($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
    
    /*
     * 获取在线客服基本信息
     */
    public function getOnlineKfList() {
        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token=' . $this->getAccessToken();
        return json_decode(file_get_contents($url),true);
    }
    /*
     * 获取客服基本信息
     */
    public function getKfList() {
        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=' . $this->getAccessToken();
        return json_decode(file_get_contents($url),true);
    }
    /*
     * 获取客户会话状态
     */
    public function getKfSession($openid){
        $url = 'https://api.weixin.qq.com/customservice/kfsession/getsession?access_token='.$this->getAccessToken().'&openid='.$openid;
        return json_decode(file_get_contents($url),true);
    }
    /*
     * 创建会话
     * POST数据示例如下：
        {
           "kf_account" : "test1@test",
           "openid" : "OPENID"
        }
     */
    public function createKfSession($data){
        $url = 'https://api.weixin.qq.com/customservice/kfsession/create?access_token='.$this->getAccessToken();
        $postdata = json_encode($data);
        return $this->sendData($url, $postdata);
    }
    /*
     * 关闭会话
     * POST数据示例如下：
        {
           "kf_account" : "test1@test",
           "openid" : "OPENID"
        }
     */
    public function closeKfSession($data){
        $url = 'https://api.weixin.qq.com/customservice/kfsession/close?access_token='.$this->getAccessToken();
        $postdata = json_encode($data);
        return $this->sendData($url, $postdata);
    }

}
