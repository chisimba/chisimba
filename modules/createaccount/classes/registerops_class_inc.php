<?php
/**
 *
 * An operations helper class for the block_register_class_inc.php
 *
 * An operations helper class for the block_register_class_inc.php which takes
 * the UI rendering code out of the block, and allows for it to be reused.
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
 * @package   security
 * @author    Modified from code by Nic Appleby
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
* An operations helper class for the block_register_class_inc.php
*
* An operations helper class for the block_register_class_inc.php which takes
* the UI rendering code out of the block, and allows for it to be reused.
*
* @package   security
* @author    Modified from code by Nic Appleby
*
*/
class registerops extends object {
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     * Standard init method for the registerops class
     * @access public
     * @return VOID
     *
     */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        // Set the jQuery version [ @todo remove when jQuery is upgraded ].
        $this->setVar('JQUERY_VERSION', '1.4.2');
        // Load jQuery Validate
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js',
          'jquery')
        );
        // Load the javascript that supports this block
        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('createaccount.js', 'createaccount')
        );
    }

    /**
     *
     * Build a registration form and return it for use in an Ajax call
     * 
     * @access public
     * @return string The rendered form
     * 
     */
    public function getBasicRegForm($title){
        $captchaLabel = $this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request');
	$objCaptcha = $this->getObject('captcha', 'utilities');
        $userName = $this->objLanguage->languageText('word_username', 'system');
        $emailAddr = $this->objLanguage->languageText('word_email', 'system', 'Email address');
        $passWord = $this->objLanguage->languageText('word_password', 'system');
        $reDraw = $this->objLanguage->languageText('word_redraw', 'security', 'Redraw');
        $register = $this->objLanguage->languageText('word_register');

        $form = '
                <div id="dialog" title="'.$title.'">
                <div id="result_message"></div>
                <form id="register"  method="post" action="javascript:void(0);">
                    <label for="username">'. $userName .'</label>
                    <input type="text" name="username" id="usernamessss" class="required" />

                    <label for="email">'. $emailAddr .'</label>
                    <input type="text" name="email" id="email" value="" class="email" />

                    <label for="password">'. $passWord .'</label>
                    <input type="password" name="password" id="password" value="" class="required" />

                    <label for="request_captcha">'.$captchaLabel.'</label>
                    <div id="captchaDiv" style="padding-top:3px;padding-bottom:3px">
                      '.$objCaptcha->show().'
                    </div>
                    <input type="text" name="request_captcha" id="request_captcha" value="" class="request_captcha" />
                    <a id="redraw">'. $reDraw .'</a>
                    <input class="submit" type="submit" id="create-user" value="' . $register . '"/>
                </form>
                </div>
            
        ';
        //<input class="submit" type="submit" id="create-user" value="' . $register . '"/>
        return $form;
    }
}