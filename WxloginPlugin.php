<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Rifty <rifty@wapui.com>
// +----------------------------------------------------------------------
namespace plugins\wxlogin;//Demo插件英文名，改成你的插件英文就行了
use cmf\lib\Plugin;
use think\Db;
use think\Request;

//Demo插件英文名，改成你的插件英文就行了
class WxloginPlugin extends Plugin
{

    public $info = [
        'name' => 'Wxlogin',//改成你的插件英文就行了
        'title' => '微信登录',
        'description' => '微信登录API-2018',
        'status' => 0,
        'author' => '陈建华 Dyoung Master',
        'version' => 'X-2018'
    ];

    public $hasAdmin = 0;//插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        return true;//安装成功返回true，失败false
    }

    // 插件卸载
    public function uninstall()
    {
        return true;//卸载成功返回true，失败false
    }

}
