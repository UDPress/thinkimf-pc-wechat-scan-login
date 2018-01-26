<?php
// +----------------------------------------------------------------------
// | QQLogin [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 Tangchao All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tangchao <79300975@qq.com>
// +----------------------------------------------------------------------
namespace plugins\wxlogin\controller;

use cmf\controller\PluginBaseController;
use plugins\wxlogin\WxLoginClass;
use think\Db;
use app\user\model\UserModel;
use think\Exception;

class IndexController extends PluginBaseController
{
    function index()
    {
        $this->config = $this->getPlugin('wxlogin')->getConfig();
        $appid = $this->config['APP_ID'];
        $appsecret = $this->config['APP_SECRET'];
        $redirect_uri = cmf_plugin_url('wxlogin://Index/index', array(), true);
        if (!isset($_GET['code'])) {
            //$appid = 'wxa2f1592c6d5befe0';
            $state = md5(uniqid(rand(), true));
            $redirect_url = urlencode($redirect_uri);
            $scope = 'snsapi_login';
            $url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $appid;
            $url .= "&redirect_uri={$redirect_url}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";
            header('Location:' . $url);
        } else {
            $code = $_GET['code'];
            if (empty($code)) {
                echo 'Error Get Authorization Code';
                exit();
            }
            $wxlogin = new WxLoginClass($appid, $appsecret);
            $oauth2_info = $wxlogin->oauth2_access_token($_GET["code"]);
            $userinfo = $wxlogin->oauth2_get_user_info($oauth2_info['access_token'], $oauth2_info['openid']);
            //var_dump($userinfo);
            $openid = $oauth2_info['openid'];
            if (empty($openid)) {
                echo 'Error Get Open ID';
                exit();
            }
            if ($openid == "") {
                echo "登录失败";
                die();
            }
            $guid = GetGuid();
            $userinfo['login'] = "ud_" . $guid;
            $userinfo['user_pass'] = $guid;
            $userinfo['openid'] = $openid;
            $log = registerOauth($userinfo);
        }
        return $this->fetch("/index");
    }
}

function GetGuid()
{
    $s = str_replace('.', '', trim(uniqid('yt', true), 'yt'));
    return $s;
}

function registerOauth($user)
{
    $openid = $user['openid'];
    $result = Db::name("third_party_user")->where('openid', $openid)->find();
    if (empty($result)) {
        $data = [
            'user_login' => $user['login'],
            'user_email' => '',
            'mobile' => '',
            'user_nickname' => $user['nickname'],
            'avatar' => $user['headimgurl'],
            'user_pass' => cmf_password($user['user_pass']),
            'last_login_ip' => get_client_ip(0, true),
            'create_time' => time(),
            'last_login_time' => time(),
            'user_status' => 1,
            "user_type" => 2,//会员
        ];
        $userId = Db::name("user")->insertGetId($data);
        $data = Db::name("user")->where('id', $userId)->find();
        $userdata = [
            'user_id' => $userId,
            'openid' => $openid,
            'union_id' => "wx",
            'create_time' => time(),
            'last_login_time' => time(),
        ];
        $partyuserId = Db::name("third_party_user")->insertGetId($userdata);
        cmf_update_current_user($data);
        return 0;
    } else {
        $data = Db::name("user")->where('id', $result['user_id'])->find();
        cmf_update_current_user($data);
        return 0;
    }
    return 1;
}