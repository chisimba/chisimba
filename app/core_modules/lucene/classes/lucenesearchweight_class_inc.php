<?php

/**
 * Calculate query weights and build query scorers.
 *
 * A Weight is constructed by a query Query->createWeight().
 * The sumOfSquaredWeights() method is then called on the top-level
 * query to compute the query normalization factor Similarity->queryNorm(float).
 * This factor is then passed to normalize(float).  At this point the weighting
 * is complete.
 *
 * @category   Chisimba
 * @package    lucene
 * @subpackage Search
 * @copyright  AVOIR UWC GNU/GPL
 */
abstract class lucenesearchweight
{
    /**
     * The weight for this query.
     *
     * @return float
     */
    abstract public function getValue();

    /**
     * The sum of squared weights of contained query clauses.
     *
     * @return float
     */
    abstract public function sumOfSquaredWeights();

    /**
     * Assigns the query normalization factor to this.
     *
     * @param $norm
     */
    abstract public function normalize($norm);
}
