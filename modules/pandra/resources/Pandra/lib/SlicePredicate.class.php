<?php
/**
 * PandraSlicePredicate
 *
 * A SlicePredicate is similar to a mathematic predicate, which is described as
 * "a property that the elements of a set have in common."
 *
 * They are used in most Get queries of Cassandra's API.
 * 
 * @author Jordan Pittier <jordan@rezel.net>
 * @author Michael Pearson <pandra-support@phpgrease.net>
 * @copyright 2010 phpgrease.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version 0.2.1
 * @package pandra
 */

class PandraSlicePredicate extends cassandra_SlicePredicate {

    const TYPE_RANGE = 'SliceRange';

    const TYPE_COLUMNS = 'Columns';

    /* @var string Type of Slice Predicate, either 'SliceRange' or 'Columns' */
    protected $_slicePredicateType = self::TYPE_RANGE;

    /**
     * SlicePredicate constructor
     * @param string $slicePredicateType Type of Slice Predicate, either 'SliceRange' or 'Columns'
     * @param array $predicateAttribute Attribute of the SlicePredicate, its value is interpreted according to the type of SlicePredicate that is being built
     */
    public function __construct($slicePredicateType = self::TYPE_RANGE, array $predicateAttribute = array()) {
        parent::__construct();
        if ($slicePredicateType == self::TYPE_COLUMNS || $slicePredicateType == self::TYPE_RANGE) {
            $this->_slicePredicateType = $slicePredicateType;
        }
        $this->buildPredicateAttribute($predicateAttribute);
    }

    /**
     * Set the type of SlicePredicate we are dealing with, only if the new type is different from the previous one
     * @param string $slicePredicateType Type of Slice Predicate, either 'SliceRange' or 'Columns'
     */
    public function setSlicePredicateType($slicePredicateType) {

        if ($slicePredicateType == self::TYPE_RANGE && $this->_slicePredicateType !== self::TYPE_RANGE) {
            $this->_slicePredicateType = $slicePredicateType;
            $this->column_names = NULL;

        } else if ($slicePredicateType == self::TYPE_COLUMNS && $this->_slicePredicateType !== self::TYPE_COLUMNS) {
            $this->_slicePredicateType = $slicePredicateType;
            $this->slice_range = NULL;
        } else {
            $msg = 'Unsupported Predicate Type';
            PandraLog::crit($msg);
            throw new RuntimeException($msg);
        }
    }

    public function buildPredicateAttribute(array $predicateAttribute) {

        if ($this->_slicePredicateType === self::TYPE_COLUMNS) {
            $this->slice_range = NULL;

            if (empty($predicateAttribute)) {
                // Warn that there's no point in building an empty predicate
                PandraLog::warning('Empty Predicate : an empty result set will be returned');
            }
            $this->column_names = $predicateAttribute;

        } elseif ($this->_slicePredicateType === self::TYPE_RANGE) {
            $this->column_names = NULL;
            $this->slice_range = new cassandra_SliceRange(
                    array('start' => '',
                            'finish' => '',
                            'reversed' => false,
                            'count' => DEFAULT_ROW_LIMIT)
            );

            foreach ($predicateAttribute as $key => $value) {
                if (isset($this->slice_range->$key)) $this->slice_range->$key = $value;
            }
        } else {
            $msg = 'Unsupported Predicate Type';
            PandraLog::crit($msg);
            throw new RuntimeException($msg);
        }
    }
}
?>