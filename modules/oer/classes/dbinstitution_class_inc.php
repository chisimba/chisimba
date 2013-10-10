<?php

/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

class dbinstitution extends dbtable {

    public $objUser;

    function init() {
        parent::init("tbl_oer_institutions");

        $this->product_adaptaion_data = 'tbl_oer_product_adaptation_data';
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     * Returns an array with all institution objects
     * @return <Array<Institution>>
     */
    function getAllInstitutions($filter = NULL) {
        //$sql = "SELECT * FROM tbl_oer_institutions";
        return $this->getAll($filter);
        //return $this->getArray($sql);
    }

    function getInstitutionIdbyType($type) {
        $sql = "SELECT id FROM tbl_oer_institutions WHERE type = '$type'";

        return $this->getArray($sql);
    }

    function getProductIdbyInstid($type) {
        $sql = "SELECT product_id FROM $this->product_adaptaion_data WHERE institution_id = '$type'";

        return $this->getArray($sql);
    }

    function addInstitution($name, $description, $type, $country, $address1, $address2, $address3, $zip, $city, $websiteLink, $keyword1, $keyword2, $thumbnail) {
        $data = array(
            'name' => $name,
            'description' => $description,
            'country' => $country,
            'type' => $type,
            'address1' => $address1,
            'address2' => $address2,
            'address3' => $address3,
            'city' => $city,
            'websiteLink' => $websiteLink,
            'keyword1' => $keyword1,
            'keyword2' => $keyword2,
            'zip' => $zip,
        );

        $id = $this->insert($data);


        // Prepare to add context to search index
        $objIndexData = $this->getObject('indexdata', 'search');
        $saveDate = date('Y-m-d H:M:S');
        //module=oer&action=4&institutionId=gen11Srv48Nme53_3499_1312799815
        $url = $this->uri(array('action' => 'viewinstitution', 'id' => $id), 'oer');
        $objTrimStr = $this->getObject('trimstr', 'strings');
        $teaser = $objTrimStr->strTrim(strip_tags($description), 500);

        $userId = $this->objUser->userId();
        $module = 'oer';

        $objIndexData->luceneIndex($id, $saveDate, $url, $name, NULL, $teaser, $module, $userId, NULL, NULL, NULL);
        return $id;
    }

    //to get an institution latitude
    function getInstitutionLatitude($InstitutionNameID) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE id='$InstitutionNameID'";
        $Institution = $this->getArray($sql);
        return $Institution[0]['loclat'];
    }

// to get an institution longitude

