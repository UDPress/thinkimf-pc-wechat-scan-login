<?php
/**
 * Created by PhpStorm.
 * User: chenjianhua
 * Date: 2018/8/5
 * Time: 12:57
 */

namespace plugins\wxlogin;

class WxLoginClass
{
    private $appId = '';
    private $appSecret = '';
    private $access_token = '';

    public function __construct($appid, $appsecret)
    {
        $this->appId = $appid;
        $this->appSecret = $appsecret;
        if (empty($this->appSecret) || empty($this->appId)) {
            throw new \Exception('请配置您申请的appid和appsecret');
        }
        //扫码登录不需要该Access Token, 语义理解需要
        //本地写入
        $res = @file_get_contents('access_token.json');
        if(!$res){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appSecret;
            $res = $this->https_request($url);
            //var_dump($res);exit;
            $result = json_decode($res, true);
            $this->access_token = $result["access_token"];
            $this->expires_time = time();
            file_put_contents('access_token.json', '{"access_token": "' . $this->access_token . '", "expires_time": ' . $this->expires_time . '}');
        }
        $result = json_decode($res, true);
        $this->expires_time = $result["expires_time"];
        $this->access_token = $result["access_token"];

        if (time() > ($this->expires_time + 3600)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appSecret;
            $res = $this->https_request($url);
            $result = json_decode($res, true);
            $this->access_token = $result["access_token"];
            $this->expires_time = time();
            file_put_contents('access_token.json', '{"access_token": "' . $this->access_token . '", "expires_time": ' . $this->expires_time . '}');
        }

    }
    //生成扫码登录的URL
    public function qrconnect($redirect_url, $scope, $state = NULL)
    {
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $this->appid . "&redirect_uri=" . urlencode($redirect_url) . "&response_type=code&scope=" . $scope . "&state=" . $state . "#wechat_redirect";
        return $url;
    }

    //生成OAuth2的Access Token
    public function oauth2_access_token($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appId . "&secret=" . $this->appSecret . "&code=" . $code . "&grant_type=authorization_code";
        $res = $this->https_request($url);
        //var_dump($res);exit;
        return json_decode($res, true);
    }

    //获取用户基本信息（OAuth2 授权的 Access Token 获取 未关注用户，Access Token为临时获取）
    public function oauth2_get_user_info($access_token,$openid)
    {
        $this->access_token = $access_token;
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $this->access_token . "&openid=" . $openid . "&lang=zh_CN";
        $res = $this->https_request($url);
        //var_dump($res);exit;
        return json_decode($res, true);
    }

    public function https_request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $out = curl_exec($ch);
        curl_close($ch);
        return $out;
    }
}
