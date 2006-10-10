<?php
/**
 * Standalone HTML loading bar with only PHP and JS interface.
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
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_Progress2
 * @since      File available since Release 2.0.0RC1
 */

if (!function_exists('ob_get_clean')) {
    function ob_get_clean()
    {
        $contents = ob_get_contents();

        if ($contents !== false) {
            ob_end_clean();
        }

        return $contents;
    }
}

if (!defined('PHP_EOL')) {
    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        // Windows
        case 'WIN':
            define('PHP_EOL', "\r\n");
            break;

        // Mac
        case 'DAR':
            define('PHP_EOL', "\r");
            break;

        // Unix
        default:
            define('PHP_EOL', "\n");
    }
}

/**
 * Standalone HTML loading bar with only PHP and JS interface.
 *
 * The HTML_Progress2_Lite class allow you to add a quick
 * horizontal or vertical loading bar to any of your xhtml document.
 * You should have a browser that accept DHTML feature.
 *
 * This class has no dependency and can be used completely outside
 * the PEAR infrastructure.
 *
 * Here is a basic example:
 * <code>
 * <html>
 * <body>
 * <?php
 * require_once 'HTML/Progress2_Lite.php';
 *
 * function myProcess()
 * {
 *     for ($i=0; $i<100000; $i++) { }
 * }
 *
 * $pbl = new HTML_Progress2_Lite();
 * $pbl->addLabel('text','txt1','Progress2 Lite - Basic Example');
 * $pbl->display();
 *
 * for($i=1; $i<=100; $i++) {
 *     $pbl->moveStep($i);
 *     myProcess();
 * }
 * ?>
 * </body>
 * </html>
 * </code>
 *
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @author     Mika Turin <turin@inbox.lv>
 * @author     Gerd Weitenberg <hahnebuechen@web.de>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 2.0.0
 * @link       http://pear.php.net/package/HTML_Progress2
 * @link       http://www.phpclasses.org/browse/package/1222.html
 *             From an original idea of Mike Turin
 * @link       http://www.phpclasses.org/browse/package/1964.html
 *             Improve version of Gerd Weitenberg
 * @since      Class available since Release 2.0.0RC1
 */

class HTML_Progress2_Lite
{
    /**
     * Label that uniquely identifies the progress bar.
     *
     * @var        string
     * @since      2.0.0
     * @access     private
     */
    var $_ident;

    /**
     * Status of the progress bar (new, show, hide).
     *
     * @var        string
     * @since      2.0.0
     * @access     private
     */
    var $_status = 'new';

    /**
     * Steps of the progress bar.
     *
     * @var        integer
     * @since      2.0.0
     * @access     private
     */
    var $_step = 0;

    /**
     * Minimum steps of the progress bar.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     */
    var $min;

    /**
     * Maximum steps of the progress bar.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     */
    var $max;

    /**
     * Progress bar position (absolute, relative).
     *
     * @var        string
     * @since      2.0.0
     * @access     public
     */
    var $position;

    /**
     * Progress bar position from left.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     */
    var $left;

    /**
     * Progress bar position from top.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     */
    var $top;

    /**
     * Progress bar width in pixel.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     */
    var $width;

    /**
     * Progress bar height in pixel.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     */
    var $height;

    /**
     * Progress bar padding in pixel.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     */
    var $padding;

    /**
     * Progress bar foreground color.
     *
     * @var        string
     * @since      2.0.0
     * @access     public
     */
    var $foreground_color = '#0033FF';

    /**
     * Progress bar foreground color.
     *
     * @var        string
     * @since      2.0.0
     * @access     public
     */
    var $background_color = '#C0C0C0';

    /**
     * Progress bar border properties
     *
     * <code>
     * $border = array(
     *    'width' => 1          # width size in pixel
     *    'style' => 'solid'    # style (solid, dashed, dotted ...)
     *    'color' => '#000000'  # color
     * );
     * </code>
     *
     * @var        array
     * @since      2.0.0
     * @access     public
     */
    var $border = array('width' => 1, 'style' => 'solid', 'color' => '#000000');

    /**
     * Direction of motion (right, left, up, down).
     *
     * @var        string
     * @since      2.0.0
     * @access     public
     */
    var $direction = 'right';

