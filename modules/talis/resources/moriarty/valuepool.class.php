<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR. 'store.class.php';

class ValuePool {
  var $bigfootSparqlService;
  var $bigfootMetabox;

  function get_value($pool_uri) {
    $values = $this->get_candidate_values($pool_uri);
    $index = rand(0, count($values) - 1);

    if ( $this->select_value( $pool_uri, $values[$index], 100) ) {
      return $values[$index];
    }
  }


  function get_candidate_values($pool_uri, $max = 5) {
    if ( !isset( $this->bigfootSparqlService) ) {
      $bigfoot = new Store(STORE_URI);
      $this->bigfootSparqlService = $bigfoot->get_sparql_service();
    }
    $query="PREFIX p: <http://purl.org/vocab/value-pools/schema#> CONSTRUCT {<$pool_uri> p:value ?v . } WHERE { <$pool_uri> p:value ?v . } LIMIT $max";

    $triples = $this->bigfootSparqlService->graph_to_triple_list($query );
    
    $candidates = array();
    if ( is_array( $triples ) ) {
      foreach ($triples as $triple) {
        if ( $triple['p'] = 'http://purl.org/vocab/value-pools/schema#value' && $triple['o_type']=='literal') {
          array_push($candidates, $triple['o']);
        }
      }
    }

    return $candidates;

  }

  function select_value($pool_uri, $value, $pool_size) {
    if ( !isset( $this->bigfootMetabox) ) {
      $bigfoot = new Store(STORE_URI);
      $this->bigfootMetabox = $bigfoot->get_metabox();
    }

    $changeset = '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
    xmlns:cs="http://purl.org/vocab/changeset/schema#">
  <cs:ChangeSet>
    <cs:subjectOfChange rdf:resource="' . $pool_uri . '"/>
    <cs:creatorName>pool</cs:creatorName>
    <cs:changeReason>Selecting key</cs:changeReason>
    <cs:removal>
      <rdf:Statement>
        <rdf:subject rdf:resource="' . $pool_uri . '"/>
        <rdf:predicate rdf:resource="http://purl.org/vocab/value-pools/schema#value"/>
        <rdf:object>' . $value . '</rdf:object>
      </rdf:Statement>
    </cs:removal>
    <cs:addition>
      <rdf:Statement>
        <rdf:subject rdf:resource="' . $pool_uri . '"/>
        <rdf:predicate rdf:resource="http://purl.org/vocab/value-pools/schema#value"/>
        <rdf:object>' . ($pool_size + $value) . '</rdf:object>
      </rdf:Statement>
    </cs:addition>
  </cs:ChangeSet>
</rdf:RDF>';

    $response = $this->bigfootMetabox->apply_changeset_rdfxml( $changeset );
    // echo "<pre>";
    // echo htmlspecialchars(print_r($response, true));
    // echo "</pre>";
    return true;
  }

}
?>
