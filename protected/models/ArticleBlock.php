<?php
class ArticleBlock extends CActiveRecord{
	public function tableName() {
		return '{{article_block}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
