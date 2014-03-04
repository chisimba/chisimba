<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Data access class for the cmsadmin module. Used to access data in the blocks table.
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Wesley Nitsckie
 * @author Warren Windvogel
 */

class dbblocks extends dbTable {

    /**
     * The user  object
     *
     * @access private
     * @var object
     */
    protected $_objUser;


    /**
     * The frontpage  object
     *
     * @access private
     * @var object
     */
    protected $_objFrontPage;

    /**
     * The language  object
     *
     * @access private
     * @var object
     */
    protected $_objLanguage;

    /**
     * Class Constructor
     *
     * @access public
     * @return void
     */
    public function init() {
        try {
            parent::init('tbl_cms_blocks');
            $this->_objUser = & $this->getObject('user', 'security');
            $this->_objLanguage = & $this->newObject('language', 'language');
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }

    /**
     * Method to save a record to the database
     *
     * @param string $pageId The id of the page
     * @param string $sectionId The id of the Section
     * @param string $blockId The id of the block
     * @param string $blockCat Category of Block. Either 'frontpage', 'content' or 'section'
     * @access public
     * @return bool
     */
    public function add($pageId=NULL, $sectionId=NULL, $blockId = '', $blockCat = 'frontpage', $left = 0) {
        //echo 'Adding : pageId->' . $pageId . ' | sectionId->' . $sectionId . ' | blockCat->' . $blockCat . ' | left->' . $left. '<br/>';

        if ($blockCat == 'frontpage') {
            //Get ordering
            $ordering = $this->getOrdering(NULL, NULL, 'frontpage');
            $newArr = array(
                    'pageid' => $pageId ,
                    'blockid' => $blockId,
                    'sectionid' => $sectionId,
                    'frontpage_block' => 1,
                    'leftside_blocks' => $left,
                    'ordering' => $ordering
            );
        } else if ($blockCat == 'content') {
            //Get ordering
            $ordering = $this->getOrdering($pageId, NULL, 'content');
            $newArr = array(
                    'pageid' => $pageId ,
                    'blockid' => $blockId,
                    'sectionid' => NULL,
                    'frontpage_block' => 0,
                    'leftside_blocks' => $left,
                    'ordering' => $ordering
            );
        } else {
            //Get ordering
            $ordering = $this->getOrdering(NULL, $sectionId, 'section');
            $newArr = array(
                    'pageid' => NULL,
                    'blockid' => $blockId,
                    'sectionid' => $sectionId,
                    'frontpage_block' => 0,
                    'leftside_blocks' => $left,
                    'ordering' => $ordering
            );
        }

        $newId = $this->insert($newArr);

        return $newId;
    }


    /**
     * Method to edit a record if it exists AND add if it doesn't exist.
     *
     * @param string $pageId The id of the page
     * @param string $sectionId The id of the Section
     * @param string $blockId The id of the block
     * @param string $blockCat Category of Block. Either 'frontpage', 'content' or 'section'
     * @access public
     * @return bool
     */
    public function editPosition($pageId=NULL, $sectionId=NULL, $blockId = '', $blockCat = 'frontpage', $left = 0) {
        //echo 'Adding : pageId->' . $pageId . ' | sectionId->' . $sectionId . ' | blockCat->' . $blockCat . ' | left->' . $left. '<br/>';

        if ($blockCat == 'frontpage') {
            //Get ordering
            $ordering = $this->getOrdering(NULL, NULL, 'frontpage');
            $newArr = array(
                    'pageid' => $pageId ,
                    'blockid' => $blockId,
                    'sectionid' => $sectionId,
                    'frontpage_block' => 1,
                    'leftside_blocks' => $left,
                    'ordering' => $ordering
            );
        } else if ($blockCat == 'content') {
            //Get ordering
            $ordering = $this->getOrdering($pageId, NULL, 'content');
            $newArr = array(
                    'pageid' => $pageId ,
                    'blockid' => $blockId,
                    'sectionid' => NULL,
                    'frontpage_block' => 0,
                    'leftside_blocks' => $left,
                    'ordering' => $ordering
            );
        } else {
            //Get ordering
            $ordering = $this->getOrdering(NULL, $sectionId, 'section');
            $newArr = array(
                    'pageid' => NULL,
                    'blockid' => $blockId,
                    'sectionid' => $sectionId,
                    'frontpage_block' => 0,
                    'leftside_blocks' => $left,
                    'ordering' => $ordering
            );
        }

        if ($sectionId != '' ) {
            $idField = $sectionId;
        } else {
            $idField = $pageId;
        }

        $block = $this->getCmsBlock($blockId);
        //var_dump($block['blockid'] . ' == ' . $blockId);
        $result = FALSE;

        if ($blockId == $block['id']) {
            //echo "updating block " . $blockId . '<br/>';
            $result = $this->update('id', $idField, $newArr);
        } else {
            //echo "inserting block " . $newArr['blockid'] . '<br/>';
            $result = $this->insert($newArr);
        }


        return $result;
    }

    /**
     * Method to edit a record
     *
     * @access public
     * @return bool
     */
    public function edit() {
        //Get entry details
        $id = $this->getParam('id');
        $pageId = $this->getParam('pageid', NULL);
        $blockId = $this->getParam('blockid');
        $ordering = $this->getParam('ordering', 1);

        $newArr = array(
                'pageid' => $pageId ,
                'blockid' => $blockId,
                'ordering' => $ordering
        );
        //Update entry
        return $this->update('id', $id, $newArr);
    }

    /**
     * Method to delete a block
     *
     * @param string $pageId The id of the page
     * @param string $blockId The id of the block
     * @return boolean
     * @access public
     */
    public function deleteBlock($pageId, $sectionId, $blockId, $blockCat) {
        if ($blockCat == 'frontpage') {
            //Get last in order
            $frontPage = 1;
            $block = $this->getAll('WHERE frontpage_block = \''.$frontPage.'\' AND blockid = \''.$blockId.'\'');
        } else if ($blockCat == 'content') {
            //Get last in order
            $block = $this->getAll('WHERE pageid = \''.$pageId.'\' AND blockid = \''.$blockId.'\'');
        } else {
            //Get last in order
            $block = $this->getAll('WHERE sectionid = \''.$sectionId.'\' AND blockid = \''.$blockId.'\'');
        }

        if(!empty($block)) {
            $id = $block['0']['id'];
            $blockOrderNo = $block['0']['ordering'];
            if ($blockCat == 'frontpage') {
                //Get front page blocks
                $pageBlocks = $this->getBlocksForFrontPage($pageId);
            } else if ($blockCat == 'content') {
                //Get page blocks
                $pageBlocks = $this->getBlocksForPage($pageId);
            } else {
                //Get section blocks
                $pageBlocks = $this->getBlocksForSection($sectionId);
            }
            $pageBlocks = $this->getBlocksForPage($pageId);
            if(count($pageBlocks) > 1) {
                foreach($pageBlocks as $pb) {
                    if($pb['ordering'] > $blockOrderNo) {
                        $newOrder = $pb['ordering'] - '1';
                        $this->update('id', $pb['id'], array('ordering' => $newOrder));
                    }
                }
            }
            return $this->delete('id', $id);
        }
    }


    /**
     * Method to delete a block without an expensive check
     *
     * @param string $pageId The id of the page
     * @param string $blockId The id of the block
     * @return boolean
     * @access public
     */
    public function deleteBlockExplicit($pageId, $sectionId, $blockId, $blockCat) {

        if ($blockCat == 'frontpage') {
            //Get last in order
            $frontPage = 1;
            $block = $this->getAll('WHERE frontpage_block = \''.$frontPage.'\' AND blockid = \''.$blockId.'\'');
        } else if ($blockCat == 'content') {
            //Get last in order
            $block = $this->getAll('WHERE pageid = \''.$pageId.'\' AND blockid = \''.$blockId.'\'');
        } else {
            //Get last in order
            $block = $this->getAll('WHERE sectionid = \''.$sectionId.'\' AND blockid = \''.$blockId.'\'');
        }

        if(!empty($block)) {
            $id = $block['0']['id'];
            return $this->delete('id', $id);
        }

        return false;
    }

    /**
     * Method to delete a block without an expensive check
     *
     * @param string $pageId The id of the page
     * @param string $blockId The id of the block
     * @return boolean
     * @access public
     */
    public function deleteAllBlocks($pageId, $sectionId, $blockId, $blockCat) {

        if ($blockCat == 'frontpage') {
            //Get last in order
            $frontPage = 1;
            $block = $this->getAll('WHERE frontpage_block = \''.$frontPage.'\' AND blockid = \''.$blockId.'\'');
        } else if ($blockCat == 'content') {
            //Get last in order
            $block = $this->getAll('WHERE pageid = \''.$pageId.'\' AND blockid = \''.$blockId.'\'');
        } else {
            //Get last in order
            $block = $this->getAll('WHERE sectionid = \''.$sectionId.'\' AND blockid = \''.$blockId.'\'');
        }

        if(!empty($block)) {
            foreach ($block as $b) {
                $id = $b['id'];
                $this->delete('id', $id);
            }
            return true;
        }

        return false;
    }


    /**
     * Method to delete a block using the row id
     *
     * @access public
     * @param string $id The row id of the block
     * @return void
     */
    public function deleteBlockById($id) {
        $this->delete('id', $id);
    }

    /**
     * Method to get all blocks attached to a page
     *
     * @param string $pageId The id of the page content
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks on the page
     */
    public function getBlocksForPage($pageId, $sectionId = NULL, $left = '0') {
        //$left = (isset($left) && !empty($left)) ? $left : 0;

        $sql = "SELECT cb.*, cb.id as cb_id, mb.moduleid, mb.blockname
                FROM tbl_cms_blocks AS cb, tbl_module_blocks AS mb
                WHERE (cb.blockid = mb.id) AND frontpage_block = 0 
                AND leftside_blocks = '{$left}' AND (pageid = '{$pageId}'";

        if(!empty($sectionId)) {
            $sql .= " OR sectionid = '{$sectionId}' ";
        }

        $sql .= ')' /*GROUP BY cb.blockid*/.' ORDER BY cb.ordering';

        return $this->getArray($sql);
    }

    /**
     * Method to get all blocks attached to a page
     *
     * @param string $pageId The id of the page content
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks on the page
     */
    public function getPositionBlocksForPage($pageId, $sectionId = NULL) {
        //$left = (isset($left) && !empty($left)) ? $left : 0;

        $sql = "SELECT cb.*, cb.id as cb_id, mb.moduleid, mb.blockname
                FROM tbl_cms_blocks AS cb, tbl_module_blocks AS mb
                WHERE (cb.blockid = mb.id) AND frontpage_block = 0 
                AND (pageid = '{$pageId}'";

        if(!empty($sectionId)) {
            $sql .= " OR sectionid = '{$sectionId}' ";
        }

        $sql .= ')' /*GROUP BY cb.blockid*/.' ORDER BY cb.ordering';

        return $this->getArray($sql);
    }


    /**
     * Method to get all blocks attached to a section
     *
     * @param string $sectionId The id of the section
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks in the section
     */
    public function getBlocksForSection($sectionId, $left = '0') {
        $left = (isset($left) && !empty($left)) ? $left : 0;

        $sql = "SELECT tbl_cms_blocks.*, moduleid, blockname FROM tbl_cms_blocks, tbl_module_blocks
                WHERE (blockid = tbl_module_blocks.id) AND sectionid = '{$sectionId}' 
                AND frontpage_block = 0  AND leftside_blocks = '{$left}' 
                "/*GROUP BY blockid*/." ORDER BY ordering";

        return $this->getArray($sql);
    }

    /**
     * Method to get all blocks attached to a section (removed the left check)
     *
     * @param string $sectionId The id of the section
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks in the section
     */
    public function getPositionBlocksForSection($sectionId) {
        $left = (isset($left) && !empty($left)) ? $left : 0;

        $sql = "SELECT tbl_cms_blocks.*, moduleid, blockname FROM tbl_cms_blocks, tbl_module_blocks
                WHERE (blockid = tbl_module_blocks.id) AND sectionid = '{$sectionId}' 
                AND frontpage_block = 0
                "/*GROUP BY blockid*/." ORDER BY ordering";

        return $this->getArray($sql);
    }

    /**
     * Method to get all blocks attached to the front page
     *
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks on the front page
     */
    public function getBlocksForFrontPage($left = '0') {
        //$left = (isset($left) && !empty($left)) ? $left : 0;

        $sql = "SELECT tbl_cms_blocks.id, tbl_cms_blocks.pageid, tbl_cms_blocks.blockid, tbl_cms_blocks.sectionid,
                        tbl_cms_blocks.frontpage_block, tbl_cms_blocks.leftside_blocks, tbl_cms_blocks.ordering,
                        tbl_module_blocks.moduleid, tbl_module_blocks.blockname 
                    FROM tbl_cms_blocks, tbl_module_blocks 
                    WHERE (tbl_cms_blocks.blockid = tbl_module_blocks.id) 
                        AND tbl_cms_blocks.frontpage_block = '1' 
                        AND tbl_cms_blocks.leftside_blocks = '{$left}' 
                "/*GROUP BY tbl_cms_blocks.blockid*/." ORDER BY tbl_cms_blocks.ordering";

        return $this->getArray($sql);
    }


    /**
     * Method to get all blocks attached to the front page
     *
     * @access public
     * @return array $pageBlocks An array of associative arrays of all blocks on the front page
     */
    public function getPositionBlocksForFrontPage() {
        //$left = (isset($left) && !empty($left)) ? $left : 0;

        $sql = "SELECT tbl_cms_blocks.id, tbl_cms_blocks.pageid, tbl_cms_blocks.blockid, tbl_cms_blocks.sectionid,
                        tbl_cms_blocks.frontpage_block, tbl_cms_blocks.leftside_blocks, tbl_cms_blocks.ordering,
                        tbl_module_blocks.moduleid, tbl_module_blocks.blockname 
                    FROM tbl_cms_blocks, tbl_module_blocks 
                    WHERE (tbl_cms_blocks.blockid = tbl_module_blocks.id) 
                        AND tbl_cms_blocks.frontpage_block = '1' 
                    "/*GROUP BY tbl_cms_blocks.blockid*/." ORDER BY tbl_cms_blocks.ordering";

        return $this->getArray($sql);
    }


    /**
     * Method to return the ordering value of new blocks (gets added last)
     *
     * @param string $pageid The id(pk) of the page the block is attached to
     * @return int $ordering The value to insert into the ordering field
     * @access public
     * @author Warren Windvogel
     */
    public function getOrdering($pageid, $sectionid, $blockCat) {
        $ordering = 1;
        if ($blockCat == 'frontpage') {
            //Get last in order
            $frontPage = 1;
            $lastOrder = $this->getAll('WHERE frontpage_block = \''.$frontPage.'\' ORDER BY ordering DESC LIMIT 1');
        } else if ($blockCat == 'content') {
            //Get last in order
            $lastOrder = $this->getAll('WHERE pageid = \''.$pageid.'\' ORDER BY ordering DESC LIMIT 1');
        } else {
            //Get last in order
            $lastOrder = $this->getAll('WHERE sectionid = \''.$sectionid.'\' ORDER BY ordering DESC LIMIT 1');
        }
        //add after this value
        if (!empty($lastOrder)) {
            $ordering = $lastOrder['0']['ordering'] + 1;
        }

        return $ordering;
    }

    /**
     * Method to return the links to be displayed in the order column on the table
     *
     * @param string $id The id of the entry
     * @return string $links The html for the links
     * @access public
     * @return string The html for the links
     * @author Warren Windvogel
     */
    public function getOrderingLink($id, $blockCat) {
        //Get the parent id
        $entry = $this->getRow('id', $id);
        $pageId = $entry['pageid'];
        $sectionId = $entry['sectionid'];

        //Get the number of sub sections in section
        $topOrder = 1;
        if ($blockCat == 'frontpage') {
            //Get last in order
            $frontPage = 1;
            $lastOrder = $this->getAll('WHERE frontpage_block = \''.$frontPage.'\' ORDER BY ordering DESC LIMIT 1');
        } else if ($blockCat == 'content') {
            //Get last in order
            $lastOrder = $this->getAll('WHERE pageid = \''.$pageId.'\' ORDER BY ordering DESC LIMIT 1');
        } else {
            //Get last in order
            $lastOrder = $this->getAll('WHERE sectionid = \''.$sectionId.'\' ORDER BY ordering DESC LIMIT 1');
        }
        if(!empty($lastOrd)) {
            $topOrder = $lastOrd['0']['ordering'];
        }
        $links = " ";

        if ($topOrder > '1') {
            //Create geticon obj
            $this->objIcon = & $this->newObject('geticon', 'htmlelements');

            if ($entry['ordering'] == '1') {
                //return down arrow link
                //icon
                $this->objIcon->setIcon('downend');
                $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
                //link
                $downLink = & $this->newObject('link', 'htmlelements');
                $downLink->href = $this->uri(array('action' => 'changeblocksorder', 'id' => $id, 'ordering' => 'up', 'pageid' => $pageId, 'sectionid' => $sectionId));
                $downLink->link = $this->objIcon->show();
                $links .= $downLink->show();
            } else if ($entry['ordering'] == $topOrder) {
                //return up arrow
                //icon
                $this->objIcon->setIcon('upend');
                $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
                //link
                $upLink = & $this->newObject('link', 'htmlelements');
                $upLink->href = $this->uri(array('action' => 'changeblocksorder', 'id' => $id, 'ordering' => 'down', 'pageid' => $pageId, 'sectionid' => $sectionId));
                $upLink->link = $this->objIcon->show();

                $links .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $upLink->show();
            } else {
                //return both arrows
                //icon
                $this->objIcon->setIcon('down');
                $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
                //link
                $downLink = & $this->newObject('link', 'htmlelements');
                $downLink->href = $this->uri(array('action' => 'changeblocksorder', 'id' => $id, 'ordering' => 'up', 'pageid' => $pageId, 'sectionid' => $sectionId));
                $downLink->link = $this->objIcon->show();
                //icon
                $this->objIcon->setIcon('up');
                $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
                //link
                $upLink = & $this->newObject('link', 'htmlelements');
                $upLink->href = $this->uri(array('action' => 'changeblocksorder', 'id' => $id, 'ordering' => 'down', 'pageid' => $pageId, 'sectionid' => $sectionId));
                $upLink->link = $this->objIcon->show();
                $links .= $downLink->show() . '&nbsp;' . $upLink->show();
            }
        }

        return $links;
    }

    /**
     * Method to update the order of the blocks
     *
     * @param string $id The id of the entry to move
     * @param int $ordering How to update the order(up or down).
     * @access public
     * @return bool
     * @author Warren Windvogel
     */
    public function changeOrder($id, $ordering, $pageId, $sectionId) {
        //Get array of all blocks in level
        if ($blockCat == 'frontpage') {
            //Get last in order
            $frontPage = 1;
            $fpContent = $this->getAll('WHERE frontpage_block = \''.$frontPage.'\' ORDER BY ordering');
        } else if ($blockCat == 'content') {
            //Get last in order
            $fpContent = $this->getAll('WHERE pageid = \''.$pageId.'\' ORDER BY ordering');
        } else {
            //Get last in order
            $fpContent = $this->getAll('WHERE sectionid = \''.$sectionId.'\' ORDER BY ordering');
        }

        //Search for entry to be reordered and update order
        foreach($fpContent as $content) {
            if ($content['id'] == $id) {
                if ($ordering == 'up') {
                    $changeTo = $content['ordering'];
                    $toChange = $content['ordering'] + 1;
                    $updateArray = array(
                            'ordering' => $toChange
                    );
                    $this->update('id', $id, $updateArray);
                } else {
                    $changeTo = $content['ordering'];
                    $toChange = $content['ordering'] - 1;
                    $updateArray = array(
                            'ordering' => $toChange
                    );
                    $this->update('id', $id, $updateArray);
                }
            }
        }

        //Get other entry to change
        $entries = $this->getAll('WHERE pageid = \''.$pageId.'\' AND ordering = \''.$toChange.'\'');
        foreach($entries as $entry) {
            if ($entry['id'] != $id) {
                $upArr = array(
                        'ordering' => $changeTo
                );
                $this->update('id', $entry['id'], $upArr);
            }
        }
    }

    /**
     * Method to return the ordering value of new blocks (gets added last)
     *
     * @param string $pageid The id(pk) of the page the block is attached to
     * @param string $sectionid The id(pk) of the section the block is attached to
     * @param string $blockCat Whether to add the block to a 'section', 'content' page or the 'frontpage'
     * @return string $middleColumnContent The form to add remove blocks from a page
     * @access public
     * @author Warren Windvogel
     */
    public function getAddRemoveBlockForm($pageid, $sectionid, $blockCat) {
        //Load the checkbox class
        $this->loadClass('checkbox', 'htmlelements');

        //Create heading
        $objH =& $this->newObject('htmlheading', 'htmlelements');
        $objH->type = '3';

        //Create the form
        $objForm =& $this->newObject('form', 'htmlelements');
        $objForm->name = 'addblockform';
        $objForm->id = 'addblockform';

        if ($blockCat == 'frontpage') {
            //Set heading
            $objH->str = $this->_objLanguage->languageText('mod_cmsadmin_blocksforfrontpage', 'cmsadmin');
            //Set form action
            $objForm->setAction($this->uri(array('action' => 'saveblock', 'blockcat' => $blockCat), 'cmsadmin'));
            //Get blocks currently attached to item
            $currentBlocks = $this->getBlocksForFrontPage();
        } else if ($blockCat == 'content') {
            //Set heading
            $objH->str = $this->_objLanguage->languageText('mod_cmsadmin_blocksforcontent', 'cmsadmin');
            //Set form action
            $objForm->setAction($this->uri(array('action' => 'saveblock', 'pageid' => $pageid, 'blockcat' => $blockCat), 'cmsadmin'));
            //Get blocks currently attached to item
            $currentBlocks = $this->getBlocksForPage($pageid);
        } else {
            //Set heading
            $objH->str = $this->_objLanguage->languageText('mod_cmsadmin_blocksforsection', 'cmsadmin');
            //Set form action
            $objForm->setAction($this->uri(array('action' => 'saveblock', 'sectionid' => $sectionid, 'blockcat' => $blockCat), 'cmsadmin'));
            //Get blocks currently attached to item
            $currentBlocks = $this->getBlocksForSection($sectionid);
        }

        //Create table to store form elements
        $objTable =& $this->newObject('htmltable', 'htmlelements');
        $objTable->cellspacing = '2';
        $objTable->cellpadding = '2';
        $objTable->border = '1';
        $objTable->width = '70%';
        //Create header cell
        $objTable->startHeaderRow();
        $objTable->addHeaderCell($this->_objLanguage->languageText('phrase_blockname'));
        $objTable->addHeaderCell($this->_objLanguage->languageText('word_order'));
        $objTable->endHeaderRow();

        //Get current blocks on page


        if(!empty($currentBlocks)) {
            foreach($currentBlocks as $tbk) {
                $tb = $this->getBlock($tbk['blockid']);
                //Add entry to table for changing order
                $objTable->startRow();
                $objTable->addCell($tb['blockname']);
                $objTable->addCell($this->getOrderingLink($tbk['id'], $blockCat));
                $objTable->endRow();
            }
        }

        $boxes = "";

        //Get all entries in blocks table
        $blocks = $this->getBlockEntries();
        foreach($blocks as $block) {
            $blockName = $block['blockname'];
            $blockId = $block['id'];

            $checked = FALSE;
            if(!empty($currentBlocks)) {
                foreach($currentBlocks as $blk) {
                    if($blk['blockid'] == $blockId) {
                        $checked = TRUE;
                    }
                }
            }
            $checkbox = new checkbox($blockId, $blockName, $checked);

            $boxes .= $checkbox->show().'&nbsp;'.$blockName.'&nbsp;'.'&nbsp;';
        }

        //Create submit button
        $objButton =& $this->newObject('button', 'htmlelements');
        $objButton->setToSubmit();
        $objButton->value = $this->_objLanguage->languageText('word_save');
        $objButton->id = 'submit';
        $objButton->name = 'submit';

        $objForm->addToForm($boxes);
        $objForm->addToForm('<br/>'.'&nbsp;'.'<br/>'.$objButton->show());

        $middleColumnContent = "";
        $middleColumnContent .= $objH->show();
        $middleColumnContent .= $objTable->show();
        $middleColumnContent .= '<br/>'.'&nbsp;'.'<br/>';
        $objH->str = $this->_objLanguage->languageText('mod_cmsadmin_addremoveblocks', 'cmsadmin');
        $middleColumnContent .= $objH->show();
        $middleColumnContent .= $objForm->show();

        return $middleColumnContent;
    }




    /**
     * Method to return the form that allows you to add blocks to pages with ordering and position
     *
     * @param string $pageid The id(pk) of the page the block is attached to
     * @param string $sectionid The id(pk) of the section the block is attached to
     * @param string $blockCat Whether to add the block to a 'section', 'content' page or the 'frontpage'
     * @return string $middleColumnContent The form to add remove blocks from a page
     * @access public
     * @author Warren Windvogel, Charl Mert
     */
    public function getPositionBlockForm($pageid, $sectionid, $blockCat) {

        //Including Module TextBlcok integration here
        $objModule =$this->newObject('modules', 'modulecatalogue');
        if ($objModule->checkIfRegistered('textblock')) {
            $objTextBlock =$this->newObject('dbtextblock', 'textblock');
        } else {
            $objTextBlock = false;
        }


        //Load the checkbox class
        $this->loadClass('checkbox', 'htmlelements');

        //Create heading
        $objH =& $this->newObject('htmlheading', 'htmlelements');
        $objH->type = '3';

        //Create the form
        $objForm =& $this->newObject('form', 'htmlelements');
        $objForm->name = 'addblockform';
        $objForm->id = 'addblockform';

        if ($blockCat == 'frontpage') {
            //Set heading
            //$objH->str = $this->_objLanguage->languageText('mod_cmsadmin_blocksforfrontpage', 'cmsadmin');
            //Set form action
            $objForm->setAction($this->uri(array('action' => 'saveblock', 'blockcat' => $blockCat), 'cmsadmin'));
            //Get blocks currently attached to item
            $currentBlocks = $this->getPositionBlocksForFrontPage();
        } else if ($blockCat == 'content') {
            //Set heading
            //$objH->str = $this->_objLanguage->languageText('mod_cmsadmin_blocksforcontent', 'cmsadmin');
            //Set form action
            $objForm->setAction($this->uri(array('action' => 'saveblock', 'pageid' => $pageid, 'blockcat' => $blockCat), 'cmsadmin'));
            //Get blocks currently attached to item
            $currentBlocks = $this->getPositionBlocksForPage($pageid);
        } else {
            //Set heading
            //$objH->str = $this->_objLanguage->languageText('mod_cmsadmin_blocksforsection', 'cmsadmin');
            //Set form action
            $objForm->setAction($this->uri(array('action' => 'saveblock', 'sectionid' => $sectionid, 'blockcat' => $blockCat), 'cmsadmin'));

            $hidden = new hiddeninput('sectionId', $sectionid);
            $objForm->addToForm($hidden);

            $hidden = new hiddeninput('blockcat', $blockCat);
            $objForm->addToForm($hidden);

            //Get blocks currently attached to item
            $currentBlocks = $this->getPositionBlocksForSection($sectionid);
        }

        /*
            //Create table to store form elements
            $objTable =& $this->newObject('htmltable', 'htmlelements');
            $objTable->cellspacing = '2';
            $objTable->cellpadding = '2';
            $objTable->border = '1';
            $objTable->width = '70%';
            //Create header cell
            $objTable->startHeaderRow();
            $objTable->addHeaderCell($this->_objLanguage->languageText('phrase_blockname'));
            $objTable->addHeaderCell($this->_objLanguage->languageText('word_order'));
            $objTable->endHeaderRow();

            //Get current blocks on page
            

            if(!empty($currentBlocks)) {
                foreach($currentBlocks as $tbk) {
                    $tb = $this->getBlock($tbk['blockid']);
                    //Add entry to table for changing order
                    $objTable->startRow();
                    $objTable->addCell($tb['blockname']);
                    $objTable->addCell($this->getOrderingLink($tbk['id'], $blockCat));
                    $objTable->endRow();
                }
            }
        */

        //Create table to store form elements
        $tbl_blocks =& $this->newObject('htmltable', 'htmlelements');
        $tbl_blocks->cellspacing = '2';
        $tbl_blocks->cellpadding = '2';
        $tbl_blocks->border = '1';
        $tbl_blocks->width = '480px';

        $boxes = "";

        //Get all entries in blocks table
        $blocks = $this->getBlockEntries();
        foreach($blocks as $block) {
            $blockName = $block['blockname'];
            if ($objTextBlock) {
                $txtBlock = $objTextBlock->getBlock($block['id']);
                if ($txtBlock['title'] != '') {
                    $blockName = $txtBlock['title'];
                }
            }

            $blockId = $block['id'];

            $checked = FALSE;
            if(!empty($currentBlocks)) {
                foreach($currentBlocks as $blk) {
                    if($blk['blockid'] == $blockId) {
                        $checked = TRUE;
                    }
                }
            }
            $checkbox = new checkbox($blockId, $blockName, $checked);

            $position = new dropdown('position_'.$blockId);
            $position->addOption('0', $this->_objLanguage->languageText('word_right'));
            $position->addOption('1', $this->_objLanguage->languageText('word_left'));

            //Setting the Default Option
            foreach ($currentBlocks as $active) {
                if (isset($active['blockid'])) {
                    $activeId = $active['blockid'];
                }

                if (isset($active['leftside_blocks'])) {
                    $activePosition = $active['leftside_blocks'];
                }

                if ($activeId == $blockId) {
                    $position->setSelected($activePosition);
                }
            }

            $tbl_blocks->startRow();
            $tbl_blocks->addCell($checkbox->show());
            $tbl_blocks->addCell($blockName);

            if ($blockCat == 'frontpage') {
                $tbl_blocks->addCell($position->show());

            } else if ($blockCat == 'content') {
                $tbl_blocks->addCell($position->show());
            } else {
                //Using Default Left block because content renders in 2 column layout
            }

            $tbl_blocks->endRow();
        }

        //Create submit button
        $objButton =& $this->newObject('button', 'htmlelements');
        $objButton->setToSubmit();
        $objButton->value = $this->_objLanguage->languageText('word_save');
        $objButton->id = 'submit';
        $objButton->name = 'submit';

        $hidden = new hiddeninput('loadposition', '1');

        $objForm->addToForm($tbl_blocks->show());
        $objForm->addToForm($hidden->show());
        $objForm->addToForm('<br/>'.'&nbsp;'.'<br/>'.$objButton->show());

        $middleColumnContent = "";
        $middleColumnContent .= $objH->show();
        //$middleColumnContent .= $objTable->show();
        $middleColumnContent .= '<br/>'.'&nbsp;'.'<br/>';
        $objH->str = $this->_objLanguage->languageText('mod_cmsadmin_addremoveblocks', 'cmsadmin');
        $middleColumnContent .= $objH->show();
        $middleColumnContent .= $objForm->show();

        return $middleColumnContent;
    }




    /************************ tbl_module_block methods *************************/

    /**
     * Method to return all entries in blocks table
     *
     * @return array $entries An array of all entries in the module_blocks table
     * @access public
     */
    public function getBlockEntries() {
        $sql = 'SELECT * FROM tbl_module_blocks';
        $entries = $this->getArray($sql);

        return $entries;
    }

    /**
     * Method to return an entries in blocks table
     *
     * @param string $blockId The id of the block
     * @return array $entry An associative array of the blocks details
     * @access public
     */
    public function getBlock($blockId) {
        $entry = $this->getArray('SELECT * FROM tbl_module_blocks WHERE id = \''.$blockId.'\'');
        $entry = $entry['0'];

        return $entry;
    }

    /**
     * Method to return entries from the tbl_cms_blocks table
     *
     * @param string $blockId The id of the block
     * @return array $entry An associative array of the blocks details
     * @access public
     */
    public function getCmsBlock($blockId) {
        $entry = $this->getArray('SELECT * FROM tbl_cms_blocks WHERE blockid = \''.$blockId.'\'');

        if (isset($entry[0])) {
            $entry = $entry[0];
        } else {
            $entry = FALSE;
        }

        return $entry;
    }


    /**
     * Method to return an entries in blocks table
     *
     * @param string $blockName The name of the block
     * @return array $entry An associative array of the blocks details
     * @access public
     */
    public function getBlockByName($blockName) {
        $entry = $this->getArray('SELECT * FROM tbl_module_blocks WHERE blockname = \''.$blockName.'\'');

        if (count($entry) == 0) {
            return FALSE;
        } else {
            return $entry['0'];
        }
    }

    /**
     * Method to determine if the page is front page or not
     *
     * @param string $pageid The name of the block
     * @return Boolean
     * @access public
     */
    public function isFrontPage($pageid) {
        $entry = $this->getArray('SELECT * FROM tbl_cms_content_frontpage WHERE content_id = \''.$pageid.'\'');
        if (count($entry) == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}

?>
