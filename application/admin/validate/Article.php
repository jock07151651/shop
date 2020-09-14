<?php


namespace app\admin\validate;

use think\Validate;

class Article extends Validate
{
    protected $rule =   [
        'title'  => 'require|unique:article',
        'cate_id' => 'require',
        'email' => 'email',
        'link_url' => 'url',
    ];

    protected $message  =   [
        'title.require' => '名称不能为空！',
        'title.unique'     => '名称不能重复！',
        'cate_id.min' => '所属栏目不能为空！'

    ];

}