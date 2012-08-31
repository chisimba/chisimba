<?php

/**
 * Edit grid class
 * 
 * Chisimba edit grid class for context permissions
 * 
 * PHP versions 4 and 5
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
 * @version   $Id$
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

$this->loadClass( 'viewgrid','contextpermissions' );

/**
 * Edit grid class
 * 
 * Chisimba edit grid class for context permissions
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
class editgrid extends viewgrid {

    /**
     * Description for var
     * @var    string
     * @access public
     */
    var $name = '';

    /**
     * Description for var
     * @var    string
     * @access public
     */
    var $actionCondition = 'condition';

    /**
     * Description for var
     * @var    string
     * @access public
     */
    var $actionRule = 'rule';

    /**
     * Description for var
     * @var    string
     * @access public
     */
    var $actionAction = 'action';
    /**
     * Method to show the X on the grid.
     *
     * @param  object The object action or rule used as the lookup.
     * @param  string The id of the rule or condition to be found.
     * @access public
     * @author Jonathan Abrahams
     * @return Show   'X' if TRUE , otherwise '-'
     */
    function showX( $objLookup, $id )
    {
        $isFound = $objLookup->hasID($id);
        if( $isFound ) {
            $objFound = $objLookup->getID($id);
            $isValid = $objFound->isValid();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return object Return description (if any) ...
     * @access public
     */
    function show()
    {
        
        $show = parent::show();
        $objForm = new form('form1');
        $objForm->action = $this->uri( array('action'=>'edit_main') );
        $objForm->displayType = 3;
        $objForm->addToForm( $show );
        $objForm->addToForm( "<input type='hidden' name='button' value=''>");
        $objForm->addToForm( "<input type='hidden' name='class' value=''>");
        $objForm->addToForm( "<input type='hidden' name='id' value=''>");
        return $objForm->show();

    }
    /**
    * Method to create a link.
    * @param  string The action, rule, or condition name.
    * @param  string The action to perform.
    * @param  string The reference id for the object.
    * @return string The HTML link element.
    */
    function lnkText($objLink, $action, $id )
    {
        $lnkText = parent::lnkText( $objLink, $action, $id );
        if( $id == $this->name && $action == $this->getParam('class') ) {
            $bodyParams = "onload=\"document.form1['objName'].focus();document.form1['objName'].select();\"";
            $this->objEngine->setVar( 'bodyParams', $bodyParams );

            $tinName = new textinput( 'objName', $objLink->_name );
            $tinName->extra = "onDblClick =\"javascript: document.form1['objName'].value='';\"";
            $tinName->extra.= 'onKeyPress ="javascript: if( event.keyCode==13){ ';
            $tinName->extra.= "document.form1['button'].value='save';";
            $tinName->extra.= "document.form1['class'].value='$action';";
            $tinName->extra.= "document.form1['id'].value='$id';";
            $tinName->extra.= 'document.form1.submit();}"';
            if( isset( $objLink->_params ) ) {
                $tinName->extra.= " title = '{$objLink->_params}'";
            }

            $lnkText = $tinName->show();
            if( get_class( $objLink ) == 'condition' ) {
                $lnkEdit = $this->newObject( 'link', 'htmlelements' );
                $lnkEdit->href = '#';
                $lnkEdit->link = 'Edit';
                $lnkEdit->extra = 'onClick ="javascript:';
                $lnkEdit->extra.= "document.form1['button'].value='edit';";
                $lnkEdit->extra.= "document.form1['class'].value='$action';";
                $lnkEdit->extra.= "document.form1['id'].value='$id';";
                $lnkEdit->extra.= 'document.form1.submit();"';
            }
            $lnkSave = $this->newObject( 'link', 'htmlelements' );
            $lnkSave->href = '#';
            $lnkSave->link = 'Save';
            $lnkSave->extra = 'onClick ="javascript:';
            $lnkSave->extra.= "document.form1['button'].value='save';";
            $lnkSave->extra.= "document.form1['class'].value='$action';";
            $lnkSave->extra.= "document.form1['id'].value='$id';";
            $lnkSave->extra.= 'document.form1.submit();"';

            $lnkCancel = $this->newObject( 'link', 'htmlelements' );
            $lnkCancel->href = '#';
            $lnkCancel->link = 'Cancel';
            $lnkCancel->extra = 'onClick ="javascript:';
            $lnkCancel->extra.= 'document.form1.submit();"';
            $lnkDelete = $this->newObject( 'link', 'htmlelements' );
            $lnkDelete->href = '#';
            $lnkDelete->link = 'Delete';
            $lnkDelete->extra = 'onClick ="javascript:';
            $lnkDelete->extra.= "document.form1['button'].value='delete';";
            $lnkDelete->extra.= "document.form1['class'].value='$action';";
            $lnkDelete->extra.= "document.form1['id'].value='$id';";
            $lnkDelete->extra.= 'document.form1.submit();"';

            $show = '<TABLE width=100%><TR align=center><TD>'.$lnkText.'</TD></TR>';
            $show.= '<TR align=center><TD>';
            $show.= $lnkSave->show().' / '.$lnkCancel->show().' / '.$lnkDelete->show();
            $show.= isset( $lnkEdit ) ? ' / '.$lnkEdit->show() : NULL;
            $show.= '</TD></TR></TABLE>';
            return $show;
        } else {

            return $lnkText;
        }
        
    }

    /**
     * Method to add a grid cell.
     *
     * @param  object  The object action or rule used as the lookup.
     * @param  string  The id of the rule or condition to be found.
     * @param  string  The class for the row.
     *                     
     * @access public 
     * @author Jonathan Abrahams
     * @return nothing Inserts new cell into the table.
     */
    function addGridCell( $objLookup, $id, $class )
    {
        extract( $this->_properties );
        $X = $this->showX( $objLookup, $id );

        $condRule = $this->class == 'condition_rule' && $objLookup->_name == $this->name;
        $actRule = $this->class == 'action_rule' && $id == $this->name;
        $cond = $this->class == 'condition' && $id == $this->name;
        $act  = $this->class == 'action' && $objLookup->_name == $this->name;
        
        if( $cond || $act || $actRule || $condRule )  {
            $checkbox = new checkbox( "List[".get_class($objLookup)."][$objLookup->_name][$id]" );
            $checkbox->setChecked( $X );
            $X = $checkbox->show();
        } else {
            $X = $X ? "X" : "-";
        }
        $this->objGrid->addCell(
            $X,
            $colWidth, NULL , 'center', $class );
    }

}
?>