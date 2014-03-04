<?php

/**
 * This class contains util methods for displaying full original product details
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @version    0.001
 * @package    oer

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     pwando paulwando@gmail.com
 */

/**
 * Description of documentgenerator_class_inc
 *
 * @author Paul Mungai paulwando@gmail.com, manie
 *
 */
class documentgenerator extends object {

    public $pdf;
    private $dbproducts;
    public $objConfig;
    public $buffer;

    public function init() {
        $this->pdf = $this->getObject('tcpdfwrapper', 'pdfmaker');
        $this->objViewAdaptation = $this->getObject("viewadaptation", "oer");
        $this->dbproducts = $this->getObject("dbproducts", "oer");
        //Create the configuration object
        $this->objConfig = $this->getObject("altconfig", "config");
        $this->buffer = "";
    }

    /**
     * Function that renders the product in Pdf
     *
     * @param string $productId
     * @param string $prodType
     * @return string
     */
    public function showProductPDF($productId, $prodType) {
        $this->pdf->initWrite();
        $prodData = "";
        $ext = ".pdf";
        if ($prodType == "adaptation") {
            $prodData = $this->objViewAdaptation->buildAdaptationForPrint($productId);
        }
        $this->pdf->partWrite($prodData);
        //doc random identifier
        $randNo = mt_rand(1000, 15000);
        $prodTitle = $randNo;
        //Get product title
        $prodTitle = $this->dbproducts->getProductTitle($productId);
        if ($prodTitle != Null && !empty($prodTitle)) {
            $prodTitle = $randNo . "_" . str_replace(" ", "_", $prodTitle);
        }
        //Get content path
        $fbasepath = $this->objConfig->getItem("KEWL_CONTENT_BASEPATH");
        //Doc name
        $docName = $prodTitle . $ext;

        $docBasePath = $fbasepath . $docName;

        //Close and output PDF document
        ob_start();
        //Param F: save to a local server file with the name given by name in param 1 of Output
        //$this->pdf->Output($docName, "D");
        $this->pdf->Output($docBasePath, "F");
        ob_end_clean();

        $fpath = $this->objConfig->getItem("KEWL_CONTENT_PATH");
        $sitepath = $this->objConfig->getItem("KEWL_SITE_ROOT");
        $docPath = $sitepath . $fpath . $docName;
        return $docPath;
    }

    /**
     * Function that generates docs into diff word formats i.e. .doc, .odt
     * @param String $productId
     * @param String $prodType product type
     * @param String $ext file extension
     * @param String $randno a random number
     * @return document path
     */
    public function showProductWordFormats($productId, $prodType, $ext) {
        $prodData = "";
        if (empty($ext)) {
            $ext = ".doc";
        }
        //doc random identifier
        $randNo = mt_rand(1000, 15000);
        if ($prodType == "adaptation") {
            $prodData = $this->objViewAdaptation->buildAdaptationForPrint($productId);
            //Remove all images
            $prodData = preg_replace("/<img[^>]+\>/i", " ", $prodData);
        }
        //doc random identifier
        $randNo = mt_rand(1000, 15000);
        $prodTitle = $randNo;
        $prodTitle = $this->dbproducts->getProductTitle($productId);
        if ($prodTitle != Null && !empty($prodTitle)) {
            $prodTitle = $randNo . "_" . str_replace(" ", "_", $prodTitle);
        }
        $fbasepath = $this->objConfig->getItem("KEWL_CONTENT_BASEPATH");

        $docBasePath = $fbasepath . $prodTitle . $ext;

        //Form the document
        $fp = fopen($docBasePath, 'w+');
        fwrite($fp, $prodData);
        fclose($fp);
        $fpath = $this->objConfig->getItem("KEWL_CONTENT_PATH");
        $sitepath = $this->objConfig->getItem("KEWL_SITE_ROOT");
        $docPath = $sitepath . $fpath . $prodTitle . $ext;
        return $docPath;
    }

}

?>