    /**
     * Progress bar frame properties
     *
     * <code>
     * $frame = array(
     *    'show' => false,      # frame show (true/false)
     *    'left' => 200,        # frame position from left
     *    'top' => 100,         # frame position from top
     *    'width' => 320,       # frame width
     *    'height' => 90,       # frame height
     *    'color' => '#C0C0C0', # frame color
     *    'border-width' => 2,                                   # frame border width
     *    'border-style' => 'solid',                             # frame border style (solid,
     *                                                           # dashed, dotted, inset ...)
     *    'border-color' => '#DFDFDF #404040 #404040 #DFDFDF'    # frame border color (3dfx)
     * );
     * </code>
     *
     * @var        array
     * @since      2.0.0
     * @access     public
     * @see        setFrameAttributes()
     */
    var $frame = array('show' => false);

    /**
     * Progress bar labels properties
     *
     * <code>
     * $label = array(
     *    'name' => array(                  # label name
     *      'type' => 'text',               # label type
     *                                      # (text,button,step,percent,crossbar)
     *      'value' => '&nbsp;',            # label value
     *      'left' => ($left),              # label position from left
     *      'top' => ($top - 16),           # label position from top
     *      'width' => 0,                   # label width
     *      'height' => 0,                  # label height
     *      'align' => 'left',              # label align
     *      'background-color' => 'transparent',          # label background color
     *      'font-family' => 'Verdana, Tahoma, Arial',    # label font family
     *      'font-size' => 11,                            # label font size
     *      'font-weight' => 'normal',                    # label font weight
     *      'font-style' => 'normal',                     # label font style
     *      'color' => '#000000'                          # label font color
     * );
     * </code>
     *
     * @var        array
     * @since      2.0.0
     * @access     public
     * @see        addLabel(), setLabelAttributes()
     */
    var $label = array();

    /**
     * Constructor (ZE1)
     *
     * @since      2.0.0
     * @access     public
     */
    function HTML_Progress2_Lite($options = array(), $id = null)
    {
        $this->__construct($options, $id);
    }

    /**
     * Constructor (ZE2) Summary.
     *
     * @param      array     $options       (optional) has of style parameters
     *                                                 for the progress bar
     * @param      string    $id            (optional) progress bar unique identifier
     *
     * @return     object
     * @since      2.0.0
     * @access     protected
     */
    function __construct($options = array(), $id = null)
    {
        if (is_null($id)) {
            $this->_ident = substr(md5(microtime()), 0, 6);
        } else {
            $this->_ident = $id;
    }

        $default_options = array(
            'position' => 'absolute',
            'left' => 10,
            'top' => 25,
            'width' => 300,
            'height' => 25,
            'padding' => 0,
            'min' => 0,
            'max' => 100
        );
        $allowed_options = array_keys($default_options);

        $options = array_merge($default_options, $options);

        foreach($options as $prop => $val) {
            if (in_array($prop, $allowed_options)) {
                $this->{$prop} = $val;
            } else {
                trigger_error("option '$prop' is not allowed", E_USER_WARNING);
            }
        }
        $this->_setStep(0);

        ob_implicit_flush(1);
    }

