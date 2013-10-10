<?php

/**
 * This class provides the interface to the database for managing section year
 *
 * @author davidwaf
 */
class dbyear extends dbTable {

    private $tableName = 'tbl_oer_year';

    function init() {
        parent::init($this->tableName);
    }

    /**
     * returns the year nodes of the sections
     * @param type $productId
     * @return type 
     */
    function getCurriculum($productId) {
        $sql =
                "select * from tbl_oer_year where product_id = '$productId'";
        $result = $this->getArray($sql);

        if (count($result) > 0) {
            return $result[0];
        } else {
            return NULL;
        }
    }

    /**
     * Creates a new year record
     * @param type $data
     * @return type 
     */
    function addYear($data) {
        return $this->insert($data);
    }

}

?>
