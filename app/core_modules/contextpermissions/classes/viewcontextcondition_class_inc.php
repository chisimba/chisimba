<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextpermissions
* @subpackage access
* @version 0.1
* @since 04 Febuary 2005
* @author Jonathan Abrahams
* @filesource
*/
// Inheret methods from Conditions
$this->loadClass( 'viewcondition', 'decisiontable');
/**
 * Class used for viewing a list of conditions of type context.
 *
 * @package contextpermissions
 * @category access
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 *
 * @access public
 * @author Jonathan Abrahams
 */
class viewContextCondition extends viewCondition
{
    /**
     * The object initialisation method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return nothing
     */
    function init()
    {
        parent::init();
        // Callback methods available
        $this->_methods[] = 'isAdmin';
        $this->_methods[] = 'dbFieldCheck';
        $this->_methods[] = 'dependsOnContext';
        $this->_methods[] = 'isContextMember';
        $this->_methods[] = 'isMember';
        $this->_methods[] = 'hasPermission';
        $this->_methods[] = 'hasContextPermission';
        $this->objLanguage = $this->getObject( 'language', 'language');

    }
    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array
     */
    function isAdmin()
    {
        $lblMustBeAdmin = $this->objLanguage->languageText('mod_contextpermissions_lblMustBeAdmin',"[The user must be and administrator.]");
        $lblIsAdmin = $this->objLanguage->languageText('mod_contextpermissions_lblIsAdmin',"[Is Administrator]");
        
        $element = "<input type='hidden' name='value' value='isAdmin'>$lblMustBeAdmin";
        $lblName = $lblIsAdmin;
        return array('lblName'=>$lblName,'element'=>$element);
    }

    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @author Jonathan Abrahams
     * @return array
     */
     function dependsOnContext($dependOn='TRUE')
     {
        $objRadio = $this->newObject('radio','htmlelements');
        $function = 'dependsOnContext | ';
        $objRadio->radio('value');
        $objRadio->addOption($function.'TRUE','TRUE');
        $objRadio->addOption($function.'FALSE','FALSE');
        $selected = $function.$dependOn;
        $objRadio->setSelected( $selected );

        $lblDependsOnContext = $this->objLanguage->code2Txt('mod_contextpermissions_lblDependsOnContext');
        $objLabel = &$this->getObject('label', 'htmlelements');
        $objLabel->label( $lblDependsOnContext, 'input_value' );
        $lblName = $objLabel->show();

        return array('lblName'=>$lblName,'element'=>$objRadio->show());
     }

    /**
     * CallBack method used by the evaluate method.
     *
     * @access public
     * @param string Group name relative to the context.
     * @author Jonathan Abrahams
     * @return true|false
     */
     function isContextMember($relPath=NULL)
     {
        $this->loadClass('dropdown','htmlelements');
        $objDropDown = new dropdown('ddbContext');
        $objDropDown->dropdown('value');
        $options = array('Lecturers','Students','Guest');
        $lblSelectContextGroup = $this->objLanguage->code2Txt('mod_contextpermissions_lblSelectContextGroup');
        $objDropDown->addOption( 'isContextMember', $lblSelectContextGroup );
        // Get root path of context
        foreach( $options as $groupName ){
            $objDropDown->addOption("isContextMember | ".$groupName,$groupName);
        }
        $objDropDown->setSelected( 'isContextMember | '.$relPath );
       
        $lblRelativeContextPath = $this->objLanguage->code2Txt('mod_contextpermissions_lblRelativeContextPath');
        $objLabel = &$this->getObject('label', 'htmlelements');
        $objLabel->label( $lblRelativeContextPath, 'input_value' );
        $lblName = $objLabel->show();        
        
        return array('lblName'=>$lblName,'element'=>$objDropDown->show());
     }

