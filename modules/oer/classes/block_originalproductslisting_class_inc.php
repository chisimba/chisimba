<?php

$this->loadClass('link', 'htmlelements');

/**
 * This class lists the original products
 *
 * @author davidwaf
 */
class block_originalproductslisting extends object {

    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = "";
    }

    public function show() {
        $modeRaw = $this->configData;
        $modeParts = explode("__", $modeRaw);
        $mode = $modeParts[0];
        $filter = "";
        $filterOptions="";
        if (count($modeParts) == 3) {
            $filter = $modeParts[1];
            $filterOptions=$modeParts[2];
        }
        $objProductManager = $this->getObject("productmanager", "oer");
        return $objProductManager->getOriginalProductListingPaginated($mode,$filterOptions, $filter);
    }

}

?>
