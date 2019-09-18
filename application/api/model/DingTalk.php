<?php
namespace app\api\model;


class DingTalk
{
    public $redis = null;
    public $agentId = '282796815';
    public $appKey = 'dingmwqidsjyey6vjn0r';
    public $appSecr = 'oR3nfMtWr50BLIMfhlyUjnXAatiK4NskTkzcn3wLnSZU-NEQOQgtPjKpIs3Hnipf';
    public $accessToken = '';

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
}