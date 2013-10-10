<?php

/**
 * Description of block_filteroriginalproduct_class_inc
 *
 * @author davidwaf
 */
class block_filterproduct extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_oer_filterproducts', 'oer');
    }

    function show() {
        $data = explode("__", $this->configData);
        $action=$data[0];
        $filterOptions=$data[1];
        $filtermanager = $this->getObject("filtermanager", "oer");
        return $filtermanager->buildFilterProductsForm($action, 'mod_oer_typeofproduct',$filterOptions);
    }

}

?>
