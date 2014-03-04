<?php

/**
 * this block is used for rendering product details
 *
 * @author davidwaf
 */
class block_vieworiginalproduct extends object {

    function init() {
        $this->title = "";
    }

    function show() {
        $id = $this->configData;
        $objProductManager = $this->getObject("vieworiginalproduct", "oer");
        return $objProductManager->buildProductDetails($id);
    }

}
?>

