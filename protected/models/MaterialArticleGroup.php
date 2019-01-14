<?php
/**
 * 素材图文管理表
 * Created by PhpStorm.
 * User: yjh
 * Date: 2016/11/2
 * Time: 14:18
 */
class MaterialArticleGroup extends CActiveRecord{
    public function tableName() {
        return '{{material_article_group}}';
    }
    public static function model($className=__CLASS__){
        return parent::model($className);
    }

    public function getArticleGroups(){
        $articleGroups = Dtable::toArr($this->findAll());
        return $articleGroups;
    } 

    /**
     * 创建文章编码
     * @param $support_staff_id
     * @return string
     * author: yjh
     */
    public function createArticleCode($id){

        $group_code = $this->findByPk($id)->group_code;
        $result = Dtable::toArr(MaterialArticleTemplate::model()->find("group_id = $id and article_code like '" . $group_code . "%' order by LENGTH(article_code) desc,article_code  desc"));
        //自动生成图文编码
        if (empty($result)) {
            $article_code = $group_code . "1";
        } else {
            $groupName = $result['article_code'];
            $groupNum = (int)str_replace( $group_code,"",$groupName) + 1;
            $article_code = $group_code . $groupNum;
        }
        return $article_code;

    }

}