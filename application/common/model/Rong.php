<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2020/3/9
 * Time: 16:40
 */

namespace app\common\model;


class Rong
{
    protected $account = "202003091732163379647c04531dbe5e61";
    protected $authToken = "f409c63de00546b9aa4dcf4034a1c03d";
    protected $appId = "32953c8362ea43e2a56ac1105eb5a000";
    protected $appToken = "4fd38a7b787443ee97ce6bc696b83b17";
    protected $baseUrl = "https://wdapi.yuntongxin.vip";
    protected $softVersion = "20181221";

    public function getSign($datetime) {
        $sign = md5("{$this->authToken}:{$this->appId}:{$datetime}");
        $sign = strtoupper($sign);

        return $sign;
    }

    public function getAuth($datetime)
    {
        $auth = base64_encode($this->appId.":".$this->appToken.":".$datetime);

        return $auth;
    }

    public function createSeatAccount($mobile)
    {
        $url = "{$this->baseUrl}/{$this->softVersion}/rest/CreateSeatAccount/v1";
        $data = [
            'appId' => $this->appId,
            'bindNumber' => $mobile
        ];
        $params = json_encode($data);

        return $this->post($url, $params);
    }

    public function call($caller, $callee)
    {
        $url = "{$this->baseUrl}/{$this->softVersion}/rest/click/call/event/v1";
        $data = [
            'AccountSid'    => $this->authToken,
            'Appid'         => $this->appId,
            'Caller'        => $caller,
            'Callee'        => $callee
        ];

        $params = json_encode($data);

        return $this->post($url, $params);
    }

    public function post($url, $data)
    {


        $datetime = date('YmdHis');

        $sign = $this->getSign($datetime);
        $auth = $this->getAuth($datetime);

        $url = $url.'?sig='.$sign;
        echo "<pre>";
        echo $url;
        $header = [
            'Content-type:application/json;charset=utf-8',
            'Accept:application/json',
            'Authorization:'.$auth
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $msg = curl_exec($ch);
        $request = curl_getinfo($ch);
        $error = curl_error($ch);
        echo "<pre>";
        print_r($request);
        // var_dump($error);
        curl_close($ch);

        $result = json_decode($msg, 1);
        return $result;
    }
}