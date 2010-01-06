<?php
/**
 * TagCloud.php
 *
 * TagCloud.php contains class HTML_TagCloud.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  HTML
 * @package   HTML_TagCloud
 * @author    Shoma Suzuki <shoma@catbot.net>
 * @author    Bastian Onken <bastian.onken@gmx.net>
 * @copyright 2008 Bastian Onken
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/HTML_TagCloud
 * @since     File available since Release 0.1.0
 */

// {{{ class HTML_TagCloud

/**
 * HTML Tag Cloud
 *
 * HTML_TagCloud enables you to generate a "tag cloud" in HTML.
 *
 * @category  HTML
 * @package   HTML_TagCloud
 * @author    Shoma Suzuki <shoma@catbot.net>
 * @author    Bastian Onken <bastian.onken@gmx.net>
 * @copyright 2008 Bastian Onken
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 0.2.3
 * @link      http://pear.php.net/package/HTML_TagCloud
 * @link      http://search.cpan.org/~lyokato/HTML-TagCloud-Extended-0.10/lib/HTML/TagCloud/Extended.pm
 * @since     Class available since Release 0.1.0
 */
class HTML_TagCloud
{
    // {{{ properties

    /**
     * Defines the base font size
     *
     * @var int
     */
    protected $baseFontSize = 24;

    /**
     * Limits the range of generated font-sizes
     *
     * @var int
     */
    protected $fontSizeRange = 12;

    /**
     * Name of CSS class the TagCloud will get (assigned to the div the cloud
     * will get wrapped into)
     *
     * @var string
     */
    protected $cssClass = 'tagcloud';

    /**
     * Stores the font-size unit, potentional values are: mm,cm,in,pt,pc,px,em
     *
     * @var string
     */
    protected $sizeSuffix = 'px';

    /**
     * Defines colors of the different levels to that tags will be assigned to
     * (based on tag's age)
     *
     * @var array
     */
    protected $epocLevel = array(
        array(
            'earliest' => array(
                'link'    => 'cccccc',
                'visited' => 'cccccc',
                'hover'   => 'cccccc',
                'active'  => 'cccccc',
            ),
        ),
        array(
            'earlier' => array(
                'link'    => '9999cc',
                'visited' => '9999cc',
                'hover'   => '9999cc',
                'active'  => '9999cc',
            ),
        ),
        array(
            'later' => array(
                'link'    => '9999ff',
                'visited' => '9999ff',
                'hover'   => '9999ff',
                'active'  => '9999ff',
            ),
        ),
        array(
            'latest' => array(
                'link'    => '0000ff',
                'visited' => '0000ff',
                'hover'   => '0000ff',
                'active'  => '0000ff',
            ),
        ),
    );

    /**
     * Stores the TagCloud elements
     *
     * @var array
     */
    private $_elements = array();

    /**
     * @var int
     */
    private $_max = 0;

    /**
     * @var int
     */
    private $_min = 0;

    /**
     * @var int
     */
    private $_maxEpoc;

    /**
     * @var int
     */
    private $_minEpoc;

    /**
     * @var float
     */
    private $_factor = 1;

    /**
     * @var float
     */
    private $_epocFactor = 1;

    /**
     * @var int
     */
    private $_minFontSize;

    /**
     * @var int
     */
    private $_maxFontSize;

    /**
     * Stores an unique TagCloud-ID, necessary for multiple TagClouds on one
     * HTML page
     *
     * @var string
     */
    private $_uid;

    // }}}
    // {{{ public function __construct()

    /**
     * Class constructor
     *
     * @param int    $baseFontSize  base font size of output tag (option)
     * @param int    $fontSizeRange font size range
     * @param string $latestColor   color of latest tag (usually dark)
     * @param string $earliestColor color of earliest tag (usually light)
     * @param int    $thresholds    number of timelines to set up
     *
     * @since Method available since Release 0.1.0
     */
    public function __construct($baseFontSize = null, $fontSizeRange = null,
                                $latestColor = null, $earliestColor = null,
                                $thresholds = 4)
    {
        // to be able to set up multiple tag clouds in one page we need to set
        //  up a unique id that will prefix the css names later
        $this->_uid = 'tagcloud'.uniqid();
        // if $baseFontSize was given, set to value, otherwise keep the original
        //  value of HTML_TagCloud::baseFontSize
        if (!is_null($baseFontSize)) {
            $this->baseFontSize = $baseFontSize;
        }
        // if $fontSizeRange was given, set to value, otherwise keep the
        //  original value of HTML_TagCloud::fontSizeRange
        if (!is_null($fontSizeRange)) {
            $this->fontSizeRange = $fontSizeRange;
        }
        // make sure that we are in a positive font range
        if ($this->baseFontSize - $this->fontSizeRange > 0) {
            $this->_minFontSize = $this->baseFontSize - $this->fontSizeRange;
        } else {
            $this->_minFontSize = 0;
        }
        $this->_maxFontSize = $this->baseFontSize + $this->fontSizeRange;
        // override default epocLevel settings
        if (!is_null($latestColor) && !is_null($earliestColor) && $thresholds > 0) {
            $this->epocLevel = $this->_generateEpocLevel($latestColor,
                                                         $earliestColor,
                                                         $thresholds);
        }
    }

