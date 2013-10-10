<?php

require_once 'PHPExcel.php';
require_once 'PHPExcel/IOFactory.php';

class pdfgenerator extends object {

    public function init() {
        $this->objDbGift = $this->getObject("dbgift");
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objDepartments = $this->getObject("dbdepartments");
        $this->objUser = $this->getObject('user', 'security');
        $this->objGift = $this->getObject("giftops");
    }

    function generatePdf($departmentid, $departmentname) {
        $gifts = $this->objDbGift->getGifts($departmentid);
        $objPHPExcel = new PHPExcel();

// Set properties
        $objPHPExcel->getProperties()->setCreator("Gift Register")
                ->setLastModifiedBy("Gift Register")
                ->setTitle("Office 2007 XLSX Gift Register Export")
                ->setSubject("Office 2007 XLSX Gift Register Export")
                ->setDescription("Gift Register for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Gift Register");


        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', $departmentname);

// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A2', 'Gift Name')
                ->setCellValue('B2', 'Type')
                ->setCellValue('C2', 'Description')
                ->setCellValue('D2', 'Donor')
                ->setCellValue('E2', 'Value')
                ->setCellValue('F2', 'Recipient')
                ->setCellValue('G2', 'Date Received')
                ->setCellValue('H2', 'Date Recorded');

        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
        $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
        $objPHPExcel->getActiveSheet()->getStyle('C2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
        $objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
        $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
        $objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
        $objPHPExcel->getActiveSheet()->getStyle('G2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
        $objPHPExcel->getActiveSheet()->getStyle('H2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);


        if (count($gifts) > 0) {
            $row = 3;
            foreach ($gifts as $gift) {
                //$data[] = array($gift['giftname'], $gift['gift_type'], $gift['donor'], $gift['value'], $gift['recipient'], $gift['date_recieved'], $gift['tran_date']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $gift['giftname']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $gift['gift_type']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, strip_tags($gift['description']));

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $gift['donor']);
                $value = $this->objGift->formatMoney($gift['value'], TRUE);

                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, 'R' . $value);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $this->objUser->fullname($gift['recipient']));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $gift['date_recieved']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $gift['tran_date']);
                $row++;
            }
        }

// Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Gift Register');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="giftregisterexport.pdf"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
        $objWriter->setSheetIndex(0);
        $objWriter->save('php://output');
    }

}

?>
