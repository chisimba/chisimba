<?php
/**
* Class jscript extends object.
* @author Fernando Martinez
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class containg the javascript functions used in the pbl
 *
 * This class should provide javascript functionality required by the client side, ie, the html page generated.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 0.9
 */

class jScript extends object
{
    /**
    * Method to construct the class
    */
    public function jScript()
    {
        // init
    }

    /**
     * Create a javascript function to submit a form that contains multiple buttons.
     * All of which trigger a submit action if pressed.
     * @param string $frmName The name of the form
     * @return string $html The generated html code
     */
    public function submitMultiBtnForm($frmName)
    {
        $html = "
        <script language='javascript'>
        <!--
            function submitIt(opt)
            {
            document." . $frmName . ".option.value = opt;
            document." . $frmName . ".submit();
            }
        //-->
        </script>";
        return $html;
    }

    /**
     * Create javascript code for a function that display a popup window.
     * @return string $html The generated html code
     */
    public function showPopUp()
    {
        $html = "
        <script language='javascript'>
        <!--
            function showPopUp(uri,wd,ht)
            {
            dim='height='+ht+',width='+wd;
            dim+=',titlebar=no,status=no,toolbar=no,menubar=no,scrollbars=yes,location=no,resizable=no';
            window.open(uri,'',dim);
            }
        //-->
        </script>";
        return $html;
    }

    /**
     * Create javascript function that clear and gives focus to a text control in a given form.
     * @param string $formName The name of the form
     * @param string $controlName The name of the control
     * @return string $html The generated html code
     */
    public function clearFocus($formName, $controlName)
    {
        $html = "
        <script language='javascript'>
        <!--
            function clearfocus()
            {
            document." . $formName . "." . $controlName . ".focus();
            document." . $formName . "." . $controlName . ".value = '';
            }
        // -->
        </script>";
        return $html;
    }

    /**
     * Create javascript code for a function that validates a given element of a given form by comparing it to specified value using specified operator
     * @param string $formName The name of the form
     * @param string $controlName The name of the control
     * @param string $errorMsg The error message to display
     * @param string $value The value to be validated
     * @param string $operator The operator to use for comparison
     * @return string $html The generated html code
     */
    public function validateElement($formName, $controlName, $errorMsg, $value, $operator = "==")
    {
        $html = "
        <script language='javascript'>
        <!--
            function Validate_" . $formName . "_" . $controlName . "()
            {
            if (!(document." . $formName . "." . $controlName . ".value " . $operator . "'" . $value . "'))
                alert('" . $errorMsg . "');
            }
        //-->
        </script>";
        return $html;
    }

}

?>