<?php

class dbrttjnlp extends dbtable {

    function init() {
        parent::init('tbl_rtt_jnlp');
        
    }

    public function saveParams($data){
        
        $this->insert($data);
    }
    
    
     public function getParams($username){
         $sql=
         "select * from tbl_rtt_jnlp where userid='$username'";
        $array = $this->getArray($sql);
        return $array;
    }
}

?>
