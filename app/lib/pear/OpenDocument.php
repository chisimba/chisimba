<?php
/**
* OpenDocument base class
* 
* OpenDocument class handles reading and modifying files in OpenDocument format
*
* PHP version 5
*
* LICENSE: This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
* 
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
* 
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/

require_once 'OpenDocument/ZipWrapper.php';
require_once 'OpenDocument/Exception.php';
require_once 'OpenDocument/Textelement.php';
require_once 'OpenDocument/Span.php';
require_once 'OpenDocument/Paragraph.php';
require_once 'OpenDocument/Heading.php';
require_once 'OpenDocument/Bookmark.php';
require_once 'OpenDocument/Hyperlink.php';

/**
* OpenDocument base class
*
* OpenDocument class handles reading and modifying files in OpenDocument format
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument
{
    /**
     * Path to opened OpenDocument file
     *
     * @var string
     * @access private
     */
    private $path;
    
    /**
     * DOMNode of current node
     *
     * @var DOMNode
     * @access provate
     */
    private $cursor;
    
    /**
     * DOMNode contains style information
     *
     * @var DOMNode
     * @access private
     */
    private $styles;
    
    /**
     * DOMNode contains fonts declarations
     *
     * @var DOMNode
     * @access private
     */
    private $fonts;
    
    /**
     * Mime type information
     *
     * @var string
     * @access private
     */
    private $mimetype;
    
    /**
     * Flag indicates whether it is a new file
     *
     * @var bool
     * @access private
     */
    private $create = false;

    /**
     * DOMDocument for content file
     *
     * @var DOMDocument
     * @access private
     */
    private $contentDOM;

    /**
     * DOMXPath object for content file
     *
     * @var DOMXPath
     * @access private
     */
    private $contentXPath;

    /**
     * DOMDocument for meta file
     *
     * @var DOMDocument
     * @access private
     */
    private $metaDOM;

    /**
     * DOMXPath for meta file
     *
     * @var DOMXPath
     * @access private
     */
    private $metaXPath;

    /**
     * DOMDocument for settings file
     *
     * @var DOMDocument
     * @access private
     */
    private $settingsDOM;

    /**
     * DOMXPath for setting file
     *
     * @var DOMXPath
     * @access private
     */
    private $settingsXPath;

    /**
     * DOMDocument for styles file
     *
     * @var DOMDocument
     * @access private
     */
    private $stylesDOM;

    /**
     * DOMXPath for styles file
     *
     * @var DOMXPath
     * @access private
     */
    private $stylesXPath;

    /**
     * DOMDocument for styles file
     *
     * @var DOMDocument
     * @access private
     */
    private $manifestDOM;

    /**
     * DOMXPath for manifest file
     *
     * @var DOMXPath
     * @access private
     */
    private $manifestXPath;
            
    /**
     * Collection of children objects
     *
     * @var ArrayIterator
     * @access read-only
     */
    private $children;

    /**
     * File with document contents
     */
    const FILE_CONTENT = 'content.xml';
    
    /**
     * File with meta information
     */
    const FILE_META = 'meta.xml';
    
    /**
     * File with editor settings
     */
    const FILE_SETTINGS = 'settings.xml';
    
    /**
     * File with document styles
     */
    const FILE_STYLES = 'styles.xml';
    
    /**
     * File with mime type
     */
    const FILE_MIMETYPE = 'mimetype';
    
    /**
     * File with manifest information
     */
    const FILE_MANIFEST = 'META-INF/manifest.xml';

    /**
     * text namespace URL
     */
    const NS_TEXT = 'urn:oasis:names:tc:opendocument:xmlns:text:1.0';
    
    /**
     * style namespace URL
     */
    const NS_STYLE = 'urn:oasis:names:tc:opendocument:xmlns:style:1.0';
    
    /**
     * fo namespace URL
     */
    const NS_FO = 'urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0';
    
    /**
     * office namespace URL
     */
    const NS_OFFICE = 'urn:oasis:names:tc:opendocument:xmlns:office:1.0';
    
    /**
     * svg namespace URL
     */
    const NS_SVG = 'urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0';
    
    /**
     * xlink namespace URL
     */
    const NS_XLINK = 'http://www.w3.org/1999/xlink';

    /**
     * Constructor
     *
     * @param string $filename optional
     *               specify file name if you want to open existing file
     *               to create new document pass nothing or empty string
     * @throws OpenDocument_Exception
     */
    public function __construct($filename = '')
    {
        if (!strlen($filename)) {
            $filename = dirname(__FILE__) . '/OpenDocument/templates/default.odt';
            $this->create = true;
        }
        
        if (!is_readable($filename)) {
            throw new OpenDocument_Exception(OpenDocument_Exception::ACCESS_FILE_ERR);
        }
        $this->path = $filename;

        //get mimetype
        if (!$this->mimetype = ZipWrapper::read($filename, self::FILE_MIMETYPE)) {
            throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_MIMETYPE_ERR);
        }

        //get content
        $this->contentDOM = new DOMDocument();
        if (!$this->contentDOM->loadXML(ZipWrapper::read($filename, self::FILE_CONTENT))) {
            throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_CONTENT_ERR);
        }
        $this->contentXPath = new DOMXPath($this->contentDOM);

        //get meta data
        $this->metaDOM = new DOMDocument();
        if (!$this->metaDOM->loadXML(ZipWrapper::read($filename, self::FILE_META))) {
            throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_META_ERR);
        }
        $this->metaXPath = new DOMXPath($this->metaDOM);

        //get settings
        $this->settingsDOM = new DOMDocument();
        if (!$this->settingsDOM->loadXML(ZipWrapper::read($filename, self::FILE_SETTINGS))) {
            throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_SETTINGS_ERR);
        }
        $this->settingsXPath = new DOMXPath($this->settingsDOM);

        //get styles
        $this->stylesDOM = new DOMDocument();
        if (!$this->stylesDOM->loadXML(ZipWrapper::read($filename, self::FILE_STYLES))) {
            throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_STYLES_ERR);
        }
        $this->stylesXPath = new DOMXPath($this->stylesDOM);

        //get manifest information
        $this->manifestDOM = new DOMDocument();
        if (!$this->manifestDOM->loadXML(ZipWrapper::read($filename, self::FILE_MANIFEST))) {
            throw new OpenDocument_Exception(OpenDocument_Exception::LOAD_MANIFEST_ERR);
        }
        $this->manifestXPath = new DOMXPath($this->manifestDOM);
        
        //set cursor
        $this->cursor = $this->contentXPath->query('/office:document-content/office:body/office:text')->item(0);
        $this->styles = $this->contentXPath->query('/office:document-content/office:automatic-styles')->item(0);
        $this->fonts  = $this->contentXPath->query('/office:document-content/office:font-face-decls')->item(0);
        $this->contentXPath->registerNamespace('text', self::NS_TEXT);
        
        $this->listChildren();
        $this->setMax();
    }

    /**
     * Magic method
     * Provide read only access to cursor private variable
     *
     * @param  string $name
     * @return mixed
     */
    private function __get($name)
    {
        switch ($name) {
        case 'cursor':
            return $this->cursor;
        default:
        }
    }
    
    /**
     * Get children list
     *
     * @return ArrayIterator
     * @access public
     */
    public function getChildren()
    {
        return $this->children->getIterator();
    }
    
    /**
     * Create ArrayObject of document children objects
     * 
     * @access private
     */
    private function listChildren()
    {
        $this->children = new ArrayObject;
        if ($this->cursor instanceof DOMNode) {
            $childrenNodes = $this->cursor->childNodes;
            foreach ($childrenNodes as $child) {
                switch ($child->nodeName) {
                case 'text:p':
                    $element = new OpenDocument_Paragraph($child, $this);
                    break;
                case 'text:h':
                    $element = new OpenDocument_Heading($child, $this);
                    break;
                default:
                    $element = false;
                }
                if ($element) {
                    $this->children->append($element);
                }
            }
        }
    }
    
    
    /**
     * Delete document child element
     *
     * @param OpenDocument_Element $element
     * @access public
     */
    public function deleteElement(OpenDocument_Element $element)
    {
        $this->cursor->removeChild($element->getNode());
        unset($element);
    }
    
    /**
     * Set maximum values of style name suffixes
     * 
     * @access private
     */
    private function setMax()
    {
        $classes = array('OpenDocument_Paragraph', 'OpenDocument_Heading', 'OpenDocument_Hyperlink');
        $max = array();
        if ($this->cursor instanceof DOMNode) {
            $nodes = $this->cursor->getElementsByTagName('*');
            foreach ($nodes as $node) {
                if ($node->hasAttributeNS(self::NS_TEXT, 'style-name')) {
                    $style_name = $node->getAttributeNS(self::NS_TEXT, 'style-name');
                    foreach ($classes as $class) {
                        $reflection = new ReflectionClass($class);
                        $prefix = $reflection->getConstant('styleNamePrefix');
                        if (preg_match("/^$prefix(\d)+$/", $style_name, $m)) {
                            $max[$class] = isset($max[$class]) ? ($max[$class] < $m[1] ? $m[1] : $max[$class]) : $m[1];
                        }
                    }
                }
            }
        }
        foreach ($classes as $class) {
            $method = new ReflectionMethod($class, 'setStyleNameMaxNumber');
            if (!isset($max[$class])) {
                $max[$class] = 0;
            }
            $method->invoke(null, $max[$class]);
        }
    }

    /************************* Elements **************************/
    
    /**
     * Create OpenDocument_Paragraph
     *
     * @param string $text optional
     * @return OpenDocument_Paragraph
     * @access public
     */
    public function createParagraph($text = '')
    {
        return OpenDocument_Paragraph::instance($this, $text);
    }
    
    /**
     * Create Open_document_Heading
     *
     * @param string $text
     * @param integer $level
     * @return OpenDocument_Heading
     * @access public
     */
    public function createHeading($text = '', $level = 1)
    {
        return OpenDocument_Heading::instance($this, $text, $level);
    }

    /**
     * Create OpenDocument_Bookmark
     *
     * @param string $name
     * @param string $type
     * @return OpenDocument_Bookmark
     * @access public
     * @todo finish method
     */
    public function createBookmark($name, $type = 'start')
    {
        if (!in_array($type, array('start', 'end'))) {
            $type = 'start';
        }
        $bookmark = new OpenDocument_Bookmark($this->contentDOM->createElementNS(self::NS_TEXT, 'bookmark-' . $type), $this, $name, $type);
        $this->cursor->appendChild($bookmark->getNode());
        $bookmark->getNode()->setAttributeNS(self::NS_TEXT, 'name', $name);
        return $bookmark;
    }

    
    /********************* Styles ****************************/   
    
    /**
     * Apply style information to object
     * If object has no style information yet, then create new style node
     * If object style information is similar to other object's style info, then apply the same style name
     *     And if object old style information was not shared with other objects then delete old style info
     *     Else leave old style info
     * Else just add new style description
     *
     * @param string $style_name
     * @param string $name
     * @param mixed $value
     * @param OpenDocument_StyledElement $object
     * @return string $style_name
     */
    public function applyStyle($style_name, $name, $value, OpenDocument_StyledElement $object)
    {
        //check if other nodes have the same style name
        $nodes = $this->cursor->getElementsByTagName('*');
        $count = 0;
        foreach ($nodes as $node) {
            if ($node->hasAttributeNS(self::NS_TEXT, 'style-name')
             && $node->getAttributeNS(self::NS_TEXT, 'style-name') == $style_name) {
                $count ++;
                if ($count > 1) {
                    break;
                }
            }
        }

        $generate = false;
        
        //get style node
        if ($count > 1) {
            $style = $this->getStyleNode($style_name)->cloneNode(true);
            $this->styles->appendChild($style);
            $generate = true;
            $style_name = uniqid('tmp');//$object->generateStyleName();
            $style->setAttributeNS(self::NS_STYLE, 'name', $style_name);
        } else {
            $style = $this->getStyleNode($style_name);
        }

        if (empty($style)) {
            if (empty($style_name)) {
                $generate = true;
                $style_name = uniqid('tmp');
            }
            $style = $this->contentDOM->createElementNS(self::NS_STYLE, 'style');
            $style->setAttributeNS(self::NS_STYLE, 'name', $style_name);
            $style->setAttributeNS(self::NS_STYLE, 'family', 'paragraph');
            $style->setAttributeNS(self::NS_STYLE, 'parent-style-name', 'Standard');
            $this->styles->appendChild($style);
        }

        $nodes = $style->getElementsByTagNameNS(self::NS_STYLE, 'text-properties');
        if ($nodes->length) {
            $text_properties = $nodes->item(0);
        } else {
            $text_properties = $this->contentDOM->createElementNS(self::NS_STYLE, 'text-properties');
            $style->appendChild($text_properties);
        }
        $text_properties->setAttribute($name, $value);

        //find alike style
        $nodes = $this->styles->getElementsByTagNameNS(self::NS_STYLE, 'style');
        foreach ($nodes as $node) {
            if (!$style->isSameNode($node) && $this->compareChildNodes($style, $node)) {
                $style->parentNode->removeChild($style);
                return $node->getAttributeNS(self::NS_STYLE, 'name');
            }
        }
        
        if ($generate) {
            $style_name = $object->generateStyleName();
            $style->setAttributeNS(self::NS_STYLE, 'name', $style_name);
        }
        return $style->getAttributeNS(self::NS_STYLE, 'name');
    }

    /**
     * Get array of style values
     *
     * @param string $style_name
     * @param array $properties
     * @return array
     */
    public function getStyle($style_name, $properties)
    {
        $style = array();
        if ($node = $this->getStyleNode($style_name)) {
            $nodes = $node->getElementsByTagNameNS(self::NS_STYLE, 'text-properties');
            if ($nodes->length) {
                $text_properties = $nodes->item(0);
                foreach ($properties as $property) {
                    list($prefix, $name) = explode(':', $property);
                    $ns = $text_properties->lookupNamespaceURI($prefix);
                    $style[$property] = $text_properties->getAttributeNS($ns, $name);
                }
            }
        }
        return $style;
    }
    
    /**
     * Get style node
     *
     * @param string $style_name
     * @return DOMNode
     */
    private function getStyleNode($style_name)
    {
        $nodes = $this->styles->getElementsByTagNameNS(self::NS_STYLE, 'style');
        foreach ($nodes as $node) {
            $node->getAttributeNS(self::NS_STYLE, 'name');
            if ($node->getAttributeNS(self::NS_STYLE, 'name') == $style_name) {
                return $node;
            }
        }
        return false;
    }
    
    /**
     * Check if two style info are similar
     *
     * @param string $style_name1
     * @param string $style_name2
     * @return bool
     */
    private function compareStyles($style_name1, $style_name2)
    {
        $style_node1 = $this->getStyleNode($style_name1);
        $style_node2 = $this->getStyleNode($style_name2);
        return $this->compareNodes($style_node1, $style_node2);
    }
    
    /********************* Fonts ****************************/
    
    /**
     * Get array of declared font names
     *
     * @return array
     */
    private function getFonts()
    {
        $nodes = $this->fonts->getElementsByTagNameNS(self::NS_STYLE, 'font-face');
        $fonts = array();
        foreach ($nodes as $node) {
            $fonts[] = $node->getAttributeNS(self::NS_STYLE, 'name');
        }
        return $fonts;
    }
    
    /**
     * Add new font declaration
     *
     * @param string $font_name
     * @param string $font_family optional
     */
    public function addFont($font_name, $font_family = '')
    {
        if (!in_array($font_name, $this->getFonts())) {
            $node = $this->contentDOM->createElementNS(self::NS_STYLE, 'font-face');
            $this->fonts->appendChild($node);
            $node->setAttributeNS(self::NS_STYLE, 'name', $font_name);
            if (!strlen($font_family)) {
                $font_family = $font_name;
            }
            $node->setAttributeNS(self::NS_SVG, 'font-family', $font_family);
        }
    }
    
    /**
     * Compare two DOMNode nodes
     *
     * @param mixed $node1
     * @param mixed $node2
     * @return bool
     */
    function compareNodes($node1, $node2)
    {
        if (!($node1 instanceof DOMNode) || !($node2 instanceof DOMNode)) {
            return false;
        }
        $attributes = $node1->attributes;
        if ($attributes->length == $node2->attributes->length) {
            for ($i = 0; $i < $attributes->length; $i ++) {
                $name = $attributes->item($i)->name;
                $value = $attributes->item($i)->value;
                if (!$node2->hasAttribute($name) || $node2->getAttribute($name) != $value) {
                    return false;
                }
            }
        } else {
            return false;
        }
        
        $children = $node1->childNodes;
        if ($children->length == $node2->childNodes->length) {
            for ($i = 0; $i < $children->length; $i ++) {
                $node = $children->item($i);
                $matches = $this->getChildrenByName($node2, $node->nodeName);
                $test = false;
                foreach ($matches as $match) {
                    if ($this->compareNodes($node, $match)) {
                        $test = true;
                        break;
                    }
                }
                if (!$test) {
                    return false;
                }
            }
        } else {
            return false;
        }
        
        return true;
    }
    
    /**
     * Compare DOMNode children
     *
     * @param DOMNode $node1
     * @param DOMNode $node2
     * @return bool
     */
    private function compareChildNodes(DOMNode $node1, DOMNode $node2)
    {
        $children = $node1->childNodes;
        if ($children->length == $node2->childNodes->length) {
            for ($i = 0; $i < $children->length; $i ++) {
                $node = $children->item($i);
                $matches = $this->getChildrenByName($node2, $node->nodeName);
                $test = false;
                foreach ($matches as $match) {
                    if ($this->compareNodes($node, $match)) {
                        $test = true;
                        break;
                    }
                }
                if (!$test) {
                    return false;
                }
            }
        } else {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get DOMNode children by name
     *
     * @param DOMNode $node
     * @param string $name
     * @return array
     */
    private function getChildrenByName(DOMNode $node, $name)
    {
        $nodes = array();
        foreach ($node->childNodes as $node) {
            if ($node->nodeName == $name) {
                array_push($nodes, $node);
            }
        }
        return $nodes;
    }
    
    /**
     * Test function
     * @todo remove or finish function
     */
    public function output()
    {
        $list = $this->contentXPath->query('/office:document-content/office:font-face-decls/style:font-face');
        echo $list->length;
        foreach ($list as $node) {
            echo '<br />';
            foreach ($node->attributes as $attribute) {
                echo $attribute->name . '=' . $attribute->value;
            }
        }
        echo $this->contentDOM->saveXML();
    }
    
    /**
     * Save changes in document or save as a new document / under another name
     *
     * @param string $filename optional
     * @throws OpenDocument_Exception
     */
    public function save($filename = '')
    {
        if (strlen($filename)) {
            $this->path = $filename;
        }
        //write mimetype
        if (!ZipWrapper::write($this->path, self::FILE_MIMETYPE, $this->mimetype)) {
            throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_MIMETYPE_ERR);
        }

        //write content
        $xml = str_replace("'", '&apos;', $this->contentDOM->saveXML());
        if (!ZipWrapper::write($this->path, self::FILE_CONTENT, $xml)) {
            throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_CONTENT_ERR);
        }

        //write meta
        $xml = str_replace("'", '&apos;', $this->metaDOM->saveXML());
        if (!ZipWrapper::write($this->path, self::FILE_META, $xml)) {
            throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_META_ERR);
        }

        //write settings
        $xml = str_replace("'", '&apos;', $this->settingsDOM->saveXML());
        if (!ZipWrapper::write($this->path, self::FILE_SETTINGS, $xml)) {
            throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_SETTINGS_ERR);
        }

        //write styles
        $xml = str_replace("'", '&apos;', $this->stylesDOM->saveXML());
        if (!ZipWrapper::write($this->path, self::FILE_STYLES, $xml)) {
            throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_STYLES_ERR);
        }

        //write manifest
        $xml = str_replace("'", '&apos;', $this->manifestDOM->saveXML());
        if (!ZipWrapper::write($this->path, self::FILE_MANIFEST, $xml)) {
            throw new OpenDocument_Exception(OpenDocument_Exception::WRITE_MANIFEST_ERR);
        }
    }
}
?>