<?php

/**
 * this block is used to render form for adding / editing section info
 * of a product
 *
 * @author davidwaf
 */
class block_sectionnode extends object {

    function init() {
        $this->title = "";
    }

    function show() {
        $data = explode("|", $this->configData);
        $productId = $data[0];
        $sectionId = $data[1];
        $isOriginalProduct = $data[2];        
        $objSectionManager = $this->getObject("sectionmanager", "oer");
        return $objSectionManager->buildCreateEditNodeForm($productId,$sectionId, $isOriginalProduct);
    }

}

?>
