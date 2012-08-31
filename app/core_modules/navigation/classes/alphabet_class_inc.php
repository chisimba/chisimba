<?php
/**
* This class provides some methods for working with
* linked letters of the alphabet
*/

class alphabet extends object
{

     var $objLanguage;

    /**
    * @var $listAllName
    */
     var $listAllName;

    /**
    * Constructor class for the buttons class, used to
    * instatntiate a local access to the language object
    */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
    * Method to display the letters of the alphabet with a link
    * to perform an action based on $link depending on what letter
    * is clicked. If $link is supplied, then an active link is made
    * @param string $link:the link that should be activated when
    * the letter is clicked. It takes the form
    * FILE.PHP?action=someaction&somevariable=somevalue&letter=LETTER
    * where LETTER will be replaced by the letter as the function
    * goes through its loop
    * @author Derek Keats
        * @author James Scoble
    */
    function putAlpha($link=NULL, $caps=TRUE, $listAllName=Null,$target=Null)
        {
        if ($target!=Null){
                    $target=" target=\"".$target."\" ";
                } else {
                    $target='';
                }

        // I've added a variable called listAllName that allows you to change the word for 'List All Records'
        // Tohir 26 August 2004, 9:00am
        if ($listAllName == '') {
            $this->listAllName = $this->objLanguage->languageText("word_list");
        } else {
            $this->listAllName = $listAllName;
        }

        $ret=NULL;
        //if ($caps) {
            $lBound=65;
            $uBound=90;
        //}
        for ($i=$lBound; $i<=$uBound; $i++) {
            $link2=str_replace("LETTER",chr($i),$link);
            $linkall=str_replace("LETTER", "listall", $link);
            $ret.=' | <a href="'.$link2.'"'.$target.'>'.chr($i)."</a>\n";
        }
        $ret.=' | <a href="'.$linkall.'"'.$target.'>'.$this->listAllName.'</a> |';
        return $ret;
    }

    /**
    * Method to display the alphabet as in function putAlpha() with a second double alphabet below.
    * A | B | C | ...
    * AA | AB | AC | ...
    *
    * @author Megan Watson
    */
    function putDoubleAlpha($link=NULL, $letter, $listAllName=Null,$target=Null)
    {
        if ($target!=Null){
            $target=" target=\"".$target."\" ";
        } else {
            $target='';
        }

        if ($listAllName == '') {
            $this->listAllName = $this->objLanguage->languageText("word_list");
        } else {
            $this->listAllName = $listAllName;
        }

        $alpha1 = '';
        $alpha2 = '';
        $lBound=65;
        $uBound=90;
            
        for ($i=$lBound; $i<=$uBound; $i++) {
            $link2=str_replace("LETTER",chr($i),$link);
            $alpha1 .= ' | <a href="'.$link2.'"'.$target.'>'.chr($i)."</a>\n";
        }
        $linkall=str_replace("LETTER", "listall", $link);
        $alpha1 .=' | <a href="'.$linkall.'"'.$target.'>'.$this->listAllName.'</a> |';
        
        // Double alphabet
        for ($i = $lBound; $i <= $uBound; $i++) {
            $link2 = str_replace("LETTER", $letter.chr($i), $link);
            $alpha2 .= ' | <a href="'.$link2.'"'.$target.'>'.$letter.chr($i)."</a>\n";
        }
        
        $ret = $alpha1.'<br /><font size="1">'.$alpha2.'</font>';
        return $ret;
    }

}
?>