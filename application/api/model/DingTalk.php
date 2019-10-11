<?php
namespace app\api\model;


class DingTalk
{
    public $redis = null;
    public $agentId = '282796815';
    public $corpId = 'ding7f6f146b7c5505bc35c2f4657eb6378f';
    public $appKey = 'dingmwqidsjyey6vjn0r';
    public $appSecr = 'oR3nfMtWr50BLIMfhlyUjnXAatiK4NskTkzcn3wLnSZU-NEQOQgtPjKpIs3Hnipf';
    public $accessToken = '';
    public $jsapiTicket = '';

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('localhost');
        $this->redis->auth('Lcz19860109');

        $this->getAccessToken();
    }

    /**
     * 获取token
     */
    public function getAccessToken()
    {
        $key = 'dd_access_token';
        $this->accessToken = $this->redis->get($key);
        if(true) {
            $url = "https://oapi.dingtalk.com/gettoken?appkey={$this->appKey}&appsecret={$this->appSecr}";
            $json = file_get_contents($url);
            $result = json_decode($json, true);

            if ($result['errcode'] == '0') {
                $this->accessToken = $result['access_token'];
                $this->redis->set($key, $this->accessToken);
                $this->redis->setex($key, 7000, $this->accessToken);
            } else {

            }
        }

        return $this->accessToken;
    }

    public function getTicket()
    {
        $key = 'dd_ticket';
        $this->jsapiTicket = $this->redis->get($key);
        if(true) {
            $url = "https://oapi.dingtalk.com/get_jsapi_ticket?access_token=".$this->accessToken;
            $json = file_get_contents($url);
            $result = json_decode($json, true);

            if ($result['errcode'] == '0') {
                $this->jsapiTicket = $result['ticket'];
                $this->redis->set($key, $this->jsapiTicket);
                $this->redis->setex($key, 7000, $this->jsapiTicket);
            } else {

            }
        }

        return $this->jsapiTicket;
    }

    public function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }

    public function isvConfig()
    {
        $ticket = $this->getTicket();
        $nonceStr = 'hongsiyun';
        $timeStamp = time();
        $url = 'http://h5.hongsizg.com/';
        // $url = $_SERVER['HTTP_REFERER'];
        $sign = $this->sign($ticket, $nonceStr, $timeStamp, $url);
        $config = [
            'agentId'   => $this->agentId,
            'corpId'    => $this->corpId,
            'timeStamp' => $timeStamp,
            'nonceStr'  => $nonceStr,
            'signature' => $sign
        ];

        return $config;
    }

    public function getUserInfo($code)
    {
        $url = "https://oapi.dingtalk.com/user/getuserinfo?access_token={$this->accessToken}&code={$code}";
        $json = file_get_contents($url);
        $result = json_decode($json, true);

        return $result;
    }

    public function getUser($userId)
    {
        $url = "https://oapi.dingtalk.com/user/get?access_token=ACCESS_TOKEN&userid={$userId}";
    }

    public function sendJobMessage($userIds, $message)
    {
        // $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token={$this->accessToken}";
        $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2";
        $data['access_token'] = $this->accessToken;
        $data['agent_id'] = $this->agentId;
        $data['userid_list'] = implode(',', $userIds);
        $data['msg'] = $message;
        // $result = \HttpRequest::postData($url, $data);
        $result = $this->curlPost($url, $data);
    }

    public function textMessage($content)
    {
        $data['msgtype'] = 'text';
        $data['text']['content'] =  $content;

        return json_encode($data);
    }

    public function linkMessage($title, $text, $link)
    {
        $data['msgtype'] = 'link';
        $data['link'] = [
            "messageUrl" => $link,
            "picUrl" => "@lALOACZwe2Rk",
            "title" => $title,
            "text" => $text
        ];

        return json_encode($data);
    }

    public function curlPost($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $rs = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        return $rs;
    }


    private function curPageURL()
    {
        $pageURL = 'http';

        if (array_key_exists('HTTPS',$_SERVER)&&$_SERVER["HTTPS"] == "on")
        {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
}