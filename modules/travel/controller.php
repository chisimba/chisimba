<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for the Travel Portal
*
* @author Nic Appleby
* @package travel
*
*/
class travel extends controller {
    
    /**
     * Standard chisimba init method
     *
     */
    public function init() {
        $this->objHotels = $this->getObject('dbhotels');
        $this->objCountryCodes = $this->getObject('dbcountrycodes');
        $this->objLanguage = $this->getObject('language','language');
        $this->objConfig = $this->getObject('altconfig','config');
        $this->objHotelImages = $this->getObject('dbhotelimages');
        $this->objHotelDescriptions = $this->getObject('dbhoteldescriptions');
    }
    
    /**
     * Standard Chisimba dispatch method
     */
    public function dispatch() {
        $action = $this->getParam('action',$this->getParam("amp;action"));
        switch ($action) {
            
            case "import_hotels":
                $filename = $this->getParam('filename');
                $this->objHotels->import($filename);
                echo "Done.";
                break;
            
            case "import_images":
                $filename = $this->getParam('filename');
                $this->objHotelImages->import($filename);
                echo "Done.";
                break;
                
            case "import_descriptions":
                $filename = $this->getParam('filename');
                $this->objHotelDescriptions->import($filename);
                echo "Done.";
                break;
                
            case "import_countries":
                $filename = $this->getParam('filename');
                $this->objCountryCodes->import($filename);
                echo "Done.";
                break;
            
            case "country_autocomplete":
                $this->suppressPageTemplate = true;
                $term = $this->getParam('searchStr');
                $res = $this->objHotels->getCity($term);
                echo "<ul>";
                foreach ($res as $country) {
                    echo "<li>{$country['city']}, {$country['country']}</li>";
                }
                echo "</ul>";
                break;
                
            case "count_records":
                $filename = $this->getParam('filename');
                echo $this->objHotels->countRecords($filename);
                break;
                
            case "hotel results":
                $searchStr = $this->getParam('searchStr');
                if ($pos = strpos($searchStr,',')) {
                    $cityString = substr($searchStr,0,$pos);
                    $countryString = substr($searchStr,$pos+2);
                    $this->setVar('countryString',$countryString);
                } else {
                    $cityString = $searchStr;
                }
                $this->setVar('page',$this->getParam('page',1));
                $this->setVar('cityString',$cityString);
                return "hotelresults_tpl.php";
                break;
                
            case "view hotel":
                $this->setVar('id',$this->getParam('id'));
                return "viewhotel_tpl.php";    
            
            case "search hotels":
            default:
                return "search_tpl.php";
                break;
        }
    }
    
    /**
     * Standard Chisimba requiresLogin method
     */
    public function requiresLogin() {
        switch ($this->getParam('action')) {
            
            case "import_hotels":
                return true;
                break;
            
            default:
                return false;
                break;
        }
    }
    
}