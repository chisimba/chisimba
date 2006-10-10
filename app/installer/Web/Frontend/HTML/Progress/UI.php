<?php
/**
 * HTML loading bar with only PHP and JS interface.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    HTML_Progress
 * @subpackage Progress_UI
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_Progress
 * @since      File available since Release 1.0
 */

require_once 'HTML/Common.php';

/**
 * HTML loading bar with only PHP and JS interface.
 *
 * The HTML_Progress_UI class provides a basic look and feel
 * implementation of a progress bar.
 *
 * @category   HTML
 * @package    HTML_Progress
 * @subpackage Progress_UI
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_Progress2
 * @since      Class available since Release 1.0
 */

class HTML_Progress_UI extends HTML_Common
{
    /**
     * Whether the progress bar is horizontal, vertical, polygonal or circle.
     * The default is horizontal.
     *
     * @var        integer
     * @since      1.0
     * @access     private
     * @see        getOrientation(), setOrientation()
     */
    var $_orientation;

    /**
     * Whether the progress bar is filled in 'natural' or 'reverse' way.
     * The default fill way is 'natural'.
     *
     * <ul>
     * <li>since 0.5 : 'way'  =  bar fill way
     *   <ul>
     *     <li>with Progress Bar Horizontal,
     *              natural way is : left to right
     *        <br />reverse way is : right to left
     *     <li>with Progress Bar Vertical,
     *              natural way is : down to up
     *        <br />reverse way is : up to down
     *     <li>with Progress Circle or Polygonal,
     *              natural way is : clockwise
     *        <br />reverse way is : anticlockwise
     *   </ul>
     * </ul>
     *
     * @var        string
     * @since      1.0
     * @access     private
     * @see        getFillWay(), setFillWay()
     */
    var $_fillWay;

    /**
     * The cell count of the progress bar. The default is 10.
     *
     * @var        integer
     * @since      1.0
     * @access     private
     * @see        getCellCount(), setCellCount()
     */
    var $_cellCount;

    /**
     * The cell coordinates for a progress polygonal shape.
     *
     * @var        array
     * @since      1.2.0
     * @access     private
     * @see        getCellCoordinates(), setCellCoordinates()
     */
    var $_coordinates;

    /**
     * The width of grid in cell-size of the polygonal shape.
     *
     * @var        integer
     * @since      1.2.0
     * @access     private
     * @see        getCellCoordinates(), setCellCoordinates()
     */
    var $_xgrid;

    /**
     * The height of grid in cell-size of the polygonal shape.
     *
     * @var        integer
     * @since      1.2.0
     * @access     private
     * @see        getCellCoordinates(), setCellCoordinates()
     */
    var $_ygrid;

    /**
     * The progress bar's structure
     *
     * <ul>
     * <li>['cell']
     *     <ul>
     *     <li>since 1.0 : 'id'             =  cell identifier mask
     *     <li>since 1.0 : 'class'          =  css class selector
     *     <li>since 0.1 : 'width'          =  cell width
     *     <li>since 0.1 : 'height'         =  cell height
     *     <li>since 0.1 : 'active-color'   =  active color
     *     <li>since 0.1 : 'inactive-color' =  inactive color
     *     <li>since 0.1 : 'spacing'        =  cell spacing
     *     <li>since 0.6 : 'color'          =  foreground color
     *     <li>since 0.6 : 'font-size'      =  font size
     *     <li>since 0.6 : 'font-family'    =  font family
     *     <li>since 1.2 : 'background-color' = background color
     *     </ul>
     * <li>['border']
     *     <ul>
     *     <li>since 1.0 : 'class'  =  css class selector
     *     <li>since 0.1 : 'width'  =  border width
     *     <li>since 0.1 : 'style'  =  border style
     *     <li>since 0.1 : 'color'  =  border color
     *     </ul>
     * <li>['string']
     *     <ul>
     *     <li>sicne 1.0 : 'id'                =  string identifier
     *     <li>since 0.6 : 'width'             =  with of progress string
     *     <li>since 0.6 : 'height'            =  height of progress string
     *     <li>since 0.1 : 'font-family'       =  font family
     *     <li>since 0.1 : 'font-size'         =  font size
     *     <li>since 0.1 : 'color'             =  font color
     *     <li>since 0.6 : 'background-color'  =  background color
     *     <li>since 0.6 : 'align'             =  horizontal align  (left, center, right, justify)
     *     <li>since 0.6 : 'valign'            =  vertical align  (top, bottom, left, right)
     *     </ul>
     * <li>['progress']
     *     <ul>
     *     <li>since 1.0 : 'class'             =  css class selector
     *     <li>since 0.1 : 'background-color'  =  bar background color
     *     <li>since 1.0 : 'auto-size'         = compute best progress size
     *     <li>since 0.1 : 'width'             =  bar width
     *     <li>since 0.1 : 'height'            =  bar height
     *     </ul>
     * </ul>
     *
     * @var        array
     * @since      1.0
     * @access     private
     * @see        HTML_Progress::toArray()
     */
    var $_progress = array();

