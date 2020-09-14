<?php


namespace app\admin\validate;

use think\Validate;

class Cate extends Validate
{
    protected $rule =   [
        'cate_name'  => 'require|unique:cate|min:2',
    ];

    protected $message  =   [
        'cate_name.require' => '名称不能为空！',
        'cate_name.unique'     => '名称不能重复！',
        'cate_name.min' => '分类名过短！'
    ];

}