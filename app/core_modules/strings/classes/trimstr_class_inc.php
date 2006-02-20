<?php
/**
 * This class provides some methods for working with
 * linked letters of the alphabet
 */

class trimstr extends object {

    /**
     * Constructor class for the trimstr class
     */
    function init()
    { 

    } 

    /**
     * Method to grab the left $len characters from a string, 
     * but not to chop into a word. If the string is longer than 
     * that, cut, then add '...' to the end of the string, 
     * if the $more flag is set to true
     * 
     * This method is based on grab_length provided by Andrew Collington
     * 
     * @author Andrew Collington, php@amnuts.com, http://php.amnuts.com/ 
     * @author Modified for KNG by Derek (almost no modfications needed) 
     * @param string $str the sting to be processed
     * @param int $len The length to which to trim the string
     * @param bool $more TRUE | FALSE for adding ... to the end
     * 
     * @todo -ctrimstr Implement trimstr. Implement checking that it is 
     *   not breaking HTML
     */
    function strTrim($str = null, $len = 150, $more = true)
    {
        if (!$str) return null;
        if (is_array($str)) return $str; 
        // Lop off whitespace
        $str = trim($str); 
        // if it's les than the size given, then return it
        if (strlen($str) <= $len) return $str; 
        // else get that size of text
        $str = substr($str, 0, $len); 
        // backtrack to the end of a word
        if ($str != "") {
            // check to see if there are any spaces left
            if (!substr_count($str, " ")) {
                if ($more) $str .= "...";
                return $str;
            } 
            // backtrack
            while (strlen($str) && ($str[strlen($str)-1] != " ")) {
                $str = substr($str, 0, -1);
            } 
            $str = substr($str, 0, -1);
            if ($more) $str .= "...";
        } 
        return $this->cleanHtml($str); // strips out the HTML to prevent damage to the interface
    } 
    
    /**
    * Method to wrap a string to $wrap characters
    * 
    * @param string $str The input string to wrap
    * @param int $wrap The number of characters to wrap, defaults to 72 
    * as is the convention for email
    * @Example $textToMail = mail($email,$subject,wraptext($text,72)); 
    */
    function wrapString($str,$wrap=72) { 
        $str = strip_tags($str);
        return wordwrap($str, $wrap, "<br />\n"); 
    } 
    
    
    /**
    * Method to remove HTML from string
    */
    function cleanHtml($str) 
    {
        return strip_tags($str);

    }
} 
?>
