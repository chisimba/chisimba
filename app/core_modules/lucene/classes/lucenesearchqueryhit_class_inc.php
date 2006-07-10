<?php

class lucenesearchqueryhit
{
    /**
     * Object handle of the index
     * @var searchlucene
     */
    protected $_index = null;

    /**
     * Object handle of the document associated with this hit
     * @var search_Lucene_Document
     */
    protected $_document = null;

    /**
     * Number of the document in the index
     * @var integer
     */
    public $id;

    /**
     * Score of the hit
     * @var float
     */
    public $score;


    /**
     * Constructor - pass object handle of search_Lucene index that produced
     * the hit so the document can be retrieved easily from the hit.
     *
     * @param search_Lucene $index
     */

    public function __construct(lucenesearch $index)
    {
        $this->_index = $index;
    }


    /**
     * Convenience function for getting fields from the document
     * associated with this hit.
     *
     * @param string $offset
     * @return string
     */
    public function __get($offset)
    {
        return $this->getDocument()->getFieldValue($offset);
    }


    /**
     * Return the document object for this hit
     *
     * @return search_Lucene_Document
     */
    public function getDocument()
    {
        if (!$this->_document instanceof lucenedocument) {
            $this->_document = $this->_index->getDocument($this->id);
        }

        return $this->_document;
    }


    /**
     * Return the index object for this hit
     *
     * @return search_Lucene
     */
    public function getIndex()
    {
        return $this->_index;
    }
}
?>