    /**
     * Moves the progress bar in all directions (left, right, up , down).
     *
     * @param      string    $direction     fill way of the progress bar
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function setDirection($direction)
    {
        $this->direction = $direction;

        if ($this->_status != 'new') {
            $bar = ob_get_clean();
            $position = $this->_computePosition();

            $cssText  = 'left:' . $position['left'] . 'px;';
            $cssText .= 'top:' . $position['top'] . 'px;';
            $cssText .= 'width:' . $position['width'] . 'px;';
            $cssText .= 'height:' . $position['height'] . 'px;';

            $bar .= $this->_changeElementStyle('pbar', '', $cssText);

            echo $bar . PHP_EOL;
            ob_start();
        }
    }

    /**
     * Add a new label to the progress bar.
     *
     * @param      string    $type          Label type (text,button,step,percent,crossbar)
     * @param      string    $name          Label name
     * @param      string    $value         (optional) default label value
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        setLabelAttributes(), removeLabel()
     */
    function addLabel($type, $name, $value = '&nbsp;')
    {
        switch($type) {
        case 'text':
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'left' => $this->left,
                'top' => $this->top - 16,
                'width' => 0,
                'height' => 0,
                'align' => 'left',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'font-style' => 'normal',
                'color' => '#000000'
            );
            break;
        case 'button':
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'action' => '',
                'target' => 'self',
                'left' => $this->left,
                'top' => $this->top + $this->height + 10,
                'width' => 0,
                'height' => 0,
                'align' => 'center',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'font-style' => 'normal',
                'color' => '#000000'
            );
            break;
        case 'step':
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'left' => $this->left + 5,
                'top' => $this->top + 5,
                'width' => 10,
                'height' => 0,
                'align' => 'right',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'font-style' => 'normal',
                'color' => '#000000'
            );
            break;
        case 'percent':
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'left' => $this->left + $this->width - 50,
                'top' => $this->top - 16,
                'width' => 50,
                'height' => 0,
                'align' => 'right',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'font-style' => 'normal',
                'color' => '#000000'
            );
            break;
        case 'crossbar':
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'left' => $this->left + ($this->width / 2),
                'top' => $this->top - 16,
                'width' => 10,
                'height' => 0,
                'align' => 'center',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'font-style' => 'normal',
                'color' => '#000000'
            );
            break;
        }
    }

    /**
     * Removes a label to the progress bar.
     *
     * @param      string    $name          Label name
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        addLabel()
     */
    function removeLabel($name)
    {
        if (isset($this->label[$name]) && $this->label[$name]['type'] != 'button') {
            unset($this->label[$name]);
        }
    }

    /**
     * Add a new button with the progress bar.
     *
     * @param      string    $name          Button name
     * @param      string    $value         Label value
     * @param      string    $action        Action to do (see QUERY_STRING)
     * @param      string    $target        (optional) Frame target (default is self)
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        removeButton(), addLabel()
     */
    function addButton($name, $value, $action, $target = 'self')
    {
        $this->addLabel('button', $name, $value);
        $this->label[$name]['action'] = $action;
        $this->label[$name]['target'] = $target;
    }

    /**
     * Removes a button to the progress bar.
     *
     * @param      string    $name          Label name
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        addButton()
     */
    function removeButton($name)
    {
        if (isset($this->label[$name]) && $this->label[$name]['type'] == 'button') {
            unset($this->label[$name]);
        }
    }

    /**
     * Build a frame around the progress bar.
     *
     * @param      array     $attributes    (optional) hash of style parameters
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function setFrameAttributes($attributes = array())
    {
        $default = array(
            'show' => true,
            'left' => 200,
            'top' => 100,
            'width' => 320,
            'height' => 90,
            'color' => '#C0C0C0',
            'border-width' => 2,
            'border-style' => 'solid',
            'border-color' => '#DFDFDF #404040 #404040 #DFDFDF'
        );
        $allowed_options = array_keys($default);

        $options = array_merge($default, $attributes);

        foreach($options as $prop => $val) {
            if (in_array($prop, $allowed_options)) {
                $this->frame[$prop] = $val;
            } else {
                trigger_error("frame option '$prop' is not allowed", E_USER_WARNING);
            }
        }
    }

    /**
     * Defines main style of a progress bar.
     *
     * @param      array     $attributes    (optional) hash of style parameters
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function setBarAttributes($attributes = array())
    {
        $cssText = $cssText_border = '';
        $bar  = ob_get_clean();

        foreach ($attributes as $attrName => $attrVal) {
            if ($attrName == 'border-style') {
                $this->border['style'] = $attrVal;
                $cssText_border .= 'borderStyle:' . $this->border['style'] . ';';

            } elseif ($attrName == 'border-width') {
                $this->border['width'] = $attrVal;
                $cssText_border .= 'borderWidth:' . $this->border['width'] . 'px;';

            } elseif ($attrName == 'border-color') {
                $this->border['color'] = $attrVal;
                $cssText_border .= 'borderColor:' . $this->border['color'] . ';';

            } elseif ($attrName == 'background-color') {
                $this->background_color = $attrVal;
                $cssText_border .= 'backgroundColor:' . $this->background_color . ';';

            } elseif ($attrName == 'color') {
                $this->foreground_color = $attrVal;
                $cssText .= 'backgroundColor:' . $this->foreground_color . ';';
            }
        }

        if ($this->_status != 'new') {
            if (!empty($cssText_border)) {
                $bar .= $this->_changeElementStyle('pbrd', '', $cssText_border);
            }
            if (!empty($cssText)) {
                $bar .= $this->_changeElementStyle('pbar', '', $cssText);
            }
        }

        echo $bar . PHP_EOL;
        ob_start();
    }

    /**
     * Defines style of a progress bar label.
     *
     * @param      string    $name          Label identifier
     * @param      array     $attributes    (optional) hash of style parameters
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        addLabel()
     */
    function setLabelAttributes($name, $attributes = array())
    {
        $fontfamily = $fontweight = '';

        $bar = ob_get_clean();

        foreach ($attributes as $attrName => $attrVal) {
            if ($attrName == 'color') {
                $this->label[$name]['color'] = $attrVal;
                if ($this->_status != 'new') {
                    $cssText = 'color:' . $this->label[$name]['color'] . ';';
                    $bar .= $this->_changeElementStyle('plbl', $name, $cssText);
                }

            } elseif ($attrName == 'background-color') {
                $this->label[$name]['background-color'] = $attrVal;
                if ($this->_status != 'new') {
                    $cssText = 'backgroundColor:' . $this->label[$name]['background-color'] . ';';
                    $bar .= $this->_changeElementStyle('plbl', $name, $cssText);
                }

            } elseif ($attrName == 'font-size') {
                $this->label[$name]['font-size'] = $font = $attrVal;
            } elseif ($attrName == 'font-family') {
                $this->label[$name]['font-family'] = $font = $attrVal;
            } elseif ($attrName == 'font-weight') {
                $this->label[$name]['font-weight'] = $font = $attrVal;
            } elseif ($attrName == 'font-style') {
                $this->label[$name]['font-style'] = $font = $attrVal;

            } elseif ($attrName == 'value') {
                $this->label[$name]['value'] = $attrVal;
                if ($this->_status != 'new') {
                    $bar .= $this->_changeLabelText($name, $this->label[$name]['value']);
                }

            } elseif ($attrName == 'left') {
                $this->label[$name]['left'] = $attrVal;
            } elseif ($attrName == 'top') {
                $this->label[$name]['top'] = $attrVal;
            } elseif ($attrName == 'width') {
                $this->label[$name]['width'] = $attrVal;
            } elseif ($attrName == 'height') {
                $this->label[$name]['height'] = $attrVal;
            } elseif ($attrName == 'align') {
                $this->label[$name]['align'] = $attrVal;
            }
        }
        if (isset($font)) {
            if ($this->_status != 'new') {
                $cssText = 'fontSize:' . $this->label[$name]['font-size'] . 'px;'
                         . 'fontFamily:' . $this->label[$name]['font-family'] . ';'
                         . 'fontWeight:' . $this->label[$name]['font-weight'] . ';'
                         . 'fontStyle:' . $this->label[$name]['font-style'] . ';';

                $bar .= $this->_changeElementStyle('plbl', $name, $cssText);
            }
        }

        if ($this->_status != 'new') {
            $cssText = 'top:' . $this->label[$name]['top'] . 'px;'
                     . 'left:' . $this->label[$name]['left'] . 'px;';

            if($this->label[$name]['width'] > 0) {
                $cssText .= 'width:' . $this->label[$name]['width'] . 'px;';
            }
            if($this->label[$name]['height'] > 0) {
                $cssText .= 'height:' . $this->label[$name]['height'] . 'px;';
            }
            $cssText .= 'textAlign:' . $this->label[$name]['align'] .';';

            $bar .= $this->_changeElementStyle('plbl', $name, $cssText);
        }

        echo $bar . PHP_EOL;
        ob_start();
    }

    /**
     * Changes new step value of the progress bar.
     *
     * @param      integer   $step          new step value
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function moveStep($step)
    {
        $this->_setStep($step);
        $position = $this->_computePosition();
        $bar = ob_get_clean();

        $cssText = '';
        if ($this->direction == 'right' || $this->direction == 'left') {
            if ($this->direction == 'left') {
                $cssText .= 'left:' . $position['left'] . 'px;';
            }
            $cssText .= 'width:' . $position['width'] . 'px;';
        }
        if ($this->direction == 'up' || $this->direction == 'down') {
            if ($this->direction == 'up') {
                $cssText .= 'top:' . $position['top'] . 'px;';
            }
            $cssText .= 'height:' . $position['height'] . 'px;';
        }
        $bar .= $this->_changeElementStyle('pbar', '', $cssText);

        foreach($this->label as $name => $data) {
            switch($data['type']) {
            case 'step':
                $bar .= $this->_changeLabelText($name, $this->_step.'/'.$this->max);
                break;
            case 'percent':
                $bar .= $this->_changeLabelText($name, $this->_computePercent() . '%');
                break;
            case 'crossbar':
                $bar .= $this->_changeCrossItem($name);
                break;
            }
        }

        echo $bar . PHP_EOL;
        ob_start();
    }

    /**
     * Changes value of the progress bar to the next step.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        moveStep()
     */
    function moveNext()
    {
        $this->moveStep($this->_step + 1);
    }

    /**
     * Changes value of the progress bar to the minimum step.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        moveStep()
     */
    function moveMin()
    {
        $this->moveStep($this->min);
    }

    /**
     * Returns the progress bar structure as HTML.
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     * @see        display()
     */
    function toHtml()
    {
        $html = '';
        $js = '';

        //$this->_setStep($this->_step);
        $position = $this->_computePosition();

        $style_brd = 'position:absolute;'
                   . 'top:' . $this->top . 'px;'
                   . 'left:' . $this->left . 'px;'
                   . 'width:' . $this->width . 'px;'
                   . 'height:' . $this->height . 'px;'
                   . 'background-color:' . $this->background_color . ';';

        if ($this->border['width'] > 0 ) {
            $style_brd .= 'border-width:' . $this->border['width'] . 'px;'
                       .  'border-style:' . $this->border['style'] . ';'
                       .  'border-color:' . $this->border['color'] . ';';
        }
        $style_bar = 'position:absolute;'
                   . 'top:' . $position['top'] . 'px;'
                   . 'left:' . $position['left'] . 'px;'
                   . 'width:' . $position['width'] . 'px;'
                   . 'height:' . $position['height'] . 'px;'
                   . 'background-color:' . $this->foreground_color . ';';

        if ($this->frame['show']) {
            if ($this->frame['border-width'] > 0) {
                $border = 'border-width:' . $this->frame['border-width'] . 'px;'
                        . 'border-style:' . $this->frame['border-style'] . ';'
                        . 'border-color:' . $this->frame['border-color'] . ';';
            }
            if ($this->position == 'relative') {
                $html = '<div id="tfrm' . $this->_ident . '"'
                      . ' style="position:relative;top:0;left:0;">'
                      . PHP_EOL;
                $top = $left = 0;
            } else {
                $top = $this->frame['top'];
                $left = $this->frame['left'];
            }
            $html .= '<div id="pfrm' . $this->_ident .'" style="'
                  .  'position:absolute;'
                  .  'top:' . $top . 'px;'
                  .  'left:' . $left . 'px;'
                  .  'width:' . $this->frame['width'] . 'px;'
                  .  'height:' . $this->frame['height'] . 'px;'
                  .  $border
                  .  'background-color:' . $this->frame['color'] . ';">'
                  .  PHP_EOL;
        } else {
            if ($this->position == 'relative') {
                $html = '<div id="tfrm' . $this->_ident . '"'
                      . ' style="position:relative;top:0;left:0;">'
                      . PHP_EOL;
            }
        }

        $html .= '<div id="pbrd'.$this->_ident.'" style="'.$style_brd.'">'.PHP_EOL;
        $html .= '<div id="pbar'.$this->_ident.'" style="'.$style_bar.'"></div></div>'.PHP_EOL;

        foreach ($this->label as $name => $data) {
            $style_lbl = 'position:absolute;'
                       . 'top:' . $data['top'] . 'px;'
                       . 'left:' . $data['left'] . 'px;'
                       . 'text-align:' . $data['align'] . ';';

            if ($data['width'] > 0) {
                $style_lbl .= 'width:' . $data['width'] . 'px;';
            }
            if ($data['height'] > 0) {
                $style_lbl .= 'height:' . $data['height'] . 'px;';
            }
            $style_lbl .= 'color:' . $data['color'] .';'
                       .  'font-size:' . $data['font-size'] . 'px;'
                       .  'font-family:' . $data['font-family'] . ';'
                       .  'font-weight:' . $data['font-weight'] . ';';

            if ($data['background-color'] != '') {
                $style_lbl .= 'background-color:' . $data['background-color'] . ';';
            }

            switch($data['type']) {
            case 'button':
                $html .= '<div><input id="plbl' . $name . $this->_ident
                      .  '" type="button" value="' . $data['value']
                      .  '" style="' . $style_lbl
                      .  '" onclick="' . $data['target']
                      .  '.location.href=\'' . $data['action'] . '\'" /></div>'
                      .  PHP_EOL;
                break;
            case 'step':
                $html .= '<div id="plbl' . $name . $this->_ident
                      .  '" style="' . $style_lbl . '">'
                      .  $this->_step
                      .  '</div>'
                      .  PHP_EOL;
                break;
            case 'percent':
                $html .= '<div id="plbl' . $name . $this->_ident
                      .  '" style="' . $style_lbl . '">'
                      .  $this->_computePercent() . '%'
                      .  '</div>'
                      .  PHP_EOL;
                break;
            case 'text':
            case 'crossbar':
                $html .= '<div id="plbl' . $name . $this->_ident
                      .  '" style="' . $style_lbl . '">'
                      .  $data['value']
                      .  '</div>'
                      .  PHP_EOL;
                if ($data['type'] == 'crossbar') {
                    $js .= 'function setRotaryCross'.$name.$this->_ident.'() {'.PHP_EOL;
                    $js .= ' cross = document.getElementById("plbl'.$name.$this->_ident.'").firstChild.nodeValue;'.PHP_EOL;
                    $js .= ' switch(cross) {'.PHP_EOL;
                    $js .= '  case "--": cross = "\\\\"; break;'.PHP_EOL;
                    $js .= '  case "\\\\": cross = "|"; break;'.PHP_EOL;
                    $js .= '  case "|": cross = "/"; break;'.PHP_EOL;
                    $js .= '  default: cross = "--"; break;'.PHP_EOL;
                    $js .= ' }'.PHP_EOL;
                    $js .= ' document.getElementById("plbl'.$name.$this->_ident.'").firstChild.nodeValue = cross;'.PHP_EOL;
                    $js .= '}'.PHP_EOL;
                }
                break;
            }
        }

        if (count($this->label) > 0) {
            $js .= 'function setLabelText'.$this->_ident.'(name,text) {'.PHP_EOL;
            $js .= ' name = "plbl" + name + "'.$this->_ident.'";'.PHP_EOL;
            $js .= ' document.getElementById(name).firstChild.nodeValue=text;'.PHP_EOL;
            $js .= '}'.PHP_EOL;
        }

        $js .= 'function setElementStyle'.$this->_ident.'(prefix,name,styles) {'.PHP_EOL;
        $js .= ' name = prefix + name + "'.$this->_ident.'";'.PHP_EOL;
        $js .= ' styles = styles.split(";");'.PHP_EOL;
        $js .= ' styles.pop();'.PHP_EOL;
        $js .= ' for(var i=0; i<styles.length; i++)'.PHP_EOL;
        $js .= ' {'.PHP_EOL;
        $js .= '   s = styles[i].split(":");'.PHP_EOL;
        $js .= '   c = "document.getElementById(name).style."+s[0]+"=\""+s[1]+"\"";'.PHP_EOL;
        $js .= '   eval(c);'.PHP_EOL;
        $js .= ' }'.PHP_EOL;
        $js .= '}'.PHP_EOL;

        if ($this->frame['show']) {
            $html .= '</div>'.PHP_EOL;
        }
        if ($this->position == 'relative') {
            $html .= '</div>'.PHP_EOL;
        }

        $html .= '<script type="text/JavaScript">'.PHP_EOL;
        $html .= $js;
        $html .= '</script>'.PHP_EOL;

        return $html;
    }

    /**
     * Show the renders of the progress bar.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        toHtml()
     */
    function display()
    {
        $this->_status = 'show';
        echo $this->toHtml();
    }

    /**
     * Hides the progress bar.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function hide()
    {
        if ($this->_status=='show') {
            $this->_status = 'hide';
            $this->_hide();
        }
    }

    /**
     * Shows a progress bar hidden.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function show()
    {
        if ($this->_status=='hide') {
            $this->_status = 'show';
            $this->_hide();
        }
    }

    /**
     * Show or Hide a progress bar depending of its current status.
     *
     * @return     void
     * @since      2.0.0
     * @access     private
     */
    function _hide()
    {
        if ($this->_status == 'hide') {
            $cssText = 'visibility:hidden;';
        } else {
            $cssText = 'visibility:visible;';
    }
        $bar  = ob_get_clean();

        $bar .= $this->_changeElementStyle('pbrd', '', $cssText);
        $bar .= $this->_changeElementStyle('pbar', '', $cssText);

        if ($this->frame['show']) {
            $bar .= $this->_changeElementStyle('pfrm', '', $cssText);
        }
        foreach($this->label as $name => $data) {
            $bar .= $this->_changeElementStyle('plbl', $name, $cssText);
        }

        echo $bar . PHP_EOL;
        ob_start();
    }

    /**
     * Calculate the current percent of progress.
     *
     * @return     integer
     * @since      2.0.0
     * @access     private
     */
    function _computePercent()
    {
        $percent = round(($this->_step - $this->min) / ($this->max - $this->min) * 100);
        if ($percent > 100) {
            $percent = 100;
        }
        return $percent;
    }

    /**
     * Calculate the new position in pixel of the progress bar value.
     *
     * @return     void
     * @since      2.0.0
     * @access     private
     */
    function _computePosition()
    {
        switch ($this->direction) {
        case 'right':
        case 'left':
            $bar = $this->width;
            break;
        case 'down':
        case 'up':
            $bar = $this->height;
            break;
        }
        $pixel = round(  ($this->_step - $this->min) * ($bar - ($this->padding * 2))
                       / ($this->max - $this->min) );
        if ($this->_step <= $this->min) {
            $pixel = 0;
        }
        if ($this->_step >= $this->max) {
            $pixel = $bar - ($this->padding * 2);
        }

        switch ($this->direction) {
        case 'right':
            $position['left'] = $this->padding;
            $position['top'] = $this->padding;
            $position['width'] = $pixel;
            $position['height'] = $this->height - ($this->padding * 2);
            break;
        case 'left':
            $position['left'] = $this->width - $this->padding - $pixel;
            $position['top'] = $this->padding;
            $position['width'] = $pixel;
            $position['height'] = $this->height - ($this->padding * 2);
            break;
        case 'down':
            $position['left'] = $this->padding;
            $position['top'] = $this->padding;
            $position['width'] = $this->width - ($this->padding * 2);
            $position['height'] = $pixel;
            break;
        case 'up':
            $position['left'] = $this->padding;
            $position['top'] = $this->height - $this->padding - $pixel;
            $position['width'] = $this->width - ($this->padding * 2);
            $position['height'] = $pixel;
            break;
        }
        return $position;
    }

    /**
     * Sets the new step value of the progress bar.
     *
     * @param      integer   $step          new step value
     *
     * @return     void
     * @since      2.0.0
     * @access     private
     */
    function _setStep($step)
    {
        if($step > $this->max) {
            $step = $this->max;
        }
        if($step < $this->min) {
            $step = $this->min;
        }
        $this->_step = $step;
    }

    /**
     * Sends a DOM command (emulate cssText attribute) through a javascript function
     * to change styles of a progress bar's element.
     *
     * @param      string    $prefix        prefix identifier of the element
     * @param      string    $element       element name (label id.)
     * @param      string    $styles        styles of a DOM element
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _changeElementStyle($prefix, $element, $styles)
    {
        $cmd = '<script type="text/JavaScript">'
             . 'setElementStyle' . $this->_ident
             . '("' . $prefix . '","' . $element . '","' . $styles . '");'
             . '</script>';

        return $cmd;
    }

    /**
     * Sends a DOM command (emulate firstChild.nodeValue) through a javascript function
     * to change label value of a progress bar's element.
     *
     * @param      string    $element       element name (label id.)
     * @param      string    $text          element value (label content)
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _changeLabelText($element, $text)
    {
        $cmd = '<script type="text/JavaScript">'
             . 'setLabelText' . $this->_ident
             . '("' . $element . '","' . $text . '");'
             . '</script>';

        return $cmd;
    }

    /**
     * Sends a DOM command through a javascript function
     * to change the next frame animation of a cross bar's element.
     *
     * @param      string    $element       element name (cross id.)
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _changeCrossItem($element)
    {
        $cmd = '<script type="text/JavaScript">'
             . 'setRotaryCross' . $element . $this->_ident . '();'
             . '</script>';

        return $cmd;
    }
}
?>