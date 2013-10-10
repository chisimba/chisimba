<?php
/**
 * (c) 2010 phpgrease.net
 *
 * For licensing terms, plese see license.txt which should distribute with this source
 *
 * Explicit usage:
 *          $clause = new PandraClauseLiteral('abcdefg');
 *          $clause->match();
 *
 * Query Callback
 *          $q = new PandraQuery();
 *          $q->Literal('abcdefg'));
 *
 * @package Pandra
 * @link http://www.phpgrease.net/projects/pandra
 * @author Michael Pearson <pandra-support@phpgrease.net>
 */

class PandraClauseLiteral extends PandraClause {

    private $_arg = NULL;

    private $_strict = FALSE;

    /**
     * @param mixed $arg value to match against
     * @param bool $strict match identical type and value (default false)
     */
    public function __construct() {
        $this->_arg = func_get_arg(0);
        if (func_num_args() > 1) {
            $this->_strict = func_get_arg(1);
        }
    }

    public function match($value) {
        return $this->_strict ? 
                    $value === $this->_arg :
                    $value == $this->_arg;
    }
}
?>