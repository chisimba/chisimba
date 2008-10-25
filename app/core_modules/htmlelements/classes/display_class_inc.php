<?php
/**
 * display_class_inc.php
 *
 * This file contains the display class which can be used to generate
 * html of an entire page
 *
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
 * @package   htmlelements
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

$siteSkin = "cil_blue_skin.php";

//Include the site skin
include_once('../config/'.$siteSkin);
?>
<link rel=StyleSheet href="../config/<?php echo $skincss ?>" type="text/css">
<?
/**
 * Display Class
 *
 * This class can be used to generate entire html pages to send to a client
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class Display
{

    /**
     * Variable used to store the HTML of the webpage as it is generated
     *
     * @var    string
     * @access public
     */
  public $webpage;

    /**
     * Method to load in the webpage
     *
     * @param  object $webpage reference to the webpage object
     * @return void
     * @access public
     */
  public function Display(&$webpage) {
    $this->webpage = &$webpage;
  }

    /**
     * Method to write a string to the display
     * this method simply wraps the native
     * php function echo() with no
     * additional functionality
     *
     * @param  string $s the string to write
     * @return void
     * @access public
     */
  public function String($s) {
    echo($s);
  }

    /**
     * This method displays the HTML page header
     *
     * @return void
     * @access public
     */
  function Header() {
    $s="<html><head><title>".$this->webpage->GetTitle()."</title>".
    $this->webpage->GetHead()."</head>";
    echo($s);
  }

    /**
     * Method to display the page body
     *
     * @return void
     * @access public
     */
  function Body() {
    $s.="<body>".$this->webpage->GetBody();
    echo($s);
  }

    /**
     * Method to display page footer
     *
     * @return void
     * @access public
     */
  function Bottom() {
    $s=$this->webpage->GetBottom()."</body></html>";
    echo($s);
  }

    /**
     * Method to display the entire page
     *
     * @return void
     * @access public
     */
  function Page()
  {
    $this->Header();
    $this->Body();
    $this->Bottom();
  }

    /**
     * Method to display an error
     * This method simply wraps the native php
     * function die() offering no addtional
     * functionality
     *
     * @param  string $err The error text.
     * @return void
     * @access public
     */
  function FatalError($err)
  {
    die($err);
  }
}
?>