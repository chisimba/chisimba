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
class dbhotelimages extends dbtable {
    
    public function init() {
        parent::init("tbl_travel_hotel_images");
        $this->objLanguage = $this->getObject('language','language');
        $this->objConfig = $this->getObject('altconfig','config');
    }
    
    public function import($filename, $skipFirstLine = true) {
        try {
            $fh = fopen($filename,'rb');

            $i = fgetcsv($fh,null,'|');
            if ($skipFirstLine) {
                $i = fgetcsv($fh,null,'|');
            }
            $keys = array('id', 'name', 'caption', 'url', 'supplier', 'width', 'height', 'bytesize', 'thumbnail');
            $count = 0;
            while ($i) {
                $final = array_combine($keys,$i);
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
    
    public function getImage($id) {
        $image = $this->getAll("WHERE id = '$id' ORDER BY url ASC LIMIT 0 , 1");
        if (!empty($image)) {
            return current($image);
        } else {
            $temp['caption'] = $this->objLanguage->languageText('mod_travel_noimage','travel');
            $temp['thumbnail'] = $this->objConfig->getskinRoot()."_common/icons/noimage.jpg";
            return $temp;
        }
    }
    
}
?>