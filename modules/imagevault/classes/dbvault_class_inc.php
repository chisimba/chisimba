<?php
/**
 *
 * Database access for ImageVault
 *
 * Database access for Image Vault. It allow access to the table
 *  which contains a list of posted images for the gallery.
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
 * @category  Chisimba
 * @package   imagevault
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 AVOIR
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
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
 *
 * Database access for ImageVault
 *
 * Database access for Simple gallery. It allow access to the table
 *  which contains a list of posted images for the gallery.
 *
 * @package   imagevault
 * @author    Paul Scott <pscott@uwc.ac.za>
 *
 */
class dbvault extends dbtable
{

    /**
     *
     * Intialiser for the simpleblog database connector
     * @access public
     * @return VOID
     *
     */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_imagevault_images');
    }     
     
     /**
      * Method to insert a post to your posts table
      *
      * @param  integer $userid
      * @param  array   $postarr
      * @param  string  $mode
      * @return array
      */
    public function insertImage($userid, $insarr)
    {   
        $this->changeTable('tbl_imagevault_images');
        $insarr['dateuploaded'] = $this->now();
        $imgid = $this->insert($insarr);
        return $imgid;
    }
    
    public function retrieveData($userid)
    {
        return $this->getAll("WHERE userid = '$userid'");
    }
    
    public function insertMeta($metadata) {
        $this->changeTable('tbl_imagevault_meta');
        $metadata['modificationdatetime'] = $this->now();
        return $this->insert($metadata);
    }
    
    public function getLicense($userid) {
        $this->changeTable('tbl_imagevault_license');
        $data = $this->getAll("WHERE userid = '$userid'");
        if(!empty($data)) {
            return $data['license'];
        }
        else {
            return FALSE;
        }
    }
    
    public function updateImageWithMetaId($recordid, $metaid) {
        $this->changeTable('tbl_imagevault_images');
        $this->update('id', $recordid, array('metadataid' => $metaid), 'tbl_imagevault_images');
    }
    
    public function insertKeywords($kinsarr) {
        $this->changeTable('tbl_imagevault_meta_keywords');
        $kinsarr['datecreated'] = $this->now();
        return $this->insert($kinsarr);
    }
    
    public function insertFileData($insarr) {
        $this->changeTable('tbl_imagevault_meta_file');
        return $this->insert($insarr);
    }
    
    public function insertComputed($insarr) {
        $this->changeTable('tbl_imagevault_meta_computed');
        return $this->insert($insarr);
    }
    
    public function insertGPS($insarr) {
        $this->changeTable('tbl_imagevault_meta_gps');
        return $this->insert($insarr);
    }
    
    public function insertIFD0Data($insarr) {
        $this->changeTable('tbl_imagevault_meta_ifd0');
        return $this->insert($insarr);
    }
    
    public function insertIFD1Data($insarr) {
        $this->changeTable('tbl_imagevault_meta_ifd1');
        return $this->insert($insarr);
    }
    
    public function insertSubIFDData($insarr) {
        $this->changeTable('tbl_imagevault_meta_subifd');
        return $this->insert($insarr);
    }
    
    public function insertThumbData($insarr) {
        $this->changeTable('tbl_imagevault_meta_thumbnail');
        return $this->insert($insarr);
    }
    
    public function insertExifData($insarr) {
        $this->changeTable('tbl_imagevault_meta_exif');
        return $this->insert($insarr);
    }
    
    public function changeTable($table) {
        return parent::init($table);
    }
    
    
}
?>
