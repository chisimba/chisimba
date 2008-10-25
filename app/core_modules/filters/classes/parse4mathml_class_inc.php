<?php

/**
 * Class to Parse for MathML expressions and render them for display in browsers
 *
 * This class takes a string, and then searches for MathML expressions that are wrapped in
 * [MATH] tags and renders them for proper MathML display. A sample text is:
 *
 * [MATH]x+y=z[/MATH]
 *
 * The text needs to be enclosed by the [MATH] tags, else it will be displayed as normal HTML.
 *
 * Two rendering options are available:
 * 1) The first is to render it as an image (default)
 * 2) The second is to render it in an iframe with a MathML doctype and stylesheet
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
 * @package   filters
 * @author    Jeremy 'O Connor <joconnor@uwc.ac.za>
 * @author    Tohir Solomons   <tsolomons@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
 * Class to Parse for MathML expressions and render them for display in browsers
 *
 * This class takes a string, and then searches for MathML expressions that are wrapped in
 * [MATH] tags and renders them for proper MathML display. A sample text is:
 *
 * [MATH]x+y=z[/MATH]
 *
 * The text needs to be enclosed by the [MATH] tags, else it will be displayed as normal HTML.
 *
 * Two rendering options are available:
 * 1) The first is to render it as an image (default)
 * 2) The second is to render it in an iframe with a MathML doctype and stylesheet
 *
 * @author Jeremy O'Connor
 * @author Tohir Solomons
 */
class parse4mathml extends object
{

    /**
    * @var string $renderType Mode to render MathML: either image or iframe
    */
    public $renderType = 'image';

    /**
    * Constructor
    */
    public function init()
    {
    }

    /**
    * Method to Parse a String for MathML expressions and render them
    * @param  string $str String to Parse
    * @return string String with MathML expressions rendered as either iframes or images
    */
    public function parseAll($str)
    {
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the mathml module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('mathml');
        if ($isRegistered){
            $this->objMathImg = $this->getObject('mathimg','mathml');
            // Search for all items in [MATH] Tags
            $search = '/\[MATH\](.*)\[\/MATH\]/U';
            // Get All Matches
            preg_match_all($search, $str, $matches, PREG_PATTERN_ORDER);
            // Check whether there are matches
            if (!empty($matches)) {
                // Go Through Matches
                foreach ($matches[1] as $match)
                {
                    // Render Result
                    if ($this->renderType == 'iframe') { // Either Iframe
                        $replace = $this->renderAsIframe($match);
                    } else { // Or Image
                        $replace = $this->renderAsImage($match);
                    }
                    // Replace Text
                    $str = preg_replace('/'.preg_quote('[MATH]'.$match.'[/MATH]','/').'/', $replace, $str);
                }
            }

        }

        // Return String
        return $str;
    }

    /**
    * Method to Render a MathML expression as iframe
    * @access private
    * @param  string  $match Expression to Render
    * @return string
    */
    private function renderAsIframe($match)
    {
        $iframe = new iframe();
        $iframe->width = 150;
        $iframe->height = 120;
        $iframe->src = $this->uri(array('action'=>'render','formula'=>$match),'mathml');
        $iframe->frameborder = '0';

        return $iframe->show();
    }

    /**
    * Method to Render a MathML expression as an Image
    * @access private
    * @param  string  $match Expression to Render
    * @return string  Image Tag with Path to Image
    */
    private function renderAsImage($match)
    {
        return $this->objMathImg->render($match);
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $str Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function parse($str)
    {
        return $this->parseAll($str);
    }
}
?>