<?php
class createpagefromfile {

    function init() {
        $this->objAltConfig = $this->getObject('altconfig','config');
        $this->objAltConfig = $this->getObject('altconfig','config');
        $this->siteRoot=$this->objAltConfig->getsiteRoot();
        $this->moduleUri=$this->objAltConfig->getModuleURI();

    }

    function createPage($file,$pageid,$context,$chapter) {
        $path=$this->siteRoot."/usrfiles/".    $file['path'];
        $ext=$this->getExt($path);
        $content='[FILEPREVIEW id="'.$file['fileid'].'" comment="" /]';
        if($ext == 'pdf') {
            $content='[PDF]'.$path.'[/PDF]';
        }

        $menutitle = $file['name'];
        $headerscripts ='';
        $language = 'en';
        $pagecontent = $content;
        $parent ='root';
        $chapter = stripslashes($chapter);
        return $this->nextAction('savepage',
                array(
                    'menutitle'=>$menutitle,
                    'headerscripts'=>$headerscripts,
                    'language'=>$language,
                    'pagecontent'=>$pagecontent,
                    'parent'=>$parent,
                    'chapter'=>$chapter));

    }

    /**
     * return an extension of a given file
     * @param <type> $filename
     */
    function getExt($filename) {
        $filename = strtolower($filename) ;
        $exts = split("[/\\.]", $filename) ;
        $n = count($exts)-1;
        $ext = $exts[$n];
    }
}

?>
