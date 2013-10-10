<?php
// security check - must be included in all Chisimba scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* 
* Class to import hotel information for the travel module
*
* @author Nic Appleby
* @category Chisimba
* @package travel
* @copyright GNU/GPL UWC 2008
*
*/
class dbhotels extends dbtable {
    
    public function init() {
        parent::init("tbl_travel_hotels");
        $this->objUser = $this->getObject('user','security');
    }
    
    public function countRecords($filename) {
        $fh = fopen($filename,'rb');

        $i = fgetcsv($fh,null,'|');
        $count = 0;
        while ($i) {
            $count++;
            $i = fgetcsv($fh,null,'|');
        }
            fclose($fh);
        return $count;
    }
    
    public function citySearch($cityString,$page,$countryCode = null) {
        $start = 25*($page-1);
        $sql = "SELECT * FROM tbl_travel_hotels WHERE city LIKE '%$cityString%'";
        if (isset($countryCode)) {
            $sql .= "AND country = '$countryCode'";
        }
        $sql .= "ORDER BY highrate DESC LIMIT $start , 25";
        $rs = $this->getArray($sql);
        return $rs;
    }
    
    public function hotelCount($cityString,$countryCode = null) {
        $sql = "SELECT COUNT(id) as total FROM tbl_travel_hotels WHERE city LIKE '%$cityString%'";
        if (isset($countryCode)) {
            $sql .= "AND country = '$countryCode'";
        }
        $rs = $this->getArray($sql);
        $rs = current($rs);
        return $rs['total'];
    }
    
    public function getCity($searchStr) {
        $sql = "SELECT DISTINCT city, country
                FROM tbl_travel_hotels
                WHERE city LIKE '$searchStr%'
                ORDER BY city";
        $result = $this->getArray($sql);
        return $result;
    }
    
    public function import($filename, $skipFirstLine = true) {
        try {
            $fh = fopen($filename,'rb');

            $i = fgetcsv($fh,null,'|');
            if ($skipFirstLine) {
                $i = fgetcsv($fh,null,'|');
            }
            $keys = array('id', 'name', 'airportcode', 'address1', 'address2', 'address3', 'city', 'stateprovince', 'country', 'postalcode', 'longitude', 'latitude', 'lowrate', 'highrate', 'marketinglevel', 'confidence', 'hotelmodified', 'propertytype', 'timezone', 'gmtoffset', 'yearpropertyopened', 'yearpropertyrenovated', 'nativecurrency', 'numberofrooms', 'numberofsuites', 'numberoffloors', 'checkintime', 'checkouttime', 'hasvaletparking', 'hascontinentalbreakfast', 'hasinroommovies', 'hassauna', 'haswhirlpool', 'hasvoicemail', 'has24hoursecurity', 'hasparkinggarage', 'haselectronicroomkeys', 'hascoffeeteamaker', 'hassafe', 'hasvideocheckout', 'hasrestrictedaccess', 'hasinteriorroomaccess', 'hasexteriorroomaccess', 'hascombination', 'hasfitnessfacility', 'hasgameroom', 'hastenniscourt', 'hasgolfcourse', 'hasinhousedining', 'hasinhousebar', 'hashandicapaccessible', 'haschildrenallowed', 'haspetsallowed', 'hastvinroom', 'hasdataports', 'hasmeetingrooms', 'hasbusinesscentre', 'hasdrycleaning', 'hasindoorpool', 'hasoutdoorpool', 'hasnonsmokingrooms', 'hasairporttransportation', 'hasairconditioning', 'hasclothingiron', 'haswakeupservice', 'hasminibarinroom', 'hasroomservice', 'hashairdryer', 'hascarrentdesk', 'hasfamilyrooms', 'haskitchen', 'hasmap', 'propertydescription', 'gdschaincode', 'gdschaincodename', 'destinationid', 'drivingdirections', 'nearbyattractions', 'created', 'modified', 'creatorid', 'modifierid');
            $count = 0;
            
            while ($i) {
                $i[] = date("Y-m-d H:i:s");
                $i[] = null;
                $i[] = $this->objUser->userId();
                $i[] = null;

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
    
}
?>