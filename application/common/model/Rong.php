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
    protected $hinits = [
        1000    => "Header 参数有误",
        1001    => "Sig 签名有误",
        1002    => "应用余额不足",
        1003    => "赠送金额不足",
        1004    => "IP 未被允许",
        1005    => "Auth 签名有误",
        1006    => "Sig 有误",
        1007    => "账号有误，主叫号码是否绑定",
        2001    => "请求参数有误，参数大小写、格式",
        2002    => "被叫号码限制外呼",
        2005    => "外显号码有误，确保已存在旧号码",
        2006    => "号码已被使用，解绑后重复绑定",
        2007    => "账号存在异常，余额等不足导致",
        2008    => "号码未被绑定，无需解绑",
        2009    => "主叫号码未被绑定到坐席不可用",
        2010    => "新绑定号码已存在",
        2011    => "号码格式有误",
        2012    => "坐席数不足，查看坐席签约数是否已达最大值",
        2013    => "不存在该录音文件",
        2014    => "被叫号码限制外呼，存在较高风险",
        2015    => "不存在该绑定记录",
        2016    => "平台达到系统流控，请一分钟后再试",
        2017    => "应用余额不足扣除月租金额",
        2018    => "外呼临时异常请一分钟后再试",
        2019    => "线路调整中，暂停使用",
        2020    => "禁止外呼，请绑定移动卡外呼",
        2021    => "主被叫号码相同，禁止外呼",
        2022    => "接口暂停使用",
        2023    => "接口无使用权限",
        2024    => "号码等待验证通过",
        2025    => "座席绑定号码禁止外呼，更换座席绑定号码",
        3000    => "短时间内调用接口频次限制，请稍等几秒重试",
        3001    => "获取通话录音地址异常",
        3003    => "未找到话单",
        3004    => "获取话单异常",
        3005    => "未找到坐席相关信息",
        3006    => "禁止外呼时间段",
        4002    => "请求异常，参数和调用地址不匹配导致格式错误，检查 调用地址和参数是否一致"
    ];

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

    public function getRecordList($startTime, $endTime, $maxId)
    {
        $url = "{$this->baseUrl}/{$this->softVersion}/rest/click/call/recordlist/v1";
        $data = [
             'BillList' => [
                'Appid' => $this->appId,
                'StartTime' => $startTime,
                'EndTime'   => $endTime,
                'MaxId' => $maxId
            ]
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
        // echo "<pre>";
        // echo $url;
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
        // echo "<pre>";
        // print_r($request);
        // var_dump($error);
        curl_close($ch);

        $result = json_decode($msg, 1);

        $result['header'] = $header;
        $result['url'] = $url;
        $result['data'] = $data;

        if($result['Flag'] != 1 && $result['Flag'] != 200) {
            $flag = isset($result['Flag']) ? $result['Flag'] : $result['statuscode'];
            if(isset($this->hinits[$flag])) {
                $result['message'] = $this->hinits[$flag];
            } else {
                $result['message'] = "未知异常,异常号是：{$flag}，请联系管理员";
            }
        } else {
            $result['message'] = '接口请求成功';
        }

        return $result;
    }
}