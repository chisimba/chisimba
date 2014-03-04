<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
class pdfgen extends controller
{
    public $objLog;
    public $objLanguage;
    public $objPdf;
    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init() 
    {
        try {
            $this->objPdf = $this->getObject('fpdfwrapper');
            //var_dump($this->objPdf); die();
            //$this->objLanguage = $this->getObject('language', 'language');
            //Get the activity logger class
            //$this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            //$this->objLog->log();
            
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
    /**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
    public function dispatch($action = Null) 
    {
        switch ($action) {
            default:
            case 'test':
                $output = $this->objPdf->simplePdf("How many apples make 5? You may also include line breaks and newlines here and it will format correctly.
            										NOTE: HTML tags will not be parsed and will simply be rendered as plain text...");
                break;

            case 'table':
                $type = $this->getParam('type');
                $header = array(
                    'column 0',
                    'column 1',
                    'column 2',
                    'column3',
                    'col4'
                );
                $data = array(
                    array(
                        '0',
                        '1',
                        '2',
                        '3'
                    ) ,
                    array(
                        'a',
                        'b',
                        'c',
                        'd'
                    ) ,
                    array(
                        'stuff',
                        'r',
                        't',
                        'gf'
                    ) ,
                    array(
                        'blah',
                        'blah',
                        'blah',
                        'blah'
                    )
                );
                $output = $this->objPdf->basicTable($header, $data);
        }
    }
}
?>
