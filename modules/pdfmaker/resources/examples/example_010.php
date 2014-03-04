<?php
//============================================================+
// File name   : example_010.php
// Begin       : 2008-03-04
// Last Update : 2008-05-28
//
// Description : Example 010 for TCPDF class
//               Text on multiple columns
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
 * @abstract TCPDF - Example: Text on multiple columns
 * @author Nicola Asuni
 * @copyright 2004-2008 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2008-03-04
 */
require_once ('../config/lang/eng.php');
require_once ('../tcpdf.php');
// extend TCPF with custom functions
class MYPDF extends TCPDF
{
    //number of colums
    protected $ncols = 3;
    // columns width
    protected $colwidth = 57;
    //Current column
    protected $col = 0;
    //Ordinate of column start
    protected $y0;
    //Set position at a given column
    public function SetCol($col) 
    {
        $this->col = $col;
        // space between columns
        if ($this->ncols > 1) {
            $column_space = round((float)($this->w-$this->original_lMargin-$this->original_rMargin-($this->ncols*$this->colwidth)) /($this->ncols-1));
        } else {
            $column_space = 0;
        }
        // X position of the current column
        $x = $this->original_lMargin+($col*($this->colwidth+$column_space));
        $this->SetLeftMargin($x);
        $this->SetRightMargin($this->w-$x-$this->colwidth);
        $this->SetX($x);
    }
    //Method accepting or not automatic page break
    public function AcceptPageBreak() 
    {
        if ($this->col < ($this->ncols-1)) {
            //Go to next column
            $this->SetCol($this->col+1);
            //Set ordinate to top
            $this->SetY($this->y0);
            //Keep on page
            return false;
        } else {
            $this->AddPage();
            //Go back to first column
            $this->SetCol(0);
            $this->y0 = $this->GetY();
            //Page break
            return false;
        }
    }
    // Set chapter title
    public function ChapterTitle($num, $label) 
    {
        $this->SetFont('FreeSerif', '', 14);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(0, 6, "Chapter $num : $label", 0, 1, 'L', 1);
        $this->Ln(4);
        //Save ordinate
        $this->y0 = $this->GetY();
    }
    // Print chapter body
    public function ChapterBody($file) 
    {
        // store current margin values
        $lMargin = $this->lMargin;
        $rMargin = $this->rMargin;
        // get esternal file content
        $txt = file_get_contents($file, false);
        //Font
        $this->SetFont('dejavusans', '', 8);
        //Output text in a 6 cm width column
        $this->MultiCell($this->colwidth, 5, $txt, 0, 'J', 0, 1, 0, 0, true, 0);
        $this->Ln();
        //Go back to first column
        $this->SetCol(0);
        // restore previous margin values
        $this->SetLeftMargin($lMargin);
        $this->SetRightMargin($rMargin);
    }
    //Add chapter
    public function PrintChapter($num, $title, $file) 
    {
        $this->AddPage();
        $this->ChapterTitle($num, $title);
        $this->ChapterBody($file);
    }
}
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Nicola Asuni");
$pdf->SetTitle("TCPDF Example 010");
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
// ---------------------------------------------------------
$pdf->PrintChapter(1, 'A RUNAWAY REEF', '../cache/chapter_demo_1.txt');
$pdf->PrintChapter(2, 'THE PROS AND CONS', '../cache/chapter_demo_2.txt');
// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output("example_010.pdf", "I");
//============================================================+
// END OF FILE
//============================================================+

?>