<?php
    // If there is a message, then we send it to the screen
    // Using languageText to translate it unless the textFlag
    // flag is set, which over-rides this.
    
    if (isset($this->message)){
        if (isset($this->textFlag)){
            $text=$this->message;
        } else {
            $text=$this->objLanguage->languageText($this->message);
        }
    } else {
        $text=$this->objLanguage->languageText('phrase_confirmchange','Changes Made');
    }
?>
<table align='center'>
<tr><td>
<h2><?php echo $text; ?></h2>
</td></tr>
<tr><td>
<?php
    $objButtons=&$this->getObject('navbuttons','navigation');
    print $objButtons->pseudoButton($this->uri(array()),$objLanguage->languagetext('word_ok','OK'));
?>
</td></tr>
</table>
