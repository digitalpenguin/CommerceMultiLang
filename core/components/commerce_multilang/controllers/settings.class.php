<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';
/**
 * Loads the product settings page.
 *
 * @package commerce_multilang
 * @subpackage controllers
 */
class CommerceMultiLangSettingsManagerController extends CommerceMultiLangBaseManagerController {
    public function process(array $scriptProperties = array()) {

    }
    public function getPageTitle() { return $this->modx->lexicon('commerce_multilang'); }
    public function loadCustomCssJs() {
    
    
        $this->addJavascript($this->commerce_multilang->getOption('jsUrl').'mgr/extras/griddraganddrop.js');
        $this->addJavascript($this->commerce_multilang->getOption('jsUrl').'mgr/widgets/combos.widget.js');
        $this->addJavascript($this->commerce_multilang->getOption('jsUrl').'mgr/widgets/producttypes.grid.js');
        $this->addJavascript($this->commerce_multilang->getOption('jsUrl').'mgr/widgets/settings.panel.js');
        $this->addLastJavascript($this->commerce_multilang->getOption('jsUrl').'mgr/sections/settings.js');
    
    }

    public function getTemplateFile() { return $this->commerce_multilang->getOption('templatesPath').'settings.tpl'; }
}