    function getInstitutionLongitude($name) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE name='$name'";
        $Institution = $this->getArray($sql);
        return $Institution[0]['loclong'];
    }

    /*
     * Function to get the name of an institution
     * @param id id of the institution record
     * @return string institution name
     */

    function getInstitutionName($id) {
        $sql = "SELECT name FROM tbl_oer_institutions WHERE id='$id'";
        $institutionName = $this->getArray($sql);
        if (count($institutionName) > 0) {
            return $institutionName[0]['name'];
        } else {
            return Null;
        }
    }

    /**
     * this tests if the supplied institutionid has been used in adaptation.
     * Ideally, if this is the case, the institution cannot be deleted until
     * it is di-linked from the pridct
     * @param type $institutionId 
     */
    public function  isInstitutionInUse($institutionId){
        $sql=
        "select institutionid from tbl_oer_products where institutionid = '$institutionId'";
        $rows=$this->getArray($sql);
        if(count($rows) > 0){
            return true;
        }else{
            return false;
        }
    }
    
    /*
     * Function to get the institution data by id
     * @param id id of the institution record
     * @return array institution data
     */

    function getInstitutionById($id) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE id = '" . $id . "'";
        $result = $this->getArray($sql);
        if (count($result) > 0) {
            return $result[0];
        } else {
            return Null;
        }
    }

    function getInstitutionByName($name) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE name='$name'";
        $InstitutionName = $this->getArray($sql);
        return $InstitutionName[0]['name'];
    }

    //To handle the latitude and longitude to feet on the map
    function getlocationcoords($lat, $lon, $width, $height) {
        $x = (($lon + 180) * ($width / 360));
        $y = ((($lat * -1) + 90) * ($height / 180));
        return array("x" => round($x), "y" => round($y));
    }

    // function is responsible to dispaly the map and its images
    function MapHandler($im, $lat, $long) {
        if (empty($long)
        )
            $long = 28.0316;
        if (empty($lat)
        )
            $lat = -26.19284;
        $red = imagecolorallocate($im, 255, 0, 0);
        $scale_x = imagesx($im);
        $scale_y = imagesy($im);
        $pt = $this->getlocationcoords($lat, $long, $scale_x, $scale_y);
        imagefilledrectangle($im, $pt["x"] - 2, $pt["y"] - 2, $pt["x"] + 2, $pt["y"] + 2, $red);
        header("Content-Type: image/png");
    }

    public function getInstitutionThumbnail($thumbnail) {
        //TODO Ntsako Handle the situation where the institution is not in the table
        $sql = "SELECT * FROM tbl_oer_institutions WHERE thumbnail = '$thumbnail'";
        $thumbnail = $this->getArray($sql);
        return $thumbnail[0];
    }

    public function isInstitution($name) {
        //$sql = "IF EXISTS(SELECT * FROM tbl_oer_institution WHERE name = '$name')";

        $sql = "SELECT * FROM tbl_oer_institutions WHERE name = '$name'";
        if (count($this->getArray($sql)) != 0) {
            //return true;
            return count($this->getArray($sql));
        } else {
            return false;
        }
        //return count($this->getArray($sql));
    }

    //this function delete  a record

    function deleteInstitution($id) {
        $sql = "DELETE FROM tbl_oer_institutions WHERE id='$id'";
        $this->getArray($sql);

        $objIndexData = $this->getObject('indexdata', 'search');
        $objIndexData->removeIndex($id);
    }

    //this function edit the instituin name
    //TODO MUST ALSO EDIT THUMBNAIL
    function editInstitution($id, $name, $description, $type, $country, $address1, $address2, $address3, $zip, $city, $websiteLink, $keyword1, $keyword2, $thumbnail) {
        $data = array(
            'name' => $name,
            'description' => $description,
            'country' => $country,
            'type' => $type,
            'address1' => $address1,
            'address2' => $address2,
            'address3' => $address3,
            'city' => $city,
            'websiteLink' => $websiteLink,
            'keyword1' => $keyword1,
            'keyword2' => $keyword2,
            'zip' => $zip
        );

        $result = $this->update(
                'id', $id, $data
        );

        if ($result != FALSE) {

            // Call Object
            $objIndexData = $this->getObject('indexdata', 'search');

            // Prep Data
            
            $docDate = date('Y-m-d H:M:S');
            ;
            $url = $this->uri(array('action' => '4', 'institutionId' => $id), 'oer');
            $title = stripslashes($name);

            // Remember to add all info you want to be indexed to this field
            $contents = stripslashes($description);

            // A short overview that gets returned with the search results
            $objTrim = $this->getObject('trimstr', 'strings');
            $teaser = $objTrim->strTrim(strip_tags(stripslashes($description)), 300);

            $module = 'oer';
            $userId = $this->objUser->userId();
            $additionalSearchIndex = array(
                'country' => $country,
                'type' => $type,
                'city' => $city,
            );

            // Add to Index
            $objIndexData->luceneIndex($id, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $additionalSearchIndex);

            return $result;
        } else {
            return FALSE;
        }
    }

    public function getInstitutionCountry($country) {
        //TODO Ntsako Handle the situation where the institution is not in the table
        $sql = "SELECT * FROM tbl_oer_institutions WHERE country = '$country'";
        $country = $this->getArray($sql);
        return $country[0];
    }

    function findInstitutionTypeID($type) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE type = '$type'";
        $type = $this->getArray($sql);
        return $type[0];
    }

    function getLastInstitutionId() {
        $sql = "SELECT id FROM tbl_oer_institutions ORDER BY puid DESC LIMIT 1";
        $id = $this->getArray($sql);
        return $id;
    }

    /**
     * updates institution with given data
     * @param type $data
     * @param type $id 
     */
    function updateInstitution($data, $id) {
        $this->update("id", $id, $data);
    }

}

?>