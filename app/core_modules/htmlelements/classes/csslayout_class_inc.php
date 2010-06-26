<?php

/**
 * csslayout_class_inc.php
 *
 * This file contains the csslayout class which is ued to help
 * developer make use of two or three column layouts for module
 * templates
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
require_once("ifhtml_class_inc.php");


/**
 * CSS Layout Class
 *
 *
 * The Css Layout class helps developers to display either two or three column layouts using CSS.
 * The layouts are particular to the ones used in the Chisimba system and works hand in hand with the stylesheet.
 * Any additional layouts implemented in this class should also correspond with the stylesheet
 *
 * One of the problems with CSS Layouts is that it is difficult to control the height of columns.
 * We have overcome this by using javascript. An article on this is available at:
 * http://www.sitepoint.com/print/exploring-limits-css-layout
 *
 * A note on how the layouts look:
 * The two column layout has a left side column and a broad middle column
 * The three column layout has a left and right side column and a broad middle column
 *
 * NB! At present there is no accommodation for a two column layout with a broad
 * middle column and a right side column
 *
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.sitepoint.com/print/exploring-limits-css-layout
 * @example
 *  $cssLayout = $this->newObject('csslayout', 'htmlelements');
 *  $cssLayout->setNumColumns(3);
 *  $cssLayout->setLeftColumnContent('Content in Left Column');
 *  $cssLayout->setMiddleColumnContent('Content in Middle Column');
 *  $cssLayout->setRightColumnContent('Content in Right Column');
 *  echo $cssLayout->show();
 */
class csslayout extends object implements ifhtml
{

    /**
     * The number of columns the layout should have: either two or three
     *
     * @var integer $numColumns
     */
    public $numColumns;

    /**
     * The contents of the left hand side column
     *
     * @var string $leftColumnContent
     */
    public $leftColumnContent;

    /**
     * The contents of the right hand side column
     *
     * @var string $rightColumnContent
     */
    public $rightColumnContent;

    /**
     * The contents of the middle column
     *
     * @var string $middleColumnContent
     */
    public $middleColumnContent;

    /**
     * The contents of the footer
     *
     * @var string $footerContent
     */
    public $footerContent;

    /**
     * The current skin engine in being used
     *
     * @var string $middleColumnContent
     */
    public $skinEngine;

    /**
    *
    * An object for fixing the column lengths
    *
    * @var string $middleColumnContent
    */
    public $objFixlength;

    /**
    *
    * The type of layout to produce. Valid types are:
    *    standard - normal 2 column layout
    *    canvas_stacked - the left and right columns are stacked within the canvas
    *    canvas_below - the left and right columns appear below the middle one
    *    canvas_slidein - the left and right columns slide in from the sides
    *
    */

    /**
    * Constructor Method for the class
    *
    * This method sets the default number of columns
    * to two, and sets the content of all the columns
    * to be empty.
    */
    public function init()
    {
        $this->numColumns = 2;
        $this->leftColumnContent = NULL;
        $this->rightColumnContent = NULL;
        $this->middleColumnContent = NULL;
        $this->isCanvasEnabled = TRUE;
	$this->objSkin = $this->getObject('skin', 'skin');
	$this->skinEngine = $this->objSkin->getSkinEngine();
        $this->objFixlength = $this->getObject('cssfixlength', 'htmlelements');
        $this->loadSettings();
    }

    private function loadSettings()
    {
        if (isset ($_SESSION['skinhassettings'])) {
            if ($_SESSION['skinhassettings']) {
                $this->skinVersion = $_SESSION['skinVersion'];
                $this->layoutType = $_SESSION['layoutType'];
                return TRUE;
            }
        } else {
            $objSkin = $this->getObject('skin', 'skin');
            $configFile = $objSkin->getSkinLocation().'/settings.json';
            if(file_exists($configFile)) {
                $jsonRaw = file_get_contents($configFile);
                $jsonObj = json_decode($jsonRaw);
                $this->skinVersion = $jsonObj->skinVersion;
                $this->layoutType = $jsonObj->layoutType;
            } else {
                // Read the old skinversion.txt
                $file = $objSkin->getSkinLocation().'/skinversion.txt';
                $this->skinVersion = trim(file_get_contents($file));
                $this->layoutType = "default";
            }
            $_SESSION['skinVersion'] = $this->skinVersion;
            $_SESSION['layoutType'] = $this->layoutType;
            $_SESSION['skinhassettings'] = TRUE;
            return TRUE;
        }
    }

