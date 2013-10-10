<?php

    class computerscience extends controller
    {
        public $objLanguage;

        public function init()
        {
            // Instantiate the language object
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objDict = & $this->getObject('editform');
            $this->objXml = $this->getObject('xmlthing', 'utilities');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objUser = $this->getObject('user', 'security');
        }

        public function dispatch($action)
        {
            switch ($action) {
                //Default to view and display view template
                case "add":
                    if(!file_exists($this->objConfig->getContentBasepath().'users/'.$this->objUser->userId())) {
                        mkdir ($this->objConfig->getContentBasepath().'users/'.$this->objUser->userId(), 0777);
                        chmod ($this->objConfig->getContentBasepath().'users/'.$this->objUser->userId(), 0777);
                    }
                    if(!file_exists($this->objConfig->getContentBasepath().'users/'.$this->objUser->userId().'/aiml/')) {
                        mkdir ($this->objConfig->getContentBasepath().'users/'.$this->objUser->userId().'/aiml/', 0777);
                        chmod ($this->objConfig->getContentBasepath().'users/'.$this->objUser->userId().'/aiml/', 0777);
                    }
                    else {
                        chmod ($this->objConfig->getContentBasepath().'users/'.$this->objUser->userId().'/aiml/', 0777);
                    }
                    $filename = $this->objConfig->getContentBasepath().'users/'.$this->objUser->userId().'/aiml/'.$this->objUser->userId().'_std-cs4fn.aiml';;

                    if(!file_exists($filename)) {
                        $this->objXml->createDoc();
                        $this->objXml->startElement('aiml');
                        $this->objXml->writeAtrribute('version', '1.0');
                    }
                    else {
                        $this->objXml->editDoc();
                    }
                    $pattern = $this->getParam('txtPatternOne', "");
                    if($pattern != "")
                    {
                        $that = $this->getParam('txtThatOne');
                        $template = $this->getParam('txtTemplateOne');
                        $this->objXml->startElement("category");
                        $this->objXml->writeElement("pattern", strtoupper($pattern));
                        if($that != "")
                        {
                            if(file_exists($filename)) {
                            $xml = simplexml_load_file($filename);
                            $cats = $xml->category;
                            foreach ($cats as $cat) {
                                $catarr[] = $cat->pattern;
                            }
                        }
                        else {
                            $catarr = array();
                        }
                            // so now we can get the correct pattern out
                            $this->objXml->startElement('template');
                            $this->objXml->writeElement("srai", $catarr[$that]);
                            $this->objXml->endElement();
                        }
                        else {
                            $this->objXml->writeElement("template", $template);
                        }
                        $this->objXml->endElement();
                    }

                    if(!file_exists($filename)) {
                        // write to file as a new file
                        // end the aiml element
                        $this->objXml->endElement();
                        $document = $this->objXml->dumpXML();

                        // unhtmlentities $document
                        $table = array_flip(get_html_translation_table(HTML_ENTITIES));
                        $document = strtr($document, $table);
                        file_put_contents($filename, $document);
                    }
                    else {
                        // Open the file and read it, then append to it
                        $contents = file_get_contents($filename);
                        $contents = str_replace("</aiml>", '', $contents);
                        // end the aiml element
                        $this->objXml->endElement();
                        $document = $this->objXml->dumpXML();
                        // unhtmlentities $document
                        $table = array_flip(get_html_translation_table(HTML_ENTITIES));
                        $document = strtr($document, $table);
                        $document .= "</aiml>";
                        $document = $contents.$document;

                        file_put_contents($filename, $document);
                    }
                    $message = $this->objLanguage->languageText("mod_computerscience_word_updated", "computerscience");
                    $this->nextAction('', array('message' => $message));


                    return 'editadd_tpl.php';
                    break;

                    case 'editaiml':
                        echo "not yet implemented";
                        die();
                        break;

                    case 'reloadbot':
                        $pathToScript = $this->getResourcePath('');
                        // first stop the bot, unlink the brn file and restart it
                        $this->endSession();
                        $respath = $this->getResourcePath('').'aiml/';
                        $brn = $pathToScript."standard.brn";

                        if(file_exists($brn)) {
                            unlink($brn);
                        }

                        // restart the bot
                        $exeString = $pathToScript."bot.py";

                        exec ( $exeString . " > /dev/null &", $output );
                        //var_dump($output);
                        $message = $this->objLanguage->languageText("mod_computerscience_botreloaded", "computerscience");
                        $this->nextAction('', array('message' => $message));
                        die();
                        break;

                    case 'killbot':
                        $this->endSession();
                        $message = $this->objLanguage->languageText("mod_computerscience_botkilled", "computerscience");
                        $this->nextAction('', array('message' => $message));
                        break;

                    case 'publishaiml':
                        $filename = $this->objConfig->getContentBasepath().'users/'.$this->objUser->userId().'/aiml/'.$this->objUser->userId().'_std-cs4fn.aiml';
                        // copy the file to the resources/aiml directory
                        $to = $this->getResourcePath('').'aiml/'.$this->objUser->userId().'_std-cs4fn.aiml';
                        copy($filename, $to);
                        // now update the std-definitions file
                        $respath = $this->getResourcePath('').'aiml/';
                        foreach(glob($respath.'*.aiml') as $file) {
                            $filearray[] = "aiml/".basename($file);
                        }

                        $stdxml = $this->objDict->rebuildStdDefs($filearray);
                        unlink($this->getResourcePath('')."std-startup.xml");

                        file_put_contents($this->getResourcePath('')."/std-startup.xml", $stdxml);

                        $message = $this->objLanguage->languageText("mod_computerscience_aimlpublished", "computerscience");
                        $this->nextAction('', array('message' => $message));
                        break;

                    default:
                        $message = $this->getParam('message', NULL);
                        $filename = $this->objConfig->getContentBasepath().'users/'.$this->objUser->userId().'/aiml/'.$this->objUser->userId().'_std-cs4fn.aiml';
                        if(file_exists($filename)) {
                            $xml = simplexml_load_file($filename);
                            $cats = $xml->category;
                            foreach ($cats as $cat) {
                                $catarr[] = $cat->pattern;
                            }
                        }
                        else {
                            $catarr = array();
                        }

                        $str = $this->objDict->buildForm($catarr);
                        $this->setVar('message', $message);
                        $this->setVar('str', $str);

                        return 'editadd_tpl.php';
                        break;
            }
        }

        private function endSession() {
            //return exec("killall python");
            $pids = $this->getPID ( 'bot.py' );
            if (count ( $pids ) > 0) {
                foreach ( $pids as $pid ) {
                    return exec ( "kill " . $pid );
                }
            }
        }

        private function getPID($param) {
            exec ( "ps aux", $result );
            $r2 = array ();
            foreach ( $result as $line ) {
                if (strpos ( $line, $param )) {
                    $l2 = substr ( $line, strpos ( $line, ' ' ), - 1 );
                    $l2 = trim ( $l2 );
                    $l2 = substr ( $l2, 0, strpos ( $l2, ' ' ) );
                    $l2 = trim ( $l2 );
                    $r2 [] = $l2;
                }
            }
            return $r2;
        }

    }
?>
