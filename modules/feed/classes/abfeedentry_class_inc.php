<?php

abstract class abfeedentry extends feedelement
{
    /**
     * Root XML element for entries. Subclasses must define this to a
     * non-null value.
     *
     * @var string
     */
    protected $_rootElement;

    /**
     * Root namespace for entries. Subclasses may define this to a
     * non-null value.
     *
     * @var string
     */
    protected $_rootNamespace = null;


    /**
     * abfeedentry constructor
     *
     * The abfeedentry constructor takes the URI of the feed the entry
     * is part of, and optionally an XML construct (usually a
     * SimpleXMLElement, but it can be an XML string or a DOMNode as
     * well) that contains the contents of the entry.
     */
    public function __construct($uri = null, $element = null)
    {
        if (!($element instanceof DOMElement)) {
            if ($element) {
                // Load the feed as an XML DOMDocument object
                @ini_set('track_errors', 1);
                $doc = new DOMDocument();
                $success = @$doc->loadXML($element);
                @ini_restore('track_errors');

                if (!$success) {
                    throw new customException("DOMDocument cannot parse XML: $php_errormsg");
                }

                $element = $doc->getElementsByTagName($this->_rootElement)->item(0);
                if (!$element) {
                    throw new customException('No root <' . $this->_rootElement . '> element found, cannot parse feed.');
                }
            } else {
                $doc = new DOMDocument('1.0', 'utf-8');
                if ($this->_rootNamespace !== null) {
                    $element = $doc->createElementNS(feed::lookupNamespace($this->_rootNamespace), $this->_rootElement);
                } else {
                    $element = $doc->createElement($this->_rootElement);
                }
            }
        }

        parent::__construct($element);
    }

}
?>