<?php
/**
* This class provides some methods for working with
* strings to generate onscreen layout
*/

class strlayout extends object 
{
    /**
    * Constructor class for the trimstr class
    */
    function init()
    {
    } 
    /**
    * array text2columns(string text [,int n [, bool return_formated [, string css_class]]]) 
    * 
    * Function returns an array containing n number of entries (defaults to 2) 
    * of 'equal' length (word wrap) from desired text. Created for columnizing 
    * larger textblocks faciliating layout. Set return_formated flag to TRUE 
    * if you'd like the columns returned within a prelayouted html table 
    * (with="100%") (defaults to FALSE) and includes the css_class to all cells 
    * (defaults to NULL).
    * 
    * @param string $str The sting for format
    * @param int $cols The number of columns
    * @param bool $formated True | False,  if true it is returned 
    *    preformatted into a table
    * @param string $class The CSS class to use for layout
    */

    function text2columns($str, $cols = 2, $formated = false, $class = null)
    {
        $size = strlen($str) / $cols;
        $tmpstr = explode(" ", $str);
        $i = 0;
        for($j = 0; $j < $cols; $j++) {
            while ($i <= sizeof($tmpstr)) {
                if (strlen($col[$j]) < $size) {
                    $col[$j] .= $tmpstr[$i] . " ";
                    $i++;
                } else {
                    break;
                } 
            } 
            rtrim($col[$j]);
        } 
        if ($formated != false) {
            if ($class != null) {
                $class = ' class="' . $class . '"';
            } 
            $form = '<table width="100%" cellspacing="0"><tr' . $class . ' valign="top">';
            for($i = 0; $i < $cols; $i++) {
                $form .= '<td>' . $col[$i] . '</td>';
            } 
            $form .= '</tr></table>';
            return $form;
        } else {
            return $col;
        } 
    } 
} // end class

?>
