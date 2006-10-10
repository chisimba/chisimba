<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
require_once 'HTML/Table/Matrix/Filler.php';
require_once 'HTML/Table/Matrix/Filler/InCC.php';

/**
 * Fill outwards, clockwise.
 *
 * @author Arpad Ray <arpad@rajeczy.com>
 * @package HTML_Table_Matrix
 */
class HTML_Table_Matrix_Filler_OutCC extends HTML_Table_Matrix_Filler_InCC {

    /**
     * Constructor
     *
     * @param Object $matrix Reference to the HTML_Table_Matrix instance we are
     *                       filling data for.
     * @param array $options Options for this Filler
     * @return void
     */
    function HTML_Table_Matrix_Filler_OutCC(&$matrix, $options = false) {
        $this->setOptions($options);
        $this->matrix = $matrix;
        $this->callback = 'array_reverse';
    }
}
?>
