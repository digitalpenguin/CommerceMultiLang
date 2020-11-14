<?php
/**
 * Make an image the main one for the product
 * 
 * @package commerce_multilang
 * @subpackage processors
 */

class CMLProductImageMakeMainProcessor extends modObjectUpdateProcessor {
    public $classKey = 'CMLProductImage';
    public $languageTopics = array('commerce_multilang:default');
    public $objectType = 'commerce_multilang.productimage';

    public function initialize() {
        $this->object = $this->modx->getObject($this->classKey,array(
            'id'    =>  $this->getProperty(1,$this->getProperty('id'))
        ));
        return true;
    }

    public function beforeSave() {
        $main = $this->getProperty('main');
        if($main == 0) {
            $this->object->set('main', 1);
        }
        return true;
    }

    public function afterSave() {
        $images = $this->modx->getCollection('CMLProductImage',array(
            'product_id:='    =>  $this->object->get('product_id'),
            'AND:id:!=' => $this->object->get('id')
        ));
        foreach ($images as $image) {
            $image->set('main', 0);
            $image->save();
        }
        return parent::afterSave();
    }


}
return 'CMLProductImageMakeMainProcessor';