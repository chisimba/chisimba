<?php

/**
 *  Field indexer Lucene integration class
 *
 * @package lucene
 * @category field
 * @author Paul Scott
 * @copyright AVOIR UWC GNU/GPL
 * @filesource
 */

class lucenefield extends object
{
	/**
 	 * A field is a section of a Document.  Each field has two parts,
 	 * a name and a value. Values may be free text or they may be atomic
 	 * keywords, which are not further processed. Such keywords may
 	 * be used to represent dates, urls, etc.  Fields are optionally
 	 * stored in the index, so that they may be returned with hits
 	 * on the document.
 	 *
 	 * This class is based on the Zend_Search section of the Zend Framework 0.1.x
 	 */

	public $kind;

    public $name        = 'body';
    public $stringValue = null;
    public $isStored    = false;
    public $isIndexed   = true;
    public $isTokenized = true;
    public $isBinary    = false;

    public $storeTermVector = false;

    public $boost = 1.0;

    public function init()
    {
    	//maybe we need language - dunno yet...
    }

    public function setup($name, $stringValue, $isStored, $isIndexed, $isTokenized, $isBinary = false)
    {
    	$this->name = $name;
        if (!$isBinary)
        {
            /**
             * @todo Correct UTF-8 string should be required in future
             * Until full UTF-8 support is not completed, string should be normalized to ANSII encoding
             */
            $this->stringValue = iconv('', 'ASCII//TRANSLIT', $stringValue);
        }

        else {
            $this->stringValue = $stringValue;
        }

        $this->isStored    = $isStored;
        $this->isIndexed   = $isIndexed;
        $this->isTokenized = $isTokenized;
        $this->isBinary    = $isBinary;

        $this->storeTermVector = false;
        $this->boost           = 1.0;
    }

    /**
     * Constructs a String-valued Field that is not tokenized, but is indexed
     * and stored.  Useful for non-text fields, e.g. date or url.
     *
     * @param string $name
     * @param string $value
     * @return search_Lucene_Field
     */
    static public function keyword($name, $value)
    {
        return $this->setup($name, $value, true, true, false);
    }

    /**
     * Constructs a String-valued Field that is not tokenized nor indexed,
     * but is stored in the index, for return with hits.
     *
     * @param string $name
     * @param string $value
     * @return search_Lucene_Field
     */
    static public function unIndexed($name, $value)
    {
        return $this->setup($name, $value, true, false, false);
    }

    /**
     * Constructs a Binary String valued Field that is not tokenized nor indexed,
     * but is stored in the index, for return with hits.
     *
     * @param string $name
     * @param string $value
     * @return search_Lucene_Field
     */
    static public function binary($name, $value)
    {
        return $this->setup($name, $value, true, false, false, true);
    }

    /**
     * Constructs a String-valued Field that is tokenized and indexed,
     * and is stored in the index, for return with hits.  Useful for short text
     * fields, like "title" or "subject". Term vector will not be stored for this field.
     *
     * @param string $name
     * @param string $value
     * @return search_Lucene_Field
     */
    static public function text($name, $value)
    {
        return $this->setup($name, $value, true, true, true);
    }


    /**
     * Constructs a String-valued Field that is tokenized and indexed,
     * but that is not stored in the index.
     *
     * @param string $name
     * @param string $value
     * @return search_Lucene_Field
     */
    static public function unStored($name, $value)
    {
        return $this->setup($name, $value, false, true, true);
    }

}
?>