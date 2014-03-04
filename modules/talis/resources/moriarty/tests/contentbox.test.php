<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'contentbox.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';

class ContentboxTest extends PHPUnit_Framework_TestCase {
  var $_simple_rss_feed = '<?xml version="1.0" encoding="utf-8"?>
<rdf:RDF xmlns="http://purl.org/rss/1.0/" xmlns:dct="http://purl.org/dc/terms/" xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:os="http://a9.com/-/spec/opensearch/1.1/" xmlns:sioc="http://rdfs.org/sioc/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/">
  <channel rdf:about="http://example.org/store/items?query=scooby&amp;max=10&amp;offset=0">
    <title>scooby</title>
    <link>http://example.org/store/items?query=scooby&amp;max=10&amp;offset=0</link>
    <description>Results of a search for scooby on store</description>
    <items>
      <rdf:Seq rdf:about="urn:uuid:daeec0bd-efae-4542-9062-14be77745201">
        <rdf:li resource="http://jingyeluo.blogspot.com/2006/11/appdomain-process-and-components.html"/>
        <rdf:li resource="http://jingyeluo.blogspot.com/2006/10/export-import-goodie-fromto-photoshop.html"/>
      </rdf:Seq>
    </items>
    <os:startIndex>0</os:startIndex>
    <os:itemsPerPage>10</os:itemsPerPage>
    <os:totalResults>45</os:totalResults>
  </channel>

  <item rdf:about="http://jingyeluo.blogspot.com/2006/11/appdomain-process-and-components.html">
    <title>Item</title>
    <link>http://jingyeluo.blogspot.com/2006/11/appdomain-process-and-components.html</link>
    <dct:modified>2006-11-11T20:52:30Z</dct:modified>
    <relevance:score>1.0</relevance:score>
    <dc:subject>.net</dc:subject>
    <rdf:type rdf:resource="http://rdfs.org/sioc/ns#Post"/>
    <dc:title>AppDomain, process and components...</dc:title>
    <rdf:type rdf:resource="http://rdfs.org/sioc/ns#Container"/>
    <dc:identifier>tag:blogger.com,1999:blog-6082242.post-6256881250483349557</dc:identifier>
    <dc:creator>Jingye</dc:creator>
    <sioc:links_to>
      <rdf:Description rdf:about="http://en.wikipedia.org/wiki/Component"/>
    </sioc:links_to>
    <sioc:links_to>
      <rdf:Description rdf:about="http://www.gotdotnet.com/team/clr/AppdomainFAQ.aspx#_Toc514058484"/>
    </sioc:links_to>
  </item>
  <item rdf:about="http://jingyeluo.blogspot.com/2006/10/export-import-goodie-fromto-photoshop.html">
    <title>Item</title>
    <link>http://jingyeluo.blogspot.com/2006/10/export-import-goodie-fromto-photoshop.html</link>
    <rdf:type rdf:resource="http://rdfs.org/sioc/ns#Post"/>
    <sioc:links_to>
      <rdf:Description rdf:about="http://www.imphotography.com/downloads/installactions.htm"/>
    </sioc:links_to>
    <dc:identifier>tag:blogger.com,1999:blog-6082242.post-360084643546808887</dc:identifier>
    <dc:title>Export &amp; Import Goodie from/to Photoshop</dc:title>
    <dct:modified>2006-10-27T17:00:40Z</dct:modified>
    <dc:subject>photoshop tips</dc:subject>
    <sioc:links_to>
      <rdf:Description rdf:about="http://photos1.blogger.com/blogger2/3778/742/1600/screenshot.gif"/>
    </sioc:links_to>
    <relevance:score>1.0</relevance:score>
    <rdf:type rdf:resource="http://rdfs.org/sioc/ns#Container"/>
    <dc:creator>Jingye</dc:creator>
    <sioc:links_to>
      <rdf:Description rdf:about="http://jingyeluo.blogspot.com/2006/10/photoshop-action-and-batch-to-watermark.html#links"/>
    </sioc:links_to>
  </item>
</rdf:RDF>';

  function test_search_gets_contentbox_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items");
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search( 'scooby' );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_search_passes_max_parameter() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=45&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items");
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search( 'scooby', 45);
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_search_passes_offset_parameter() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=45&offset=12", $fake_request );

    $cb = new Contentbox("http://example.org/store/items");
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search( 'scooby', 45, 12);
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_search_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items");
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search( 'scooby' );
    $this->assertTrue( in_array('Accept: application/rss+xml', $fake_request->get_headers() ) );
  }

  function test_search_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search( 'scooby' );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_search_to_triple_list_gets_contentbox_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items");
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search_to_triple_list( 'scooby' );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_search_to_triple_list_passes_max_parameter() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=45&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items");
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search_to_triple_list( 'scooby', 45);
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_search_to_triple_list_passes_offset_parameter() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=45&offset=12", $fake_request );

    $cb = new Contentbox("http://example.org/store/items");
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search_to_triple_list( 'scooby', 45, 12);
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_search_to_triple_list_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items");
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search_to_triple_list( 'scooby' );
    $this->assertTrue( in_array('Accept: application/rss+xml', $fake_request->get_headers() ) );
  }

  function test_search_to_triple_list_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $response = $cb->search_to_triple_list( 'scooby' );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }

  function test_search_to_triple_list() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $triples = $cb->search_to_triple_list( 'scooby' );
    $this->assertTrue( is_array( $triples ) );
  }

  function test_search_to_triple_list_parses_response() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $triples = $cb->search_to_triple_list( 'scooby' );
    $this->assertEquals( 38, count( $triples ) );
  }

  function test_search_to_resource_list_parses_channel_title() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $resources = $cb->search_to_resource_list( 'scooby', 10, 0);
    $this->assertEquals( "scooby", $resources->title );
  }
  function test_search_to_resource_list_parses_start_index() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $resources = $cb->search_to_resource_list( 'scooby', 10, 0);
    $this->assertEquals( 0, $resources->start_index );
  }

  function test_search_to_resource_list_parses_items_per_page() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $resources = $cb->search_to_resource_list( 'scooby', 10, 0);
    $this->assertEquals( 10, $resources->items_per_page );
  }

  function test_search_to_resource_list_parses_total_results() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $resources = $cb->search_to_resource_list('scooby', 10, 0);
    $this->assertEquals( 45, $resources->total_results );
  }


  function test_search_to_resource_list_parses_channel_description() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $resources = $cb->search_to_resource_list('scooby', 10, 0);
    $this->assertEquals( 'Results of a search for scooby on store', $resources->description );
  }

  function test_search_to_resource_list_parses_items_as_things() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $resources = $cb->search_to_resource_list('scooby', 10, 0);
    $this->assertEquals( 2, count($resources->items) );
  }

  function test_search_to_resource_list_parses_items() {
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_simple_rss_feed;

    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/items?query=scooby&max=10&offset=0", $fake_request );

    $cb = new Contentbox("http://example.org/store/items", new FakeCredentials());
    $cb->request_factory = $fake_request_factory;

    $resources = $cb->search_to_resource_list('scooby', 10, 0);
    $this->assertEquals( 2, count($resources->items) );
    $this->assertEquals( "AppDomain, process and components...", $resources->items[0]['http://purl.org/dc/elements/1.1/title'][0] );
    $this->assertEquals( "Export & Import Goodie from/to Photoshop", $resources->items[1]['http://purl.org/dc/elements/1.1/title'][0] );
  }

}

?>
