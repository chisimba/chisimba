<?php
class block_learningtools extends object{
    function init(){
      $this->objLanguage = $this->getObject ( 'language', 'language' );
      $this->title=$this->objLanguage->languageText('mod_learningcontent_toolstitle', 'learningcontent');
    }

    function show(){
      return "test";
    }
}
?>
