<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package groupadmin
* @subpackage view
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams
* @filesource
*/
/**
* View class for the groupadmin members
* @package groupadmin
* @author Jonathan Abrahams
*/
class groupadmin_members extends object {
    /**
    * @var groupadmin an object reference to the groupadmin
    * @access private
    */
    var $_objGroupAdmin;

    /**
    * @var array an array to store the group members
    * @access private
    */
    var $_groupDirectMembers;

    /**
    * @var array an array to store the all group members
    * @access private
    */
    var $_groupSubMembers;

    /**
    * @var userDb $_objUsers an association to the userDb object.
    * @access private
    */
    var $_tableHeaders;

    /**
    * Method to initialize the groupadmin_members object.
    */
    function init(){
        $this->_objGroupAdmin =& $this->getObject('groupAdminModel', 'groupadmin');
        $_groupDirectMembers = array();
        $_groupSubMembers = array();
        $_tableHeaders = array();
    }
    
    /**
    * Method to get the groupadmin object
    */
    function & groupadmin() {
        return $this->_objGroupAdmin;
    }

    /**
    * Private method to construct a sorted table.
    *
    * It makes use of the Javascrip 'sorttable.js' resource
    *
    * @param array an array containg the row data
    * @return string DHTML sortable table
    * @access private
    */
    function _sortedTable( $list, $table = 't' ) {
        //JS initialization started
        $lsbList = '<SCRIPT language=JavaScript>';
        $lsbList.= "var $table = new SortTable('$table');";
        
        // Construct the table columns and header
        foreach( $this->_tableHeaders as $hdr ){
            $lsbList.= "$table.AddColumn('.$hdr.','','','');";
        }

        // Construct the table rows
        $oddeven = 'even';
        foreach( $list as $item) {
            // Row data added
            $firstName = '"'.$item["firstName"].'"';
            $surname   = '"'.$item["surname"].'"';
            $lsbList.= "$table.AddLine($firstName, $surname);";

            // Rows should alternate odd/even
            $oddeven = ($oddeven=='odd') ? 'even' : 'odd';
            $lsbList.= "$table.AddLineProperties('class=\"$oddeven\"');";
        }

        $lsbList.= '</SCRIPT>';
        // JS initialization done!
        
        // HTML Table construction
        $lsbList.= '<TABLE width=99% border=0>';
        $lsbList.= '<THEAD><TR>';
        
        // Active links on each header for sorting.
        foreach( $this->_tableHeaders as $key=>$hdr ){
            $lsbList.= "<TH width=\"50%\"><A href=\"javascript:SortRows($table,'$key')\">$hdr</A></TH>";
        }
        
        $lsbList.= '</TR></THEAD><TBODY>';

        // JS can now generate the sorted table
        $lsbList.= "<SCRIPT>$table.WriteRows()</SCRIPT>";
        $lsbList.= '</TBODY></TABLE>';
        // HTML TABLE construction done!
        
        // Return completed HTML sortable table
        return $lsbList;
    }

    /**
    * Method to set the selected groupId. ( Required )
    *
    * @param string the unique ID of an existing group.
    */
    function setGroupId( $groupId ) {
        $groupadmin =& $this->groupadmin();
        $this->_groupDirectMembers = $groupadmin->getGroupUsers( $groupId, array('firstName', 'surname') );
        $this->_groupSubMembers = $groupadmin->getSubGroupUsers( $groupId, array('firstName', 'surname') );
    }

    /**
    * Method to set the table header. ( Required )
    *
    * @param array an array containg the header data
    */
    function setHeaders( $headers ) {
        $this->_tableHeaders = $headers ;
    }

    /**
    * Method to show the sorted table.
    *
    * @return string DHTML sortable table ready to be showed
    */
    function show( $table = 't' ){
        return $this->_sortedTable( $this->_groupSubMembers, $table );
    }
    
    /**
    * Method to get the count of members in the selected folder.
    *
    * @return string count of members in selected group.
    */
    function getFolderCount( ) {
        return count ( $this->_groupDirectMembers );
    }

    /**
    * Method to get the count of all members in the selected folder and subfolders.
    *
    * @return string count of all members in selected group.
    */
    function getTotalCount( ) {
        return count ( $this->_groupSubMembers );
    }

}
