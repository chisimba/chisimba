<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package MODULE NAME
* @subpackage CLASS CATEGORY
* @version 0.1
* @since DD MMMM YYYY
* @author Jonathan Abrahams
* @filesource
*/
$this->loadClass( 'viewgrid','contextpermissions' );
class editgrid extends viewgrid {
    var $name = '';
    var $actionCondition = 'condition';
    var $actionRule = 'rule';
    var $actionAction = 'action';
    /**
     * Method to show the X on the grid.
     *
     * @param object The object action or rule used as the lookup.
     * @param string The id of the rule or condition to be found.
     * @access public
     * @author Jonathan Abrahams
     * @return Show 'X' if TRUE , otherwise '-'
     */
    function showX( &$objLookup, $id )
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
    * @param string The action, rule, or condition name.
    * @param string The action to perform.
    * @param string The reference id for the object.
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
                $lnkEdit = &$this->newObject( 'link', 'htmlelements' );
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
     * @param object The object action or rule used as the lookup.
     * @param string The id of the rule or condition to be found.
     * @param string The class for the row.
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
