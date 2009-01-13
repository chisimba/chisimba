<?php

/**
 * Class Form
 * The Form class creates a form object
 * that has the ability to  contain other
 * html objects
 * PHP version 5
 *
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author2   Paul Mungai <pwando@uonbi.ac.ke>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 * *
 *            Example using type 4 form.
 *
    $objForm = new form('formname',$this->uri(array('action'=>'actiontotake')));
    $objForm->displayType = 4;
    $objForm->addToFormEx($objLanguage->languageText('informational1'));
    $objForm->addToFormEx($objLanguage->languageText('label1'),new textinput('name1'));
    $objForm->addToFormEx($objLanguage->languageText('label2'),new textinput('name2'));
    $objForm->addToFormEx($objLanguage->languageText('label3'),new textinput('name3'));
    echo $objForm->show();

 *
 *            Example using type 5 form. -- deprecated; use the fieldset class.
 *
    $objForm = new form('formname',$this->uri(array('action'=>'actiontotake')));
    $objForm->displayType = 5;
    $objForm->beginFieldset('legend');
    $objForm->addToFormEx($objLanguage->languageText('informational1'));
    $objForm->addToFormEx($objLanguage->languageText('label1'),new textinput('name1'));
    $objForm->addToFormEx($objLanguage->languageText('label2'),new textinput('name2'));
    $objForm->addToFormEx($objLanguage->languageText('label3'),new textinput('name3'));
    $objForm->endFieldset();
    echo $objForm->show();

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

// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");



class form implements ifhtml
{
    /**
     *
     * @var string $name
     *             The name of the form
     */
    public $name;

    /**
     *
     * @var array $elements
     *            The array that holds all the objects
     */
    public $elements;

    /**
     *
     * @var string $action
     *             The action of the form
     */
    public $action;
    /**
     *
     * @var string $target
     *             The target of the form
     */
    public $target;
    /**
     *
     * @var int displayType
     *          the display of the elements on the form
     */
    public $displayType;

    /**
     *
     * @var boolean $autoshow
     *              set the form to automatically call the
     *              elements 'show' method
     */
    var $autoshow = false;

    /**
    *
    * @var string $method
    * sets the method of the form ,
    * either 'post' (default) or 'get'
    */
    public $method = 'post';

    /**
     *
     * @var string $javascript
     *             the javascript that will validate
     *             this form when submitted
     */
    public $javascript;

    /**
    *
    * @var boolean $hasRules
    *flag to check if there is any rules set
    */
    public $hasRules=false;

    /**
    *
    * @var string $extra
    *extra
    */
    public $extra;

    /**
    *
    * @var string $extra
    *extra
    */
    public $submit;

   /**
    *
    * @var string $encType
    * sets the enctype of the form ,
    * default is multipart/form-data
    */
    public $encType;
    /**
     * **************END OF VARIABLES**************
     */

    /**
     * Form contructor
     */
    public function form($name = null, $action = null)
    {
        // set the name
        $this->name = $name;
        // set the action
        $this->action = $action;
        // instantiate the new array
        $this->elements = array();
        // set the default display type
        $this->displayType = 3;

        // Set Target to Null as default
        $this->target = NULL;

        // // Set onSubmit to an empty string as default
        $this->onSubmit = '';

        $this->id = 'form_'.$name;

    }

    /**
     * Method to set the Form action
     *
     * @Param string $action
     */
    public function setAction($action = Null)
    {
        $this->action = $action;
    }
    /**
     * Method to set the Form onSubmit function
     *
     * @Param string $submit
     */
