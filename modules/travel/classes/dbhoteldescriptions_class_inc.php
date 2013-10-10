<?php
// security check - must be included in all Chisimba scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* 
* Class to import country code information for the travel module
*
* @author Nic Appleby
* @category Chisimba
* @package travel
* @copyright GNU/GPL UWC 2008
*
*/
class dbhoteldescriptions extends dbtable {
    
    public function init() {
        parent::init("tbl_travel_hotel_descriptions");
        $this->objUser = $this->getObject('user','security');
    }
    
    public function import($filename, $skipFirstLine = true) {
        try {
            $fh = fopen($filename,'rb');

            $i = fgetcsv($fh,null,'|');
            if ($skipFirstLine) {
                $i = fgetcsv($fh,null,'|');
            }
            $keys = array('id', 'marketinglevel', 'description', 'gdschaincode', 'gdschaincodename');
            $count = 0;
            while ($i) {
                $final = array_combine($keys,$i);
                $final['creatorid'] = $this->objUser->userId();
                $final['created'] = date('Y-m-d');
                if ($this->insert($final)) {
                   $count++;
                }
                $i = fgetcsv($fh,null,'|');
            }
            fclose($fh);
            return $count;
        } catch (Exception $e) {
            echo "Failed with exception $e. inserted $count records";
        }
     }
    
    public function getDescription($id) {
        $desc = $this->getAll("WHERE id = '$id' LIMIT 0 , 1");
        if (!empty($desc)) {
            return current($desc);
        } else {
            return false;
        }
    }
    
}
?>