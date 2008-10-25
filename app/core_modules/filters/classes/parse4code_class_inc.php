<?php

/**
 * Class for parsing programming language code and syntax highlighting them
 * 
 * tags such as 
 *     [code lang="php"]
 *       $a="Hello";
 *       $b="World";
 *       echo $a . " " . $b .".";
 *     [code]
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
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id: parse4kngtext_class_inc.php 2808 2007-08-03 09:05:13Z paulscott $
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
 * Class for parsing programming language code and syntax highlighting them
 * 
 * tags such as 
 *     [code lang="php"]
 *       $a="Hello";
 *       $b="World";
 *       echo $a . " " . $b .".";
 *     [code]
* 
* @author     Tohir Solomons
* @version    $Id: parse4kngtext_class_inc.php 2808 2007-08-03 09:05:13Z paulscott $
* @copyright  2003 GPL
*/
class parse4code extends object 
{
    /**
     * Constructor
     */
    public function init()
    {
        
    }

    /**
     * Method to parse text for [code] blocks
     * 
     * @param  string $txt Text to parse
     * @return string parsed text with code blocks syntax highlighted
     * @access public 
     */
    public function parse($txt)
    {
        preg_match_all('%\[code\ lang=("|&quot;)\w*("|&quot;)\].*?\[/code\]%si', $txt, $result, PREG_PATTERN_ORDER);
        $results = $result[0];
        
        //var_dump($results);
        if (count($results) > 0) {
            foreach ($results as $result)
            {
                $txt = str_replace($result, $this->renderText($result), $txt);
            }
        }
        
        return $txt;
    }
    
    /**
     * Method to convert the [code] into syntax highlighted text
     * @param string $snippet Snippet of Text
     * @return string Syntax highlighted text
     */
    private function renderText($snippet)
    {
        preg_match_all('%\[code\ lang=(?:"|&quot;)(?P<language>\w*)(?:"|&quot;)\](?P<code>.*?)\[/code\]%si', $snippet, $result, PREG_PATTERN_ORDER);
        
        //echo '<pre>'.print_r($result).'</pre>';
        
        $source = strip_tags($result['code'][0]);
        $source = str_replace('&nbsp;', ' ', $source);
        $source = html_entity_decode($source);
        
        $geshiwrapper = $this->newObject('geshiwrapper', 'utilities');
        $geshiwrapper->source = $source;
        $geshiwrapper->language = $result['language'][0];
        
        $geshiwrapper->startGeshi();
        
        return $geshiwrapper->show();
    }

} # end of class

?>