    /**
    * Method to set the number of columns the layout will have.
    *
    * We only cater for two or three column layouts as per the KEWL.Nextgen project.
    * This function first checks that the parameter is either two or three (for the columns) before assigning it to the variable.
    *
    * @param integer $number The number of columns
    * @return void
    * @access public
    */
    public function setNumColumns($number)
    {
        if ($number == 1 OR $number == 2 OR $number == 3) {
            $this->numColumns = $number;
        }
    }

    /**
    * Method to set the content of the left column
    *
    * @param string $content Content of the left hand side column
    * @return void
    * @access public
    */
    public function setLeftColumnContent($content)
    {
        $this->leftColumnContent = $content;
    }

    /**
    * Method to set the content of the right column
    *
    * @param string $content Content of the right hand side column
    * @return void
    * @access public
    */
    public function setRightColumnContent($content)
    {
        $this->rightColumnContent = $content;
    }

    /**
    * Method to set the content of the middle column
    *
    * @param string $content Content of the middle column
    * @return void
    * @access public
    */
    public function setMiddleColumnContent($content)
    {
        $this->middleColumnContent = $content;
    }

    /**
    * Method to set the content of the footer
    *
    * @param string $content Content of the footer
    * @return void
    * @access public
    */
    public function setFooterContent($content)
    {
        $this->footerContent = $content;
    }

    /**
    * Show method - Method to display the layout
    * This method also places the appropriate javascript in the header
    *
    * @return string $result The rendered object in HTML code
    * @access public
    */
    public function show()
    {
        
        $this->setVar('numColumns', $this->numColumns);

        if ($this->skinEngine == 'default' 
         || $this->skinEngine == ''
         || $this->skinEngine == 'html5' ) {
            $methd = $this->getSkinVersion();
            return $this->$methd();
        // For the UWC skin engine
        } else if ($this->skinEngine == 'university') {
            return $this->skinUniversity();
        }
    }

    /**
     *
     * Get the skin version so that it can be turned into a method to call
     *
     * @return string The skin version
     * @access Private
     *
     */
    private function getSkinVersion()
    {
        if ($this->skinVersion == '2.0') {
            return "skinTwo";
        } elseif ($this->skinVersion == '3.0') {
            return "skinThree";
        } else {
            return "skinDefault";
        }
    }

    /**
     *
     * Method to return the contents prepared for the legacy skin. This is just
     * copied from the old show method. Eventually it will need to be deprecated.
     *
     * @return string The formatted content
     * @access Private
     *
     */
    private function skinDefault()
    {
        
        // Fix the column lengths.
        if ($this->numColumns == 2) {
                $this->putTwoColumnFixInHeader();
        } else {
                // else, load the three column javascript fix
                $this->putThreeColumnFixInHeader();
        }
        // Send the number of columns to the page template
        // Useful for modifications on that level
        $this->setVar('numColumns', $this->numColumns);
        // Depending on the number of columns, use approprate css styles.
        if ($this->numColumns == 1) {
            $result = '
                <div id="onecolumn">
                <div id="content">
                <div id="contentcontent">
                '.$this->middleColumnContent.'
                </div>
                </div>
                </div>';
        } else if ($this->numColumns == 2) {
            $result = '
                <div id="twocolumn">
                <div id="wrapper">
                <div id="content">
                <div id="contentcontent">
                '.$this->middleColumnContent.'
                </div>
                </div>
                </div>';
            $result .= '
                <div id="left">
                <div id="leftcontent">
                '.$this->leftColumnContent.'
                </div>
                </div>
                </div>';
        } else {
            // for a three column layout, first load the right column, then the middle column
            $result = '
                <div id="threecolumn">
                <div id="wrapper">
                <div id="content">
                <div id="contentcontent">
                '.$this->middleColumnContent.'
                </div>
                </div>
                </div>';
            $result .= '
                <div id="left">
                <div id="leftcontent">
                '.$this->leftColumnContent.'
                </div>
                </div>';
            $result .= '
                <div id="right">
                <div id="rightcontent">
                '.$this->rightColumnContent.'
                </div>
                </div>
                </div>';
        }
        return $result;
    }

