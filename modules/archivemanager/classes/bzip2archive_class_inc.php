<?php
    require_once "archive_class_inc.php";

    class bzip2archive extends archive{

        private $bzip2;

        public function init(){
            parent::init();
        }

        public function open(){
            $this->bzip2 = bzopen(parent::getfilename(),"r");
        }

        public function extractTo($foldername){
            //echo bzread($this->bzip2);

        }
    }
?>
