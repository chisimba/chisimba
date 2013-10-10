<?php

class langadmin extends controller {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objLangAdmin = $this->getObject("langutil");
        $this->objConfig = $this->getObject('altconfig', 'config');
        $objMkDir = $this->getObject('mkdir', 'files');
    }

    /**
     * Standard Dispatch Function for Controller
     * @param <type> $action
     * @return <type>
     */
    public function dispatch($action) {
        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */

        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to show the Home Page of the Module
     */
    public function __home() {

        return "home_tpl.php";
    }

    function __addLang() {
// set some info about the new lang
        $langid = $this->getParam('langid');
        $name = $this->getParam("name");
        $meta = $this->getParam("meta");
        $errorText = $this->getParam("errortext");


        $langData = array(
            'lang_id' => $langid,
            'table_name' => 'tbl_' . $langid,
            'name' => $name,
            'meta' => $meta,
            'error_text' => $errorText,
            'encoding' => 'UTF-8',
        );
        $this->objLangAdmin->addLanguage($langData);
        return $this->nextAction("home");
    }

    function __hide() {
        $hiddenlangs = $this->getObject("dbhiddenlangs");
        $id = $this->getParam("langid");
        $hiddenlangs->hideLang($id);
        return $this->nextAction("home");
    }

    function __unhide() {
        $hiddenlangs = $this->getObject("dbhiddenlangs");
        $id = $this->getParam("langid");
        $hiddenlangs->unhideLang($id);
        return $this->nextAction("home");
    }

    function __showNewLangTemplate() {

        return "addeditlang_tpl.php";
    }

    function __viewLangItems() {
        return "viewlangitems_tpl.php";
    }

    function __editTranslation() {
        $code = $this->getParam("code");
        $this->objDbLangText = $this->getObject("dblanguagetext", "langadmin");
        $item = $this->objDbLangText->getLanguageTextItem($code);
        $this->setVarByRef("code", $code);
        $this->setVarByRef("description", $item[0]['description']);

        return "translate_tpl.php";
    }

    function __addItem() {
        $code = $this->getParam("code");
        $translation = $this->getParam("translation");

        $currLang = $this->objLanguage->currentLanguage();
        $stringArray = array($currLang => $translation);
        $arrName = explode("_", $code);
        $module = $arrName[1];
        $module = $arrName[1];
        if ($module == 'unesco') {
            $module = $module . "_" . $arrName[2];
        }
        $this->objLanguage->addLangItem($code, $module, $stringArray);
        return $this->nextAction("home");
    }

    function __exportLangItems() {
        $langid = $this->getParam("langid");
        $_SESSION['language'] = $langid;
        $objMkDir = $this->getObject('mkdir', 'files');
        $destinationDir = $this->objConfig->getcontentBasePath() . '/langadmin/' . $langid;
        $objMkDir->mkdirs($destinationDir);
        @chmod($destinationDir, 0777);
        $langFile = $destinationDir . '/' . $langid . '_language_items.txt';
        $fh = fopen($langFile, 'w') or die("can't open file");
        $this->objLangText = $this->getObject("dblanguagetext", "langadmin");
        $texts = $this->objLangText->getLanguageTextItems();

        foreach ($texts as $text) {
            $line = "";

            $code = $text['code'];
            $line.=$code . "~";
            $line.=$text['description'] . '~';

            $arrName = explode("_", $code);
            $module = $arrName[1];
            if ($module == 'unesco') {
                $module = $module . "_" . $arrName[2];
            }
//get the english translation first
            $_SESSION['language'] = 'en';
            $engTranslation = $this->objLanguage->languageText($code, $module);
            $line.=$engTranslation . '~';

            $_SESSION['language'] = $langid;
            $line.=$this->objLanguage->languageText($code, $module);
            $line.="\n";
            fwrite($fh, $line);
        }


        fclose($fh);

        $file = $langFile;

        header("Content-Disposition: attachment; filename=" . $langid . " _language_items.txt");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");
        header("Content-Length: " . filesize($file));
        flush(); // this doesn't really matter.

        $fp = fopen($file, "r");
        while (!feof($fp)) {
            echo fread($fp, 65536);
            flush(); // this is essential for large downloads
        }
        fclose($fp);
    }

    /**
     * Used to push through upload results for AJAX
     */
    function __ajaxuploadresults() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $id = $this->getParam('id');
        $this->setVarByRef('id', $id);

        $fileid = $this->getParam('fileid');
        $this->setVarByRef('fileid', $fileid);

        $filename = $this->getParam('filename');
        $this->setVarByRef('filename', $filename);

        return 'ajaxuploadresults_tpl.php';
    }

    function __uploadFile() {
        $langid = $this->getParam("langid");
        $this->setVarByRef('langid', $langid);
        return "upload_tpl.php";
    }

    /**
     * Used to do the actual upload
     *
     */
    function __doajaxupload() {

        $langid = $this->getParam("langid");
        $_SESSION['language'] = $langid;
        $objMkDir = $this->getObject('mkdir', 'files');
        $destinationDir = $this->objConfig->getcontentBasePath() . '/langadmin/';
        $objMkDir->mkdirs($destinationDir);
        @chmod($destinationDir, 0777);
        $langFile = $destinationDir . '/' . $langid . '_language_items.txt';
        $dir = $destinationDir;

        $generatedid = $this->getParam('id');
        $filename = $this->getParam('filename');

        $objUpload = $this->newObject('upload', 'files');
        $objUpload->permittedTypes = array(
            'txt'
        );
        $objUpload->overWrite = TRUE;
        $objUpload->uploadFolder = $destinationDir . '/';

        $result = $objUpload->doUpload(TRUE, $docname);

        if ($result['success'] == FALSE) {

            $filename = isset($_FILES['fileupload']['name']) ? $_FILES['fileupload']['name'] : '';

            return $this->nextAction('erroriframe', array('message' => 'Unsupported file extension.Only use txt', 'file' => $filename, 'id' => $generatedid));
        } else {

            $filename = $result['filename'];
            $file = fopen($destinationDir . '/' . $filename, "r") or exit("Unable to open file!");
//Output a line of the file until the end is reached
            while (!feof($file)) {

                $line = fgets($file);
                if (empty($line)) {
                    continue;
                }
                $parts = explode("~", $line);

                $stringArray = array($langid => $parts[3]);
                $arrName = explode("_", $parts[0]);
                $module = $arrName[1];
                $module = $arrName[1];
                if ($module == 'unesco') {
                    $module = $module . "_" . $arrName[2];
                }

                $this->objLanguage->addLangItem($parts[0], $module, $stringArray);
            }
        }
        fclose($file);
        return $this->nextAction('ajaxuploadresults', array('id' => $generatedid, 'fileid' => $id,
                    'filename' => $$filename));
    }

    /**
     * Used to show upload errors
     *
     */
    function __erroriframe() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $id = $this->getParam('id');
        $this->setVarByRef('id', $id);

        $message = $this->getParam('message');
        $this->setVarByRef('message', $message);

        return 'erroriframe_tpl.php';
    }

}

?>