    // }}}
    // {{{ public function getUid()

    /**
     * returns the unique id of the tag cloud
     *
     * @return string unique id
     *
     * @since Method available since Release 0.2.0
     */
    public function getUid()
    {
        return $this->_uid;
    }

    // }}}
    // {{{ public function getElementCount()

    /**
     * returns the number of elements in the tag cloud
     *
     * @return integer number of elements in the tag cloud
     *
     * @since Method available since Release 0.2.2
     */
    public function getElementCount()
    {
        return count($this->_elements);
    }

    // }}}
    // {{{ public function addElement()

    /**
     * add a Tag Element to build Tag Cloud
     *
     * @param string $name      tagname
     * @param string $url       URL to which the tag leads to
     * @param int    $count     number of occurrences of this tag
     * @param int    $timestamp unixtimestamp
     *
     * @return void
     *
     * @since Method available since Release 0.1.0
     */
    public function addElement($name, $url = '', $count = 0, $timestamp = null)
    {
        $i                                = count($this->_elements);
        $this->_elements[$i]['name']      = $name;
        $this->_elements[$i]['url']       = $url;
        $this->_elements[$i]['count']     = $count;
        $this->_elements[$i]['timestamp'] = $timestamp == null ? time() : $timestamp;
    }

    // }}}
    // {{{ public function addElements()

    /**
     * add a Tag Element to build Tag Cloud
     *
     * @param array $tags Associative array to $this->_elements
     *
     * @return  void
     *
     * @since Method available since Release 0.1.0
     */
    public function addElements($tags)
    {
        $this->_elements = array_merge($this->_elements, $tags);
    }

    // }}}
    // {{{ public function clearElements()

    /**
     * clear Tag Elements
     *
     * @return void
     *
     * @since Method available since Release 0.1.0
     */
    public function clearElements()
    {
        $this->_elements = array();
    }

    // }}}
    // {{{ public function buildAll()

    /**
     * build HTML and CSS at once.
     *
     * @param array $param parameters that influence the HTML output
     *
     * @return string HTML and CSS
     *
     * @since Method available since Release 0.1.0
     */
    public function buildAll($param = array())
    {
        $html  = '<style type="text/css">'."\n";
        $html .= $this->buildCSS().'</style>'."\n";
        $html .= $this->buildHTML($param);
        return $html;
    }

    // }}}
    // {{{ public function html_and_css()

    /**
     * Alias to buildAll. Compatibilities for Perl Module.
     *
     * @param array $param 'limit' => int limit of generation tag num.
     *
     * @return string HTML and CSS
     *
     * @see HTML_TagCloud::_buildAll
     * @since Method available since Release 0.1.0
     * @deprecated Method deprecated in Release 0.1.3
     * @legacy
     */
    public function html_and_css($param = array())
    {
        return $this->buildAll($param);
    }

    // }}}
    // {{{ public function buildHTML()

    /**
     * build HTML part
     *
     * @param array $param 'limit' => int limit of generation tag num.
     *
     * @return string HTML
     *
     * @since Method available since Release 0.1.0
     */
    public function buildHTML($param = array())
    {
        $htmltags = $this->_buidHTMLTags($param);
        return $this->_wrapDiv($htmltags);
    }

    // }}}
    // {{{ public function buildCSS()

    /**
     * build CSS part
     *
     * @return string base CSS
     *
     * @since Method available since Release 0.1.0
     */
    public function buildCSS()
    {
        $css = '';
        foreach ($this->epocLevel as $item) {
            foreach ($item as $epocName => $colors) {
                foreach ($colors as $attr => $color) {
                    $css .= 'a.'.$this->_uid.'_'.$epocName.':'.$attr.' {'
                           .'text-decoration: none; color: #'.$color.';}'."\n";
                }
            }
        }
        return $css;
    }

    // }}}
    // {{{ private function _buidHTMLTags()

