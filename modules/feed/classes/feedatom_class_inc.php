<?php
require_once('abfeed_class_inc.php');
class feedatom extends abfeed
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'feedentryatom';

    /**
     * The element name for individual feed elements (Atom <entry>
     * elements).
     *
     * @var string
     */
    protected $_entryElementName = 'entry';

    /**
     * The default namespace for Atom feeds.
     *
     * @var string
     */
    protected $_defaultNamespace = 'atom';


    /**
     * Override abfeed to set up the $_element and $_entries aliases.
     */
    public function __wakeup()
    {
        parent::__wakeup();

        // Find the base feed element and create an alias to it.
        $element = $this->_element->getElementsByTagName('feed')->item(0);
        if (!$element) {
            // Try to find a single <entry> instead.
            $element = $this->_element->getElementsByTagName($this->_entryElementName)->item(0);
            if (!$element) {
                throw new customException('No root <feed> or <' . $this->_entryElementName
                                              . '> element found, cannot parse feed.');
            }

            $doc = new DOMDocument($this->_element->version,
                                   $this->_element->actualEncoding);
            $feed = $doc->appendChild($doc->createElement('feed'));
            $feed->appendChild($doc->importNode($element, true));
            $element = $feed;
        }

        $this->_element = $element;

        // Find the entries and save a pointer to them for speed and
        // simplicity.
        $this->_buildEntryCache();
    }


    /**
     * Easy access to <link> tags keyed by "rel" attributes.
     *
     * If $elt->link() is called with no arguments, we will attempt to
     * return the value of the <link> tag(s) like all other
     * method-syntax attribute access. If an argument is passed to
     * link(), however, then we will return the "href" value of the
     * first <link> tag that has a "rel" attribute matching $rel:
     *
     * $elt->link(): returns the value of the link tag.
     * $elt->link('self'): returns the href from the first <link rel="self"> in the entry.
     *
     * @param string $rel The "rel" attribute to look for.
     * @return mixed
     */
    public function link($rel = null)
    {
        if ($rel === null) {
            return parent::__call('link', null);
        }

        // index link tags by their "rel" attribute.
        $links = parent::__get('link');
        if (!is_array($links)) {
            if ($links instanceof feedelement) {
                $links = array($links);
            } else {
                return $links;
            }
        }

        foreach ($links as $link) {
            if (empty($link['rel'])) {
                continue;
            }
            if ($rel == $link['rel']) {
                return $link['href'];
            }
        }

        return null;
    }


    /**
     * Make accessing some individual elements of the feed easier.
     *
     * Special accessors 'entry' and 'entries' are provided so that if
     * you wish to iterate over an Atom feed's entries, you can do so
     * using foreach ($feed->entries as $entry) or foreach
     * ($feed->entry as $entry).
     *
     * @param string $var The property to access.
     * @return mixed
     */
    public function __get($var)
    {
        switch ($var) {
            case 'entry':
                // fall through to the next case
            case 'entries':
                return $this;

            default:
                return parent::__get($var);
        }
    }

}
?>