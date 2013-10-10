<?php

/**
 *
 * Database access for OER groups
 *
 * Database access for OER groups for enabling display, edit, add of groups
 *
 * PHP version 5
 *
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
 * 
 *
 * @category  Chisimba
 * @package   OER
 * @author    UNKNOWN
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 * 
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Database access for OER groups
 *
 * Database access for OER groups for enabling display, edit,
 * add of groups
 * 
 * @package   switchboard
 * @author    Derek Keats derek@dkeats.com
 *
 */
class dbgroups extends dbtable {

    /**
     * 
     * Constructor which sets the database table
     * 
     * @access public
     * @return VOID
     * 
     */
    public function init() {
        parent::init("tbl_oer_groups");
    }

    /**
     * 
     * Get am array of group data with a limit
     *
     * @param integer $start The starting pagenumber
     * @param integer $limit The number of records
     * @return string array The array of records
     * @access public
     * 
     */
    public function getgroups($start, $limit) {
        $sql = "select * from tbl_oer_groups limit $start,$limit";
        return $this->getArray($sql);
    }

    /**
     * returns distinct regions that are available for all groups
     */
    function getGroupRegions() {
        $sql =
                "select distinct region from tbl_oer_groups where region is not null";
        return $this->getArray($sql);
    }

    /**
     * returns distinct countries that are available for all groups
     */
    function getGroupCountries() {
        $sql =
                "select distinct country from tbl_oer_groups where country is not null";
        return $this->getArray($sql);
    }

    /**
     * returns a list of groups that have adapted products
     * @return type 
     */
    public function getGroupsThatHaveAdapatations() {
        $sql = "select distinct gr.* from tbl_oer_groups gr, tbl_oer_products pr where gr.contextcode=pr.groupid";
        return $this->getArray($sql);
    }

    /**
     * returns id of a product that matches the supplied latitude and longitude
     * @param type $lat
     * @param type $long 
     */
    public function getAdapationIdByLocation($lat,$long){
        $sql=
        "select distinct pr.id from tbl_oer_products pr,tbl_oer_groups gr where gr.contextcode=pr.groupid and gr.loclat='$lat' and gr.loclong='$long'";
         $result=$this->getArray($sql);
         if(count($result) > 0){
             return $result[0]['id'];
         }else{
             return NULL;
         }
        }
    /**
     * 
     * Return an array of all groups
     * 
     * @return string array The array of records
     * @access public
     * 
     */
    public function getAllGroups() {
        $sql = "select * from tbl_oer_groups where parent_id is  NULL";
        return $this->getArray($sql);
    }