    /**
     *
     * Return the content formatted for version 2 skins
     *
     * @return string The formatted content
     * @access Private
     *
     */
    private function skinTwo()
    {
        if ($this->numColumns == 1) {
            $result = '	<div id="content"> '. $this->middleColumnContent .'</div>';
        } elseif ($this->numColumns == 2) {
            $leftCol = '<div id="left">'.$this->leftColumnContent.'</div>';
            $middleCol = '<div id="content"> '. $this->middleColumnContent .'</div>';
            $result = '	<div id="twocolumn">
              ' . $leftCol .'
              ' . $middleCol . '
              </div>
              ';
             $this->objFixlength->fixTwoSkinTwo();
        } elseif  ($this->numColumns == 3)  {
            // for a three column layout, first load the right column, then the middle column.
            $result = '	<div id="threecolumn">
                <div id="left">'.$this->leftColumnContent.'</div>
                <div id="right">'.$this->rightColumnContent.'</div>
                <div id="content"> '. $this->middleColumnContent .'</div>
                </div>';
             $this->objFixlength->fixThreeSkinTwo();
        }
        return $result;
    }

    /**
     *
     * Return the content formatted for version 3 skins
     *
     * @return string The formatted content
     * @access Private
     *
     */
    private function skinThree()
    {
        if ($this->numColumns == 1) {
            // Put the middle bit in region 2 for canvas enabled skins
            $result = $this->addBodyRegion($this->middleColumnContent, "Region2");
            $result = '<div id="onecolumn">
                ' . $result . '
              </div>
              ';
        } elseif ($this->numColumns == 2) {
            // Put the left bit in region 1 for canvas enabled skins
            $leftCol = $this->addBodyRegion($this->leftColumnContent, "Region1");
            // Put the middle bit in region 2 for canvas enabled skins
            $middleCol = $this->addBodyRegion($this->middleColumnContent, "Region2");
            $result = '<div id="twocolumn">

              ' . $leftCol . '
              ' . $middleCol . '

              </div>
              ';
            // Use fixThree even for two columns
            $this->objFixlength->fixThree();
        } elseif  ($this->numColumns == 3)  {
            if (!isset($this->layoutCode)) {
                $this->layoutCode = "_default";
            }

            switch ($this->layoutCode) {
                case "canvas_stacked31":
                    $narrowCol = $this->addBodyRegion(
                       $this->rightColumnContent
                      . $this->leftColumnContent, "Region3"
                    );
                    $middleCol = $this->addBodyRegion($this->middleColumnContent, "Region2");
                    $result = '<div id="twocolumn">

                        ' . $narrowCol . '
                        ' . $middleCol . '

                        </div>
                    ';
                    $this->objFixlength->fixTwo();
                    break;

                case "canvas_stacked13":
                    $narrowCol = $this->addBodyRegion(
                       $this->leftColumnContent
                      . $this->rightColumnContent, "Region3"
                    );
                    $middleCol = $this->addBodyRegion($this->middleColumnContent, "Region2");
                    $result = '<div id="twocolumn">

                        ' . $narrowCol . '
                        ' . $middleCol . '

                        </div>
                    ';
                    $this->objFixlength->fixTwo();
                    break;


                case "_default":
                default:
                    // Put the left bit in region 1 for canvas enabled skins
                    $leftCol = $this->addBodyRegion($this->leftColumnContent, "Region1");
                    // Put the middle bit in region 2 for canvas enabled skins
                    $middleCol = $this->addBodyRegion($this->middleColumnContent, "Region2");
                    // Put the right bit in region 2 for canvas enabled skins
                    $rightCol = $this->addBodyRegion($this->rightColumnContent, "Region3");
                    $result = '	<div id="threecolumn">

                        ' . $leftCol . '
                        ' . $rightCol . '
                        ' . $middleCol . '

                        </div>';
                    $this->objFixlength->fixThree();
                    break;
            }
        }
        return $result;
    }