    /**
     * calc Tag level and create whole HTML of each Tags
     *
     * @param array $param limit of Tag Number
     *
     * @return string HTML
     *
     * @since Method available since Release 0.1.0
     */
    private function _buidHTMLTags($param)
    {
        // get total number of tags
        $total = count($this->_elements);
        if ($total == 0) {
            // no tag elements, return with "not enough data"
            return '<p>not enough data</p>'."\n";
        } elseif ($total == 1) {
            // only 1 element was set, no need to process sizes or colors, so
            // just create html with standard setup and return
            $tag  = $this->_elements[0];
            $type = $this->_uid.'_'
                   .key($this->epocLevel[count($this->epocLevel) - 1]);
            return $this->createHTMLTag($tag, $type, $this->baseFontSize);
        }
        // okay, there are more elements, let's calculate their environment
        // at first, check if there is a limit of returned elements set up
        $limit = array_key_exists('limit', $param) ? $param['limit'] : 0;
        // sort elements, consider limit if available ("0" will disable limit)
        $this->_sortTags($limit);
        // get maximum and minimum count values
        //  (values will be stored in $this->_min and $this->_max
        $this->_calcMumCount();
        // get maximum and minimum timestamp values
        //  (values will be stored in $this->_minEpoc and $this->_maxEpoc
        $this->_calcMumEpoc();
        // get font size delta
        $range = $this->_maxFontSize - $this->_minFontSize;
        // calculate the factor for building the font size deltas
        if ($this->_max != $this->_min) {
            $this->_factor = $range / (sqrt($this->_max) - sqrt($this->_min));
        } else {
            $this->_factor = 1;
        }
        // calculate the factor for building the color deltas
        if ($this->_maxEpoc != $this->_minEpoc) {
            $this->_epocFactor = count($this->epocLevel) /
                                 (sqrt($this->_maxEpoc) - sqrt($this->_minEpoc));
        } else {
            $this->_epocFactor = 1;
        }
        // build html
        $rtn = array();
        foreach ($this->_elements as $tag) {
            $count   = isset($tag['count']) ? $tag['count'] : 0;
            $countLv = $this->_getCountLevel($count);
            if (!isset($tag['timestamp']) || empty($tag['timestamp'])) {
                $epocLv = count($this->epocLevel) - 1;
            } else {
                $epocLv = $this->_getEpocLevel($tag['timestamp']);
            }
            $colorType = $this->epocLevel[$epocLv];
            $type      = $this->_uid.'_'.key($colorType);
            $fontSize  = $this->_minFontSize + $countLv;
            $rtn[]     = $this->createHTMLTag($tag, $type, $fontSize);
        }
        return implode('', $rtn);
    }

    // }}}
    // {{{ protected function _createHTMLTag()

    /**
     * create a Element of HTML part
     *
     * deprecated due to wrong function naming: one leading underscore must only
     * be used in private context.
     *
     * @param array  $tag      tagname
     * @param string $type     css class of time line param
     * @param float  $fontSize size of the font for this tag
     *
     * @return string a Element of Tag HTML
     *
     * @see HTML_TagCloud::createHTMLTag()
     * @since Method available since Release 0.1.0
     * @deprecated Method deprecated in Release 0.1.3
     * @legacy
     */
    protected function _createHTMLTag($tag, $type, $fontSize)
    {
        return $this->createHTMLTag($tag, $type, $fontSize);
    }

    // }}}
    // {{{ protected function createHTMLTag()

    /**
     * create a Element of HTML part
     *
     * @param array  $tag      tagname
     * @param string $type     css class of time line param
     * @param float  $fontSize size of the font for this tag
     *
     * @return string a Element of Tag HTML
     *
     * @since Method available since Release 0.1.3
     */
    protected function createHTMLTag($tag, $type, $fontSize)
    {
        return '<a href="'.(!empty($tag['url']) ? $tag['url'] : '').'"'
               .' style="font-size:'.$fontSize.$this->sizeSuffix.';"'
               .' class="tagcloudElement '.$type.'">'
               .htmlspecialchars($tag['name'])
               .'</a> &nbsp;'."\n";
    }

    // }}}
    // {{{ protected function generateEpocLevel()

    /**
     * build the epocLevel Array automatically by calculating an array of colors
     *
     * @param string $latestColor   color of latest epocLevel (usually dark)
     * @param string $earliestColor color of earliest epocLevel (usually light)
     * @param int    $thresholds    number of levels to generate colors for
     *
     * @return array epocLevel
     *
     * @since Method available since Release 0.2.0
     */
    private function _generateEpocLevel($latestColor, $earliestColor, $thresholds)
    {
        include_once 'Image/Color.php';
        $imageColor = new Image_Color();
        $imageColor->setWebSafe(false);
        $imageColor->setColors($latestColor, $earliestColor);
        $epocLevel = array();
        foreach ($imageColor->getRange($thresholds) as $key => $color) {
            $epocLevel[]['epocLevel'.$key] = array(
                'link'    => $color,
                'visited' => $color
            );
        }
        return array_reverse($epocLevel);
    }

