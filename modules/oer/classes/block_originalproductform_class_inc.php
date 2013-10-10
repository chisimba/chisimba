<?php

/**
 * Builds a form for creating new products
 *
 * @author davidwaf
 */
class block_originalproductform extends object {

    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->title="";
    }

    /**
     * contructs the form and returns it for display
     * @return type 
     */
    public function show() {
        $objProductManager = $this->getObject("productmanager", "oer");
        $data = explode("|", $this->configData);
        $id = NULL;
        $step = '1';
        if (count($data == 2)) {
            $id = $data[0];
            $step = $data[1];
        }
        switch ($step) {
            case '1':
                return $objProductManager->buildProductFormStep1($id);
                break;
            case '2':
                return $objProductManager->buildProductFormStep2($id);
                break;
            case '3':
                return $objProductManager->buildProductFormStep3($id);
                break;
           
        }
    }

}

?>