    /**
     * CallBack method to evaluate the value parameter for groups.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Full path to the group seperated by a delimiter.
     * @return true|false Returns result of the evaluation.
     * @version V0.1
     */
    function isMember($absPath=NULL)
    {
        $function = 'isMember | ';
        // Groups dropdown selection
        $objGroups = &$this->getObject('groupadminmodel','groupadmin');
        $objDropDown =  &$this->getObject( 'dropdown','htmlelements');
        $objDropDown->dropdown('value');

        $lblSelectGroup = $this->objLanguage->languageText('mod_contextpermissions_lblSelectGroup',"[-- Select a group --]");
        $objDropDown->addOption( 'isMember', $lblSelectGroup );
        foreach( $objGroups->getGroups(array('id')) as $row ){
            $currAbsPath = $objGroups->getFullPath($row['id']);
            $objDropDown->addOption( $function.$currAbsPath, $currAbsPath );
        }
        $objDropDown->setSelected( $function.$absPath );

        $lblAbsolutePath = $this->objLanguage->languageText('mod_contextpermissions_lblAbsolutePath',"[Absolute group path: ]");
        $objLabel = &$this->getObject('label', 'htmlelements');
        $objLabel->label( $lblAbsolutePath, 'input_value' );
        $lblName = $objLabel->show();

        return array('lblName'=>$lblName,'element'=>$objDropDown->show() );
    }

    /**
     * Callback method to evaluate the value parameter for permissions.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Access control list reference name.
     * @return true|false Returns result of the evaluation.
     * @version V0.1
     */
    function hasPermission($aclName=NULL)
    {
        $function = 'hasPermission | ';
        // Permissions dropdown selection
        $objPerms = &$this->getObject('permissions_model','permissions');
        $objDropDown =  $this->newObject( 'dropdown','htmlelements');
        $objDropDown->dropdown('value');

        $lblSelectACL = $this->objLanguage->languageText('mod_contextpermissions_lblSelectACL',"[-- Select an access control list --]");
        $objDropDown->addOption( 'hasPermission', $lblSelectACL );
        // Get root path of context
        foreach( $objPerms->getAcls( array('id','name','description') ) as $acl ){
            extract( $acl );
            $objDropDown->addOption($function.$name,$name);
        }
        $objDropDown->setSelected( $function.$aclName );

        $lblACL = $this->objLanguage->languageText('mod_contextpermissions_lblACL',"[Access control list: ]");
        $objLabel = &$this->getObject('label', 'htmlelements');
        $objLabel->label( $lblACL, 'input_value' );
        $lblName = $objLabel->show();
        
        return array('lblName'=>$lblName,'element'=>$objDropDown->show());
    }

    /**
     * Callback method to evaluate the value parameter for permissions.
     *
     * @access public
     * @author Jonathan Abrahams
     * @param string Access control list reference name.
     * @return true|false Returns result of the evaluation.
     * @version V0.1
     */
    function hasContextPermission($aclName=NULL)
    {
        $function = 'hasContextPermission | ';
        $objDropDown =  $this->newObject( 'dropdown','htmlelements');
        $objDropDown->dropdown('value');
        
        $lblSelectContextACL = $this->objLanguage->languageText('mod_contextpermissions_lblSelectContextACL',"[-- Select an access control list for the context--]");
        $objDropDown->addOption( 'hasContextPermission', $lblSelectContextACL );
        $options = array('isAuthor','isEditor','isReader');
        // Get context permissions
        foreach( $options as $aclName ){
            $objDropDown->addOption($function.$aclName,$aclName);
        }
        $objDropDown->setSelected( $function.$aclName );

        $lblACL = $this->objLanguage->languageText('mod_contextpermissions_lblACL',"[Access control list: ]");
        $objLabel = &$this->getObject('label', 'htmlelements');
        $objLabel->label( $lblACL, 'input_value' );
        $lblName = $objLabel->show();
        
        return array('lblName'=>$lblName,'element'=>$objDropDown->show());
    }
} /* end of class contextCondition */
?>
