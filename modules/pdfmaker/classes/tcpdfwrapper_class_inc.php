<?php
// security check - must be included in all scripts
/*
*Author: Paul Mungai
*University of Nairobi
*/
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
require_once ($this->getResourcePath('tcpdf.php', 'pdfmaker'));
class tcpdfwrapper extends object
{
    public $pdf;
    public function init() 
    {
        define('FPDF_FONTPATH', $this->getResourcePath('font/'));
        define('TCPDF_LANGPATH', $this->getResourcePath('lang/'));
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
    }
    //html parser
    //function that writes and outputs a pdf document from HTML
    function WriteHTML($html, $font = Null, $fontSize = Null) 
    {
        // set document information
        // set default header data
        //$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        // set header and footer fonts
        $this->pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $this->pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        //set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //set some language-dependent strings
        if(empty($l))
         $l = Null;
        $this->pdf->setLanguageArray($l);
        //initialize document
        $this->pdf->AliasNbPages();
        // add a page
        $this->pdf->AddPage();
        // set font
        if (empty($font)) $font = "freeserif";
        if (empty($fontSize)) $fontSize = 11;
        $this->pdf->SetFont($font, "", $fontSize);
        // output the HTML content
        $this->pdf->writeHTML($html, true, 0, true, 0);
        // reset pointer to the last page
        $this->pdf->lastPage();
        //Close and output PDF document
        $this->pdf->Output('eportfolio.pdf', 'I', 'I');
    }
    /*
    Function to initialize the tcpdf
    Note: only use when using partWrite
    $this->pdf->initWrite()
    $html is the html string
    $this->pdf->partWrite($html, $font=Null, $fontSize=Null)
    $this->pdf->show()
    */
    function initWrite() 
    {
        // set header and footer fonts
        $this->pdf->setHeaderFont(Array(
            PDF_FONT_NAME_MAIN,
            '',
            PDF_FONT_SIZE_MAIN
        ));
        $this->pdf->setFooterFont(Array(
            PDF_FONT_NAME_DATA,
            '',
            PDF_FONT_SIZE_DATA
        ));
        //set margins
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        //set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        //set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //set some language-dependent strings
        if(empty($l))
         $l = Null;
        $this->pdf->setLanguageArray($l);
        //initialize document
        $this->pdf->AliasNbPages();
    }
    /*
    Function to write html on a new page
    Example
    $this->pdf->initWrite()
    $this->pdf->partWrite($html, $font=Null, $fontSize=Null)
    $this->pdf->show()
    */
    function partWrite($html, $font = Null, $fontSize = Null) 
    {
        // add a page
        $this->pdf->AddPage();
        // set font
        if (empty($font)) $font = "freeserif";
        if (empty($fontSize)) $fontSize = 11;
        $this->pdf->SetFont($font, "", $fontSize);
        // output the HTML content
        $this->pdf->writeHTML($html, true, 0, true, 0);
        // reset pointer to the last page
        $this->pdf->lastPage();
    }
    //Add a new page (use with partWrite)
    function addPage() 
    {
        // add a page
        $this->pdf->AddPage();
    }
    //Construct output
    function Output($docname,$dest="D")
    {
        $this->pdf->Output($docname, $dest);
    }

    function show() 
    {
        //Close and output PDF document
        $this->pdf->Output('document.pdf', 'I');
    }
}
?>
