<?php
return [
    'APP_ID'     => [
        'title' => 'APP ID',
        'type'  => 'text',
        'value' => '',
        'tip'   => 'APP ID'
    ],
    'APP_SECRET'     => [
        'title' => 'APP SECRET',
        'type'  => 'text',
        'value' => '',
        'tip'   => 'APP SECRET'
    ],
    'AUTHORIZE'     => [
        'title' => 'AUTHORIZE',
        'type'  => 'text',
        'value' => 'response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect',
        'tip'   => '微信登录认证类型SNS,response_type=code'
    ],
    'CALLBACK'     => [
        'title' => '回调地址',
        'type'  => 'text',
        'value' => '',
        'tip'   => '微信登录回调地址:域名/plugin/wxlogin/index/index.html'
    ],

];
