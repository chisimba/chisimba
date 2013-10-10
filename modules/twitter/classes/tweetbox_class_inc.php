<?php
/**
 *
 * Twitter interface elements
 *
 * Twitter is a module that creates an integration between your Chisimba
 * site using your Twitter account.
 *
 * PHP version 5
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
 * @package   twitter
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: tweetbox_class_inc.php 17334 2010-03-30 19:14:43Z dkeats $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* tweetbox is a helper class for the V part of MVC for the twitter module.
* It returns the twitter input text box, with a jQuery based limit script.
*
* @author Derek Keats
* @package twitter
*
*/
class tweetbox extends object
{


    /**
    *
    * Constructor for the twitterview class
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->url = $this->uri(array(
          "action" => "sendtweet"), "twitter");
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
    *
    * Method to render the tweetbox
    *
    * @access public
    * @return string The rendered tweetbox
    *
    */
    public function show()
    {
        return $this->getLimitBox(140, "chisimba_tweet");
    }

    /**
     *
     * Limit information will shown in a div whose id is ‘charlimitinfo’.
     *
     * @access public
     * @return string The Javascript for the limit box
     *
     */
    public function getLimitBox($chars, $textboxid)
    {
        $this->addLimitHeaderScript();
        $this->addBindingLimitsHeaderScript($chars, $textboxid);
        return $this->sendWidget();

    }

    /**
    *
    * Method to add the jQuery binding limts (limit text chars in textbox)
    * to the page header
    *
    * @access public
    * @return boolean TRUE
    *
    */
    public function addLimitHeaderScript()
    {
        $js = "<script language=\"javascript\">
            function limitChars(textid, limit, infodiv)
            {
                var text = jQuery('#'+textid).val();
                var textlength = text.length;
                if(textlength > limit) {
                    jQuery('#' + infodiv).html('You cannot write more then '+limit+' characters!');
                    jQuery('#'+textid).val(text.substr(0,limit));
                    return false;
                } else {
                    jQuery('#' + infodiv).html((limit - textlength));
                    return true;
                }
            }" . "</script>";
        $this->appendArrayVar('headerParams', $js);
        unset($js);
        return TRUE;
    }

    /**
    *
    * Method to add the jQuery binding limts (limit text chars in textbox)
    * to the page header
    *
    * @access public
    * @return boolean TRUE
    *
    */
    public function addBindingLimitsHeaderScript($chars, $textboxid) {
        $js = "<script type=\"text/javascript\">
        jQuery(function() {
            jQuery('#" . $textboxid . "').keyup(function(){
                limitChars('" . $textboxid . "', " . $chars .", 'charlimitinfo');
            })
        });
        " . "</script>";
        $this->appendArrayVar('headerParams', $js);
        unset($js);
        return TRUE;
    }

    /**
    *
    * Method to render the script that intercepts the submit call
    * and passes if off to the jQuery forms plugin that handles
    * ajax calls.
    *
    * @access public
    * @return String A string containing the script.
    *
    */
    public function renderFormScript()
    {
        return "<script type=\"text/javascript\">
        function resetTweets() {
            document.getElementById('myLastTweet').innerHTML=document.myEditorForm.tweet.value
               + '<br /><span class=\"minute\">Right now</span>';
            document.myEditorForm.tweet.value='';
            document.getElementById('charlimitinfo').innerHTML = '140';
            return true;
        }
        // wait for the DOM to be loaded
        jQuery(document).ready(function() {
            // bind 'myEditorForm' and provide a simple callback function
            jQuery('#myEditorForm').ajaxForm(function() {
                resetTweets();
            });
        });
        </script>";
    }

    /**
    * Method to send the Tweetbox widget. Used to render the tweetbox
    * block for example
    *
    * @access public
    * @return string Form and labels for the tweetbox or error if no twitter login found.
    *
    */
    public function sendWidget()
    {
        //Load the Ajax form processing
        $objJQuery = $this->getObject('jquery', 'jquery');
        $objJQuery->loadFormPlugin();
        $this->loadClass('button', 'htmlelements');
        //--- Create a Tweet button (not translatable!)
        $objButton = new button('submit','Tweet');
        // Add the login icon
        $objButton->setIconClass("twitter");
        // Set the button type to submit
        $objButton->setToSubmit();
        
        $js2 = $this->renderFormScript();
        $this->appendArrayVar('headerParams', $js2);
        if ($this->hasTwitterLogon()) {
           $ret = "<form name=\"myEditorForm\" id=\"myEditorForm\" action=\""
              . $this->url . "\" method=\"post\">"
              . "<table cellpadding=\"4\" width=\"98%\"><tr><td><span class=\"minute\">"
              . $this->objLanguage->languageText("mod_twitter_entertext", "twitter")
              . "</span></td><td><span class=\"charlimit\"><div name=\"charlimitinfo\" id=\"charlimitinfo\">140</div></span></td></tr></table>"
              . "<textarea name=\"tweet\" id=\"chisimba_tweet\" cols=\"19\" rows=\"4\">"
              . "</textarea><br />"
              . $objButton->show() . "</form>";
        } else {
            $ret = $this->objLanguage->languageText("mod_twitter_nologon", "twitter");
        }
        return $ret;
    }

    /**
    *
    * Method to determing if the user has a twitter login specified
    * in userparams.
    *
    * @access public
    * @return boolean TRUE|FALSE
    *
    */
    public function hasTwitterLogon()
    {
        $objUserParams = $this->getObject("dbuserparamsadmin","userparamsadmin");
        $objUserParams->readConfig();
        $userName = $objUserParams->getValue("twittername");
        $password = $objUserParams->getValue("twitterpassword");
        if ($userName == NULL && $password==NULL) {
            return FALSE;
        } else {
            return TRUE;
        }
    }


}
?>