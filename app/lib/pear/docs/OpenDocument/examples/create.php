<?php
require_once 'OpenDocument.php'; // open document class

//create a new OpenDocument Text file
$odt = new OpenDocument;
//add heading
$h = $odt->createHeading('Heading', 1);
//create paragraph
$p1 = $odt->createParagraph('Paragraph 1');
//set paragraph styles
$p1->style->fontSize = '12pt';
$p1->style->fontName = 'Times New Roman';
$p1->style->color = '#009900';
$p1->style->underlineStyle = 'dotted';
$p1->style->underlineColor = '#009999';
$p1->style->underlineWidth = '2pt';
//create second paragraph
$p2 = $odt->createParagraph('Paragraph 2');
//copy styles from first one
$p2->style->copy($p1->style);
//insert space into paragraph
$p1->createTextElement(' ');
//create a link inside a paragraph
$a1 = $p1->createHyperlink('', 'http://ya.ru', 'simple', '_self', 'link');
//insert text in link
$span = $a1->createSpan('Ya.ru');
//apply color to text
$span->style->color = '#000099';
//insert space to heading
$h->createTextElement(' ');
//create link in heading
$a2 = $h->createHyperlink('Ya.ru', 'http://ya.ru');
//apply underline color to link
$a2->style->underlineColor = '#990000';
//save as test.odt
$odt->save('test.odt');

echo 'saved as test.odt';
?>