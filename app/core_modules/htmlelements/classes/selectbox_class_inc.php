<?php
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

// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
* @copyright  (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package    htmlelements
* @subpackage view
* @version    0.1
* @since      28 February 2005
* @author     Jonathan Abrahams
* @filesource
*/
/**
* Class used to generate the select using javascript selectbox.js.
*
* Using the selectbox:
*   Example of code found in the module content template.
* <code>
*   $objForm = $this->newObject('form','htmlelements');
*   $objForm->name = "form1";
*   $objForm->action = $this->uri ( array( 'action' => 'processform' ) );
*
*   // Create the selectbox object
*   $objSelectBox = $this->newObject('selectbox','htmlelements');
*   // Initialise the selectbox.
*   $objSelectBox->create( $objForm, 'leftList[]', 'Left Header', 'rightList[]', 'Right Header' );
*
*   // Populate the selectboxes
*   $objData = &$this->getObject('data');
*   $objSelectBox->insertLeftOptions( $objData->getAll(), 'value', 'label' );
*   $objSelectBox->insertRightOptions( array() );
*
*   // Insert the selectbox into the form object.
*   $objForm->addToForm( $objSelectBox->show() );
*
*   // Get and insert the save and cancel form buttons
*   $arrFormButtons = $objSelectBox->getFormButtons();
*   $objForm->addToForm( implode( ' / ', $arrFormButtons ) );
*
*   // Show the form
*   $objForm->show();
* </code>
*
*   Now for the controllers dispatch action 'processform':
* <code>
*   // Test for the button parameter.
*   if( $this->getParam( 'button' ) == 'save' ) {
*       $rightData = $this->getParam( 'rightList' );
*       .. do the save action ..
*   } else if ( $this->getParam( 'button' ) == 'cancel' ) {
*       .. do the cancel action ..
*   }
* </code>
*/
class selectbox extends object implements ifhtml
{

    /**
    * @var dropdown Object reference to the left selectbox.
    */
    public $objLeftList;
    /**
    * @var dropdown Object reference to the right selectbox.
    */
    public $objRightList;
    /**
    * @var form Object reference to the form object containing the selectboxes.
    */
    public $objForm;
    /**
    * @var language Object reference to the Language object.
    */
    public $objLanguage;
    /**
    * @var array The Left and Right selectbox headers.
    */
    public $arrHeaders = array();
    /**
    * @var array All the button objects.
    */
    public $arrBtnObject = array();
    /**
    * @var array All the control button objects.
    */
    public $arrBtnCntrlObject = array();

    /**
    * Method to initialise the object
    */
    public function init()
    {
        // Insert the javascript into the header
        $this->appendArrayVar( 'headerParams', $this->getJavascriptFile('selectbox.js','htmlelements') );
        $this->objLanguage = $this->getObject('language', 'language');

        $this->loadClass('dropdown','htmlelements');
    }
    
    /**
    * Method to create a new Selectbox object.
    *
    * The form and dropdown objects are initialised.
    *
    * @param  form     The form object to connect to.
    * @param  dropdown The Left selectbox dropdown name.
    * @param  string   The Left selectbox header.
    * @param  dropdown The Right selectbox to connect to.
    * @param  string   The Right selectbox dropdown name.
    * @return nothing  Connect this object to the required objects.
    */
    public function create( &$objForm, $ddbLeftName, $tblLeftHeader, $ddbRightName, $tblRightHeader )
    {
        // Connection to external form.
        $this->objForm = &$objForm;
        
        // Create the left dropdown selectbox.
        $this->objLeftList = new dropdown($ddbLeftName);
        $this->arrHeaders['hdrLeft']= $tblLeftHeader;
        
        // Create the right dropdown selectbox.
        $this->objRightList = new dropdown($ddbRightName);
        $this->arrHeaders['hdrRight']= $tblRightHeader;
        
        // initialise the hidden form fields.
        $this->objForm->addToForm( "<input type='hidden' name='button' value='' />" );
        
        // configure the dropdown as select box.
        $this->objLeftList->extra = ' multiple="1" size="10" style="width:100pt;" ';
        $this->onDblClickParam( $this->objLeftList, $this->moveSelectedOptions( $this->objLeftList, $this->objRightList));

        // configure the dropdown as select box.
        $this->objRightList->extra = ' multiple="1" size="10" style="width:100pt;" ';
        $this->onDblClickParam( $this->objRightList, $this->moveSelectedOptions( $this->objRightList, $this->objLeftList ) );

        // Create the button objects as links.
        $params = array( 'href' => '#' );
        $this->setBtnObject( 'btnRight', $this->newObject('link','htmlelements'), $params );
        $this->setBtnObject( 'btnAllRight', $this->newObject('link','htmlelements'), $params );
        $this->setBtnObject( 'btnLeft', $this->newObject('link','htmlelements'), $params );
        $this->setBtnObject( 'btnAllLeft', $this->newObject('link','htmlelements'), $params );

        // Get the default labels
        $this->setBtnLabel( 'btnRight', htmlspecialchars('>>'), 'link' );
        $this->setBtnLabel( 'btnAllRight', htmlspecialchars('All >>'), 'link' );
        $this->setBtnLabel( 'btnLeft', htmlspecialchars('<<'), 'link' );
        $this->setBtnLabel( 'btnAllLeft', htmlspecialchars('All <<'), 'link' );

        // Set the onClick actions.
        $this->setBtnOnClick( 'btnRight', $this->moveSelectedOptions( $this->objLeftList, $this->objRightList ) );
        $this->setBtnOnClick( 'btnAllRight', $this->moveAllOptions( $this->objLeftList, $this->objRightList ) );
        $this->setBtnOnClick( 'btnLeft', $this->moveSelectedOptions( $this->objRightList, $this->objLeftList ) );
        $this->setBtnOnClick( 'btnAllLeft', $this->moveAllOptions( $this->objRightList, $this->objLeftList ) );

        // Create the control button objects as links
        $this->setBtnObject( 'btnSave', $this->newObject('link','htmlelements'), $params, 'arrBtnCntrlObject' );
        $this->setBtnObject( 'btnCancel', $this->newObject('link','htmlelements'), $params, 'arrBtnCntrlObject' );

        // Set the default control button labels
        $this->setBtnLabel( 'btnSave', $this->objLanguage->languageText( 'word_save' ), 'link', 'arrBtnCntrlObject' );
        $this->setBtnLabel( 'btnCancel', $this->objLanguage->languageText( 'word_cancel' ), 'link', 'arrBtnCntrlObject' );

        // Set the Control buttons onClick action.
        $this->setBtnOnClick('btnSave', $this->selectAllOptions( $this->objRightList ).$this->setFormButton('save').$this->submitForm(), 'arrBtnCntrlObject' );
        $this->setBtnOnClick('btnCancel', $this->setFormButton('cancel').$this->submitForm(), 'arrBtnCntrlObject' );
    }

    /**
    * Method to insert the options into the left select box.
    *
    * @param  array   The list of options in an associated array.
    * @param  string  The value field name in the associated array.
    * @param  string  The label field name in the associated array.
    * @return nothing AddOptions into the Left selectbox.
    */
    public function insertLeftOptions( $arrList=array(), $valueField='', $labelField='' )
    {
        foreach( $arrList as $option ){
            $this->objLeftList->addOption( $option[$valueField], $option[$labelField] );
        }
    }

    /**
    * Method to insert the options into the right select box.
    *
    * @param  array   The list of options in an associated array.
    * @param  string  The value field name in the associated array.
    * @param  string  The label field name in the associated array.
    * @return nothing AddOptions into the Right selectbox.
    */
    public function insertRightOptions( $arrList=array(), $valueField='', $labelField='' )
    {
        foreach( $arrList as $option ){
            $this->objRightList->addOption( $option[$valueField], $option[$labelField] );
        }
    }
    
    /**
    * Method to wrap the javascript function to move selected options.
    *
    * @param  dropdown The from dropdown object.
    * @param  dropdown The to dropdown object.
    * @return string   Parsed javascript moveSelectedOptions function.
    */
    public function moveSelectedOptions( &$from, &$to )
    {
        return sprintf("moveSelectedOptions(document.%s['%s'], document.%s['%s'],true);",
            $this->objForm->name, $from->name, $this->objForm->name, $to->name );
    }

    /**
    * Method to wrap the javascript function to move all options.
    *
    * @param  dropdown The from dropdown object.
    * @param  dropdown The to dropdown object.
    * @return string   Parsed javascript moveAllOptions function.
    */
    public function moveAllOptions( &$from, &$to )
    {
        return sprintf("moveAllOptions(document.%s['%s'], document.%s['%s'],true);",
            $this->objForm->name, $from->name, $this->objForm->name, $to->name );
    }

    /**
    * Method to wrap the javascript function to select all options.
    *
    * @param  dropdown The dropdown object to select from.
    * @return string   Parsed javascript selectAllOptions function.
    */
    public function selectAllOptions( &$object )
    {
        return sprintf("selectAllOptions(document.%s['%s']);",
            $this->objForm->name,
            $object->name);
    }
    
    /**
    * Method to set the forms hidden button value field.
    *
    * @param  string The value field new value.
    * @return string Parsed javascript to set the forms button value.
    */
    public function setFormButton( $value )
    {
        return sprintf("document.%s['%s'].value='%s';",
            $this->objForm->name, 'button', $value );
    }

    /**
    * Method to submit the form.
    *
    * @return string Parsed javascript to submit the form.
    */
    public function submitForm( )
    {
        return sprintf("document.%s.submit();", $this->objForm->name );
    }

    /**
    * Method to parse a htmlelement extra field with onDblClick.
    *
    * @param  object  The htmlelement object with extra param to set on double click
    * @param  string  The javascript to run on double click.
    * @return nothing Parse the extra params for the htmlelement with onDblClick.
    */
    public function onDblClickParam( &$object, $onDblClick )
    {
        $object->extra .= sprintf(' onDblClick="javascript: %s"', $onDblClick );
    }

    /**
    * Method to insert a selectbox into a table.
    * The individual selectbox table layout.
    *
    * @param  htmltable The table to insert the selectbox into.
    * @param  dropdown  The selectbox to be inserted.
    * @param  string    The header of the table.
    * @return nothing   Insert selectbox into a table.
    */
    public function selectBoxTable( &$table, &$selectBox, $header=NULL )
    {
        // Insert header if given
        if( $header ) {
            $table->startRow();
                $table->addCell($header, null, null, null, 'heading');
            $table->endRow();
        }
        // Insert the selectbox
        $table->startRow();
            $table->addCell($selectBox->show());
        $table->endRow();
    }
    
    /**
    * Method to layout the selectboxes into a table.
    *
    * @param  htmltable The table to layout the selectboxes.
    * @param  htmltable The left selectbox to be inserted.
    * @param  array     The header of the table.
    * @param  htmltable The right selectbox to be inserted.
    * @return nothing   Layout selectboxes into a table.
    */
    public function layoutTable( &$table, &$tblLeft, $arrButtons, &$tblRight )
    {
        // Insert left selectbox table, buttons, right selectbox table.
        $table->width = NULL;
        $table->startRow();
            $table->addCell( $tblLeft->show(), '100pt' );
            $table->addCell( implode( '<br />', $arrButtons), '100pt', 'center', 'center' );
            $table->addCell( $tblRight->show(), '100pt' );
        $table->endRow();
    }

    /**
    * Method to set the onClick param of the button objects.
    *
    * @param  string  The button name as an index into the array of button objects.
    * @param  string  The parsed javascript for the onClick action.
    * @param  string  The name of the array of object buttons ( either arrBtnObject or arrBtnCntrlObject )
    * @return nothing Set the extra parameter for the given button.
    */
    public function setBtnOnClick( $button, $onClick, $arrBtnObject='arrBtnObject' )
    {
        $this->{$arrBtnObject}[$button]->extra .= sprintf(' onClick ="javascript: %s"', $onClick );
    }

    /**
    * Method to set the label param of the button objects.
    *
    * @param  string  The button name as an index into the array of button objects.
    * @param  object  A   new instance of the button/link htmlelement object.
    * @param  array   The array of htmlement object parameters.
    * @param  string  The name of the array of object buttons ( either arrBtnObject or arrBtnCntrlObject )
    * @return nothing Set the label parameter for the buttons.
    */
    function setBtnObject( $button, &$objButton,  $params = array(), $arrBtnObject='arrBtnObject' )
    {
        $this->{$arrBtnObject}[$button] = &$objButton;
        foreach( $params as $field=>$value ) {
            $this->{$arrBtnObject}[$button]->{$field} = $value;
        }
    }
    
    /**
    * Method to set the label param of the button objects.
    *
    * @param  string  The button name as an index into the array of button objects.
    * @param  string  The label used for the htmlelement object.
    * @param  string  The label fieldname for the htmlelement object.
    * @param  string  The name of the array of object buttons ( either arrBtnObject or arrBtnCntrlObject )
    * @return nothing Set the label parameter for the buttons.
    */
    public function setBtnLabel( $button, $label, $field, $arrBtnObject='arrBtnObject' )
    {
        $this->{$arrBtnObject}[$button]->{$field} = $label;
    }

    /**
    *  Method to show the selectbox.
    * @return HTML the selectbox as html.
    */
    public function show()
    {
        //Construct tables for left selectboxes
        $tblLeft = $this->newObject( 'htmltable','htmlelements');
        $this->selectBoxTable( $tblLeft, $this->objLeftList, $this->arrHeaders['hdrLeft'] );

        //Construct tables for right selectboxes
        $tblRight = $this->newObject( 'htmltable', 'htmlelements');
        $this->selectBoxTable( $tblRight, $this->objRightList, $this->arrHeaders['hdrRight'] );

        //Construct tables for selectboxes and buttons: left, buttons, and right
        $tblSelectBox = $this->newObject( 'htmltable', 'htmlelements' );
        $buttons = array();
        foreach( $this->arrBtnObject as $object ) {
            $buttons[] = $object->show();
        }
        $this->layoutTable( $tblSelectBox, $tblLeft, $buttons, $tblRight );

        return $tblSelectBox->show();
    }

    /**
    * Method to get the form buttons( btnSave and btnCancel ).
    * @return array the form buttons in and array as HTML.
    */
    public function getFormButtons()
    {
        $buttons = array();
        foreach( $this->arrBtnCntrlObject as $object ) {
            $buttons[] = $object->show();
        }
        return $buttons;
    }
}
?>