<?php
/**
 * This snippet returns a formatted list of links to the same product in different languages
 * within categories on other contexts.
 * Similar in concept to the language links in Babel.
 */

$request = $modx->sanitizeString($_REQUEST['q']);
// Grab cached output if it exists.
if($modx->cacheManager->get('cml_language_links_'.$request, [xPDO::OPT_CACHE_KEY => 'commercemultilang'])) {
    $modx->cacheManager->set('cml_language_links_'.$request,$output, 3600, [xPDO::OPT_CACHE_KEY => 'commercemultilang']);
}

// Grab xpdo instance with needed tables loaded
$commerceMultiLang = $modx->getService('commercemultilang', 'CommerceMultiLang', $modx->getOption('commercemultilang.core_path', null, $modx->getOption('core_path') . 'components/commercemultilang/') . 'model/commercemultilang/', $scriptProperties);
if (!($commerceMultiLang instanceof CommerceMultiLang))
    return '';
$xpdo = &$commerceMultiLang->modx;

$contextKey = $modx->context->get('key');

$alias = $_GET['product']['alias'];
$alias = $modx->sanitizeString($alias);
// Gets id of the product resource viewport
$productDetailId = $modx->getOption('commercemultilang.product_detail_page');
$output = '';
if($productDetailId) {

    $langs = array();
    $productId = null;

    $contextLangs = $commerceMultiLang->getLanguages();
    $productLangs = array();
    $c = $xpdo->newQuery('CommerceMultiLangProduct');
    $c->leftJoin('CommerceMultiLangProductData','ProductData','CommerceMultiLangProduct.id=ProductData.product_id');
    $c->where(array('ProductData.alias'=>$alias));
    $c->select('CommerceMultiLangProduct.id,ProductData.alias');
    if ($c->prepare() && $c->stmt->execute()) {
        $product = $c->stmt->fetch(PDO::FETCH_ASSOC);
        if($product) {
            $productId = $product['id'];
            $l = $modx->newQuery('CommerceMultiLangProductLanguage');
            $l->where(array(
                'product_id'    =>  $product['id']
            ));
            $l->select('lang_key,category');
            if ($l->prepare() && $l->stmt->execute()) {
                $productLangs = $l->stmt->fetchALL(PDO::FETCH_ASSOC);
            }
        }
    }

    // merge the arrays so the product's category is included for each context
    $languages = array();
    foreach($contextLangs as $contextLang) {
        foreach($productLangs as $productLang) {
            if($contextLang['lang_key'] == $productLang['lang_key']) {
                $contextLang['category'] = $productLang['category'];
                array_push($languages,$contextLang);
            }
        }
    }

    // Grabs the extension type for documents then strips it from the alias
    $contentType = $modx->getObject('modContentType',array(
        'mime_type' => 'text/html'
    ));
    if($contentType) {
        $extension = $contentType->get('file_extensions');
    }

    // Get count so we can not include separator on last item
    $count = count($languages);
    $idx = 1;
    foreach($languages as $language) {
        $url = $modx->makeUrl($language['category'],$language['context_key'],'','full');

        // Check if active language and set active class
        $active = '';
        if($language['lang_key'] === $modx->getOption('cultureKey')) {
            $active = 'active';
        }

        // Make sure the correct extension is used.
        if ($extension) {
            $alias = str_replace($extension, '', $alias);
            $alias = $alias.$extension;
        }
        $url = $url.$alias;

        // Set template chunk
        $chunkName = 'language_links_tpl';
        if($scriptProperties['tpl']) {
            $chunkName = $scriptProperties['tpl'];
        }

        if($count > $idx) {
            $output .= $modx->getChunk($chunkName, array(
                'link'          => $url,
                'name'          => $language['name'],
                'active'        => $active,
                'cultureKey'    => $language['lang_key'],
                'separator'     => $scriptProperties['separator']
            ));
        } else {
            $output .= $modx->getChunk($chunkName, array(
                'link'          => $url,
                'name'          => $language['name'],
                'active'        => $active,
                'cultureKey'    => $language['lang_key']
            ));

        }
        $idx++;
    }
}
// Cache the output
$options = array(
    xPDO::OPT_CACHE_KEY => 'commercemultilang',
);
if(!$modx->cacheManager->get('cml_language_links_'.$request, $options)) {
    $modx->cacheManager->set('cml_language_links_'.$request,$output, 3600, $options);
}
return $modx->cacheManager->get('cml_language_links_'.$request, $options);