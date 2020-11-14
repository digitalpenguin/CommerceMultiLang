<?php
/**
 * Update a product image
 * 
 * @package commerce_multilang
 * @subpackage processors
 */

class CMLProductImageUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'CMLProductImage';
    public $languageTopics = array('commerce_multilang:default');
    public $objectType = 'commerce_multilang.productimage';

    public function afterSave() {
        // Grabs related language table
        $imageLanguages = $this->modx->getCollection('CMLProductImageLanguage',[
            'product_image_id'    => $this->object->get('id')
        ]);
        foreach ($imageLanguages as $language) {
            $language->set('product_image_id',$this->object->get('id'));
            $langKey = $language->get('lang_key');
            //$this->modx->log(1,'Language: '.print_r($language->toArray(),true));
            $lkLength = strlen($langKey);
            foreach($this->getProperties() as $key => $value) {
                // Get the lang_key from the submitted field name and check if it matches current language row
                if(substr($key, -$lkLength) == $langKey) {
                    // If there's a match extract the field name with underscore and lang_key
                    $keyLength = strlen($key);
                    $fieldLength = $keyLength - $lkLength;
                    $fieldName = substr($key,0,$fieldLength-1); //-1 to compensate for underscore
                    // Set the new value
                    $language->set($fieldName,$value);

                }
            }
            $language->save();
        }

        return parent::afterSave();
    }

}
return 'CMLProductImageUpdateProcessor';