<?php
/**
 * View class for the groupadmin members
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
 * @package   groupadmin
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * View class for the groupadmin members
 *
 * @copyright  (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
 * @package    groupadmin
 * @subpackage view
 * @version    0.1
 * @since      22 November 2004
 * @author     Paul Scott based on methods by Jonathan Abrahams
 * @filesource
 */

class groupadmin_members extends object {
    /**
     * object reference to the groupadmin
     *
     * @var    groupadmin an
     * @access private   
     */
    private $_objGroupAdmin;

    /**
     * an array to store the group members
     *
     * @var    array  
     * @access private
     */
    private $_groupDirectMembers;

    /**
     * an array to store the all group members
     *
     * @var    array  
     * @access private
     */
    private $_groupSubMembers;

    /**
     * an association to the userDb object.
     *
     * @var    userDb  $_objUsers
     * @access private
     */
    private $_tableHeaders;

    /**
     * Method to initialize the groupadmin_members object.
     *
     * @access public
     * @param  void  
     * @return void  
     */
    public function init(){
        $this->_objGroupAdmin = $this->newObject('groupAdminModel', 'groupadmin');
        $_groupDirectMembers = array();
        $_groupSubMembers = array();
        $_tableHeaders = array();
    }

    /**
     * Method to get the groupadmin object
     *
     * @access public
     * @param  void  
     * @return void  
     */
    public function & groupadmin() {
        return $this->_objGroupAdmin;
    }

    /**
    * Private method to construct a sorted table.
    *
    * It makes use of the Javascript 'sorttable.js' resource
    *
    * @param  array   an array containg the row data
    * @return string  DHTML sortable table
    * @access private
    */
    private function _sortedTable( $list, $table = 't' ) {

    	$myTable =  $this->newObject('htmltable', 'htmlelements');
    	$myTable->width='60%';
        $myTable->border='0';
        $myTable->cellspacing='1';
        $myTable->cellpadding='10';
    	//JS initialization started
        $lsbList = '<script language="javascript" type="text/javascript">
           //<![CDATA[
                      ';
        $lsbList.= "var $table = new SortTable('$table');";

        // Construct the table columns and header
        $myTable->startHeaderRow();

  
        foreach( $this->_tableHeaders as $hdr ){
            $lsbList.= "$table.AddColumn('.$hdr.','','','');";
            $myTable->addHeaderCell($hdr);
        }
 		$myTable->endHeaderRow();
 		
        // Construct the table rows
        $rowcount = 0;
        foreach( $list as $item) {
        	$oddOrEven = ($rowcount == 0) ? "even" : "odd";
            // Row data added
            $firstName = '"'.$item["firstname"].'"';
            $surname   = '"'.$item["surname"].'"';
            $lsbList.= "$table.AddLine($firstName, $surname);";

            // Rows should alternate odd/even
            
            //$lsbList.= "$table.AddLineProperties('class=\"$oddeven\"');";
            $myTable->startRow();
	        $myTable->addCell($item["firstname"],null,null,null,$oddOrEven);
	        $myTable->addCell($item["surname"],null,null,null,$oddOrEven);
	        $myTable->endRow();
	        
	        $rowcount = ($rowcount == 0) ? 1 : 0;
        }

        $lsbList.= '
        //]]>
       </script>';
        // JS initialization done!

        // HTML Table construction
        $lsbList.= '<TABLE width="99%" border="0">';
        $lsbList.= '<THEAD><TR>';

        // Active links on each header for sorting.
        foreach( $this->_tableHeaders as $key=>$hdr ){
            $lsbList.= "<TH width=\"50%\"><A href=\"javascript:SortRows($table,'$key')\">$hdr</A></TH>";
        }

        $lsbList.= '</TR></THEAD><TBODY>';

        // JS can now generate the sorted table
        $lsbList.= "<script language=\"javascript\" type=\"text/javascript\">
       //<![CDATA[
         $table.WriteRows()
        //]]>
         </script>";
        $lsbList.= '</TBODY></TABLE>';
        // HTML TABLE construction done!

        // Return completed HTML sortable table
        //return $lsbList;
        return $myTable->show();
        
    	//$featureBox = & $this->newObject('featurebox', 'navigation');
    	
    	return 'SORT TABLE DOES  NOT WORK IN CHISIMBA';
    }

    /**
     * Method to set the selected groupId. ( Required )
     *
     * @access public
     * @param  string the unique ID of an existing group.
     * @return void  
     */
    public function setGroupId( $groupId ) {
        $groupadmin =& $this->groupadmin();
        $this->_groupDirectMembers = $groupadmin->getGroupUsers( $groupId, array('firstName', 'surname') );
        $this->_groupSubMembers = $groupadmin->getSubGroupUsers( $groupId, array('firstName', 'surname') );
      
    }

    /**
     * Method to set the table header. ( Required )
     *
     * @access public
     * @param  array  an array containg the header data
     * @return void  
     */
    public function setHeaders( $headers ) {
        $this->_tableHeaders = $headers ;
    }

    /**
     * Method to show the sorted table.
     *
     * @access public
     * @param  string $table default t
     * @return string DHTML sortable table ready to be showed
     */
    public function show( $table = 't' ){
        return $this->_sortedTable( $this->_groupSubMembers, $table );
    }

    /**
     * Method to get the count of members in the selected folder.
     *
     * @access public
     * @param  void  
     * @return string count of members in selected group.
     */
    public function getFolderCount( ) {
        return count ( $this->_groupDirectMembers );
    }

    /**
     * Method to get the count of all members in the selected folder and subfolders.
     *
     * @access public
     * @param  void  
     * @return string count of all members in selected group.
     */
    public function getTotalCount( ) {
        return count ( $this->_groupSubMembers );
    }

}
?>
