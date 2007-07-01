<?
$header = $this->getObject('htmlheading', 'htmlelements');
echo $header->show();
?>

<textarea cols = 100 rows = 30>
<? echo $xbelOutput ?>
</textarea>

<?
$link = $this->newObject('link','htmlelements');
$link->href = $this->uri(NULL);
$link->link=$this->objLanguage->languageText("word_back");
echo '<p>'.$link->show().'</p>';
?>