<?php
require_once('feedelement_class_inc.php');
abstract class abfeed extends feedelement implements Iterator
{

    /**
     * Current index on the collection of feed entries for the
     * Iterator implementation.
     *
     * @var integer
     */
    protected $_entryIndex = 0;

    /**
     * Cache of feed entries.
     *
     * @var array
     */
    protected $_entries;

    /**
     * Feed constructor
     *
     * The abfeed constructor takes the URI of a feed or a
     * feed represented as a string and loads it as XML.
     *
     * @throws customException If loading the feed failed.
     *
     * @param string $uri The full URI of the feed to load, or NULL if not retrieved via HTTP.
     * @param string $string The feed as a string, or NULL if retrieved via HTTP.
     */
    public function __construct($uri = null, $string = null)
    {
        if ($uri !== null) {
            // Retrieve the feed via HTTP
            $client = feeds::getHttpClient();
            $client->setUri($uri);
            $response = $client->get();
            if ($response->getStatus() !== 200) {
                throw new customException('Feed failed to load, got response code ' . $response->getStatus());
            }
            $this->_element = $response->getBody();
        } else {
            // Retrieve the feed from $string
            $this->_element = $string;
        }

        $this->__wakeup();
    }


    /**
     * Load the feed as an XML DOMDocument object
     */
    public function __wakeup()
    {
        @ini_set('track_errors', 1);
        $doc = new DOMDocument();
        $success = @$doc->loadXML($this->_element);
        @ini_restore('track_errors');

        if (!$success) {
            throw new customException("DOMDocument cannot parse XML: $php_errormsg");
        }

        $this->_element = $doc;
    }


    /**
     * Prepare for serialization
     *
     * @return array
     */
    public function __sleep()
    {
        $this->_element = $this->saveXML();

        return array('_element');
    }


    /**
     * Cache the individual feed elements so they don't need to be
     * searched for on every operation.
     *
     * @internal
     */
    protected function _buildEntryCache()
    {
        $this->_entries = array();
        foreach ($this->_element->childNodes as $child) {
            if ($child->localName == $this->_entryElementName) {
                $this->_entries[] = $child;
            }
        }
    }


    /**
     * Get the number of entries in this feed object.
     *
     * @return integer Entry count.
     */
    public function count()
    {
        return count($this->_entries);
    }


    /**
     * Required by the Iterator interface.
     *
     * @internal
     */
    public function rewind()
    {
        $this->_entryIndex = 0;
    }


    /**
     * Required by the Iterator interface.
     *
     * @internal
     *
     * @return mixed The current row, or null if no rows.
     */
    public function current()
    {
        return new $this->_entryClassName(
            null,
            $this->_entries[$this->_entryIndex]);
    }


    /**
     * Required by the Iterator interface.
     *
     * @internal
     *
     * @return mixed The current row number (starts at 0), or NULL if no rows
     */
    public function key()
    {
        return $this->_entryIndex;
    }


    /**
     * Required by the Iterator interface.
     *
     * @internal
     *
     * @return mixed The next row, or null if no more rows.
     */
    public function next()
    {
        ++$this->_entryIndex;
    }


    /**
     * Required by the Iterator interface.
     *
     * @internal
     *
     * @return boolean Whether the iteration is valid
     */
    public function valid()
    {
        return (0 <= $this->_entryIndex && $this->_entryIndex < $this->count());
    }

}
?>