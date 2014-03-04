<?php
/**
 * (c) 2010 phpgrease.net
 *
 * For licensing terms, plese see license.txt which should distribute with this source
 *
 * Explicit usage:
 *          $clause = new PandraClauseNumericRange(array('from' => 0, 'to' => 200));
 *          $clause->match();
 *
 * Query Callback
 *          $q = new PandraQuery();
 *          $q->NumericRange(array('from' => 0, 'to' => 200));
 *
 * @package Pandra
 * @link http://www.phpgrease.net/projects/pandra
 * @author Michael Pearson <pandra-support@phpgrease.net>
 */

class PandraClauseNumericRange extends PandraClause {

    private $_args = array();

    public function __construct() {
        $this->_args = func_get_arg(0);
    }

    public function match($value) {
        $fromMatch = (isset($this->_args['from'])) ? is_numeric($value) && $this->_args['from'] <= $value : is_numeric($value);
        $toMatch = (isset($this->_args['to'])) ? is_numeric($value) && $this->_args['to'] >= $value : is_numeric($value);
        return $fromMatch && $toMatch;
    }
}
?>