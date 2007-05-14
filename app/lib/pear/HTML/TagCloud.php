<?php
/**
 * HTML Tag Cloud 
 *
 * HTML_TagCloud enables you to generate a "tag cloud" in HTML.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    HTML_TagCloud
 * @author     Shoma Suzuki <shoma@catbot.net> 
 * @copyright  2006 Shoma Suzuki
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @see        http://search.cpan.org/~lyokato/HTML-TagCloud-Extended-0.10/lib/HTML/TagCloud/Extended.pm
 *
 */
// {{{ class HTML_TagCloud
class HTML_TagCloud {
    // {{{ class vars

    /**
     * @var    int
     * @access protected
     */
    protected $baseFontSize = 24;
    /**
     * @var    int
     * @access protected
     */
    protected $fontSizeRange = 12;

    /**
     * @var    string
     * @access protected
     */
    protected $cssClass = 'tagcloud';

    /**
     * @var    string
     * @access protected
     * mm,cm,in,pt,pc,px,em
     */
    protected $sizeSuffix = 'px';

    /**
     * @var    array
     * @access protected
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
     * @var    array
     * @access private
     */
    private $_elements = array();

    /**
     * @var    int
     * @access private
     */
    private $_max = 0;

    /**
     * @var    int
     * @access private
     */
    private $_min = 0;


    /**
     * @var    int
     * @access private
     */
    private $_maxEpoc;

    /**
     * @var    int
     * @access private
     */
    private $_minEpoc;

    /**
     * @var    float
     * @access private
     */
    private $_factor = 1;

    /**
     * @var    float
     * @access private
     */
    private $_epocFactor = 1;


