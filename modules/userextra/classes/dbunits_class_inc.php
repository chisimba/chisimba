<?php
class dbunits extends dbtable {
/**
 * Assign the table name in dbtable to be the table specified below
 */
    public function init() {
        parent::init("tbl_userextra_units");
        
    }

    public function addUnit($data) {

        $result = $this->insert($data);
        return $result;
    }

    public function deleteUnits(){
       $sql="delete from tbl_userextra_units";
       $this->getArray($sql);
     }

   public function getTotal(){
    $sql="select count(unitcode) as totalcount from tbl_userextra_units";
    $total=0;
    $rows=$this->getArray($sql); 
    if(count($rows) > 0){
      $total=$rows[0]['totalcount'];
      
    }
    return $total;
   }
   
    public function getUnits($start, $end) {
        $qry = "SELECT * FROM tbl_userextra_units limit $start,$end";
        $info = $this->getArray($qry);
        return $info;
    }

}
?>
