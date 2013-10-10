<?php

/**
 * Class to Control which Chapters should be available in a context
 *
 * This allows for a single chapter to be reused in multiple contexts
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
 * @version    $Id: db_contextcontent_contextchapter_class_inc.php 15438 2009-11-06 16:10:24Z davidwaf $
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
 * Class to Control which Chapters should be available in a context
 *
 * This allows for a single chapter to be reused in multiple contexts
 *
 * @author Tohir Solomons
 *
 */
class db_learningcontent_contextchapter extends dbtable {

/**
 * Constructor
 */
    public function init() {
        parent::init('tbl_learningcontent_chaptercontext');
        $this->objUser =& $this->getObject('user', 'security');

    }

    /**
     * Method to get the number of pages in a context
     * @param string $contextCode Code of Context to get num pages
     */
    public function getNumContextChapters($contextCode) {
        return $this->getRecordCount('WHERE contextcode=\''.$contextCode.'\'');
    }

    /**
     * Method to get the list of chapters in a particular context
     * @param string $context Context Code
     * @return array List of Chapters
     */
    public function getContextChapters($context) {

        return $this->query($this->getContextChaptersSQL($context));
    }


    /**
     * Method to get the SQL statement to get the list of chapters in a context
     * @param string $context Context Code
     * @return string SQL statement
     */
    public function getContextChaptersSQL($context) {
        $sql = 'SELECT tbl_learningcontent_chaptercontext.visibility, tbl_learningcontent_chaptercontext.scorm, tbl_learningcontent_chaptercontent. *, tbl_learningcontent_chaptercontext.id as contextchapterid, (Select count(id) FROM  tbl_learningcontent_order WHERE tbl_learningcontent_chaptercontent.chapterid = tbl_learningcontent_order.chapterid) as pagecount
FROM tbl_learningcontent_chaptercontext, tbl_learningcontent_chaptercontent
WHERE (tbl_learningcontent_chaptercontent.chapterid = tbl_learningcontent_chaptercontext.chapterid) AND tbl_learningcontent_chaptercontext.contextcode=\''.$context.'\' ORDER BY tbl_learningcontent_chaptercontext.chapterorder';

        return $sql;
    }
    /**
     * Method to get the title of a chapter by providing the record id of the chapter
     * @param string $chapterId
     * @return string Title of Chapter : FALSE
     */
    public function getContextChapterTitle($chapterId) {
        $sql = 'SELECT tbl_learningcontent_chaptercontent.chaptertitle FROM tbl_learningcontent_chapters, tbl_learningcontent_chaptercontent WHERE (tbl_learningcontent_chaptercontent.chapterid = tbl_learningcontent_chapters.id) AND tbl_learningcontent_chapters.id=\''.$chapterId.'\' LIMIT 1';

        $results = $this->getArray($sql);

        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0]['chaptertitle'];
        }
    }

    /**
     * Method to get the details of a chapter
     * @param string $chapterid Record Id of the Chapter
     * @return array Details of the chapter
     */
    public function getChapter($chapterid) {
        $sql = 'SELECT tbl_learningcontent_chaptercontext.visibility, tbl_learningcontent_chaptercontent. *, tbl_learningcontent_chaptercontext.id as contextchapterid
FROM tbl_learningcontent_chaptercontext, tbl_learningcontent_chaptercontent, tbl_learningcontent_chapters
WHERE (tbl_learningcontent_chaptercontent.chapterid = tbl_learningcontent_chaptercontext.chapterid AND tbl_learningcontent_chaptercontext.chapterid = tbl_learningcontent_chapters.id) AND tbl_learningcontent_chapters.id=\''.$chapterid.'\' LIMIT 1';

        $results = $this->getArray($sql);

        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }



    /**
     * Method to add a chapter to a context
     * @param string $chapterId Record Id of the CHapter
     * @param string $context Context Code
     * @param string $visibility Visibility status of chapter within a context
     */
    public function addChapterToContext($chapterId, $context, $visibility, $scorm = 'N') {
        $objChapterContent = $this->getObject('db_learningcontent_chaptercontent');
        $chapterContent = $objChapterContent->getChapterContent($chapterId);

        if ($chapterContent == FALSE) {
            return FALSE;
        } else {
            $order = $this->getLastOrder($context)+1;

            $result = $this->insertTitle($context, $chapterId, $order, $visibility, $scorm);

            // If Successfully Added, Index Chapter
            if ($result != FALSE) {
                $this->indexChapter($context, $chapterContent);

                $this->addDynamicBlocksToDB($result, $context, $chapterContent['chaptertitle']);
            }



            return $result;
        }
    }


    /**
     * Method to add a dynamic block
     * @param string $recordId Record Id of the Item
     * @param string $context Context Code
     * @param string $chapterTitle Chapter Title
     */
    private function addDynamicBlocksToDB($recordId, $context, $chapterTitle) {
        $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

        // Add Chapter Block
        $objDynamicBlocks->addBlock(
            'contextcontent',
            'dynamicblocks_contextcontent',
            'renderChapter',
            $recordId,
            'Chapter: '.$chapterTitle,
            'context',
            $context,
            'wide');

        // Add List of Chapters - Wide
        $objDynamicBlocks->addBlock(
            'contextcontent',
            'dynamicblocks_contextcontent',
            'listChaptersWide',
            $context,
            'List of Content Chapters',
            'context',
            $context,
            'wide');

        // Add List of Chapters
        $objDynamicBlocks->addBlock(
            'contextcontent',
            'dynamicblocks_contextcontent',
            'listChapters',
            $context,
            'List of Content Chapters',
            'context',
            $context,
            'small');

    }



    /**
     * Method to add a chapter to a context - Saves Record to Database
     * @param string $chapterId Record Id of the CHapter
     * @param string $context Context Code
     * @param int $order Order of the Item
     * @param string $visibility Visibility status of chapter within a context
     */
    private function insertTitle($context, $chapterId, $order, $visibility='Y', $scorm = 'N') {
        return $this->insert(array(
        'contextcode' => $context,
        'chapterid' => $chapterId,
        'chapterorder' => $order,
        'visibility' => $visibility,
        'scorm' => $scorm,
        'creatorid' => $this->objUser->userId(),
        'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));
    }

    /**
     * Method to get the order of the last chapter in a context
     * @param string $context Context Code
     * @return integer
     */
    private function getLastOrder($context) {
        $sql = 'WHERE contextcode =\''.$context.'\' ORDER BY chapterorder DESC LIMIT 1';
        $result = $this->getAll($sql);

        if (count($result) == 0) {
            return 0;
        } else {
            return $result[0]['chapterorder'];
        }
    }



    /**
     * Method to remove a chapter from a context
     *
     * @param string $chapterId Record Id of the Chapter
     * @param string $context Context Code
     * @return boolean Result of deletion
     */
    public function removeChapterFromContext($chapterId, $context) {
        $results = $this->getAll('WHERE contextcode =\''.$context.'\' AND chapterid=\''.$chapterId.'\' ');
        if (count($results) > 0) {

            $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

            foreach ($results as $item) {
                $this->delete('id', $item['id']);
                $objDynamicBlocks->removeBlock('contextcontent', 'dynamicblocks_contextcontent', 'renderChapter', $item['id'], 'context');
            }

        }

        $objIndexData = $this->getObject('indexdata', 'search');
        $objIndexData->removeIndex('contextcontent_chapter_'.$context.'_'.$chapterId);
    }

    /**
     * Method to move a chapter up
     * @param string $id Record Id of the Chapter
     * @return boolean Result of Chapter Move
     */
    public function moveChapterUp($id) {
        $chapter = $this->getRow('id', $id);

        if ($chapter == FALSE) {
            return FALSE;
        }

        $prevChapterSQL = ' WHERE contextcode=\''.$chapter['contextcode'].'\' AND chapterorder < '.$chapter['chapterorder'].' ORDER BY chapterorder DESC';
        $prevChapter = $this->getAll($prevChapterSQL);

        if (count($prevChapter) == 0) {
            return FALSE;
        } else {
            $prevChapter = $prevChapter[0];

            $this->update('id', $chapter['id'], array('chapterorder'=>$prevChapter['chapterorder']));
            $this->update('id', $prevChapter['id'], array('chapterorder'=>$chapter['chapterorder']));

            return TRUE;
        }


    }

    /**
     * Method to move a Chapter down
     * @param string $id Record Id of the Chapter
     * @return boolean Result of Chapter Move
     */
    public function moveChapterDown($id) {
        $chapter = $this->getRow('id', $id);

        if ($chapter == FALSE) {
            return FALSE;
        }

        $nextChapterSQL = ' WHERE contextcode=\''.$chapter['contextcode'].'\' AND chapterorder > '.$chapter['chapterorder'].' ORDER BY chapterorder';
        $nextChapter = $this->getAll($nextChapterSQL);

        if (count($nextChapter) == 0) {
            return FALSE;
        } else {
            $nextChapter = $nextChapter[0];

            $this->update('id', $chapter['id'], array('chapterorder'=>$nextChapter['chapterorder']));
            $this->update('id', $nextChapter['id'], array('chapterorder'=>$chapter['chapterorder']));

            return TRUE;
        }


    }

    /**
     * Method to update the visibility status of a chapter
     * @param string $id Record Id
     * @param string $visibility Visibility Status
     * @return boolean
     */
    public function updateChapterVisibility($id, $visibility) {
        return $this->update('id', $id, array('visibility'=>$visibility));
    }

    /**
     * Method to check whether a chapter exists in a context or not
     * @param string $contextCode Context Code
     * @param string $chapterId Chapter Id
     * @return boolean
     */
    public function isContextChapter($contextCode, $chapterId) {
        $result = $this->getRecordCount('WHERE contextcode=\''.$contextCode.'\' AND chapterid=\''.$chapterId.'\' ');

        return ($result == 0) ? FALSE : TRUE;
    }

    /**
     * Method to check how many contexts are using a particular chapter
     * @param string $chapterId Chapter Id
     * @return boolean
     */
    public function getNumContextWithChapter($chapterId) {
        return $this->getRecordCount('WHERE chapterid=\''.$chapterId.'\' ');
    }

    /**
     * Method to get the number of contexts that are using a particular chapter
     * @param string $chapterId Chapter Id
     * @return boolean
     */
    public function getContextsWithChapter($chapterId) {
        $results = $this->getAll('WHERE chapterid=\''.$chapterId.'\' ');

        if (count($results) == 0) {
            return FALSE;
        } else {
            $contexts = array();

            foreach ($results as $result) {
                $contexts[] = $result['contextcode'];
            }

            return $contexts;
        }
    }


    /**
     * Method to add a chapter to the context search index
     * @param string $context Context Code
     * @param array $chapter Details of the Chapter
     */
    public function indexChapter($context, $chapter) {
    // Prepare to add context to search index
        $objIndexData = $this->getObject('indexdata', 'search');

        $docId = 'contextcontent_chapter_'.$context.'_'.$chapter['chapterid'];

        $docDate = date('Y-m-d H:M:S');
        $url = $this->uri(array('action'=>'viewchapter', 'id'=>$chapter['chapterid']), 'learningcontent');
        $title = $chapter['chaptertitle'];
        $contents = $chapter['chaptertitle'].' '.$chapter['introduction'];

        $objTrimStr = $this->getObject('trimstr', 'strings');
        $teaser = $objTrimStr->strTrim(strip_tags($chapter['introduction']), 500);

        $userId = $this->objUser->userId();
        $module = 'contextcontent';

        // Todo - Set permissions on entering course, e.g. iscontextmember.
        $permissions = NULL;



        $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, $context, NULL, $permissions);

    }

    /**
     * this gets the chapter id for a give contextcode
     * @param <type> $contextcode
     */
    public function getChapterId($contextcode) {
        $row= $this->getRow('contextcode',$contextcode);
        return $row['chapterid'];
    }
    /**
     * returns a list of chapters as a tree for contexttools
     * @param <type> $contextcode
     * @return <type>
     */
    public function getChaptersAsTree($contextcode) {
        $sql=" where contextcode ='$contextcode'";
        $dbrows=$this->getAll($sql);
        $data="[";
        foreach($dbrows as $row) {
            $data .= "{\n";
            $data .= "\t\ttext: '".$this->getContextChapterTitle($row['chapterid'])."',\n";
            $data .= "\t\tid: '".$row['chapterid']."',\n";
            $data .= "\t\tleaf: true\n";
            $data .= "\t},";
        }
        $lastChar = $data[strlen($data)-1];
        $len=strlen($data);
        if($lastChar == ',') {
            $data=substr($data, 0, (strlen ($data)) - (strlen (strrchr($data,','))));
        }
        $data.="]";
        $str = "[{\n\ttext:'Chapter List',\n\texpanded: true,\n\tchildren: ".$data."\n}]";
        return $str;
    }


}


?>
