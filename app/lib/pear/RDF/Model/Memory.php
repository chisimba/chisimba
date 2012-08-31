<?php
// ----------------------------------------------------------------------------------
// Class: RDF_Model_Memory
// ----------------------------------------------------------------------------------
/**
 * A Model_Memory is an RDF Model, which is stored in the main memory.
 * This class provides methods for manipulating Model_Memorys.
 *
 * @version V0.7
 * @author Chris Bizer <chris@bizer.de>
 * @author Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 * @author Radoslaw Oldakowski <radol@gmx.de>
 * @author Daniel Westphal <mail@d-westphal.de>
 * @package model
 * @todo nothing
 * @access public
 */
require_once 'RDF/Model.php';

class RDF_Model_Memory extends RDF_Model
{
    /**
     * Triples of the Model_Memory
     *
     * @var array
     * @access private
     */
    var $triples = array();

    /**
     * Search index
     *
     * @var array
     * @access private
     */
    var $index;

    /**
     * This is set to true if the Model_Memory is indexed
     *
     * @var boolean
     * @access private
     */
    var $indexed;

    /**
     * You can supply a base_uri
     *
     * @param string $baseURI
     * @access public
     */
    function RDF_Model_Memory($baseURI = null)
    {
        $this->setBaseURI($baseURI);
        $this->indexed = false;
    }

    /**
     * Set a base URI for the Model_Memory.
     * Affects creating of new resources and serialization syntax.
     * If the URI doesn't end with # : or /, then a # is added to the URI.
     *
     * @param string $uri
     * @access public
     */
    function setBaseURI($uri)
    {
        if ($uri != null) {
            $c = substr($uri, strlen($uri)-1 , 1);
            if (!($c == '#' || $c == ':' || $c == '/' || $c == "\\")) {
                $uri .= '#';
            }
        }
        $this->baseURI = $uri;
    }

    /**
     * Number of triples in the Model_Memory
     *
     * @return integer
     * @access public
     */
    function size()
    {
        return count($this->triples);
    }

