<?php


namespace app\index\model;

use app\index\controller\Base;


class Cate extends BaseModel
{
    // 获取帮助分类
    public function getCateByCateType($cateType) {
        return self::where('cate_type',$cateType)->select();
    }

    // 获取网店帮助分类
    public function getHelpCate($cateType) {
        return self::where(['cate_type'=>$cateType,'pid'=> 2])->select();
    }

    // 获取普通分类
    public function getComCate($cateType) {
        $comCates = self::where(['cate_type'=>$cateType,'pid'=> 0])->select();
        foreach ($comCates as $k => $v) {
            $comCates[$k]['child'] = self::getComChildCate($v['id']);
        }
        return $comCates;
    }

    // 普通分类下还有二级便查询
    public function getComChildCate($id) {
        return self::where('pid',$id)->select();
    }

    // 获取对应cateID栏目
    public function getCateByID($cateID) {
        return self::find($cateID);
    }

    // 获取栏目列表
    public function getCateList() {
        return self::field('id,pid,cate_name')->select();
    }

    // 面包屑导航
    public function position($cateID) {
        $cateList = self::getCateList();
        return $this->_position($cateList,$cateID);
    }

    private function _position($cateList,$cateID) {
        $cate = self::getCateByID($cateID);
        static $arr = [];
        if (empty($arr)) {
            $arr[] = $cate;
        }
        foreach ($cateList as $k => $v) {
            if ($cate['pid'] == $v['id']) {
                $arr[] = $v;
                self::_position($cateList,$v['id']);
            }
        }
        return array_reverse($arr);
    }
}