    /**
     * gets group info using context code
     * @param type $contextcode
     * @return type 
     */
    public function getGroupByContextCode($contextcode) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE contextcode='$contextcode'";
        $data = $this->getArray($sql);
        if (count($data) > 0) {
            return $data[0];
        } else {
            return NULL;
        }
    }

    /**
     *
     * Delete a group by group id
     * 
     * @param string $groupid The groupid of the group to delete
     * @return VOID
     * @access public
     * 
     */
    public function deleteGroup($groupid) {
        $sql = "DELETE FROM tbl_oer_groups WHERE id='$groupid'";
        $this->getArray($sql);
    }

    /**
     *
     * Return the group forum id from the group id
     * 
     * @param string $groupid The groupid of the group to lookup the forum id for
     * @return string The forum id
     * @access public
     * 
     */
    public function getGroupForumId($groupid) {
        $sql = "select id from tbl_forum where forum_workgroup = '$groupid'";
        $result = $this->getArray($sql);
        return $result[0]['id'];
    }

    public function updateGroup($data, $contextcode) {
        $this->update('contextcode', $contextcode, $data);
    }

    /**
     * saves new group
     * @param type $data
     * @return type 
     */
    public function saveNewGroup($data) {
        return $this->insert($data);
    }

    /*
     * This function take a groupId and return its latitude
     * @param $GroupID
     * return int
     */

    function getGroupLatitude($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE id='$groupid'";
        $Group = $this->getArray($sql);
        return $Group[0]['loclat'];
    }

    /*
     * This function takes a groupId and return its longitude
     * @param $GroupID
     * return int
     */

    function getGroupLongitude($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE id='$groupid'";
        $Group = $this->getArray($sql);
        return $Group[0]['loclong'];
    }

    /*
     * This function take a groupId an return the group name
     * @param $GroupId
     * return name
     */

    function getGroupName($GroupID) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE id='$GroupID'";
        $GroupName = $this->getArray($sql);
        return $GroupName[0]['name'];
    }

    function getGroupDescription($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE id='$groupid'";
        $GroupDescription = $this->getArray($sql);
        return $GroupDescription[0]['description'];
    }

    function getGroupCountry($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE id='$groupid'";
        $GroupCountry = $this->getArray($sql);
        return $GroupCountry[0]['country'];
    }

    function getGroupID($groupname) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE name='$groupname'";
        $groupID = $this->getArray($sql);
        return $groupID[0]['id'];
    }

    function getLinkedInstitution($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE id='$groupid'";
        $linkedInstitution = $this->getArray($sql);
        return $linkedInstitution[0]['linkedinstitution'];
    }

    function getThumbnail($groupid) {
        $sql = "select * from tbl_oer_groups where id='$groupid'";
        $thumbnail = $this->getArray($sql);
        return $thumbnail[0]['thumbnail'];
    }

    /*
     * This function convert the latitude and longitude and map it on a map
     * @lat
     * @lon
     * @param $width
     * @param $height
     * return array
     */

    function getlocationcoords($lat, $lon, $width, $height) {
        $x = (($lon + 180) * ($width / 360));
        $y = ((($lat * -1) + 90) * ($height / 180));
        return array("x" => round($x), "y" => round($y));
    }

    /*
     * This function is  responsuble to draw the image
     * @im
     * @lat
     * @lat
     */

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

    public function getGroupThumbnail($name) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE name = '$name'";
        $thumbnail = $this->getArray($sql);
        return $thumbnail[0];
    }

    public function isGroup($name) {
        //$sql = "IF EXISTS(SELECT * FROM tbl_oer_institution WHERE name = '$name')";

        $sql = "SELECT * FROM tbl_oer_groups WHERE name = '$name'";
        if (count($this->getArray($sql)) != 0) {
            //return true;
            return count($this->getArray($sql));
        } else {
            return false;
        }
    }

    function searchGroupByName($groupname) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE name = '$groupname'";
        return $this->getArray($sql);
    }

//To get the adaptation of group by groupid

    function getGroupProductadaptation($groupid) {
        $sql = "SELECT a.product_id,p.id from tbl_oer_product_adaptation_data a, tbl_oer_products p WHERE a.group_id = '$groupid' and p.id = a.product_id and p.deleted=0 ";
        return $this->getArray($sql);
    }

