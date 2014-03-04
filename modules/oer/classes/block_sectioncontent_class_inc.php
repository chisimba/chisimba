<?php

/**
 * this block is used to render form for adding / editing section content
 * of a product
 *
 * @author davidwaf
 */
class block_sectioncontent extends object {

    function init() {
        $this->title = "";
    }

    function show() {
        $data = explode("|", $this->configData);
        $productId=$data[0];
        $sectionId = $data[1];
        $objSectionManager = $this->getObject("sectionmanager", "oer");
        return $objSectionManager->getAddEditSectionForm($productId, $sectionId);
    }

}

?>
