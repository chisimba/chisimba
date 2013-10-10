<?php

/**
 * This is a DB layer that manages original products
 *
 * @author davidwaf
 */
class dbfeaturedproduct extends dbtable {

    private $productsTableName = 'tbl_oer_featuredproduct';

    function init() {
        parent::init($this->productsTableName);
    }

   
    /**
     *returns the id of the featured product
     * @param string $prodtype whether original or adaptation product
     * @return string
     */
    function getFeaturedProduct($prodtype) {
        $sql =
                "select * from ".$this->productsTableName." where prodtype='".$prodtype."' and status='active'";
        $data = $this->getArray($sql);        
        if (count($data) > 0) {
            return $data[0]['productid'];
        } else {
            return NULL;
        }
    }
    /**
     *  Updates the featured product of the supplied id
     * @param type $data
     * @param type $fId
     * @return type
     */
    function updateFeaturedProduct($data,$fId){
        return $this->update("id", $fId, $data);
    }
    /**
     *  batch Updates the featured product based on param values
     * @param string $prodtype
     * @param string $fId
     * @return array
     */
    function batchUpdateFProducts($prodtype, $status) {
        $sql = "UPDATE  tbl_oer_featuredproduct SET status = '".$status."' WHERE  prodtype = '".$prodtype."'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            return $data[0];
        } else {
            return null;
        }
    }
    /**
     * features a product
     * @param array $data details to mark the product as featured
     * @return string Id of new record
     */
    function setFeaturedProduct($data) {
        //Get prod type
        $prodtype = $data['prodtype'];        
        $status = "inactive";
        //mark all featured products of this type(original/adaptation) as inactive
        $this->batchUpdateFProducts($prodtype, $status);
        $id = $this->insert($data);
        return $id;
    }
}
?>