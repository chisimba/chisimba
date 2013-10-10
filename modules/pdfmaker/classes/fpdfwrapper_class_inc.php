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
require_once ($this->getResourcePath('fpdf.php', 'pdfmaker'));
class fpdfwrapper extends object
{
    public $pdf;
    public function init() 
    {
        define('FPDF_FONTPATH', $this->getResourcePath('font/'));
        $this->pdf = new FPDF();
    }
    public function simplePdf($text) 
    {
        $this->pdf->AddPage();
        /*//parse the text and look for images
        preg_match_all('/\[img\](.*)\[\/img\]/U', $text, $matches, PREG_PATTERN_ORDER);
        unset($matches[0]);
        //print_r($matches);
        $mcount = 0;
        foreach($matches as $match)
        {
        $text = preg_replace('/\[img\](.*)\[\/img\]/U', $this->pdf->image($match[$mcount], 0, 0, 0), $text);
        $mcount++;
        }
        // echo $text; die();
        */
        $this->pdf->SetFont('Times', '', 12);
        $this->pdf->MultiCell(0, 5, $text);
        //Line break
        $this->pdf->Ln();
        $this->pdf->Output();
    }
    public function pdfOutput() 
    {
        $this->pdf->Output();
    }
    //function hex2dec
    //returns an associative array (keys: R,G,B) from
    //a hex html code (e.g. #3FE5AA)
    function hex2dec($couleur = "#000000") 
    {
        $R = substr($couleur, 1, 2);
        $rouge = hexdec($R);
        $V = substr($couleur, 3, 2);
        $vert = hexdec($V);
        $B = substr($couleur, 5, 2);
        $bleu = hexdec($B);
        $tbl_couleur = array();
        $tbl_couleur['R'] = $rouge;
        $tbl_couleur['G'] = $vert;
        $tbl_couleur['B'] = $bleu;
        return $tbl_couleur;
    }
    //conversion pixel -> millimeter in 72 dpi
    function px2mm($px) 
    {
        return $px*25.4/72;
    }
    function txtentities($html) 
    {
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans = array_flip($trans);
        return strtr($html, $trans);
    }
    //////////////////////////////////////
    //html parser
    function WriteHTML($html, $font = Null, $fontSize = Null) 
    {
        $this->pdf->AddPage();
        if (empty($font)) $this->pdf->SetFont('Arial');
        else $this->pdf->SetFontSize($font);
        if (empty($fontSize)) $this->pdf->SetFontSize(12);
        else $this->pdf->SetFontSize($fontSize);
        $html = strip_tags($html, "<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><hr><td><tr><table><sup>");
        //remove all unsupported tags
        $html = str_replace("\n", '', $html); //replace carriage returns by spaces
        $html = str_replace("\t", '', $html); //replace carriage returns by spaces
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE); //explodes the string
        foreach($a as $i => $e) {
            if ($i%2 == 0) {
                //Text
                if ($this->HREF) $this->PutLink($this->HREF, $e);
                elseif ($this->pdf->tdbegin) {
                    if (trim($e) != '' and $e != "&nbsp;") {
                        $this->pdf->Cell($this->pdf->tdwidth, $this->pdf->tdheight, $e, $this->pdf->tableborder, '', $this->pdf->tdalign, $this->pdf->tdbgcolor);
                    } elseif ($e == "&nbsp;") {
                        $this->pdf->Cell($this->pdf->tdwidth, $this->pdf->tdheight, '', $this->pdf->tableborder, '', $this->pdf->tdalign, $this->pdf->tdbgcolor);
                    }
                } else $this->pdf->Write(5, stripslashes($this->txtentities($e)));
            } else {
                //Tag
                if ($e{0} == '/') $this->CloseTag(strtoupper(substr($e, 1)));
                else {
                    //Extract attributes
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v) if (ereg('^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3)) $attr[strtoupper($a3[1]) ] = $a3[2];
                    $this->OpenTag($tag, $attr);
                }
            }
        }
        $this->pdf->Output();
    }
    function OpenTag($tag, $attr) 
    {
        //Opening tag
        switch ($tag) {
            case 'SUP':
                if ($attr['SUP'] != '') {
                    //Set current font to: Bold, 6pt
                    $this->pdf->SetFont('', '', 6);
                    //Start 125cm plus width of cell to the right of left margin
                    //Superscript "1"
                    $this->pdf->Cell(2, 2, $attr['SUP'], 0, 0, 'L');
                }
                break;

            case 'TABLE': // TABLE-BEGIN
                if ($attr['BORDER'] != '') $this->pdf->tableborder = $attr['BORDER'];
                else $this->pdf->tableborder = 0;
                break;

            case 'TR': //TR-BEGIN
                break;

            case 'TD': // TD-BEGIN
                if ($attr['WIDTH'] != '') $this->tdwidth = ($attr['WIDTH']/4);
                else $this->pdf->tdwidth = 40; // SET to your own width if you need bigger fixed cells
                if ($attr['HEIGHT'] != '') $this->tdheight = ($attr['HEIGHT']/6);
                else $this->pdf->tdheight = 6; // SET to your own height if you need bigger fixed cells
                if ($attr['ALIGN'] != '') {
                    $align = $attr['ALIGN'];
                    if ($align == "LEFT") $this->pdf->tdalign = "L";
                    if ($align == "CENTER") $this->pdf->tdalign = "C";
                    if ($align == "RIGHT") $this->pdf->tdalign = "R";
                } else $this->pdf->tdalign = "L"; // SET to your own
                if ($attr['BGCOLOR'] != '') {
                    $coul = hex2dec($attr['BGCOLOR']);
                    $this->pdf->SetFillColor($coul['R'], $coul['G'], $coul['B']);
                    $this->pdf->tdbgcolor = true;
                }
                $this->pdf->tdbegin = true;
                break;

            case 'HR':
                if ($attr['WIDTH'] != '') $Width = $attr['WIDTH'];
                else $Width = $this->w-$this->pdf->lMargin-$this->pdf->rMargin;
                $x = $this->pdf->GetX();
                $y = $this->pdf->GetY();
                $this->pdf->SetLineWidth(0.2);
                $this->pdf->Line($x, $y, $x+$Width, $y);
                $this->pdf->SetLineWidth(0.2);
                $this->pdf->Ln(1);
                break;

            case 'STRONG':
                $this->SetStyle('B', true);
                break;

            case 'EM':
                $this->SetStyle('I', true);
                break;

            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag, true);
                break;

            case 'A':
                $this->HREF = $attr['HREF'];
                break;

            case 'IMG':
                if (isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
                    if (!isset($attr['WIDTH'])) $attr['WIDTH'] = 0;
                    if (!isset($attr['HEIGHT'])) $attr['HEIGHT'] = 0;
                    $this->pdf->Image($attr['SRC'], $this->pdf->GetX() , $this->pdf->GetY() , px2mm($attr['WIDTH']) , px2mm($attr['HEIGHT']));
                }
                break;
                //case 'TR':
                
            case 'BLOCKQUOTE':
            case 'BR':
                $this->pdf->Ln(5);
                break;

            case 'P':
                $this->pdf->Ln(10);
                break;

            case 'FONT':
                if (isset($attr['COLOR']) and $attr['COLOR'] != '') {
                    $coul = hex2dec($attr['COLOR']);
                    $this->pdf->SetTextColor($coul['R'], $coul['G'], $coul['B']);
                    $this->pdf->issetcolor = true;
                }
                if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']) , $this->pdf->fontlist)) {
                    $this->pdf->SetFont(strtolower($attr['FACE']));
                    $this->pdf->issetfont = true;
                }
                if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']) , $this->pdf->fontlist) and isset($attr['SIZE']) and $attr['SIZE'] != '') {
                    $this->pdf->SetFont(strtolower($attr['FACE']) , '', $attr['SIZE']);
                    $this->pdf->issetfont = true;
                }
                break;
            }
        }
        function CloseTag($tag) 
        {
            //Closing tag
            if ($tag == 'SUP') {
            }
            if ($tag == 'TD') { // TD-END
                $this->pdf->tdbegin = false;
                $this->pdf->tdwidth = 0;
                $this->pdf->tdheight = 0;
                $this->pdf->tdalign = "L";
                $this->pdf->tdbgcolor = false;
            }
            if ($tag == 'TR') { // TR-END
                $this->pdf->Ln();
            }
            if ($tag == 'TABLE') { // TABLE-END
                //$this->Ln();
                $this->pdf->tableborder = 0;
            }
            if ($tag == 'STRONG') $tag = 'B';
            if ($tag == 'EM') $tag = 'I';
            if ($tag == 'B' or $tag == 'I' or $tag == 'U') $this->SetStyle($tag, false);
            if ($tag == 'A') $this->HREF = '';
            if ($tag == 'FONT') {
                if ($this->pdf->issetcolor == true) {
                    $this->pdf->SetTextColor(0);
                }
                if ($this->pdf->issetfont) {
                    $this->pdf->SetFont('arial');
                    $this->pdf->issetfont = false;
                }
            }
        }
        function SetStyle($tag, $enable) 
        {
            //Modify style and select corresponding font
            $this->$tag+= ($enable ? 1 : -1);
            $style = '';
            foreach(array(
                'B',
                'I',
                'U'
            ) as $s) if ($this->$s > 0) $style.= $s;
            $this->pdf->SetFont('Arial', '', $style);
        }
        function PutLink($URL, $txt) 
        {
            //Put a hyperlink
            $this->SetTextColor(0, 0, 255);
            $this->SetStyle('U', true);
            $this->Write(5, $txt, $URL);
            $this->SetStyle('U', false);
            $this->SetTextColor(0);
        }
        //Simple table
        function basicTable($header, $data) 
        {
            $this->pdf->SetFont('Arial', '', 14);
            $this->pdf->AddPage();
            //Column widths
            $w = array(
                40,
                35,
                40,
                45
            );
            //Header
            for ($i = 0; $i < count($header); $i++) $this->pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
            $this->pdf->Ln();
            //Data
            foreach($data as $row) {
                $this->pdf->Cell($w[0], 6, $row[0], 'LR');
                $this->pdf->Cell($w[1], 6, $row[1], 'LR');
                $this->pdf->Cell($w[2], 6, $row[2], 'LR', 0, 'R');
                $this->pdf->Cell($w[3], 6, $row[3], 'LR', 0, 'R');
                $this->pdf->Ln();
            }
            //Closure line
            $this->pdf->Cell(array_sum($w) , 0, '', 'T');
            $this->pdf->Output();
        }
    }
?>