//public function setOnSubmit($submit)
    public function setOnSubmit($submit = NULL)
    {
        if (!is_null($submit)) {
            $this->onSubmit = $submit;
        }
    }
    /**
     * Method to set the Form Target
     *
     * @Param string $target
     */
    public function setTarget($target = Null)
    {
        $this->target = 'target = "'.$target.'"';
    }

    /**
     * Method to set the display type
     *
     * @param int $displayType : the value for the displayType
     */
    public function setDisplayType($displayType = Null)
    {
        $this->displayType = $displayType;
    }
    /**
     * Method to set the enctype
     *
     * @param int $encType : the value of the encType i.e 'multipart/form-data'
     */
    public function setEncType($encType = "application/x-www-form-urlencoded")
    {
        $this->encType = 'enctype = "'.$encType.'"';
    }

    /**
     * Method to add a object of
     * string to the form
     *
     * @param object $objElement
     */
    public function addToForm($objElement)
    {
        $this->elements[] = $objElement;
    }

    /**
     * Method to add label to form
     *
     * @param  string $label string to add to form
     * @param  $field string: the name of the field
     * @access public
     */
    public function addToFormEx($label,$field=NULL)
    {
        if (is_null($field)) {
            $this->elements[] = array('label'=>$label);
        }
        else {
            $this->elements[] = array('label'=>$label,'field'=>$field);
        }
    }

    /**
     * Method to create and array of elements
     *
     * @param string $legend
     * @access public
     */
    public function beginFieldset($legend)
    {
        $this->elements[] = array('fieldset'=>'begin', 'legend'=>$legend);
    }

    /**
     * Method to end the field set array
     *
     * @return void
     * @access public
     */
    public function endFieldset()
    {
        $this->elements[] = array('fieldset'=>'end');
    }

    /**
     * *********** SHOW SECTION *************
     */
    /**
     * Method to show the form
     *
     * @return string $str
     */
    public function show()
    {
        $str = ($this->hasRules) ? $this->_getValidatorScripts() : '';
        //$submit=($this->hasRules) ? ' onsubmit="return validate_'.$this->name.'_form(this) "' : '';
        $onSubmit=($this->hasRules) ? ' return validate_'.$this->name.'_form(this);' : $this->onSubmit; //.$this->onSubmit

        // Add onsubmit parameter around onsubmit options
        if (trim($onSubmit) != '') {
            $onSubmit = 'onsubmit="'.$onSubmit.'"';
        }

        if(!empty($this->id)){
            $id = ' id="'.$this->id.'"';
        } else {
            $id = '';
        }
        $str .= '<form
                    '.$id.'
                    name="' . $this->name . '"
                    action="' . $this->action . '"
                    method="'.$this->method.'"
                    '.$this->encType.'
                    '.$this->target.'
                    '.$onSubmit.'
                    '.$this->extra.'
                    >';

        // call the scripts
        $str .= $this->_getShowElements();
        $str .= '</form>';

        return $str;
    }

    /**
     * Method to get all the objects
     * in the $elements array
     *
     * @return string $str
     */
    private function _getShowElements()
    {
        $str = '';

        switch ($this->displayType) {
            case 1:
                $str = $this->_formTextAbove();
                break;
            case 2:
                $str = $this->_formTextLeft();
                break;
            case 3:
                $str = $this->_formFreeFormat();
                break;
            case 4:
                $str = $this->_formFormatted();
                break;
            case 5: //-- deprecated; use the fieldset class.
                $str = $this->_formFormattedNew();
                break;
            default:
                $str = $this->_formTextLeft();
        }

        return $str;
    }

    /**
     * Private Method to format the form so that
     * the label will appear above the input
     */
    private function _formTextAbove()
    {
        $str = '<table>';
        foreach($this->elements as $e => $f) {
            // if it is an input object then use that object's show method
            if (is_object($this->elements[$e])) {
                if (isset($this->elements[$e]->label)) {
                    $str .= '<tr><td valign="top"><label for="'.$this->elements[$e]->name.'">' . $this->elements[$e]->label . '</label></td></tr>';
                }
                $str .= '<tr><td>' . $this->elements[$e]->show() . '</td></tr>';
            } else {
                $str .= '<tr><td>' . $this->elements[$e] . '</td></tr>';
            }
        }
        $str .= '</table>';
        return $str;
    }
    /**
     * Private Method to format the form so that
     * the label will appear on to the left of the input
     */
    private function _formTextLeft()
    {
        $str = '<table>';

        foreach($this->elements as $e => $f) {
            $str .= '<tr>';
            if (is_object($this->elements[$e])) {
                if (isset($this->elements[$e]->label)) {
                    $str .= '<td>' . $this->elements[$e]->label . '</td>';
                }
                $str .= '<td>' . $this->elements[$e]->show() . '</td>';
            } else {
                $str .= '<td>' . $this->elements[$e] . '</td>';
            }
            $str .= '</tr>';
        }

        $str .= '</table>';
        return $str;
    }
    /**
     * Private Method to format the form
     * in the free format
     */
    private function _formFreeFormat()
    {
        $str = '';
        foreach($this->elements as $e => $f) {
            if (is_object($this->elements[$e])) {
                $str .= $this->elements[$e]->show();
            } else {
                $str .= $this->elements[$e];
            }
        }
        return $str;
    }

    /**
     * Private Method to format the form
     * in the formatted format
     */
    private function _formFormatted()
    {
        $str = '<table>';
        foreach($this->elements as $element) { //$e => $f
            if (!is_array($element)) {
                die("You need to use addToFormEx() for type 4 forms.");
            }
            $str .= '<tr class="even">';
            if (!isset($element['field'])) {
                $label = $element['label'];
                $str .= '<td align="left" colspan="2">';
                if (is_object($label)) {
                    $str .= $label->show();
                } else {
                    $str .= $label;
                }
                $str .= "</td>";
            }
            else {
                $label = $element['label'];
                $field = $element['field'];
                $str .= '<td align="left">';
                if (is_object($label)) {
                    $str .= $label->show();
                } else {
                    $str .= $label;
                }
                $str .= "</td>";
                $str .= '<td align="left">';
                if (is_object($field)) {
                    $str .= $field->show();
                } else {
                    $str .= $field;
                }
                $str .= "</td>";
            }
            $str .= '</tr>';
        }
        $str .= '</table>';
        return $str;
    }

    /**
     * Private Method to format the form
     * in the new formatted format -- deprecated; use the fieldset class.
     */
    private function _formFormattedNew() //-- deprecated; use the fieldset class.
    {
        $str = "";
        foreach($this->elements as $element) { //$e => $f
            if (!is_array($element)) {
                die("You need to use addToFormEx() for type 4/5 forms.");
            }
            if (isset($element['fieldset'])) {
                $fieldset = $element['fieldset'];
                switch($fieldset){
                    case 'begin':
                        $legend = $element['legend'];
                        $str .= "<fieldset><legend>$legend</legend>";
                        $str .= '<table>';
                        break;
                    case 'end':
                        $str .= '</table>';
                        $str .= '</fieldset>';
                        break;
                    default:
                        ;
                } // switch
            }
            else {
                $str .= '<tr>';
                if (!isset($element['field'])) {
                    $label = $element['label'];
                    $str .= '<td align="left" colspan="2">';
                    if (is_object($label)) {
                        $str .= $label->show();
                    } else {
                        $str .= $label;
                    }
                    $str .= "</td>";
                }
                else {
                    $label = $element['label'];
                    $field = $element['field'];
                    $str .= '<td align="right">';
                    if (is_object($label)) {
                        $str .= $label->show();
                    } else {
                        $str .= $label;
                    }
                    $str .= "</td>";
                    $str .= '<td align="left">';
                    if (is_object($field)) {
                        $str .= $field->show();
                    } else {
                        $str .= $field;
                    }
                    $str .= "</td>";
                }
                $str .= '</tr>';
            }
        }
        return $str;
    }

    /**
     * ********* END OF SHOW SECTION ***********
     */




    /**
     * ******** VALIDATION SECTION *********
     */

    /**
     * Method to add a validation rule
     * to an element
     *
     * @param $mix      mix    : This variable can hold anything
     * @param $errormsg string : The error message
     * @param $valcmd   string : the validation type
     *
     *                         * the following rules apply
     *                         required
     *                         maxlength
     *                         minlength
     *                         rangelength
     *                         regex
     *                         email
     *                         lettersonly
     *                         alphanumeric
     *                         numeric
     *                         nopunctuation
     *                         nonzero
     *                         uploadedfile
     *                         maxfilesize
     *                         filename
     *                         mimetype
     *                         compare
     */
    public function addRule($mix, $errormsg, $valCmd)
    {
        $this->hasRules=true;
        switch (strtolower($valCmd)) {
            case 'required':
                $this->_valRequire($mix, $errormsg);
                break;
            case 'maxlength':
                            $this->_valMaxLength($mix,$errormsg);
                            break;
            case 'minlength':
                            $this->_valMinLength($mix,$errormsg);
                            break;
            case 'rangelength':
                        $this->_valRangeLength($mix,$errormsg);
                        break;
            case 'email':
                      $this->_valEmail($mix,$errormsg);
                      break;
            case 'letteronly':
                      $this->_valLettersOnly($mix,$errormsg);
                      break;
            case 'alphanumeric':
                      //
                      break;
            case 'numeric':
                $this->_valNumeric($mix, $errormsg);
                break;
                        case 'maxnumber':
                      $this->_valMaxNumber($mix,$errormsg);
                      break;
                  case 'minnumber':
                      $this->_valMinNumber($mix,$errormsg);
                      break;
                  case 'select';
                      $this->_valSelect($mix,$errormsg);
                      break;
            case 'compare';
                $this->_valCompare($mix, $errormsg);
                break;
                        case 'regex':
                        case 'nopunctuaion';
            case 'nonzero';
            case 'url':
                $this->_validateURL($mix,$errormsg);
                break;
            case 'uploadedfile';
            case 'maxfilesize';
            case 'filename';
            case 'mimetype';
            case 'fieldexists':
                $this->_fieldExists($mix);
                break;

        }
    }

    /**
     * Method to add a form rule
     *
     * @return void
     * @access public
     */
    public function addFormRule()
    {
    }

    /**
     * Method to validate URL's
     *
     * @return if true (boolean) else string error message
     * @access private
     */
    private function _validateURL($url,$message)
    {
        if (!preg_match('/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\//i', $url, $message)) {
             return true;
        } else {
            return $message;
        }
    }
    /**
     * Method to get the validator scripts
     *
     * @return string  Return description (if any) ...
     * @access private
     */
    private function _getValidatorScripts()
    {
        $jsc = '<script language="JavaScript" type="text/javascript" src="core_modules/htmlelements/resources/validation.js"></script>
        <script language="JavaScript" type="text/javascript">
        //<![CDATA[
            //the following lines are generated
            function validate_'.$this->name.'_form(frm)
            {
                var ok = true;
                '.$this->_getJavaScripts().'
                return ok;
            }
        //]]>
        </script>';
        return $jsc;
    }

    /**
     * Method the adds a javascript method call
     * to the javascript string
     *
     * @param $stript    string : the javascript method that will be called
     * @param $fieldname string : The name of the field
     * @param $errormsg  string : the erroe message
     */
    private function _addValidationScript($script = null, $errormsg = null, $fieldname = null)
    {//.getElementByName("'.$fieldname.'");';  //document.forms['".$this->name."']
        if(isset($errormsg)){
            $errormsg = 'alert("'.$errormsg.'");';
        }
        $this->javascript .="
            var formElement = document.getElementById('input_".$fieldname."');
            if(formElement != null){
                var el = formElement;
                ok = ok && ".$script."
                if(formElement.type == 'hidden'){
                    ok = true;
                }
            }else{
                var elArray = document.getElementsByName('".$fieldname."');
                var el = elArray[0];
                if(el.type == 'radio'){;
                    var selected = false;
                    for (var i=0; i<elArray.length; i++){
                        if (elArray[i].checked){
                            selected = true;
                        }
                    }
                    ok = ok && selected;
                }else{
                    alert('Error in form validation. ID=\'input_{$fieldname}\'.');
                    return false;
                }
            }
            if (!ok){
                ".$errormsg."
                el.focus();
                if (el.type != 'select-one'){
                    el.select();
                }
                return false;
            }";
/*
        var el = frm.".$fieldname.";" ;
        if (isset($errormsg))
            $errormsg='alert("' . $errormsg . '");';
        $errmsg=
        $this->javascript .= '
                            ok = ok && ' . $script . '
                            //alert(ok);
                            if (!ok){
                                ' . $errormsg . '
                                el.focus();
                                el.select();
                                return false;
                            }
                            ';
*/
    }

    /**
     * Method to return the javascript string
     */
    private function _getJavaScripts()
    {
        return $this->javascript;
    }


    /* *********VALIDATION COMMANDS*********/



     /**
     * Method to set a required field
     *
     * @param $fieldname string : The name of the field
     * @param $errormsg  string : the erroe message
     */
    private function _valRequire($fieldname, $errormsg)
    {
        //$jmethod = 'valRequired(' . $this->name . '.' . $fieldname . '.value)';
        $jmethod = 'valRequired(el.value)';
        $this->_addValidationScript($jmethod, $errormsg, $fieldname);
    }

    /**
     * Method to compare two fields
     *
     * @param $fieldname string : The name of the field
     * @param $errormsg  string : the error message
     */
    private function _valCompare($fields, $errormsg)
    {
        $jmethod = 'valCompare(document.forms.' . $this->name . '.' . $fields[0] . '.value, document.forms.' . $this->name . '.' . $fields[1] . '.value)';
       // $jmethod = 'valCompare(el.value,' . $this->name . '.' . $fields[1] . '.value)';
        $this->_addValidationScript($jmethod, $errormsg, $fields[0]);
    }

    /**
     * Method to check for numeric field
     *
     * @param $fieldname string : The name of the field
     * @param $errormsg  string : the error message
     */
    private function _valNumeric($field, $errormsg)
    {
        $jmethod = 'valNumeric(el.value)';
        $this->_addValidationScript($jmethod, $errormsg, $field);

    }

    /**
    * Method to check the maximum field size
    * @param $mix array :Should include a field name and the length
    * @param  $errormsg string : the error message
    */
    private function _valMaxLength($mix, $errormsg)
    {
        $jmethod = 'valMaxLength(el.value,'.$mix['length'].')';
        $this->_addValidationScript($jmethod, $errormsg, $mix['name']);
    }

    /**
    * Method to check the maximum field size
    * @param $mix array :Should include a field name and the length
    * @param  $errormsg string : the error message
    */
    private function _valMinLength($mix, $errormsg)
    {
        $jmethod = 'valMinLength(el.value,'.$mix['length'].')';
        $this->_addValidationScript($jmethod, $errormsg, $mix['name']);
    }

    /**
    * Method to check the fields lenght is between a certain length
    * @param $mix array :Should include a field name, lower and upper variables
    * @param  $errormsg string : the error message
    */
    private function _valRangeLength($mix,$errormsg)
    {
        $jmethod = 'valRangeLength(el.value,'.$mix['lower'].','.$mix['upper'].')';
        $this->_addValidationScript($jmethod, $errormsg, $mix['name']);
    }

    /**
     * Method to validate the email
     *
     * @param  $fieldname string: name of the field
     * @param  $errormsg string: the error message
     * @access private
     */
    private function _valEmail($fieldname,$errormsg)
    {
        $jmethod='emailCheck(el.value)';
        $this->_addValidationScript($jmethod, null, $fieldname);
    }

    /**
     * Method to validate the letters of an email only
     *
     * @param  $fieldname string: name of the field
     * @param  $errormsg string: the error message
     * @access private
     */
    private function _valLettersOnly($fieldname,$errormsg)
    {
        $jmethod='valLettersOnly(el.value)';
        $this->_addValidationScript($jmethod, $errormsg, $fieldname);
    }

    /**
     * Method to validate the maximum number
     *
     * @param $mix array :Should include a field name and length
     * @param  $errormsg string: the error message
     * @return void
     * @access private
     */
    private function _valMaxNumber($mix,$errormsg)
    {
        $jmethod='valMaxNumber(el.value,'.$mix['maxnumber'].')';
        $this->_addValidationScript($jmethod, $errormsg, $mix['name']);
    }

    /**
     * Method to validate the minimum number
     *
     * @param $mix array :Should include a field name and length
     * @param  $errormsg string: the error message
     * @access private
     */
    private function _valMinNumber($mix,$errormsg)
    {
        $jmethod='valMinNumber(el.value,'.$mix['minnumber'].')';
        $this->_addValidationScript($jmethod, $errormsg, $mix['name']);
    }

    /**
     * Method to validate the selection
     *
     * @param  $fieldname string: name of the field
     * @param  $errormsg  string: the error message.
     * @access private
     */
    private function _valSelect($fieldname,$errormsg)
    {
        $jmethod='valSelect(el)';
        $this->_addValidationScript($jmethod, $errormsg, $fieldname);

    }
    /**
     * Method to check if a field exists
     *
     * @param  $fieldname string: name of the field
     * @param  $errormsg  string: the error message.
     * @access private
     */
    private function _fieldExists($fieldname)
    {
       $this->javascript .="
           if (document.getElementById('input_".$fieldname."')==undefined)
            return false;
       ";
    }
}
?>
