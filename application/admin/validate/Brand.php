<?php


namespace app\admin\validate;

use think\Validate;

class Brand extends Validate
{
    protected $rule =   [
        'brand_name'  => 'require|unique:brand',
        'brand_url'   => 'require|url',
        'brand_description' => 'require|min:6',
    ];

    protected $message  =   [
        'brand_name.require' => '名称不能为空！',
        'brand_name.unique'     => '名称不能重复！',
        'brand_url.url'   => '网址格式不正确！',
        'brand_url.require'   => '网址不能为空！',
        'brand_description.min'  => '描述不能少于6位!',
        'brand_description.require'  => '描述不能为空!',
    ];

}