// To get adapted group adapted product thumbnail
    function getAdaptedProductThumbnail($productid) {
        $sql = "SELECT * FROM tbl_oer_products WHERE id='$productid'";
        $array = $this->getArray($sql);
        return $array[0]['thumbnail'];
    }

    //To get group adapted product title
    function getAdaptedProductTitle($productid) {
        $sql = "SELECT * FROM tbl_oer_products WHERE id='$productid'";
        $array = $this->getArray($sql);
        return $array[0]['title'];
    }

    function getGroupUsers($groupname) {
        $sql = "SELECT * FROM tbl_oer_userextra WHERE groupmembership = '$groupname'";
        return $this->getArray($sql);
    }

    //To get adapted product co ordinates
    function getAdaptedProductLat($productid) {
        $sql = "SELECT * FROM tbl_oer_product_adaptation_data  WHERE product_id='$productid'";
        $array = $this->getArray($sql);
        $groupid = $array[0]['group_id'];
        return $this->getGroupLatitude($groupid);
    }

    function getAdaptedProductLon($productid) {
        $sql = "SELECT * FROM tbl_oer_product_adaptation_data  WHERE product_id='$productid'";
        $array = $this->getArray($sql);
        $groupid = $array[0]['group_id'];
        return $this->getGroupLongitude($groupid);
    }

    function getLastInsertId() {
        $array = $this->getLastEntry(Null, $orderField = 'id');
        return $array[0]['id'];
    }

    function getGroupInstitutions($id) {
        $sql = "SELECT * FROM tbl_oer_group_institutions WHERE group_id='$id'";
        return $this->getArray($sql);
    }

    function getInstitutions($id) {
        $arrayInstitutions = array();
        $sql = "SELECT * FROM tbl_oer_group_institutions WHERE group_id='$id' AND institution_id IS NOT NULL";
        $institutions = $this->getArray($sql);
        foreach ($institutions as $institution) {

            array_push($arrayInstitutions, $institution['institution_id']);
        }
        return $arrayInstitutions;
    }

    function getInstitution($id) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE id ='$id'";
        return $this->getArray($sql);
    }

    function getLastEntry() {
        $sql = "SELECT id FROM tbl_oer_institutions order by id desc limit 1";
        return $this->getArray($sql);
    }

    function getInstitutionThumbnail($institutionid) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE id ='$institutionid'";
        $institutionarray = $this->getArray($sql);
        return $institutionarray[0]['thumbnail'];
    }

    function getInstitutionName($institutionid) {
        $sql = "SELECT * FROM tbl_oer_institutions WHERE id ='$institutionid'";
        $institutionarray = $this->getArray($sql);
        return $institutionarray[0]['name'];
    }

    function getNoOfInstitutions($id) {
        $sql = "SELECT * FROM tbl_oer_group_institutions WHERE group_id='$id' AND institution_id IS NOT NULL";
        $institutions = $this->getArray($sql);
        return count($institutions);
    }

    function getGroupOwner($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups WHERE id='$groupid'";
        $array = $this->getArray($sql);
        return $array[0]['admin'];
    }

    function storegroupinstitution($groupid, $institutionid) {
        $data = array(
            'group_id' => $groupid,
            'institution_id' => $institutionid
        );
        $this->insert($data);
    }

    function getWebsite($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups where id = '$groupid'";
        $website = $this->getArray($sql);
        return $website[0]['website'];
    }

    function getDescription_Line_one($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups where id = '$groupid'";
        $array = $this->getArray($sql);
        return $array[0]['description_one'];
    }

    function getDescription_Line_two($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups where id = '$groupid'";
        $array = $this->getArray($sql);
        return $array[0]['description_two'];
    }

    function getDescription_Line_three($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups where id = '$groupid'";
        $array = $this->getArray($sql);
        return $array[0]['description_three'];
    }

    function getDescription_Line_four($groupid) {
        $sql = "SELECT * FROM tbl_oer_groups where id = '$groupid'";
        $array = $this->getArray($sql);
        return $array[0]['description_four'];
    }

    function getForum($groupid) {
        $sql =
                "select * from tbl_forum where forum_workgroup = '$groupid'";

        $array = $this->getArray($sql);
        return $array[0];
    }

    //boolean check if a group has a forum
    function forumExist($groupid) {
        $sql = "select * from tbl_forum where forum_workgroup = '$groupid'";
        $array = $this->getArray($sql);
        if (empty($array)) {
            return TRUE;
        } else {
            return False;
        }
    }

    function getGroup_Pkid_in_forum($groupid) {
        $sql = "select * from tbl_forum where forum_workgroup = '$groupid'";
        $array = $this->getArray($sql);
        return $array[0]['id'];
    }

    function getNumberPost($topicid) {
        $sql = "select * from tbl_forum_topic where id ='$topicid'";
        $array = $this->getArray($sql);
        return $array[0]['replies'];
    }

    function saveSubGroup($name, $website, $description, $brief_description, $interest, $parent_id) {
        $data = array(
            'name' => $name,
            'email' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'postalcode' => '',
            'website' => $website,
            'LinkedInstitution' => '',
            'loclat' => '',
            'loclong' => '',
            'description' => $description,
            'admin' => $admin,
            'thumbnail' => '',
            'description_one' => $brief_description,
            'description_two' => '',
            'description_three' => '',
            'description_four' => '',
            'interests' => $interest,
            'parent_id' => $parent_id
        );
        return $this->insert($data);
    }

    /**
     * THIS IS JUSTS A CRAP DESIGN
     * @param type $groupid
     * @return type 
     */
    function getGroupSubgroup($groupid) {
        $sql = "select * from tbl_oer_groups where parent_id='$groupid'";
        $array = $this->getArray($sql);
        return $array;
    }

}

?>