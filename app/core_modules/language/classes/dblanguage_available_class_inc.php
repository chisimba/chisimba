<?php

/**
 * 
 *
 * @author davidwaf
 */
class dblanguage_available extends dbtable {

   function init() {
        parent::init('tbl_langs_avail');
    }

    function getLanguageList() {
        $sql = "select * from tbl_langs_avail";
        return $this->getArray($sql);
    }
}

?>
