<?php
/**
 * Context Content Dynamic Blocks
 *
 * Class to generate the content of dynamic blocks in context content
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
 * @version    $Id: dynamicblocks_contextcontent_class_inc.php 11217 2008-10-30 20:45:37Z charlvn $
 * @package    contextcontent
 * @author     Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
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
 * Context Content Dynamic Blocks
 *
 * Class to generate the content of dynamic blocks in context content
 *
 * @author Tohir Solomons
 *
 */
class dynamicblocks_contextcontent extends object
{

    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objContextChapters = $this->getObject('db_contextcontent_contextchapter');
        $this->objContentOrder = $this->getObject('db_contextcontent_order');
        $this->loadClass('link', 'htmlelements');
    }
    
    /**
     * Method to render the contents of a chapter as a block
     * @param string $id Record Id of the block
     * @return string Contents of Chapter as a block
     */
    public function renderChapter($id)
    {
        $chapter = $this->objContextChapters->getRow('id', $id);
        
        if ($chapter == FALSE) {
            return '';
        } else {
            return $this->objContentOrder->getTree($chapter['contextcode'], $chapter['chapterid'], 'htmllist');
        }
    }
    
    /**
     * Method to list chapters in a context
     * @param string $contextCode Context Code
     * @return list of chapters as a block
     */
    public function listChapters($contextCode)
    {
        $chapters = $this->objContextChapters->getContextChapters($contextCode);
        
        if (count($chapters) == 0) {
            return '<div class="noRecordsMessage">'.$this->objLanguage->code2Txt('mod_contextcontent_contexthasnochaptersorcontent', 'contextcontent', NULL, 'This [-context-] does not have chapters or content').'</div>';
        } else {
            $str = '<ol>';
            foreach ($chapters as $chapter)
            {
                $link = new link ($this->uri(array('action'=>'viewchapter', 'id'=>$chapter['chapterid']), 'contextcontent'));
                $link->link = $chapter['chaptertitle'];
                
                $str .= '<li>'.$link->show().'</li>';
            }
            
            $str .= '</ol>';
            
            return $str;
        }
    }
    
    /**
     * Method to list chapters in a context
     * @param string $contextCode Context Code
     * @return list of chapters as a block
     */
    public function listChaptersWide($contextCode)
    {
        return $this->listChapters($contextCode);
    }

}


?>