    /**
     * Checks if Model_Memory is empty
     *
     * @return boolean
     * @access public
     */
    function isEmpty()
    {
        if (count($this->triples) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds a new triple to the Model_Memory without checking if the statement is already in the Model_Memory.
     * The function doesn't check if the statement is already in the Model_Memory.
     * So if you want a duplicate free Model_Memory use the addWithoutDuplicates() function (which is slower then add())
     *
     * @param object Statement    $statement
     * @access public
     * @throws PhpError
     */
    function add($statement)
    {
        if (!is_a($statement, 'RDF_Statement')) {
            $errmsg = 'Stattement expected, got unexpected: '.
                (is_object($statement) ? get_class($statement) : gettype($statement));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        $this->indexed = false;
        $this->triples[] = $statement;
    }

    /**
     * Checks if a new statement is already in the Model_Memory and adds the statement, if it is not in the Model_Memory.
     * addWithoutDuplicates() is significantly slower then add().
     *
     * @param object Statement    $statement
     * @access public
     * @throws PhpError
     */
    function addWithoutDuplicates($statement)
    {
        if (!is_a($statement, 'RDF_Statement')) {
            $errmsg = 'Statement expected, got unexpected: '.
                (is_object($statement) ? get_class($statement) : gettype($statement));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        if (!$this->contains($statement)) {
            $this->indexed = false;
            $this->triples[] = $statement;
        }
    }

    /**
     * Removes the triple from the Model_Memory.
     *
     * @param object Statement    $statement
     * @access public
     * @throws PhpError
     */
    function remove($statement)
    {
        if (!is_a($statement, 'RDF_Statement')) {
            $errmsg = 'Statement expected, got unexpected: '.
                (is_object($statement) ? get_class($statement) : gettype($statement));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }
        foreach($this->triples as $key => $value) {
            if ($this->matchStatement($value, $statement->getSubject(), $statement->getPredicate(), $statement->getObject())) {
                $this->indexed = false;
                unset($this->triples[$key]);
            }
        }
        $this->triples = array_slice($this->triples, 0);
    }

    /**
     * Short Dump of the Model_Memory.
     *
     * @access public
     * @return string
     */
    function toString()
    {
        return 'Model_Memory[baseURI=' . $this->getBaseURI() . ';  size=' . $this->size() . ']';
    }

    /**
     * Dumps of the Model_Memory including all triples.
     *
     * @access public
     * @return string
     */
    function toStringIncludingTriples()
    {
        $dump = $this->toString() . chr(13);
        foreach($this->triples as $value) {
            $dump .= $value->toString() . chr(13);
        }
        return $dump;
    }

    /**
     * Writes the RDF serialization of the Model_Memory as HTML.
     *
     * @access public
     */
    function writeAsHtml()
    {
        $ser =& new RDF_Serializer();
        $rdf = &$ser->serialize($this);
        $rdf = htmlspecialchars($rdf, ENT_QUOTES);
        $rdf = str_replace(' ', '&nbsp;', $rdf);
        $rdf = nl2br($rdf);
        echo $rdf;
    }

    /**
     * Writes the RDF serialization of the Model_Memory as HTML table.
     *
     * @access public
     */
    function writeAsHtmlTable()
    {
        RDF_Util::writeHTMLTable($this);
    }

    /**
     * Writes the RDF serialization of the Model_Memory as HTML table.
     *
     * @access public
     * @return string
     */
    function writeRDFToString()
    {
        $ser =& new RDF_Serializer();
        $rdf = &$ser->serialize($this);
        return $rdf;
    }

    /**
     * Saves the RDF,N3 or N-Triple serialization of the Model_Memory to a file.
     * You can decide to which format the model should be serialized by using a
     * corresponding suffix-string as $type parameter. If no $type parameter
     * is placed this method will serialize the model to XML/RDF format.
     * Returns FALSE if the Model_Memory couldn't be saved to the file.
     *
     * @access public 
     * @param  string $filename
     * @param  string $type
     * @throw  PhpError
     * @return boolean
     */
    function saveAs($filename, $type ='rdf')
    {
        // get suffix and create a corresponding serializer
        if ($type=='rdf') { 
            $ser=& new RDF_Serializer();
        } elseif ($type=='nt') { 
            $ser=& new RDF_NTriple_Serializer();
        } elseif ($type=='n3') { 
            $ser=& new RDF_N3_Serializer();
        } else {
            print ('Serializer type not properly defined. Use a string of "rdf","n3" or "nt".');
            return false;
        }

        return $ser->saveAs($this, $filename);
    }

    /**
     * Tests if the Model_Memory contains the given triple.
     * TRUE if the triple belongs to the Model_Memory;
     * FALSE otherwise.
     * To improve the search speed with big Model_Memorys, call index() before seaching.
     *
     * @param object Statement    &$statement
     * @return boolean
     * @access public
     */
    function contains(&$statement)
    {
        if ($this->indexed) {
            // Use index for searching
            $subject = $statement->getSubject();
            if (!isset($this->index[$subject->getLabel()])) {
                return false;
            }

            for ($i = 1; $i <= $this->index[$subject->getLabel()][0]; $i++) {
                $t = $this->triples[$this->index[$subject->getLabel()][$i]];
                if ($t->equals($statement)) {
                    return true;
                }
            }
            return false;
        } else {
            // If there is no index, use linear search.
            foreach($this->triples as $value) {
                if ($value->equals($statement)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Determine if all of the statements in a model are also contained in this Model_Memory.
     * True if all of the statements in $model are also contained in this Model_Memory and false otherwise.
     *
     * @param object Model    &$model
     * @return boolean
     * @access public
     */
    function containsAll(&$model)
    {
        if (is_a($model, 'RDF_Model_Memory')) {
            foreach($model->triples as $statement) {
                if (!$this->contains($statement)) {
                    return false;
                }
            }
            return true;
        } elseif (is_a($model, 'RDF_Model_MDB')) {
            return $model->containsAll($this);
        }

        $errmsg = 'Model expected, got unexpected: '.
            (is_object($model) ? get_class($model) : gettype($model));
        return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
    }

    /**
     * Determine if any of the statements in a model are also contained in this Model_Memory.
     * True if any of the statements in $model are also contained in this Model_Memory and false otherwise.
     *
     * @param object Model    &$model
     * @return boolean
     * @access public
     */
    function containsAny(&$model)
    {
        if (is_a($model, 'RDF_Model_Memory')) {
            foreach($model->triples as $modelStatement) {
                if ($this->contains($modelStatement)) {
                    return true;
                }
            }
            return false;
        } elseif (is_a($model, 'RDF_Model_MDB')) {
            return $model->containsAny($this);
        }

        $errmsg = 'Model expected, got unexpected: '.
            (is_object($model) ? get_class($model) : gettype($model));
        return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
    }

    /**
     * Builds a search index for the statements in the Model_Memory.
     * The index is used by the find() and contains() functions.
     * Performance example using a model with 43000 statements on a Linux machine:
     * Find without index takes 1.7 seconds.
     * Indexing takes 1.8 seconds.
     * Find with index takes 0.001 seconds.
     * So if you want to query a model more then once, build a index first.
     *
     * @access public
     */
    function index()
    {
        if (!$this->indexed) {
            // Delete old index
            $this->index = null;
            // Generate lookup table.
            foreach($this->triples as $k => $t) {
                $s = $t->getSubject();
                if (isset($this->index[$s->getLabel()][0])) {
                    $this->index[$s->getLabel()][0]++;
                } else {
                    $this->index[$s->getLabel()][0] = 1;
                }
                $this->index[$s->getLabel()][$this->index[$s->getLabel()][0]] = $k;
                // Debug
                // echo "Key: ". $s->getLabel() . " Position: ". $this->index[$s->getLabel()][0] . " Statement Key: " .$this->index[$s->getLabel()][$this->index[$s->getLabel()][0]] . "<p>";
            }

            $this->indexed = true;
        }
    }

    /**
     * Returns TRUE if the Model_Memory is indexed.
     *
     * @return boolean
     * @access public
     */
    function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * General method to search for triples.
     * null input for any parameter will match anything.
     * Example:  $result = $m->find( null, null, $node );
     * Finds all triples with $node as object.
     * Returns an empty Model_Memory if nothing is found.
     * To improve the search speed with big Model_Memorys, call index() before seaching.
     *
     * @param object Node    $subject
     * @param object Node    $predicate
     * @param object Node    $object
     * @return object Model_Memory
     * @access public
     * @throws PhpError
     */
    function find($subject, $predicate, $object)
    {
        if ((!is_a($subject, 'RDF_Resource') && $subject != null)
            || (!is_a($predicate, 'RDF_Resource') && $predicate != null)
            || (!is_a($object, 'RDF_Node') && $object != null)
        ) {
            $errmsg = 'Parameters must be subclasses of Node or null';
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        $res =& new RDF_Model_Memory($this->getBaseURI());

        if ($this->size() == 0) {
            return $res;
        }

        if ($subject == null && $predicate == null && $object == null) {
            return $this;
        }

        if ($this->indexed && $subject != null) {
            // Use index for searching
            if (!isset($this->index[$subject->getLabel()])) {
                return $res;
            }
            for ($i = 1; $i <= $this->index[$subject->getLabel()][0]; $i++) {
                $t = $this->triples[$this->index[$subject->getLabel()][$i]];
                if ($this->matchStatement($t, $subject, $predicate, $object)) {
                    $res->add($t);
                }
            }
        } else {
            // If there is no index, use linear search.
            foreach($this->triples as $value) {
                if ($this->matchStatement($value, $subject, $predicate, $object)) {
                    $res->add($value);
                }
            }
        }
        return $res;
    }

    /**
     * Method to search for triples using Perl-style regular expressions.
     * null input for any parameter will match anything.
     * Example:  $result = $m->find_regex( null, null, $regex );
     * Finds all triples where the label of the object node matches the regular expression.
     * Returns an empty Model_Memory if nothing is found.
     *
     * @param string $subject_regex
     * @param string $predicate_regex
     * @param string $object_regex
     * @return object Model_Memory
     * @access public
     */
    function findRegex($subject_regex, $predicate_regex, $object_regex)
    {
        $res =& new RDF_Model_Memory($this->getBaseURI());

        if ($this->size() == 0) {
            return $res;
        }

        if ($subject_regex == null && $predicate_regex == null && $object_regex == null) {
            return $this;
        }

        foreach($this->triples as $value) {
            if (($subject_regex == null || preg_match($subject_regex, $value->subj->getLabel()))
                && ($predicate_regex == null || preg_match($predicate_regex, $value->pred->getLabel()))
                && ($object_regex == null || preg_match($object_regex, $value->obj->getLabel()))
            ) {
                $res->add($value);
            }
        }

        return $res;
    }

    /**
     * Returns all tripels of a certain vocabulary.
     * $vocabulary is the namespace of the vocabulary inluding a # : / char at the end.
     * e.g. http://www.w3.org/2000/01/rdf-schema#
     * Returns an empty Model_Memory if nothing is found.
     *
     * @param string $vocabulary
     * @return object Model_Memory
     * @access public
     */
    function findVocabulary($vocabulary)
    {
        if ($this->size() == 0) {
            return $res;
        }
        if ($vocabulary == null || $vocabulary == "") {
            return $this;
        }

        $res =& new RDF_Model_Memory($this->getBaseURI());

        foreach($this->triples as $value) {
            if (RDF_Util::getNamespace($value->getPredicate()) == $vocabulary) {
                $res->add($value);
            }
        }
        return $res;
    }

    /**
     * Searches for triples and returns the first matching statement.
     * null input for any parameter will match anything.
     * Example:  $result = $m->findFirstMatchingStatement( null, null, $node );
     * Returns the first statement of the Model_Memory where the object equals $node.
     * Returns an null if nothing is found.
     *
     * @param object Node    $subject
     * @param object Node    $predicate
     * @param object Node    $object
     * @return object Statement
     * @access public
     */
    function findFirstMatchingStatement($subject, $predicate, $object)
    {
        $res = $this->find($subject, $predicate, $object);
        if ($res->size() != 0) {
            return $res->triples[0];
        } else {
            return null;
        }
    }

    /**
     * Searches for triples and returns the number of matches.
     * null input for any parameter will match anything.
     * Example:  $result = $m->findCount( null, null, $node );
     * Finds all triples with $node as object.
     *
     * @param object Node    $subject
     * @param object Node    $predicate
     * @param object Node    $object
     * @return integer
     * @access public
     */
    function findCount($subject, $predicate, $object)
    {
        $res = $this->find($subject, $predicate, $object);
        return $res->size();
    }

    /**
     * General method to replace nodes of a Model_Memory.
     * null input for any parameter will match nothing.
     * Example:  $m->replace($node, null, $node, $replacement);
     * Replaces all $node objects beeing subject or object in
     * any triple of the Model_Memory with the $needle node.
     *
     * @param object Node    $subject
     * @param object Node    $predicate
     * @param object Node    $object
     * @param object Node    $replacement
     * @access public
     * @throws PhpError
     */
    function replace($subject, $predicate, $object, $replacement)
    {
        if ((!is_a($replacement, 'RDF_Node'))
            || (!is_a($subject, 'RDF_Resource') && $subject != null)
            || (!is_a($predicate, 'RDF_Resource') && $predicate != null)
            || (!is_a($object, 'RDF_Node') && $object != null)
        ) {
            $errmsg = 'Parameters must be subclasses of Node or null';
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        if ($this->size() == 0) {
            continue;
        }

        foreach($this->triples as $key => $value) {
            if ($this->triples[$key]->subj->equals($subject)) {
                $this->triples[$key]->subj = $replacement;
                $this->indexed = false;
            }
            if ($this->triples[$key]->pred->equals($predicate)) {
                $this->triples[$key]->pred = $replacement;
            }
            if ($this->triples[$key]->obj->equals($object)) {
                $this->triples[$key]->obj = $replacement;
            }
        }
    }

    /**
     * Internal method that checks, if a statement matches a S, P, O or null combination.
     * null input for any parameter will match anything.
     *
     * @param object Statement    $statement
     * @param object Node    $subject
     * @param object Node    $predicate
     * @param object Node    $object
     * @return boolean
     * @access private
     */
    function matchStatement($statement, $subject, $predicate, $object)
    {
        if ($subject != null && !$statement->subj->equals($subject)) {
            return false;
        }

        if ($predicate != null && !($statement->pred->equals($predicate))) {
            return false;
        }

        if ($object != null && !($statement->obj->equals($object))) {
            return false;
        }

        return true;
    }

    /**
     * Internal method, that returns a resource URI that is unique for the Model_Memory.
     * URIs are generated using the base_uri of the Model_Memory, the prefix and a unique number.
     *
     * @param string $prefix
     * @return string
     * @access private
     */

    function getUniqueResourceURI($prefix)
    {
        $counter = 1;
        while (true) {
            $uri = $this->getBaseURI() . $prefix . $counter;
            $tempbNode =& RDF_BlankNode::factory($uri);
            $res1 = $this->find($tempbNode, null, null);
            $res2 = $this->find(null, null, $tempbNode);
            if ($res1->size() == 0 && $res2->size() == 0) {
                return $uri;
            }
            ++$counter;
        }
    }

    /**
     * Checks if two models are equal.
     * Two models are equal if and only if the two RDF graphs they represent are isomorphic.
     *
     * Warning: This method doesn't work correct with models where the same blank node has different
     * identifiers in the two models. We will correct this in a future version.
     *
     * @access public
     * @param object model &$that
     * @throws phpErrpr
     * @return boolean
     */

    function equals(&$that)
    {
        if (!is_a($that, 'RDF_Model')) {
            $errmsg = 'Model expected, got unexpected: '.
                (is_object($model) ? get_class($model) : gettype($model));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        if ($this->size() != $that->size()) {
            return false;
        }

        if (!$this->containsAll($that)) {
            return false;
        }
        return true;
    }

    /**
     * Returns a new Model_Memory that is the set-union of the Model_Memory with another model.
     * Duplicate statements are removed. If you want to allow duplicates, use addModel() which is much faster.
     *
     * The result of taking the set-union of two or more RDF graphs (i.e. sets of triples)
     * is another graph, which we will call the merge of the graphs.
     * Each of the original graphs is a subgraph of the merged graph. Notice that when forming
     * a merged graph, two occurrences of a given uriref or literal as nodes in two different
     * graphs become a single node in the union graph (since by definition they are the same
     * uriref or literal) but blank nodes are not 'merged' in this way; and arcs are of course
     * never merged. In particular, this means that every blank node in a merged graph can be
     * identified as coming from one particular graph in the original set of graphs.
     *
     * Notice that one does not, in general, obtain the merge of a set of graphs by concatenating
     * their corresponding N-triples documents and constructing the graph described by the merged
     * document, since if some of the documents use the same node identifiers, the merged document
     * will describe a graph in which some of the blank nodes have been 'accidentally' merged.
     * To merge Ntriples documents it is necessary to check if the same nodeID is used in two or
     * more documents, and to replace it with a distinct nodeID in each of them, before merging the
     * documents. (Not implemented yet !!!!!!!!!!!)
     *
     * @param object Model    $model
     * @return object Model_Memory
     * @access public
     * @throws phpErrpr
     */
    function &unite(&$model)
    {
        if (!is_a($model, 'RDF_Model')) {
            $errmsg = 'Model expected, got unexpected: '.
                (is_object($model) ? get_class($model) : gettype($model));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        $res = $this;
        $res->indexed = false;

        if (is_a($model, 'RDF_Model_Memory')) {
            foreach($model->triples as $value) {
                $res->addWithoutDuplicates($value);
            }
        } elseif (is_a($model, 'RDF_Model_MDB')) {
            $Model_Memory = &$model->getMemModel();
            foreach($Model_Memory->triples as $value) {
                $res->addWithoutDuplicates($value);
            }
        }

        return $res;
    }

    /**
     * Returns a new Model_Memory that is the subtraction of another model from this Model_Memory.
     *
     * @param object Model    $model
     * @return object Model_Memory
     * @access public
     * @throws phpErrpr
     */

    function &subtract(&$model)
    {
        if (!is_a($model, 'RDF_Model')) {
            $errmsg = 'Model expected, got unexpected: '.
                (is_object($model) ? get_class($model) : gettype($model));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        $res = $this;
        $res->indexed = false;

        if (is_a($model, 'RDF_Model_Memory')) {
            foreach($model->triples as $value) {
                $res->remove($value);
            }
        } elseif (is_a($model, 'RDF_Model_MDB')) {
            $Model_Memory = &$model->getMemModel();
            foreach($Model_Memory->triples as $value) {
                $res->remove($value);
            }
        }

        return $res;
    }

    /**
     * Returns a new Model_Memory containing all the statements which are in both this Model_Memory and another.
     *
     * @param object Model    $model
     * @return object Model_Memory
     * @access public
     * @throws phpErrpr
     */
    function &intersect(&$model)
    {
        if (!is_a($model, 'RDF_Model')) {
            $errmsg = 'Model expected, got unexpected: '.
                (is_object($model) ? get_class($model) : gettype($model));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        $res =& new RDF_Model_Memory($this->getBaseURI());

        if (is_a($model, 'RDF_Model_Memory')) {
            foreach($model->triples as $value) {
                if ($this->contains($value)) {
                    $res->add($value);
                }
            }
        } elseif (is_a($model, 'RDF_Model_MDB')) {
            $Model_Memory = &$model->getMemModel();
            foreach($Model_Memory->triples as $value) {
                if ($this->contains($value)) {
                    $res->add($value);
                }
            }
        }

        return $res;
    }

    /**
     * Adds another model to this Model_Memory.
     * Duplicate statements are not removed.
     * If you don't want duplicates, use unite().
     *
     * @param object Model    $model
     * @access public
     * @throws phpErrpr
     */
    function addModel(&$model)
    {
        if (!is_a($model, 'RDF_Model')) {
            $errmsg = 'Model expected, got unexpected: '.
                (is_object($model) ? get_class($model) : gettype($model));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }

        $this->index();
        $blankNodes_tmp = array();

        if (is_a($model, 'RDF_Model_Memory')) {
            foreach($model->triples as $value) {
                $this->_addStatementFromAnotherModel($value, $blankNodes_tmp);
            }
        } elseif (is_a($model, 'RDF_Model_MDB')) {
            $Model_Memory =& $model->getMemModel();
            foreach($Model_Memory->triples as $value) {
                $this->_addStatementFromAnotherModel($value, $blankNodes_tmp);
            }
        }
    }

    /**
     * Reifies the Model_Memory.
     * Returns a new Model_Memory that contains the reifications of all statements of this Model_Memory.
     *
     * @access public
     * @return object Model_Memory
     */
    function &reify()
    {
        $res =& new RDF_Model_Memory($this->getBaseURI());

        foreach($this->triples as $statement) {
            $pointer = &$statement->reify($res);
            $res->addModel($pointer);
        }
        return $res;
    }

    /**
     * Returns a StatementIterator for traversing the Model_Memory.
     *
     * @access public
     * @return object StatementIterator
     */
    function &getStatementIterator()
    {
        return new RDF_StatementIterator($this);
    }

    /**
     * Close the Model_Memory and free up resources held.
     *
     * @access public
     */
    function close()
    {
        unset($this->baseURI);
        unset($this->triples);
    }
} // end: Model_Memory
?>