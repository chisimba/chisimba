<?php

/**
 * condition view
 * 
 * View context conditions
 * 
 * PHP version 3
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
 * @package   contextpermissions
 * @author    Jonathan Abrahams <jabrahams@uwc.ac.za>
 * @copyright 2007 Jonathan Abrahams
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

$this->loadClass( 'viewcondition', 'decisiontable');

/**
 * condition view
 * 
 * View context conditions
 * 
 * @category  Chisimba
 * @package   contextpermissions
 * @author    Jonathan Abrahams <jabrahams@uwc.ac.za>
 * @copyright 2007 Jonathan Abrahams
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
        
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');

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
        $lblMustBeAdmin = $this->objLanguage->languageText('mod_contextpermissions_lblMustBeAdmin','contextpermissions',"[The user must be and administrator.]");
        $lblIsAdmin = $this->objLanguage->languageText('mod_contextpermissions_lblIsAdmin','contextpermissions',"[Is Administrator]");
        
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
        $function = 'dependsOnContext | ';
        $objRadio = new radio('value');
        $objRadio->addOption($function.'TRUE','TRUE');
        $objRadio->addOption($function.'FALSE','FALSE');
        $selected = $function.$dependOn;
        $objRadio->setSelected( $selected );

        $lblDependsOnContext = $this->objLanguage->code2Txt('mod_contextpermissions_lblDependsOnContext','contextpermissions');
        $objLabel = new label( $lblDependsOnContext, 'input_value' );
        $lblName = $objLabel->show();

        return array('lblName'=>$lblName,'element'=>$objRadio->show());
     }

    /**
     * CallBack method used by the evaluate method.
     *
     * @access public    
     * @param  string     Group name relative to the context.
     * @author Jonathan Abrahams
     * @return true|false
     */
     function isContextMember($relPath=NULL)
     {
        $objDropDown = new dropdown('value');
        $options = array('Lecturers','Students','Guest');
        $lblSelectContextGroup = $this->objLanguage->code2Txt('mod_contextpermissions_lblSelectContextGroup','contextpermissions');
        $objDropDown->addOption( 'isContextMember', $lblSelectContextGroup );
        // Get root path of context
        foreach( $options as $groupName ){
            $objDropDown->addOption("isContextMember | ".$groupName,$groupName);
        }
        $objDropDown->setSelected( 'isContextMember | '.$relPath );
       
        $lblRelativeContextPath = $this->objLanguage->code2Txt('mod_contextpermissions_lblRelativeContextPath','contextpermissions');
        $objLabel = new label( $lblRelativeContextPath, 'input_value' );
        $lblName = $objLabel->show();        
        
        return array('lblName'=>$lblName,'element'=>$objDropDown->show());
     }

    /**
     * CallBack method to evaluate the value parameter for groups.
     *
     * @access  public    
     * @author  Jonathan Abrahams
     * @param   string     Full path to the group seperated by a delimiter.
     * @return  true|false Returns result of the evaluation.
     * @version V0.1
     */
    function isMember($absPath=NULL)
    {
        $function = 'isMember | ';
        // Groups dropdown selection
        $objGroups = &$this->getObject('groupadminmodel','groupadmin');
        $objDropDown =  new dropdown('value');

        $lblSelectGroup = $this->objLanguage->languageText('mod_contextpermissions_lblSelectGroup','contextpermissions',"[-- Select a group --]");
        $objDropDown->addOption( 'isMember', $lblSelectGroup );
        foreach( $objGroups->getGroups(array('id')) as $row ){
            $currAbsPath = $objGroups->getFullPath($row['id']);
            $objDropDown->addOption( $function.$currAbsPath, $currAbsPath );
        }
        $objDropDown->setSelected( $function.$absPath );

        $lblAbsolutePath = $this->objLanguage->languageText('mod_contextpermissions_lblAbsolutePath','contextpermissions',"[Absolute group path: ]");
        $objLabel = new label( $lblAbsolutePath, 'input_value' );
        $lblName = $objLabel->show();

        return array('lblName'=>$lblName,'element'=>$objDropDown->show() );
    }

    /**
     * Callback method to evaluate the value parameter for permissions.
     *
     * @access  public    
     * @author  Jonathan Abrahams
     * @param   string     Access control list reference name.
     * @return  true|false Returns result of the evaluation.
     * @version V0.1
     */
    function hasPermission($aclName=NULL)
    {
        $function = 'hasPermission | ';
        // Permissions dropdown selection
        $objPerms = &$this->getObject('permissions_model','permissions');
        $objDropDown =  new dropdown('value');

        $lblSelectACL = $this->objLanguage->languageText('mod_contextpermissions_lblSelectACL','contextpermissions',"[-- Select an access control list --]");
        $objDropDown->addOption( 'hasPermission', $lblSelectACL );
        // Get root path of context
        foreach( $objPerms->getAcls( array('id','name','description') ) as $acl ){
            extract( $acl );
            $objDropDown->addOption($function.$name,$name);
        }
        $objDropDown->setSelected( $function.$aclName );

        $lblACL = $this->objLanguage->languageText('mod_contextpermissions_lblACL','contextpermissions',"[Access control list: ]");
        $objLabel = new label( $lblACL, 'input_value' );
        $lblName = $objLabel->show();
        
        return array('lblName'=>$lblName,'element'=>$objDropDown->show());
    }

    /**
     * Callback method to evaluate the value parameter for permissions.
     *
     * @access  public    
     * @author  Jonathan Abrahams
     * @param   string     Access control list reference name.
     * @return  true|false Returns result of the evaluation.
     * @version V0.1
     */
    function hasContextPermission($aclName=NULL)
    {
        $function = 'hasContextPermission | ';
        $objDropDown =  new dropdown('value');
        
        $lblSelectContextACL = $this->objLanguage->languageText('mod_contextpermissions_lblSelectContextACL','contextpermissions',"[-- Select an access control list for the context--]");
        $objDropDown->addOption( 'hasContextPermission', $lblSelectContextACL );
        $options = array('isAuthor','isEditor','isReader');
        // Get context permissions
        foreach( $options as $aclName ){
            $objDropDown->addOption($function.$aclName,$aclName);
        }
        $objDropDown->setSelected( $function.$aclName );

        $lblACL = $this->objLanguage->languageText('mod_contextpermissions_lblACL','contextpermissions',"[Access control list: ]");
        $objLabel = new label( $lblACL, 'input_value' );
        $lblName = $objLabel->show();
        
        return array('lblName'=>$lblName,'element'=>$objDropDown->show());
    }
} /* end of class contextCondition */
?>