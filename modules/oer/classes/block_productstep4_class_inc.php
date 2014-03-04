<?php

/**
 * Handles product creation step 4, which invololves thimbnail upload
 *
 * @author davidwaf
 */
class block_productstep4 extends object {

    function init() {
        $this->title="";
    }

    function show() {
        $data = explode("|", $this->configData);
        $id = NULL;
        $step = '1';
        if (count($data == 2)) {
            $id = $data[0];
            $step = $data[1];
        }
        $objProductManager = $this->getObject('productmanager', 'oer');
        return $objProductManager->buildProductFormStep4($id);
    }

}

?>