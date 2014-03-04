<?php

/**
 *
 * A utility class with helper methods
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
 * @version
 * @package    mynotes
 * @author     Nguni Phakela info@nguni52.co.za
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
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
// end security check

/**
 * 
 * A utility class with helper methods
 * 
 * @category  Chisimba
 * @author    Nguni Phakela
 * @version
 * @copyright 2010 AVOIR
 *
 */
class utility extends object {
    
    /*
     * @var $module The name of the current module, mynotes
     * @access private
     */
    private $module;

    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access private
     *
     */
    private $objLanguage;

    /**
     * Method to construct the class and initialise objects to be used.
     *
     * @access public
     * @return VOID
     */
    public function init() {
        $this->module = "mynotes";

        $this->objLanguage = $this->getObject('language', 'language');
        $this->objLink = $this->loadClass('link', 'htmlelements');
        $this->objUser = $this->getObject('user', 'security');
        $this->objDbmynotes = $this->getObject('dbmynotes', $this->module);

        $this->uid = $this->objUser->userId();
    }

    /*
     * Method to truncate the given content so that it is limited to 200 words
     * 
     * @access public
     * @param $string The content to be truncated
     * @param $length The expected length of the content to be truncated
     * @param $ellipsis The ellipsis for the content
     * @return String with truncated content that has appended to it the ellipsis 
     * 
     */
    public function wordlimit($string, $length = 50, $ellipsis = " ...") {
        $words = explode(' ', $string);
        if (count($words) > $length) {
            return implode(' ', array_slice($words, 0, $length)) . $ellipsis;
        } else {
            return $string . $ellipsis;
        }
    }

    /*
     * Method used to process the tags that are used by the tag cloud utility.
     * 
     * @param $tagCloud The array that has all the information for each tag that is
     *                  being processed.
     * @return array that is used by tag cloud containing tags and weights, with 
     * tag url.
     * 
     */
    public function processTags($tagCloud) {
        $entry = array();
        foreach ($tagCloud as $arrs) {
            if (!empty($arrs['name'])) {
                $entry [] = array('name' => $arrs['name'],
                    'url' => $this->uri(array(
                        'action' => 'search',
                        'srchstr' => $arrs['name'],
                        'srchtype' => 'tags'), $this->module),
                    'weight' => $arrs['count'] * 5,
                    'time' => time());
            }
        }

        return $entry;
    }

    /*
     * Method use to create previous and next links for the list of notes
     * 
     * @access public
     * @param $prevPageNum The previous page number
     * @param $nextPageNum The next page number
     * @return String with the previous and next links if either of them exists
     * 
     */
    public function getPrevNextLinks($prevPageNum, $nextPageNum) {
        $prevLabel = $this->objLanguage->languageText('mod_mynotes_prev', $this->module, 'TEXT: mod_mynotes_prev, not found');
        $nextLabel = $this->objLanguage->languageText('mod_mynotes_next', $this->module, 'TEXT: mod_mynotes_next, not found');

        if (empty($prevPageNum) && empty($nextPageNum)) {
            $prevPageNum = 2;
            $nextPageNum = 7;
        }

        // get previous and next link
        if ($prevPageNum == 2) {
            // display prev but not as a link
            $prevLink = "";//'&#171; ' . $prevLabel;
        } else {
            $link = new link($this->uri(array("action" => "view", 'prevnotepage' => $prevPageNum), $this->module));
            $link->link = $prevLabel;
            $prevLink = '&#171; ' . $link->show();
        }

        $noteListCount = $this->objDbmynotes->getListCount($this->uid, $prevPageNum, $nextPageNum + 1);
        if ($noteListCount <= 5) {
            $nextLink = "";//$nextLabel . ' &#187;';
        } else {
            $link = new link($this->uri(array("action" => "view", 'nextnotepage' => $nextPageNum), $this->module));
            $link->link = $nextLabel . ' &#187;';
            $nextLink = $link->show();
        }

        $ret = $prevLink . '&nbsp;&nbsp;&nbsp;' . $nextLink;

        return $ret;
    }
}
?>