    // }}}
    // {{{ public function __construct($baseFontSize = 24, $fontSizeRange = 12)
    /**
    *
    * Class constructor
    *
    * @param   int $baseFontSize    base font size of output tag (option)
    * @param   int $fontSizeRange   font size range
    * @access  public
    */
    public function __construct($baseFontSize = 24, $fontSizeRange = 12)
    {
        $this->baseFontSize = $baseFontSize;
        $this->fontSizeRange = $fontSizeRange;
        if ($this->baseFontSize - $this->fontSizeRange > 0){
            $this->minFontSize = $this->baseFontSize - $this->fontSizeRange;
        } else {
            $this->minFontSize = 0;
        }
        $this->maxFontSize = $this->baseFontSize + $this->fontSizeRange;
    }
    // }}}
    // {{{ public function addElement($name = '', $url ='', $count = 0, $timestamp = null)
    /**
    *
    * add a Tag Element to build Tag Cloud
    *
    * @return  void
    * @param   string  $tag
    * @param   string  $url
    * @param   int     $count
    * @param   int     $timestamp unixtimestamp 
    * @access  public
    */
    public function addElement($name = '', $url ='', $count = 0, $timestamp = null)
    {
        $i = count($this->_elements);
        $this->_elements[$i]['name'] = $name;
        $this->_elements[$i]['url'] = $url;
        $this->_elements[$i]['count'] = $count;
        $this->_elements[$i]['timestamp'] = $timestamp == null ? time() : $timestamp;
    }
    // }}}
    // {{{ public function addElements($tags)
    /**
    *
    * add a Tag Element to build Tag Cloud
    *
    * @return  void
    * @param   array   $tags Associative array to $this->_elements
    * @access  public
    */
    public function addElements($tags)
    {
        $this->_elements = array_merge($this->_elements, $tags);
    }
    // }}}
    // {{{ public function clearElements()
    /**
    *
    * clear Tag Elements
    *
    * @access  public
    */
    public function clearElements()
    {
        $this->_elements = array();
    }
    // }}}
    // {{{ public function buildAll($param = array())
    /**
    *
    * build HTML and CSS at once.
    *
    * @return  string HTML and CSS 
    * @param   array $param 
    * @see     _buidHTMLTags
    * @access  public
    */
    public function buildAll($param = array())
    {
        $html = '<style type="text/css">'."\n";
        $html .= $this->buildCSS()."</style>\n";
        $html .= $this->buildHTML($param);
        return $html;
    }
    // }}}
    // {{{ public function html_and_css($param = array())
    /**
    *
    * Alias to buildAll. Compatibilities for Perl Module.
    *
    * @return  string HTML and CSS 
    * @param   array  $param 'limit' => int limit of generation tag num.
    * @access  public
    */
    public function html_and_css($param = array())
    {
        return $this->buildAll($param);
    }
    // }}}
    // {{{ public function buildHTML($param = array())
    /**
    *
    * build HTML part
    *
    * @return  string HTML
    * @param   array  $param 'limit' => int limit of generation tag num.
    * @access  public
    */
    public function buildHTML($param = array())
    {
        $htmltags = $this->_buidHTMLTags($param);
        return $this->_wrapDiv($htmltags);
    }
    // }}}
    // {{{ public function buildCSS()
    /**
    *
    * build CSS part
    *
    * @return  string base CSS
    * @access  public
    */
    public function buildCSS()
    {
        $css = '';
        foreach ($this->epocLevel as $item) {
            foreach ($item as $epocName => $colors) {
                foreach ($colors as $attr => $color) {
                    $css .= "a.{$epocName}:{$attr} {text-decoration: none; color: #{$color};}\n";
                }
            }
        }
        return $css;
    }
    // }}}
    // {{{ private function _buidHTMLTags($param)
    /**
    *
    * calc Tag level and create whole HTML of each Tags
    *
    * @return  string HTML
    * @param   array $param limit of Tag Number
    * @access  private
    */
    private function _buidHTMLTags($param)
    {
        $this->total = count($this->_elements);
        // no tags elements
        if ($this->total == 0) {
            return array();
        } elseif ($this->total == 1) {
            $tag = $this->_elements[0];
            return $this->_createHTMLTag($tag, 'latest', $this->baseFontSize);
        }

        $limit = array_key_exists('limit', $param) ? $param['limit'] : 0;
        $this->_sortTags($limit);
        $this->_calcMumCount();
        $this->_calcMumEpoc();

        $range = $this->maxFontSize - $this->minFontSize;
        if ($this->_max != $this->_min) {
            $this->_factor = $range / (sqrt($this->_max) - sqrt($this->_min));
        } else {
            $this->_factor = 1;
        }

       if ($this->_maxEpoc != $this->_minEpoc) {
           $this->_epocFactor = count($this->epocLevel) 
                                   / (sqrt($this->_maxEpoc) - sqrt($this->_minEpoc));
       } else {
           $this->_epocFactor = 1;
       }
       $rtn = array();
        foreach ($this->_elements as $tag) {
            $countLv = $this->_getCountLevel($tag['count']);
            if (! isset($tag['timestamp']) || empty($tag['timestamp'])) {
                $epocLv = count($this->epocLevel) - 1;
            } else {
                $epocLv  = $this->_getEpocLevel($tag['timestamp']);
            }
            $colorType = $this->epocLevel[$epocLv];
            $fontSize  = $this->minFontSize + $countLv;
            $rtn[] = $this->_createHTMLTag($tag, key($colorType), $fontSize);
        }
        return implode("", $rtn);
    }
    // }}}
    // {{{ protected function _createHTMLTag($tag, $type, $fontSize)
    /**
    *
    * create a Element of HTML part
    *
    * @return  string a Element of Tag HTML
    * @param   array  $tag
    * @param   string $type css class of time line param
    * @param   float  $fontSize 
    * @access  protected
    */
    protected function _createHTMLTag($tag, $type, $fontSize)
    {
        return '<a href="'. $tag['url'] . '" style="font-size: '. 
               $fontSize . $this->sizeSuffix . ';" class="'.  $type .'">' .
               htmlspecialchars($tag['name']) . '</a>&nbsp;'. "\n";
    }
    // }}}
    // {{{ private function _sortTags($limit = 0)
    /**
    *
    * sort tags by name
    *
    * @return  array
    * @param   int  $limit limit element number of create TagCloud
    * @access  private
    */
    private function _sortTags($limit = 0)
    {
        usort($this->_elements, array($this, "_cmpElementsName"));
        if ($limit != 0) {
            $this->_elements = array_splice($this->_elements, 0, $limit);
        }
    }
    // }}}
    // {{{ private function _cmpElementsName($a, $b)
    /**
    *
    * using for usort()
    *
    * @return  int (bool)
    * @access  public
    */
    private function _cmpElementsName($a, $b)
    {
        if ($a['name'] == $b['name']) {
            return 0;
        }
        return ($a['name'] < $b['name']) ? -1 : 1;
    }
    // }}}
    // {{{ private function _calcMumCount()
    /**
    *
    * calc max and min tag count of use
    *
    * @access  private
    */
    private function _calcMumCount()
    {
        foreach ($this->_elements as $item) {
            $array[] = $item['count'];
        }
        $this->_min = min($array);
        $this->_max = max($array);
    }
    // }}}
    // {{{ private function _calcMumEpoc()
    /**
    *
    * calc max and min timestamp
    *
    * @access  private
    */
    private function _calcMumEpoc()
    {
        foreach ($this->_elements as $item) {
            $array[] = $item['timestamp'];
        }
        $this->_minEpoc = min($array);
        $this->_maxEpoc = max($array);
    }
    // }}}
    // {{{ private function _getCountLevel($count = 0)
    /**
    *
    * calc Tag Level of size
    *
    * @return  int level
    * @param   int $count
    * @access  private
    */
    private function _getCountLevel($count = 0)
    {
        return (int)(sqrt($count) - sqrt($this->_min) ) * $this->_factor;
    }
    // }}}
    // {{{ private function _getEpocLevel($timestamp = 0)
    /**
    *
    * calc timeline level of Tag
    *
    * @return  int     level of timeline
    * @param   int     $timestamp
    * @access  private
    */
    private function _getEpocLevel($timestamp = 0)
    {
        return (int) (sqrt($timestamp) - sqrt($this->_minEpoc)) * $this->_epocFactor;
    }
    // }}}
    // {{{ private function _wrapDiv($html)
    /**
    *
    * wrap div tag
    *
    * @return  string
    * @param   string $html
    * @access  private
    */
    private function _wrapDiv($html)
    {
        return $html == '' ? '' : '<div class="'.$this->cssClass .'">'.$html.'</div>'."\n";
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
