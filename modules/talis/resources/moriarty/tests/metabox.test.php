<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'metabox.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';
require_once MORIARTY_TEST_DIR . 'graph.test.php';
class MetaboxTest extends GraphTest {

  function make_graph($uri, $credentials = null) {
    return new Metabox($uri, $credentials);
  }

  function test_apply_versioned_changeset_rdfxml_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta/changesets", $fake_request );

    $g = new Metabox("http://example.org/store/meta", new FakeCredentials());
    $g->request_factory = $fake_request_factory;

    $response = $g->apply_versioned_changeset_rdfxml( $this->_empty_changeset );
    $this->assertEquals( "user:pwd" , $fake_request->get_auth() );
  }


}
?>
