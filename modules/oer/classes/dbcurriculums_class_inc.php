<?php

/**
 * This class provides the interface to the database for managing product curriculum
 *
 * @author davidwaf
 */
class dbcurriculums extends dbTable {

    private $tableName = 'tbl_oer_curriculums';

    function init() {
        parent::init($this->tableName);
    }

    /**
     * returns the nodes of the sections
     * @param type $productId
     * @return type 
     */
    function getCurriculum($productId) {
        $sql =
                "select * from tbl_oer_curriculums where product_id = '$productId'";
        $result = $this->getArray($sql);

        if (count($result) > 0) {
            return $result[0];
        } else {
            return NULL;
        }
    }

    /**
     * Creates a new curriculum record
     * @param type $data
     * @return type 
     */
    function addCurriculum($data) {
        return $this->insert($data);
    }
    
    /**
     * Updates the curriculum info
     * @param type $data
     * @param type $id
     * @return type 
     */
    function updateCurriculum($data,$id){
        return $this->update("id", $id, $data);
    }

}

?>
