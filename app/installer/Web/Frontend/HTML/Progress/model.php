<?php
/**
 * The HTML_Progress_Model class provides an easy way to set look and feel
 * of a progress bar with external config file.
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
 */

require_once 'Config.php';

/**
 * The HTML_Progress_Model class provides an easy way to set look and feel
 * of a progress bar with external config file.
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
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_Progress
 */

class HTML_Progress_Model extends HTML_Progress_UI
{
    /**
     * Package name used by PEAR_ErrorStack functions
     *
     * @var        string
     * @since      1.0
     * @access     private
     */
    var $_package;


    /**
     * The progress bar's UI extended model class constructor
     *
     * @param      string    $file          file name of model properties
     * @param      string    $type          type of external ressource (phpArray, iniFile, XML ...)
     *
     * @since      1.0
     * @access     public
     * @throws     HTML_PROGRESS_ERROR_INVALID_INPUT
     */
    function HTML_Progress_Model($file, $type)
    {
        $this->_package = 'HTML_Progress';

        if (!file_exists($file)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$file',
                      'was' => $file,
                      'expected' => 'file exists',
                      'paramnum' => 1));
        }

        $conf = new Config();

        if (!$conf->isConfigTypeRegistered($type)) {
            return HTML_Progress::raiseError(HTML_PROGRESS_ERROR_INVALID_INPUT, 'error',
                array('var' => '$type',
                      'was' => $type,
                      'expected' => implode (" | ", array_keys($GLOBALS['CONFIG_TYPES'])),
                      'paramnum' => 2));
        }

        $data = $conf->parseConfig($file, $type);

        $structure = $data->toArray(false);
        $this->_progress =& $structure['root'];

        if (is_array($this->_progress['cell']['font-family'])) {
            $this->_progress['cell']['font-family'] = implode(",", $this->_progress['cell']['font-family']);
        }
        if (is_array($this->_progress['string']['font-family'])) {
            $this->_progress['string']['font-family'] = implode(",", $this->_progress['string']['font-family']);
        }
        $this->_orientation = $this->_progress['orientation']['shape'];
        $this->_fillWay = $this->_progress['orientation']['fillway'];

        if (isset($this->_progress['script']['file'])) {
            $this->_script = $this->_progress['script']['file'];
        } else {
            $this->_script = null;
        }

        if (isset($this->_progress['cell']['count'])) {
            $this->_cellCount = $this->_progress['cell']['count'];
        } else {
            $this->_cellCount = 10;
        }

        $this->_updateProgressSize();
    }
}

?>