    // }}}
    // {{{ private function _sortTags()

    /**
     * sort tags by name
     *
     * @param int $limit limit element number of create TagCloud
     *
     * @return array
     *
     * @since Method available since Release 0.1.0
     */
    private function _sortTags($limit = 0)
    {
        if ($limit != 0) {
            usort($this->_elements, array($this, "_cmpElementsCountTimestamp"));
            $this->_elements = array_splice($this->_elements, 0, $limit);
            usort($this->_elements, array($this, "_cmpElementsName"));
        } else {
            usort($this->_elements, array($this, "_cmpElementsName"));
        }
    }

    // }}}
    // {{{ private function _cmpElementsName()

    /**
     * callback for usort(), considers string value "name" of a tag element
     *
     * @param array $a first element to compare
     * @param array $b second element to compare
     *
     * @return int (bool)
     *
     * @since Method available since Release 0.1.0
     */
    private function _cmpElementsName($a, $b)
    {
        if ($a['name'] == $b['name']) {
            return 0;
        }
        return ($a['name'] < $b['name']) ? -1 : 1;
    }

    // }}}
    // {{{ private function _cmpElementsCountTimestamp($a, $b)

    /**
     * callback for usort(), considers count and if count values are equal it
     *  considers timestamp as well.
     *
     * @param array $a first element to compare
     * @param array $b second element to compare
     *
     * @return int (bool)
     *
     * @since Method available since Release 0.2.1
     */
    private function _cmpElementsCountTimestamp($a, $b)
    {
        if ($a['count'] == $b['count']) {
            if ($a['timestamp'] == $b['timestamp']) {
                return 0;
            } else {
                return ($a['timestamp'] > $b['timestamp']) ? -1 : 1;
            }
        }
        return ($a['count'] > $b['count']) ? -1 : 1;
    }

    // }}}
    // {{{ private function _calcMumCount()

    /**
     * calc max and min tag count values
     *
     * @return void
     *
     * @since Method available since Release 0.1.0
     */
    private function _calcMumCount()
    {
        $array = array();
        foreach ($this->_elements as $item) {
            if (isset($item['count'])) {
                $array[] = (int)$item['count'];
            } else {
                $array[] = 0;
            }
        }
        $this->_min = min($array);
        $this->_max = max($array);
    }

    // }}}
    // {{{ private function _calcMumEpoc()

    /**
     * calc max and min timestamp
     *
     * @return void
     *
     * @since Method available since Release 0.1.0
     */
    private function _calcMumEpoc()
    {
        $array = array();
        foreach ($this->_elements as $item) {
            if (isset($item['timestamp'])) {
                $array[] = (int)$item['timestamp'];
            } else {
                $array[] = time();
            }
        }
        $this->_minEpoc = min($array);
        $this->_maxEpoc = max($array);
    }

    // }}}
    // {{{ private function _getCountLevel()

    /**
     * calc Tag Level of size
     *
     * @param int $count number of occurrences of tag to analyze
     *
     * @return int level
     *
     * @since Method available since Release 0.1.0
     */
    private function _getCountLevel($count = 0)
    {
        return (int)(sqrt($count) - sqrt($this->_min) ) * $this->_factor;
    }

    // }}}
    // {{{ private function _getEpocLevel()

    /**
     * calc timeline level of Tag
     *
     * @param int $timestamp timestamp of tag to analyze
     *
     * @return int level of timeline
     *
     * @since Method available since Release 0.1.0
     */
    private function _getEpocLevel($timestamp = 0)
    {
        return (int)(sqrt($timestamp) - sqrt($this->_minEpoc)) * $this->_epocFactor;
    }

    // }}}
    // {{{ private function _wrapDiv()

    /**
     * wrap div tag
     *
     * @param string $html HTML to wrap into a div element
     *
     * @return string HTML wrapped into a div set up with $this::cssClass
     *
     * @since Method available since Release 0.1.0
     */
    private function _wrapDiv($html)
    {
        return $html == '' ? '' : '<div class="'.$this->cssClass.' '.$this->_uid.'">'
                                  ."\n".$html.'</div>'."\n";
    }

    // }}}
}

// }}}

/*
 * vim: set expandtab tabstop=4 shiftwidth=4
 * vim600: foldmethod=marker
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
?>