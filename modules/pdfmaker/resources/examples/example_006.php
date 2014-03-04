<?php
//============================================================+
// File name   : example_006.php
// Begin       : 2008-03-04
// Last Update : 2008-07-29
//
// Description : Example 006 for TCPDF class
//               WriteHTML and RTL support
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: WriteHTML and RTL support
 * @author Nicola Asuni
 * @copyright 2004-2008 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2008-03-04
 */
require_once ('../config/lang/eng.php');
require_once ('../tcpdf.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Nicola Asuni");
$pdf->SetTitle("TCPDF Example 006");
$pdf->SetSubject("TCPDF Tutorial");
$pdf->SetKeywords("TCPDF, PDF, example, test, guide");
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(Array(
    PDF_FONT_NAME_MAIN,
    '',
    PDF_FONT_SIZE_MAIN
));
$pdf->setFooterFont(Array(
    PDF_FONT_NAME_DATA,
    '',
    PDF_FONT_SIZE_DATA
));
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
//set some language-dependent strings
$pdf->setLanguageArray($l);
//initialize document
$pdf->AliasNbPages();
// add a page
$pdf->AddPage();
// ---------------------------------------------------------
// set font
$pdf->SetFont("freeserif", "", 11);
// create some HTML content
$htmlcontent = "<h1>HTML Example</h1>&lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt; \\slash \\\\double-slash \\\\\\triple-slash<h2>List</h2>List example:<ol><li><b>bold text</b></li><li><i>italic text</i></li><li><u>underlined text</u></li><li><a href=\"http://www.tecnick.com\" dir=\"ltr\">link to http://www.tecnick.com</a></li><li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.<br />Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</li><li>SUBLIST<ol><li>row one<ul><li>sublist</li></ul></li><li>row two</li></ol></li><li><b>T</b>E<i>S</i><u>T</u> <del>line through</del></li><li><font size=\"+3\">font + 3</font></li><li><small>small text</small> normal <small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal</li></ol><dl><dt>Coffee</dt><dd>Black hot drink</dd><dt>Milk</dt><dd>White cold drink</dd></dl><div style=\"text-align:center\">IMAGES<br /><img src=\"../images/logo_example.png\" alt=\"test alt attribute\" width=\"100\" height=\"100\" border=\"0\" align=\"top\" /><img src=\"../images/tiger.ai\" alt=\"test alt attribute\" width=\"100\" height=\"100\" border=\"0\" align=\"top\" /><img src=\"../images/logo_example.jpg\" alt=\"test alt attribute\" width=\"100\" height=\"100\" border=\"0\" /></div>";
// output the HTML content
$pdf->writeHTML($htmlcontent, true, 0, true, 0);
// output some RTL HTML content
$pdf->writeHTML("<div style=\"text-align:center\">The words &#8220;<span dir=\"rtl\">&#1502;&#1494;&#1500; [mazel] &#1496;&#1493;&#1489; [tov]</span>&#8221; mean &#8220;Congratulations!&#8221;</div>", true, 0, true, 0);
// reset pointer to the last page
$pdf->lastPage();
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table
// add a page
$pdf->AddPage();
// create some HTML content
$subtable = "<table border=\"1\" cellspacing=\"1\" cellpadding=\"1\"><tr><td>a</td><td>b</td></tr><tr><td>c</td><td>d</td></tr></table>";
$htmltable = "<h2>HTML TABLE:</h2><table border=\"1\" cellspacing=\"2\" cellpadding=\"2\"><tr><th>#</th><th align=\"right\">RIGHT align</th><th align=\"left\">LEFT align</th><th>4A</th></tr><tr><td>1</td><td bgcolor=\"#cccccc\" align=\"center\" colspan=\"2\">A1 ex<i>amp</i>le <a href=\"http://www.tcpdf.org\">link</a> column span. One two tree four five six seven eight nine ten.<br />line after br<br /><small>small text</small> normal <sub>subscript</sub> normal <sup>superscript</sup> normal  bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla bla<ol><li>first<ol><li>sublist</li><li>sublist</li></ol></li><li>second</li></ol><small color=\"#FF0000\" bgcolor=\"#FFFF00\">small small small small small small small small small small small small small small small small small small small small</small></td><td>4B</td></tr><tr><td>" . $subtable . "</td><td bgcolor=\"#0000FF\" color=\"yellow\" align=\"center\">A2 € &euro; &#8364; &amp; è &egrave;<br/>A2 € &euro; &#8364; &amp; è &egrave;</td><td bgcolor=\"#FFFF00\" align=\"left\"><font color=\"#FF0000\">Red</font> Yellow BG</td><td>4C</td></tr><tr><td>1A</td><td rowspan=\"2\" colspan=\"2\" bgcolor=\"#FFFFCC\">2AA<br />2AB<br />2AC</td><td>4D</td></tr><tr><td>1B</td><td>4E</td></tr><tr><td>1C</td><td>2C</td><td>3C</td><td>4F</td></tr></table>";
// output the HTML content
$pdf->writeHTML($htmltable, true, 0, true, 0);
// Print some HTML Cells
$cellcontent = "<span color=\"red\">red</span> <span color=\"green\">green</span> <span color=\"blue\">blue</span><br /><span color=\"red\">red</span> <span color=\"green\">green</span> <span color=\"blue\">blue</span>";
$pdf->SetFillColor(255, 255, 0);
$pdf->writeHTMLCell(0, 0, '', '', $cellcontent, "LRTB", 1, 0, true, '');
$pdf->writeHTMLCell(0, 0, '', '', $cellcontent, "LRTB", 1, 1, true, 'C');
$pdf->writeHTMLCell(0, 0, '', '', $cellcontent, "LRTB", 1, 0, true, 'R');
// reset pointer to the last page
$pdf->lastPage();
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print all HTML colors
// add a page
$pdf->AddPage();
require_once ('../htmlcolors.php');
$textcolors = "<h1>HTML Text Colors</h1>";
$bgcolors = "<hr /><h1>HTML Background Colors</h1>";
foreach($webcolor as $k => $v) {
    $textcolors.= "<span color=\"#" . $v . "\">" . $v . "</span> ";
    $bgcolors.= "<span bgcolor=\"#" . $v . "\" color=\"#333333\">" . $v . "</span> ";
}
// output the HTML content
$pdf->writeHTML($textcolors, true, 0, true, 0);
$pdf->writeHTML($bgcolors, true, 0, true, 0);
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Test word-wrap
// create some HTML content
$htmltxt = "<hr /><h1>Word-wrap</h1><font face=\"courier\"><b>thisisaverylongword</b></font> <font face=\"helvetica\"><i>thisisanotherverylongword</i></font> <font face=\"times\"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face=\"dejavusans\">thisisaverylongword</font> <font face=\"courier\"><b>thisisaverylongword</b></font> <font face=\"helvetica\"><i>thisisanotherverylongword</i></font> <font face=\"times\"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face=\"dejavusans\">thisisaverylongword</font> <font face=\"courier\"><b>thisisaverylongword</b></font> <font face=\"helvetica\"><i>thisisanotherverylongword</i></font> <font face=\"times\"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face=\"dejavusans\">thisisaverylongword</font> <font face=\"courier\"><b>thisisaverylongword</b></font> <font face=\"helvetica\"><i>thisisanotherverylongword</i></font> <font face=\"times\"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face=\"dejavusans\">thisisaverylongword</font> <font face=\"courier\"><b>thisisaverylongword</b></font> <font face=\"helvetica\"><i>thisisanotherverylongword</i></font> <font face=\"times\"><b>thisisaverylongword</b></font> thisisanotherverylongword <font face=\"dejavusans\">thisisaverylongword</font> ";
// output the HTML content
$pdf->writeHTML($htmltxt, true, 0, true, 0);
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Test fonts nesting
$htmltxt = "<hr /><h1>Fonts nesting</h1>Default <font face=\"courier\">Courier <font face=\"helvetica\">Helvetica <font face=\"times\">Times <font face=\"dejavusans\">DejavuSans </font>Times </font>Helvetica </font>Courier </font>Default";
// output the HTML content
$pdf->writeHTML($htmltxt, true, 0, true, 0);
// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output("example_006.pdf", "I", "I");
//============================================================+
// END OF FILE
//============================================================+

?>