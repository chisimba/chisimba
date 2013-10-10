<?php

/**
 * This is a DB layer that manages umbrella themes
 *
 * @author davidwaf
 */
class dbumbrellathemes extends dbtable {

    private $tableName = 'tbl_oer_umbrella_themes';

    function init() {
        parent::init($this->tableName);
    }

    /**
     * this selects original products
     */
    function getUmbrellaThemes() {
        $sql = "select * from $this->tableName";
        return $this->getArray($sql);
    }
    

    /**
     * inserts a new theme
     * @param type $title
     * @return type 
     */
    function addUmbrellaTheme($title) {
        $data = array("theme" => $title);
        return $this->insert($data);
    }

}

?>
