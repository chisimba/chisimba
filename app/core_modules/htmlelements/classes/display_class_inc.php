<?php
/*************************************************************
*                         Display access class             *
*                  Created by: Fernando Martinez             *
*                        05/02/04                            *
*                  Last Modified: 29/05/04                   *
*                  Modified by: Fernando                     *
**************************************************************/

$siteSkin = "cil_blue_skin.php";

/**
 * Description for include_once
 */
include_once('../config/'.$siteSkin);
?>
<link rel=StyleSheet href="../config/<? echo $skincss ?>" type="text/css">
<?
/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 3
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

// this class should provide functionality to display a page
// ie, generate html to send to client

/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class Display
{

    /**
     * Description for var
     * @var    mixed 
     * @access public
     */
  var $webpage;
  
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown &$webpage Parameter description (if any) ...
     * @return void   
     * @access public 
     */
  function Display(&$webpage)
  {
    $this->webpage = &$webpage;
  }
  
  // display a string


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $s Parameter description (if any) ...
     * @return void   
     * @access public 
     */
  function String($s)
  {
    echo($s);
  }
  
  // display page header (title)


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
  function Header()
  {
    $s="<html><head><title>".$this->webpage->GetTitle()."</title>".
    $this->webpage->GetHead()."</head>";
    echo($s);
  }
  
  // display page body


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
  function Body()
  {
    $s.="<body>".$this->webpage->GetBody();
    echo($s);
  }
  
  // display page foot or bottom


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
  function Bottom()
  {
    $s=$this->webpage->GetBottom()."</body></html>";
    echo($s);
  }
  
  // display whole page


    /**
     * Short description for function
     * 
     * Long description (if any) ...
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
  
  // display fatal error


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $err Parameter description (if any) ...
     * @return void   
     * @access public 
     */
  function FatalError($err)
  {
    die($err);
  }
}
?>