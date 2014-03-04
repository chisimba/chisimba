<?php

/**
 * This provides adb layer that links groups to institutions
 *
 * @author davidwaf
 */
class dbgroupinstitutions extends dbtable {

    function init() {
        parent::init("tbl_oer_group_institutions");
    }

    /**
     * updates selected institutions for this group. If the group has existing 
     * institions, they are deleted first and new entry inserteds
     * @param type $data
     * @param type $contextcode 
     */
    function updateGroupInstitutions($institutions, $contextcode) {
        /* foreach ($institutions as $institution) {
          $this->getArray("delete from tbl_oer_group_institutions where group_id = '" . $contextcode + "' and institution_id='" . $institution . "'");
          } */
        $this->delete("group_id", $contextcode);
      
        foreach ($institutions as $institution) {
            $data = array(
                "group_id" => $contextcode,
                "institution_id" => $institution
            );
            $this->insert($data);
        }
    }

    /**
     * get all the institutions that belong to this group
     * @param type $contextcode
     * @return type 
     */
    function getGroupInstitutions($contextcode) {
        $sql =
                "select * from tbl_oer_group_institutions where group_id = '" . $contextcode . "'";
        return $this->getArray($sql);
    }

}

?>