    /**
    *
    * Method to load place a three column javascript fix into the header of a webpage
    * This method can also be used by other modules that just want to load the javascript fix - e.g. splash screen (prelogin)
    *
    * @access public
    * @return void
    */
    public function putThreeColumnFixInHeader()
    {
        if ($this->skinEngine == 'default' || $this->skinEngine == '') {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('x_minified.js','htmlelements'));
            $this->appendArrayVar('headerParams', $this->objFixlength->fixThreeColumnLayoutJavascript());
            $this->appendArrayVar('bodyOnLoad',$this->objFixlength->bodyOnLoadScript());
        }
    }

    /**
    * Method to load place a two column javascript fix into the header of a webpage
    * This method can also be used by other modules that just want to load the javascript fix - e.g. splash screen (prelogin)
    *
    * @return void
    * @access public
    *
    */
    public function putTwoColumnFixInHeader()
    {
        if ($this->skinEngine == 'default' || $this->skinEngine == '') {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('x_minified.js','htmlelements'));
            $this->appendArrayVar('headerParams', $this->objFixlength->fixTwoColumnLayoutJavascript());
            $this->appendArrayVar('bodyOnLoad',$this->objFixlength->bodyOnLoadScript());
        }
    }

    /**
     *
     * Wrap the content in one of the three canvas body regions as applicable
     * to version 3+ skins with canvas support.
     *
     * @param stromg $content The content to wrap
     * @param string $region The region
     * @return string the wrapped content
     *
     */
    public function addBodyRegion($content, $region)
    {
        return "<div class='Canvas_Column' id='Canvas_Content_Body_$region'>\n$content\n</div>";
    }













    // NIC - can I remove this or put in a separate class?







     /**
     *
     * Return the content formatted forthe university skin (whatever that is)
     * THis was added by Charl Mert but totally not documented.
     *
     * @return string The formatted content
     * @access Private
     *
     */
    private function skinUniversity()
    {
        // Depending on the number of columns, use approprate css styles.
        if ($this->numColumns == 1) {
        $result = '
            <div id="main">
            <div id="onecolumn">

            <div id="content">
              <div id="contentcontent">
                    '.$this->middleColumnContent.'
              </div>
            </div>

             <!--<div id="footer">
                    '.$this->footerContent.'
              </div>-->

            </div>
            </div>';
        } else if ($this->numColumns == 2) {
            $result = '
                <div id="main">
                <div id="twocolumn">
                <div id="wrapper">

                  <div id="column_left">
                        '.$this->leftColumnContent.'
                  </div>

                <div id="content">
                  <div id="contentcontent">
                        '.$this->middleColumnContent.'
                  </div>
                </div>
                  <!--<div id="footer">
                        '.$this->footerContent.'
                  </div>-->

                </div>
                </div>
                </div>';
        // One presumes this to be the version 2 skin. Dammit, why can't people
        //    document their code? Simple courtesy towards another human being.
        } else {
                // for a three column layout, first load the right column, then the middle column
                $result = '
                    <div id="main">
                    <div id="threecolumn">
                    <div id="wrapper">


                      <div id="column_left">
                            '.$this->leftColumnContent.'
                      </div>

                    <div id="content">
                    <div id="contentcontent">
                            '.$this->middleColumnContent.'
                    </div>
                    </div>

                      <div id="column_right">
                            '.$this->rightColumnContent.'
                      </div>

                      <!--<div id="footer">
                            '.$this->footerContent.'
                      </div>-->

                    </div>
                    </div>
                    </div>';

        }

        return $result;
    }
}
?>