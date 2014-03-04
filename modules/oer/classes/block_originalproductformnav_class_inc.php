<?php

/**
 * This creates a navigation for quick moving in between forms when creating
 * a new orginal product
 *
 * @author davidwaf
 */
class block_originalproductformnav extends object {

    function init() {
        $this->title="";
    }

    public function show() {
       $data = explode("|", $this->configData);
        $id = NULL;
        $step = '1';
        if (count($data) == 2) {
            $id = $data[0];
            $step = $data[1];
        } else if (count($data) == 1){
            $id = $data[0];
        }
        
        $objProductManager = $this->getObject('productmanager', 'oer');
        return $objProductManager->buildProductStepsNav($id,$step);
    }

}

?>