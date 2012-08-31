<?php
/**
 * Class for manipulating URLs and anchor tags in text strings.
 * It is mainly used for parsing strings to activate URLS, or to
 * tag external links with an icon indicating that they are off site.
 *
 * The following methods are provided:
 *   makeClickableLinks -> Turns plain text links into clickable links
 *   removeLinks -> Removes active links in the string
 *   isValidFormedUrl -> Tests if a URL is a validly formed URL
 *   tagExtLinks -> Provides a method to tag links that go off site with an icon
 *
 * @author Derek Keats
 * @version $Id$
 * @copyright 2003 GPL
 */
class url extends object {
    /**
     * Method to take a string and return it with URLS with http:// or ftp://
     * and email addresses as active links
     *
     * @param string $str The string to be parsed
     * @return the parsed string
     */
    function makeClickableLinks($str)
    {
        // Exclude matched inside anchor tags
        $not_anchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';
        // Match he protocol with ://, e.g. http://
        $protocol = '(http|ftp|https):\/\/';
        $domain = '[\w]+(.[\w]+)';
        $subdir = '([\w\-\.;,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
        $test = '/' . $not_anchor . $protocol . $domain . $subdir . '/i';
        // Match and replace where there is a protocol and no anchor
        $ret = preg_replace($test, "<a href='$0' title='$0'>$0</a>", $str);
        // Now match things beginning with www.
        $not_anchor = '(?<!"|href=|href\s=\s|href=\s|href\s=)';
        $not_http = '(?<!:\/\/)';
        $domain = 'www(.[\w]+)';
        $subdir = '([\w\-\.;,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?';
        $test = '/' . $not_anchor . $not_http . $domain . $subdir . '/is';
        return preg_replace($test, "<a href='http://$0' title='http://$0'>$0</a>", $ret);
    }

    /**
     * Method to unlink URLs in text. The numbers in the comments refer
     * to the backreferences for matches inside parentheses ()
     *
     * @param string $str The string to be parsed
     * @return the parsed string
     */
    function removeLinks($str)
    {
        $test="/(<a\s+href=)"       //1: Match the start of the anchor tag followed by
                                    //   any number of spaces followed by href followed
          ."([\"\'])"              //2: Match either of " or ' and remember it as \2 for a backreference
          . "(.*[\"'])"            //3: Match any characters up to the next " or '
          . "(.*>)"                //4: Anything else followed by the closing >
          . "(.+)"                 //5: Match any string of 1 or more characters
          . "(<\/a>)"               //6: Match the closing </a> tag
          . "/isU";                //Make it case insensitive (i), treat across newlines (s)
                                    //and make it Ungreedy (U)
          return preg_replace($test, "\$5", $str);

    }

    /**
     * Checks if a URL is validly formed based on the code in
     * PHP Classes by Lennart Groetzbach <lennartg_at_web_dot_de>
     * Adapted to KINKY by Derek
     *
     * @param String $str The Url to validate
     * @param boolean $strict Enforce strict checking?
     * @return boolean TRUE|FALSE
     */
    function isValidFormedUrl($str, $strict = false)
    {
        $test = "";
        if ($strict == true) {
            $test .= "/^http:\\/\\/([A-Za-z-\\.]*)\\//";
        } else {
            $test .= "/^http:\\/\\/([A-Za-z-\\.]*)/";
        }
        return @preg_match($test, $str);
    }

    /**
    * Method to check if Email is valid
    *
    * Adapted from: http://www.totallyphp.co.uk/code/validate_an_email_address_using_regular_expressions.htm
    *
    * @param string $email Email Address to Check for
    * @return boolean TRUE|FALSE
    */
    function isValidFormedEmailAddress($email)
    {
        if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
            return TRUE;
        } else {
          return FALSE;
        }
    }

    /**
     * Method to add something to the end of an external link
     * where external is defined as one that starts with http://
     * or ftp://
     * It uses a perl type regular expression
     * Ir was made with help from participants in the
     * Undernet IRC channel #phphelp
     * @author Derek Keats
     * @param String $str The string to be tagged
     * @param String $activ Whether to make the $repl or icon active, default TRUE
     * @param String $repl The string to add to the end of the link
     *   Defaults to the external link icon
     * @return String The string with external links tagged
     */
    function tagExtLinks($str, $activ=true, $repl = null)
    {
        if ($repl == null) {
            $this->objExterIcon = $this->newObject('geticon', 'htmlelements');
            $this->objExterIcon->setIcon("external_link");
            $objLanguage = $this->getObject('language', 'language');
            $this->objExterIcon->alt = $objLanguage->languageText("mod_strings_exlink",'strings');
            $repl = $this->objExterIcon->show();
        }

        //-->Need to change space to \s+
        if ($activ==null) {
            $test = "/(<a\s+href=)" // 1: Match the opening of the anchor tag
              ."(\"|\')" // 2: Match the first single or double quotes
              ."(http|https|ftp)" // 3: match the protocol identifier (Http, ftp, https)
              ."(:\/\/)" //4: Match the :// part of the URL
              ."(?!" . $_SERVER['HTTP_HOST'] . ")"  // 5: Do not match if it is the local server
              ."(.*)" // 6: Match any number of following characters up to the closing anchor tag
              ."(<\/a>)/isU"; // 7: Match the closing anchor tag
            return preg_replace($test, " \${0}" . $repl, $str);
        } else {
            $test = "/(<a\s+href=)" // 1: Match the opening of the anchor tag
              ."(\"|\')" // 2: Match the first single or double quotes
              ."(http|https|ftp)" // 3: match the protocol identifier (Http, ftp, https)
              ."(:\/\/)" //4: Match the :// part of the URL
              ."(?!" . $_SERVER['HTTP_HOST'] . ")" // 5: Match domain but exclude the local server
              ."(.*)" // 6: Match any characters after the server
              ."(\"|\')" //7: Match a closing single or double quote (helps pull out the URL)
              ."(.*)" // 8: Match any further characters
              ."<\/a>/isU"; // 9: Match the closing anchor
            return preg_replace($test,
              " \${0} <a href=\"\${3}\${4}\${5}\"
              target=\"_BLANK\">" . $repl . "</a>", $str);
        }

    }

    /**
    * Method to activate image links. Note that this will have to be called
    * before the tagExtLinks method, otherwise the images will appear as links
    * not as the images themselves
    */
    function activateImage($str)
    {
                // Exclude matched inside anchor tags
        $test = '(?<!"|href=|href\s=\s|href=\s|href\s=)' //make sure it is not already in an achor
          . '(http|https)(:\/\/)' // match the protocol foloweed by ://
          . '[\w]+(.[\w]+)' // match the domain
          . '([\w\-\.;,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?' //match any number of subdirectories
          . '(gif|jpg|png)'; // match the file extension
        return preg_replace($test,
              "<img src=\"${0}\"/>", $str);

    }
}

?>