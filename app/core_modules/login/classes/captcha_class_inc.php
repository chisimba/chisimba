<?php
/**
 *
 * Generate and verify a simple captcha
 *
 * Generate and verify a simple captcha for use when the login fail
 * reaches 4 tries. This is meant to be used with Ajax calls, not rendered
 * in a template.
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
 * @package   login
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://www.chisimba.com
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
 * Generate and verify a simple captcha
 *
 * Generate and verify a simple captcha for use when the login fail
 * reaches 4 tries. This is meant to be used with Ajax calls, not rendered
 * in a template.
*
* @author Derek Keats <derek@dkeats.com>
* @package login
*
*/
class captcha extends object
{

    /**
    *
    * Intialiser which is necessary but not used here
     *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table here
    }

    /**
    *
    * Generate a captcha and send it as a JPEG
    *
    * @access public
    * @return string The rendered image with headers
    *
    */
    public function show()
    {
        $ranStr = md5(microtime());
        $ranStr = substr($ranStr, 0, 6);
        $_SESSION['cap_code'] = $ranStr;
        $capImg = $this->getResourceUri('/img/cap_bg.jpg', 'login');
        $newImage = imagecreatefromjpeg($capImg);
        $txtColor = imagecolorallocate($newImage, 0, 0, 0);
        imagestring($newImage, 5, 5, 5, $ranStr, $txtColor);
        header("Content-type: image/jpeg");
        imagejpeg($newImage);
    }

    /**
    *
    * Verify the captcha and send a string back for ajax to use.
    *
    * @access public
    * @return string OK or NOTOK depending on success
    *
    */
    public function verifyCaptcha()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->getParam('captcha', NULL) == $_SESSION['cap_code']) {
                // Captcha verification is Correct.
                return 'ok';
            } else {
                // Captcha verification is wrong.
                return 'notok'  . $this->getParam('captcha', "NOTSET") . "--" . $_SESSION['cap_code'];
            }
        } else {
            return 'notok';
        }
    }

}
?>