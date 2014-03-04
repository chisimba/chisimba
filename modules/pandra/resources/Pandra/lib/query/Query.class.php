<?php
/**
 * (c) 2010 phpgrease.net
 *
 * For licensing terms, plese see license.txt which should distribute with this source
 *
 * @package Pandra
 * @link http://www.phpgrease.net/projects/pandra
 * @author Michael Pearson <pandra-support@phpgrease.net>
 */

class PandraQuery implements ArrayAccess {

    static private $_instance = NULL;

    // graph helpers
    const CONTEXT_SCF = 'PandraSuperColumnFamily';
    const CONTEXT_CF = 'PandraColumnFamily';
    const CONTEXT_SC = 'PandraSuperColumn';
    const CONTEXT_C = 'PandraColumn';

    const HYDRATE_NONE = 0;

    const HYDRATE_ARRAY = 1;

    const HYDRATE_MODEL = 2;

    private $_graph = array();

    private $_clauseChain = NULL;

    private $_context = NULL;

    private $_invoker = NULL;

    private $_ptr = NULL;

    private $_start = NULL;

    private $_finish = NULL;

    public function offsetSet($offset, $value) {}

    public function offsetExists($offset) {}

    public function offsetUnset($offset) {}

    public function offsetGet($offset) {

        // let the user send literals through etc. we'll just cast them to
        // a literal clause type
        if (!($offset instanceof PandraClause)) {
            $match = $offset;
            $offset = new PandraClauseLiteral($match);
        }

        $this->_clauseChain[] = $offset;

        // figure out what context we're in, and advance.
        switch ($this->_context) {
            case self::CONTEXT_SCF:
                return $this->graphContext(self::CONTEXT_SC);
                break;

            case self::CONTEXT_SC:
            case self::CONTEXT_CF:
                return $this->graphContext(self::CONTEXT_C);
                break;
            default:
                break;
        }

        throw new RuntimeException("Graph bounds exceeded.  Try trimming an array dimension");
    }

    /**
     * Singleton instantiation of this object
     * @return PandraCore self instance
     */
    static public function getInstance() {
        if (NULL === self::$_instance) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }
        return self::$_instance;
    }

    // @todo move to callStatic with PHP5.3 upgrade
    // anonymous claus calls
    public function __call($class, $args) {
        $class = 'PandraClause'.$class;
        if (class_exists($class)) {
            $clause = new $class(array_pop($args));
            $this->_clauseChain[] = $clause;
            return $this;
        } else {
            return NULL;
        }
    }

    /**
     * Invoker acts as the top level reference point for the query and also a
     * container prototype for the slice result
     * @param PandraColumnContainer $invoker invoking parent of the query
     */
    public function setInvoker(PandraColumnContainer $invoker) {
        // Wherever query is 'invoked' is considered the invoking parent.
        if ($this->_invoker === NULL) {
            $this->_invoker = $invoker;
        }
    }

    public function graphContext($context) {
        $this->_context = $context;

        // suck the clause chain into our new context
        $clauses = array('nameclause' => array(), 'child' => array());
        foreach ($this->_clauseChain as $clause) {
            $clauses['nameclause'][] = clone $clause;
        }

        switch ($context) {
            case self::CONTEXT_CF :
                $this->_graph[$context] = $clauses;
                $this->_ptr = &$this->_graph[$context]['child'];
                break;

           case self::CONTEXT_C :
               // columns are a child of the last container context (CF or SCF)
               $this->_ptr = $clauses;
               break;
        }

        $this->_clauseChain = NULL;
        return $this;
    }

    public function key($key) {
        $this->keyStart($key);
        $this->keyFinish($key);
    }

    public function keyStart($key) {
        $this->_start = $keys;
    }

    public function keyFinish($key) {
        $this->_finish = $keys;
    }

    // @todo
    public function load($hydration) {

    }
}
?>