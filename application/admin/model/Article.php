<?php

namespace app\admin\model;

class Article  extends Base
{
    // 添加 或 更新文章
    public function addAndSaveArticle($data,$id='') {
        return self::save($data,$id);
    }

    // 查询文章列表
    public function getArticleList() {
        return self::field('a.*,c.cate_name')->alias('a')
            ->join('cate c','a.cate_id=c.id')->order('a.id DESC')
            ->paginate(10);
    }

    // 获取指定文章
    public function getArticleByID($id) {
        return self::find($id);
    }

    // 通过栏目id获取指定文章
    public function getArticleListByID($cateID) {
        return self::where('cate_id',$cateID)->select();
    }

    // 通过id获取缩略图
    public function getThumbByID($id) {
        return self::field('thumb')->find($id);
    }

    // 删除指定文章id
    public function delArticleByID($ID) {
        return self::destroy($ID);
    }
}