<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * The class provides a base text block for use by all
 * textblocks to render their contents
 *
 * @author Derek Keats
 *
 */
class textblockbase extends object {

    public $title;
    private $objDb;
    private $objLanguage;
    public $blockContents;

    /**
     * Constructor for the class
     */
    public function init() {
        // Create an instance of the textblock DBtable object.
        $this->objDb = $this->getObject("dbtextblock", "textblock");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
        // Don't wrap textblock titles.
        $this->wrapStr = FALSE;
    }

    /**
     * Method to render the block with the text item
     * @param string $textItem The content to render
     * @access public
     *
     */
    public function setData($textItem) {
        $ar = $this->objDb->getRow("blockid", $textItem);
        if (count($ar) > 0) {
            $this->showTitle = $ar['show_title'];
            if ($this->showTitle == "1") {
                $this->title = $ar['title'];
            } else {
                $this->title = FALSE;
            }
            $cssId = "";
            $cssClass = "";
            $divEnd = "";
            $divStart = "";
            $ret="";
            $useDiv = FALSE;
            $this->cssId = $ar['css_id'];
            if ($this->cssId !== "" && $this->cssId !== NULL) {
                $cssId = " id='$this->cssId' ";
                $useDiv = TRUE;
            }
            $this->cssClass = $ar['css_class'];
            if ($this->cssClass !== "" && $this->cssClass !== NULL) {
                $cssClass = " class='$this->cssClass'";
                $useDiv = TRUE;
            }
            $objWashout = $this->getObject("washout", "utilities");
            $pText = $ar['blocktext'];
            $ret = $objWashout->parseText($pText, TRUE);
            if ($useDiv) {
                $ret = "<div $cssClass>$ret</div>";
            }
            $this->blockContents = $ret;
        } else {
            $this->title = $textItem;
            $this->blockContents = $this->objLanguage->languageText("mod_textblock_nocontent", "textblock");
        }
        return TRUE;
    }

    /**
     * Method to render the block with the text item
     * @param string $textItem The content to render
     * @access public
     *
     */
    public function setDataArr($textItem) {
        $ar = $this->objDb->getRow("blockid", $textItem);
        
        if (count($ar) > 0) {
            $this->showTitle = $ar['show_title'];
            if ($this->showTitle == "1") {
                $this->title = $ar['title'];
            } else {
                $this->title = FALSE;
            }
            $cssId = "";
            $cssClass = "";
            $divEnd = "";
            $divStart = "";
            $useDiv = FALSE;
            $this->cssId = $ar['css_id'];
            if ($this->cssId !== "" && $this->cssId !== NULL) {
                $cssId = " id='$this->cssId' ";
                $useDiv = TRUE;
            }
            $this->cssClass = $ar['css_class'];
            if ($this->cssClass !== "" && $this->cssClass !== NULL) {
                $cssClass = " class='$this->cssClass'";
                $useDiv = TRUE;
            }
            $objWashout = $this->getObject("washout", "utilities");
            $ret = $objWashout->parseText($ar['blocktext']);
            if ($useDiv) {
                $ret = "<div $cssClass>$ret</div>";
            }
            $this->blockContents = $ret;
            $dataArr = array(
                "cssId" => $this->cssId,
                "cssClass" => $this->cssClass,
                "title" => $this->title,
                "blockContents" => $this->blockContents,
                "show_title" => $ar["show_title"],
                "blockType" => $ar["show_title"],
            );
        } else {
            $this->title = $textItem;
            $this->blockContents = $this->objLanguage->languageText("mod_textblock_nocontent", "textblock");
            $dataArr = array(
                "title" => $this->title,
                "blockContents" => $this->blockContents
            );
        }
        return $dataArr;
    }

}

?>