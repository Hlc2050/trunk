<?php
class PromotionDeductionAddressRel extends CActiveRecord{
	public function tableName() {
		return '{{promotion_deduction_address_rel}}';
	}
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

}