    /**
     * External Javascript file to override internal default code
     *
     * @var        string
     * @since      1.0
     * @access     private
     * @see        getScript(), setScript()
     */
    var $_script;


    /**
     * The progress bar's UI model class constructor
     *
     * Constructor Summary
     *
     * o Creates a natural horizontal progress bar that displays ten cells/units.
     *   <code>
     *   $html = new HTML_Progress_UI();
     *   </code>
     *
     * o Creates a natural horizontal progress bar with the specified cell count,
     *   which cannot be less than 1 (minimum), but has no maximum limit.
     *   <code>
     *   $html = new HTML_Progress_UI($cell);
     *   </code>
     *
     * @param      int       $cell          (optional) Cell count
     *
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     */
    function HTML_Progress_UI()
    {
        // if you've not yet created an instance of html_progress
        if (!$GLOBALS['_HTML_PROGRESS_CALLBACK_ERRORHANDLER']) {
            // init default error handling system,
            HTML_Progress::_initErrorHandler();
        }

        $args = func_get_args();

        switch (count($args)) {
         case 1:
            /*   int cell  */
            if (!is_int($args[0])) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$cell',
                          'was' => $args[0],
                          'expected' => 'integer',
                          'paramnum' => 1));

            } elseif ($args[0] < 1) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                    array('var' => '$cell',
                          'was' => $args[0],
                          'expected' => 'greater or equal 1',
                          'paramnum' => 1));
            }
            $this->_cellCount = $args[0];
            break;
         default:
            $this->_cellCount = 10;
            break;
        }
        $this->_orientation = HTML_PROGRESS_BAR_HORIZONTAL;
        $this->_fillWay = 'natural';
        $this->_script = null;              // uses internal javascript code

        $this->_progress = array(
            'cell' =>
                array(
                    'id' => "progressCell%01s",
                    'class' => "cell",
                    'active-color' => "#006600",
                    'inactive-color' => "#CCCCCC",
                    'font-family' => "Courier, Verdana",
                    'font-size' => 8,
                    'color' => "#000000",
                    'background-color' => "#FFFFFF",
                    'width' => 15,
                    'height' => 20,
                    'spacing' => 2
                ),
            'border' =>
                array(
                    'class' => "progressBarBorder",
                    'width' => 0,
                    'style' => "solid",
                    'color' => "#000000"
                ),
            'string' =>
                array(
                    'id' => "installationProgress",
                    'width' => 50,
                    'font-family' => "Verdana, Arial, Helvetica, sans-serif",
                    'font-size' => 12,
                    'color' => "#000000",
                    'background-color' => "#FFFFFF",
                    'align' => "right",
                    'valign' => "right"
                ),
            'progress' =>
                array(
                    'class' => "progressBar",
                    'background-color' => "#FFFFFF",
                    'auto-size' => true
                )
        );
        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns HTML_PROGRESS_BAR_HORIZONTAL or HTML_PROGRESS_BAR_VERTICAL,
     * depending on the orientation of the progress bar.
     * The default orientation is HTML_PROGRESS_BAR_HORIZONTAL.
     *
     * @return     integer
     * @since      1.0
     * @access     public
     * @see        setOrientation()
     */
    function getOrientation()
    {
        return $this->_orientation;
    }

    /**
     * Sets the progress bar's orientation, which must be HTML_PROGRESS_BAR_HORIZONTAL
     * or HTML_PROGRESS_BAR_VERTICAL.
     * The default orientation is HTML_PROGRESS_BAR_HORIZONTAL.
     *
     * @param      integer   $orient        Orientation (horizontal or vertical)
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getOrientation()
     */
    function setOrientation($orient)
    {
        if (!is_int($orient)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$orient',
                      'was' => gettype($orient),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif (($orient != HTML_PROGRESS_BAR_HORIZONTAL) &&
                  ($orient != HTML_PROGRESS_BAR_VERTICAL) &&
                  ($orient != HTML_PROGRESS_POLYGONAL) &&
                  ($orient != HTML_PROGRESS_CIRCLE)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$orient',
                      'was' => $orient,
                      'expected' => HTML_PROGRESS_BAR_HORIZONTAL.' | '.
                                    HTML_PROGRESS_BAR_VERTICAL.' | '.
                                    HTML_PROGRESS_POLYGONAL.' | '.
                                    HTML_PROGRESS_CIRCLE,
                      'paramnum' => 1));
        }

        $previous = $this->_orientation;    // gets previous orientation
        $this->_orientation = $orient;      // sets the new orientation

        if ($previous != $orient) {
            // if orientation has changed, we need to swap cell width and height
            $w = $this->_progress['cell']['width'];
            $h = $this->_progress['cell']['height'];

            $this->_progress['cell']['width']  = $h;
            $this->_progress['cell']['height'] = $w;

            $this->_updateProgressSize();   // updates the new size of progress bar
        }
    }

    /**
     * Returns 'natural' or 'reverse', depending of the fill way of progress bar.
     * For horizontal progress bar, natural way is from left to right, and reverse
     * way is from right to left.
     * For vertical progress bar, natural way is from down to up, and reverse
     * way is from up to down.
     * The default fill way is 'natural'.
     *
     * @return     string
     * @since      1.0
     * @access     public
     * @see        setFillWay()
     */
    function getFillWay()
    {
        return $this->_fillWay;
    }

    /**
     * Sets the progress bar's fill way, which must be 'natural' or 'reverse'.
     * The default fill way is 'natural'.
     *
     * @param      string    $way           fill direction (natural or reverse)
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getFillWay()
     */
    function setFillWay($way)
    {
        if (!is_string($way)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$way',
                      'was' => gettype($way),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif ((strtolower($way) != 'natural') && (strtolower($way) != 'reverse')) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$way',
                      'was' => $way,
                      'expected' => 'natural | reverse',
                      'paramnum' => 1));
        }
        $this->_fillWay = strtolower($way);
    }

    /**
     * Returns the number of cell in the progress bar. The default value is 10.
     *
     * @return     integer
     * @since      1.0
     * @access     public
     * @see        setCellCount()
     */
    function getCellCount()
    {
        return $this->_cellCount;
    }

    /**
     * Sets the number of cell in the progress bar
     *
     * @param      integer   $cells         Cell count on progress bar
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getCellCount()
     */
    function setCellCount($cells)
    {
        if (!is_int($cells)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$cells',
                      'was' => gettype($cells),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($cells < 1) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$cells',
                      'was' => $cells,
                      'expected' => 'greater or equal 1',
                      'paramnum' => 1));
        }
        $this->_cellCount = $cells;

        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns the common and private cell attributes. Assoc array (defaut) or string
     *
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        setCellAttributes()
     */
    function getCellAttributes($asString = false)
    {
        if (!is_bool($asString)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $attr = $this->_progress['cell'];

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets the cell attributes for an existing cell.
     *
     * Defaults are:
     * <ul>
     * <li>Common :
     *     <ul>
     *     <li>id             = progressCell%01s
     *     <li>class          = cell
     *     <li>spacing        = 2
     *     <li>active-color   = #006600
     *     <li>inactive-color = #CCCCCC
     *     <li>font-family    = Courier, Verdana
     *     <li>font-size      = lowest value from cell width, cell height, and font size
     *     <li>color          = #000000
     *     <li>background-color = #FFFFFF (added for progress circle shape on release 1.2.0)
     *     <li>Horizontal Bar :
     *         <ul>
     *         <li>width      = 15
     *         <li>height     = 20
     *         </ul>
     *     <li>Vertical Bar :
     *         <ul>
     *         <li>width      = 20
     *         <li>height     = 15
     *         </ul>
     *     </ul>
     * </ul>
     *
     * @param      mixed     $attributes    Associative array or string of HTML tag attributes
     * @param      int       $cell          (optional) Cell index
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getCellAttributes(), getCellCount()
     */
    function setCellAttributes($attributes, $cell = null)
    {
        if (!is_null($cell)) {
            if (!is_int($cell)) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$cell',
                          'was' => gettype($cell),
                          'expected' => 'integer',
                          'paramnum' => 2));

            } elseif ($cell < 0) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                    array('var' => '$cell',
                          'was' => $cell,
                          'expected' => 'positive',
                          'paramnum' => 2));

            } elseif ($cell > $this->getCellCount()) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                    array('var' => '$cell',
                          'was' => $cell,
                          'expected' => 'less or equal '.$this->getCellCount(),
                          'paramnum' => 2));
            }

            $this->_updateAttrArray($this->_progress['cell'][$cell], $this->_parseAttributes($attributes));
        } else {
            $this->_updateAttrArray($this->_progress['cell'], $this->_parseAttributes($attributes));
        }

        $font_size   = $this->_progress['cell']['font-size'];
        $cell_width  = $this->_progress['cell']['width'];
        $cell_height = $this->_progress['cell']['height'];
        $margin = ($this->getOrientation() == HTML_PROGRESS_BAR_HORIZONTAL) ? 0 : 3;

        $font_size = min(min($cell_width, $cell_height) - $margin, $font_size);
        $this->_progress['cell']['font-size'] = $font_size;

        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns the coordinates of each cell for a polygonal progress shape.
     *
     * @return     array                    list of cell coordinates
     * @since      1.2.0
     * @access     public
     * @see        setCellCoordinates()
     */
    function getCellCoordinates()
    {
        return isset($this->_coordinates) ? $this->_coordinates : array();
    }

    /**
     * Set the coordinates of each cell for a polygonal progress shape.
     *
     * @param      integer   $xgrid     The grid width in cell size
     * @param      integer   $ygrid     The grid height in cell size
     * @param      array     $coord     (optional) Coordinates (x,y) in the grid, of each cell
     *
     * @return     void
     * @since      1.2.0
     * @access     public
     * @see        getCellCoordinates()
     */
    function setCellCoordinates($xgrid, $ygrid, $coord = array())
    {
        if (!is_int($xgrid)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$xgrid',
                      'was' => gettype($xgrid),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($xgrid < 3) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$xgrid',
                      'was' => $xgrid,
                      'expected' => 'greater than 2',
                      'paramnum' => 1));

        } elseif (!is_int($ygrid)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$ygrid',
                      'was' => gettype($ygrid),
                      'expected' => 'integer',
                      'paramnum' => 2));

        } elseif ($ygrid < 3) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$ygrid',
                      'was' => $ygrid,
                      'expected' => 'greater than 2',
                      'paramnum' => 2));

        } elseif (!is_array($coord)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$coord',
                      'was' => gettype($coord),
                      'expected' => 'array',
                      'paramnum' => 3));
        }

        if (count($coord) == 0) {
            // Computes all coordinates of a standard polygon (square or rectangle)
            $coord = $this->_computeCoordinates($xgrid, $ygrid);
        } else {
            foreach ($coord as $id => $pos) {
                if (!is_array($pos)) {
                    return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                        array('var' => '$coord[,$pos]',
                              'was' => gettype($pos),
                              'expected' => 'array',
                              'paramnum' => 3));
                }
                if ($pos[0] >= $ygrid) {
                    return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                        array('var' => '$pos[0]',
                              'was' => $pos[0],
                              'expected' => 'coordinate less than grid height',
                              'paramnum' => 3));
                }
                if ($pos[1] >= $xgrid) {
                    return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                        array('var' => '$pos[1]',
                              'was' => $pos[1],
                              'expected' => 'coordinate less than grid width',
                              'paramnum' => 3));
                }
            }
        }
        $this->_coordinates = $coord;
        $this->_xgrid = $xgrid;
        $this->_ygrid = $ygrid;

        // auto-compute cell count
        $this->_cellCount = count($coord);

        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns the progress bar's border attributes. Assoc array (defaut) or string.
     *
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        setBorderAttributes()
     */
    function getBorderAttributes($asString = false)
    {
        if (!is_bool($asString)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $attr = $this->_progress['border'];

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets the progress bar's border attributes.
     *
     * Defaults are:
     * <ul>
     * <li>class   = progressBarBorder
     * <li>width   = 0
     * <li>style   = solid
     * <li>color   = #000000
     * </ul>
     *
     * @param      mixed     $attributes    Associative array or string of HTML tag attributes
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getBorderAttributes(), HTML_Progress::setBorderPainted()
     */
    function setBorderAttributes($attributes)
    {
        $this->_updateAttrArray($this->_progress['border'], $this->_parseAttributes($attributes));

        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns the string attributes. Assoc array (defaut) or string.
     *
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        setStringAttributes()
     */
    function getStringAttributes($asString = false)
    {
        if (!is_bool($asString)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $attr = $this->_progress['string'];

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets the string attributes.
     *
     * Defaults are:
     * <ul>
     * <li>id                = installationProgress
     * <li>width             = 50
     * <li>font-family       = Verdana, Arial, Helvetica, sans-serif
     * <li>font-size         = 12
     * <li>color             = #000000
     * <li>background-color  = #FFFFFF
     * <li>align             = right
     * <li>Horizontal Bar :
     *     <ul>
     *     <li>valign        = right
     *     </ul>
     * <li>Vertical Bar :
     *     <ul>
     *     <li>valign        = bottom
     *     </ul>
     * </ul>
     *
     * @param      mixed     $attributes    Associative array or string of HTML tag attributes
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getStringAttributes(), HTML_Progress::setStringPainted()
     */
    function setStringAttributes($attributes)
    {
        $this->_updateAttrArray($this->_progress['string'], $this->_parseAttributes($attributes));
    }

    /**
     * Returns the progress attributes. Assoc array (defaut) or string.
     *
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        setProgressAttributes()
     */
    function getProgressAttributes($asString = false)
    {
        if (!is_bool($asString)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $attr = $this->_progress['progress'];

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets the common progress bar attributes.
     *
     * Defaults are:
     * <ul>
     * <li>class             = progressBar
     * <li>background-color  = #FFFFFF
     * <li>auto-size         = true
     * <li>Horizontal Bar :
     *     <ul>
     *     <li>width         = (cell_count * (cell_width + cell_spacing)) + cell_spacing
     *     <li>height        = cell_height + (2 * cell_spacing)
     *     </ul>
     * <li>Vertical Bar :
     *     <ul>
     *     <li>width         = cell_width + (2 * cell_spacing)
     *     <li>height        = (cell_count * (cell_height + cell_spacing)) + cell_spacing
     *     </ul>
     * </ul>
     *
     * @param      mixed     $attributes    Associative array or string of HTML tag attributes
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @see        getProgressAttributes()
     */
    function setProgressAttributes($attributes)
    {
        $this->_updateAttrArray($this->_progress['progress'], $this->_parseAttributes($attributes));
    }

    /**
     * Get the javascript code to manage progress bar.
     *
     * @return     string                   JavaScript URL or inline code to manage progress bar
     * @since      0.5
     * @access     public
     * @see        setScript()
     * @author     Stefan Neufeind <pear.neufeind@speedpartner.de> Contributor.
     *             See details on thanks section of README file.
     * @author     Christian Wenz <wenz@php.net> Helper.
     *             See details on thanks section of README file.
     */
    function getScript()
    {
        if (!is_null($this->_script)) {
            return $this->_script;   // URL to the linked Progress JavaScript
        }

        $js = <<< JS
var isDom = document.getElementById?true:false;
var isIE  = document.all?true:false;
var isNS4 = document.layers?true:false;
var cellCount = %cellCount%;

function setprogress(pIdent, pValue, pString, pDeterminate)
{
    if (isDom) {
        prog = document.getElementById(pIdent+'%installationProgress%');
    } else if (isIE) {
        prog = document.all[pIdent+'%installationProgress%'];
    } else if (isNS4) {
        prog = document.layers[pIdent+'%installationProgress%'];
    }
    if (prog != null) {
        prog.innerHTML = pString;
    }
    if (pValue == pDeterminate) {
        for (i=0; i < cellCount; i++) {
            showCell(i, pIdent, "hidden");
        }
    }
    if ((pDeterminate > 0) && (pValue > 0)) {
        i = (pValue-1) % cellCount;
        showCell(i, pIdent, "visible");
    } else {
        for (i=pValue-1; i >=0; i--) {
            showCell(i, pIdent, "visible");
        }
    }
}

function setVisibility(pElement, pVisibility)
{
    if (isDom) {
        document.getElementById(pElement).style.visibility = pVisibility;
    } else if (isIE) {
        document.all[pElement].style.visibility = pVisibility;
    } else if (isNS4) {
        document.layers[pElement].style.visibility = pVisibility;
    }
}

function showCell(pCell, pIdent, pVisibility)
{
    setVisibility(pIdent+'%progressCell%'+pCell+'A', pVisibility);
}

function hideProgress(pIdent)
{
    setVisibility(pIdent+'progress', 'hidden');

    for (i=0; i < cellCount; i++) {
        showCell(i, pIdent, "hidden");
    }
}

JS;
        $cellAttr = $this->getCellAttributes();
        $attr = trim(sprintf($cellAttr['id'], '   '));
        $stringAttr = $this->getStringAttributes();

        $placeHolders = array('%cellCount%', '%installationProgress%', '%progressCell%');
        $jsElement = array($this->getCellCount(), $stringAttr['id'], $attr);

        $js = str_replace($placeHolders, $jsElement, $js);

        return $js;
    }

    /**
     * Set the external JavaScript code (file) to manage progress element.
     *
     * @param      string    $url           URL to the linked Progress JavaScript
     *
     * @return     void
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     * @see        getScript()
     */
    function setScript($url)
    {
        if (!is_null($url)) {
            if (!is_string($url)) {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$url',
                          'was' => gettype($url),
                          'expected' => 'string',
                          'paramnum' => 1));

            } elseif (!is_file($url) || $url == '.' || $url == '..') {
                return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                    array('var' => '$url',
                          'was' => $url.' file does not exists',
                          'expected' => 'javascript file exists',
                          'paramnum' => 1));
            }
        }

        /*
         - since version 0.5.0,
         - default javascript code comes from getScript() method
         - but may be overrided by external file.
        */
        $this->_script = $url;
    }

    /**
     * Get the cascading style sheet to put inline on HTML document
     *
     * @return     string
     * @since      0.2
     * @access     public
     * @author     Stefan Neufeind <pear.neufeind@speedpartner.de> Contributor.
     *             See details on thanks section of README file.
     */
    function getStyle()
    {
        $tab = $this->_getTab();
        $lnEnd = $this->_getLineEnd();
        $progressAttr = $this->getProgressAttributes();
        $borderAttr = $this->getBorderAttributes();
        $stringAttr = $this->getStringAttributes();
        $cellAttr = $this->getCellAttributes();
        $orient = $this->getOrientation();

        $css  = '{%pIdent%} .' . $progressAttr['class'] . ', {%pIdent%} .' . $borderAttr['class'] . ' {' . $lnEnd;
        $css .= $tab . 'background-color: '. $progressAttr['background-color'] .';'. $lnEnd;
        $css .= $tab . 'width: '. $progressAttr['width'] .'px;'. $lnEnd;
        $css .= $tab . 'height: '. $progressAttr['height'] .'px;'. $lnEnd;
        $css .= $tab . 'position: relative;'. $lnEnd;
        $css .= $tab . 'left: 0;'. $lnEnd;
        $css .= $tab . 'top: 0;'. $lnEnd;
        $css .= '}'. $lnEnd . $lnEnd;

        $css .= '{%pIdent%} .' . $borderAttr['class'] . ' {' . $lnEnd;
        $css .= $tab . 'border-width: '. $borderAttr['width'] .'px;'. $lnEnd;
        $css .= $tab . 'border-style: '. $borderAttr['style'] .';'. $lnEnd;
        $css .= $tab . 'border-color: '. $borderAttr['color'] .';'. $lnEnd;
        $css .= '}'. $lnEnd . $lnEnd;

        $css .= '{%pIdent%} .' . $stringAttr['id'] . ' {' . $lnEnd;
        $css .= $tab . 'width: '. $stringAttr['width'] .'px;'. $lnEnd;
        if (isset($stringAttr['height'])) {
            $css .= $tab . 'height: '. $stringAttr['height'] .'px;'. $lnEnd;
        }
        $css .= $tab . 'text-align: '. $stringAttr['align'] .';'. $lnEnd;
        $css .= $tab . 'font-family: '. $stringAttr['font-family'] .';'. $lnEnd;
        $css .= $tab . 'font-size: '. $stringAttr['font-size'] .'px;'. $lnEnd;
        $css .= $tab . 'color: '. $stringAttr['color'] .';'. $lnEnd;
        $css .= $tab . 'background-color: '. $stringAttr['background-color'] .';'. $lnEnd;
        $css .= '}'. $lnEnd . $lnEnd;

        $css .= '{%pIdent%} .' . $cellAttr['class'] . 'I, {%pIdent%} .' . $cellAttr['class'] . 'A {' . $lnEnd;
        $css .= $tab . 'width: '. $cellAttr['width'] .'px;'. $lnEnd;
        $css .= $tab . 'height: '. $cellAttr['height'] .'px;'. $lnEnd;
        $css .= $tab . 'font-family: '. $cellAttr['font-family'] .';'. $lnEnd;
        $css .= $tab . 'font-size: '. $cellAttr['font-size'] .'px;'. $lnEnd;
        if ($orient == HTML_PROGRESS_BAR_HORIZONTAL) {
            $css .= $tab . 'float: left;'. $lnEnd;
        }
        if ($orient == HTML_PROGRESS_BAR_VERTICAL) {
            $css .= $tab . 'float: none;'. $lnEnd;
        }
        $css .= '}'. $lnEnd . $lnEnd;

        $css .= '{%pIdent%} .' . $cellAttr['class'] . 'I {' . $lnEnd;
        if ($orient !== HTML_PROGRESS_CIRCLE) {
            $css .= $tab . 'background-color: '. $cellAttr['inactive-color'] .';'. $lnEnd;
        }
        if ($orient == HTML_PROGRESS_CIRCLE) {
            $css .= $tab . 'background-image: url("'. $cellAttr[0]['background-image'] .'");'. $lnEnd;
            $css .= $tab . 'background-repeat: no-repeat;'. $lnEnd;
        }
        $css .= '}'. $lnEnd . $lnEnd;

        $css .= '{%pIdent%} .' . $cellAttr['class'] . 'A {' . $lnEnd;
        if ($orient !== HTML_PROGRESS_CIRCLE) {
            $css .= $tab . 'background-color: '. $cellAttr['active-color'] .';'. $lnEnd;
        }
        $css .= $tab . 'visibility: hidden;'. $lnEnd;
        if (isset($cellAttr['background-image'])) {
            $css .= $tab . 'background-image: url("'. $cellAttr['background-image'] .'");'. $lnEnd;
            $css .= $tab . 'background-repeat: no-repeat;'. $lnEnd;
        }
        $css .= '}'. $lnEnd . $lnEnd;

        return $css;
    }

    /**
     * Draw all circle segment pictures
     *
     * @param      string    $dir           (optional) Directory where pictures should be created
     * @param      string    $fileMask      (optional) sprintf format for pictures filename
     *
     * @return     array
     * @since      1.2.0RC1
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     */
    function drawCircleSegments($dir = '.', $fileMask = 'c%s.png')
    {
        if (!is_string($dir)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$dir',
                      'was' => gettype($dir),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_string($fileMask)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$fileMask',
                      'was' => gettype($fileMask),
                      'expected' => 'string',
                      'paramnum' => 2));
        }

        include_once 'Image/Color.php';

        $cellAttr  = $this->getCellAttributes();
        $cellCount = $this->getCellCount();
        $w = $cellAttr['width'];
        $h = $cellAttr['height'];
        $s = $cellAttr['spacing'];
        $c = intval(360 / $cellCount);
        $cx = floor($w / 2);
        if (fmod($w,2) == 0) {
            $cx = $cx - 0.5;
        }
        $cy = floor($h / 2);
        if (fmod($h,2) == 0) {
            $cy = $cy - 0.5;
        }

        $image = imagecreate($w, $h);

        $bg     = Image_Color::allocateColor($image,$cellAttr['background-color']);
        $colorA = Image_Color::allocateColor($image,$cellAttr['active-color']);
        $colorI = Image_Color::allocateColor($image,$cellAttr['inactive-color']);

        imagefilledarc($image, $cx, $cy, $w, $h, 0, 360, $colorI, IMG_ARC_EDGED);
        $filename = $dir . DIRECTORY_SEPARATOR . sprintf($fileMask,0);
        imagepng($image, $filename);
        $this->setCellAttributes(array('background-image' => $filename),0);

        for ($i=0; $i<$cellCount; $i++) {
            if ($this->getFillWay() == 'natural') {
                $sA = $i*$c;
                $eA = ($i+1)*$c;
                $sI = ($i+1)*$c;
                $eI = 360;
            } else {
                $sA = 360-(($i+1)*$c);
                $eA = 360-($i*$c);
                $sI = 0;
                $eI = 360-(($i+1)*$c);
            }
            if ($s > 0) {
                imagefilledarc($image, $cx, $cy, $w, $h, 0, $sA, $colorI, IMG_ARC_EDGED);
            }
            imagefilledarc($image, $cx, $cy, $w, $h, $sA, $eA, $colorA, IMG_ARC_EDGED);
            imagefilledarc($image, $cx, $cy, $w, $h, $sI, $eI, $colorI, IMG_ARC_EDGED);
            $filename = $dir . DIRECTORY_SEPARATOR . sprintf($fileMask,$i+1);
            imagepng($image, $filename);

            $this->setCellAttributes(array('background-image' => $filename),$i+1);
        }
        imagedestroy($image);
    }

    /**
     * Updates the new size of progress bar, depending of cell size, cell count
     * and border width.
     *
     * @since      1.0
     * @access     private
     * @see        setOrientation(), setCellCount(), setCellAttributes(),
     *             setBorderAttributes()
     */
    function _updateProgressSize()
    {
        if (!$this->_progress['progress']['auto-size']) {
            return;
        }

        $cell_width   = $this->_progress['cell']['width'];
        $cell_height  = $this->_progress['cell']['height'];
        $cell_spacing = $this->_progress['cell']['spacing'];

        $border_width = $this->_progress['border']['width'];

        $cell_count = $this->_cellCount;

        if ($this->getOrientation() == HTML_PROGRESS_BAR_HORIZONTAL) {
            $w = ($cell_count * ($cell_width + $cell_spacing)) + $cell_spacing;
            $h = $cell_height + (2 * $cell_spacing);
        }
        if ($this->getOrientation() == HTML_PROGRESS_BAR_VERTICAL) {
            $w  = $cell_width + (2 * $cell_spacing);
            $h  = ($cell_count * ($cell_height + $cell_spacing)) + $cell_spacing;
        }
        if ($this->getOrientation() == HTML_PROGRESS_POLYGONAL) {
            $w  = $cell_width * $this->_xgrid;
            $h  = $cell_height * $this->_ygrid;
        }
        if ($this->getOrientation() == HTML_PROGRESS_CIRCLE) {
            $w  = $cell_width;
            $h  = $cell_height;
        }

        $attr = array ('width' => $w, 'height' => $h);

        $this->_updateAttrArray($this->_progress['progress'], $attr);
    }

    /**
     * Computes all coordinates of a standard polygon (square or rectangle).
     *
     * @param      integer   $w             Polygon width
     * @param      integer   $h             Polygon height
     *
     * @return     array
     * @since      1.2.0
     * @access     private
     * @see        setCellCoordinates()
     */
    function _computeCoordinates($w, $h)
    {
        $coord = array();

        for ($y=0; $y<$h; $y++) {
            if ($y == 0) {
                // creates top side line
                for ($x=0; $x<$w; $x++) {
                    $coord[] = array($y, $x);
                }
            } elseif ($y == ($h-1)) {
                // creates bottom side line
                for ($x=($w-1); $x>0; $x--) {
                    $coord[] = array($y, $x);
                }
                // creates left side line
                for ($i=($h-1); $i>0; $i--) {
                    $coord[] = array($i, 0);
                }
            } else {
                // creates right side line
                $coord[] = array($y, $w - 1);
            }
        }
        return $coord;
    }
}

?>