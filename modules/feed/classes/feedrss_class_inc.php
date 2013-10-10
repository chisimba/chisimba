<?php
require_once('abfeed_class_inc.php');
class feedrss extends abfeed
{
    /**
     * The classname for individual channel elements.
     *
     * @var string
     */
    protected $_entryClassName = 'feedentryrss';

    /**
     * The element name for individual channel elements (RSS <item>s).
     *
     * @var string
     */
    protected $_entryElementName = 'item';

    /**
     * The default namespace for RSS channels.
     *
     * @var string
     */
    protected $_defaultNamespace = 'rss';


    /**
     * Override abfeed to set up the $_element and $_entries aliases.
     */
    public function __wakeup()
    {
        parent::__wakeup();

        // Find the base channel element and create an alias to it.
        $this->_element = $this->_element->getElementsByTagName('channel')->item(0);
        if (!$this->_element) {
            throw new customException('No root <channel> element found, cannot parse channel.');
        }

        // Find the entries and save a pointer to them for speed and
        // simplicity.
        $this->_buildEntryCache();
    }


    /**
     * Make accessing some individual elements of the channel easier.
     *
     * Special accessors 'item' and 'items' are provided so that if
     * you wish to iterate over an RSS channel's items, you can do so
     * using foreach ($channel->items as $item) or foreach
     * ($channel->item as $item).
     *
     * @param string $var The property to access.
     * @return mixed
     */
    public function __get($var)
    {
        switch ($var) {
            case 'item':
                // fall through to the next case
            case 'items':
                return $this;

            default:
                return parent::__get($var);
        }
    }

}
?>