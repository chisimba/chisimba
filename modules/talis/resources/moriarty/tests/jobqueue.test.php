<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'jobqueue.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';
require_once MORIARTY_ARC_DIR . 'ARC2.php';


class JobQueueTest extends PHPUnit_Framework_TestCase {
  function test_schedule_reset_data_posts_to_job_queue_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_schedule_reset_data_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( in_array('Content-Type: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_schedule_reset_data_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }

  function test_schedule_reset_data_uses_auth() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs", new FakeCredentials());
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }


  function test_schedule_reset_data_posts_rdfxml_where_triples_all_have_same_subject() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser = ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $subjects = array();
    foreach ($triples as $triple) {
      $subject = $triple['s'];
      $subjects[$subject] = 1;
    }

    $this->assertEquals( 1 , count($subjects));
  }


  function test_schedule_reset_data_posts_rdfxml_with_a_single_jobtype() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#jobType') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_schedule_reset_data_posts_rdfxml_with_a_single_start_time() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_schedule_reset_data_posts_rdfxml_with_a_type_of_job_request() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://schemas.talis.com/2006/bigfoot/configuration#JobRequest') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_reset_data_posts_rdfxml_with_a_job_type_of_reset_data_job() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#jobType' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://schemas.talis.com/2006/bigfoot/configuration#ResetDataJob') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_reset_data_posts_rdfxml_with_correct_start_time() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  gmmktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime' && $triple['o_type'] == 'literal' && $triple['o'] == '2007-08-06T10:11:00Z') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_reset_data_posts_rdfxml_with_supplied_label() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data(  gmmktime(10, 11, 0, 8, 6, 2007), "My job" );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://www.w3.org/2000/01/rdf-schema#label' && $triple['o_type'] == 'literal' && $triple['o'] == 'My job') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }
  function test_schedule_reset_data_uses_current_time_if_none_supplied() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reset_data();

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $time = 0;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime' && $triple['o_type'] == 'literal') {
        $time = strtotime($triple['o']);
        break;
      }
    }

    $this->assertTrue( gmmktime() - $time < 5);
  }


  function test_schedule_snapshot_posts_to_job_queue_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_schedule_snapshot_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( in_array('Content-Type: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_schedule_snapshot_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }

  function test_schedule_snapshot_uses_auth() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs", new FakeCredentials());
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }


  function test_schedule_snapshot_posts_rdfxml_where_triples_all_have_same_subject() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser = ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $subjects = array();
    foreach ($triples as $triple) {
      $subject = $triple['s'];
      $subjects[$subject] = 1;
    }

    $this->assertEquals( 1 , count($subjects));
  }


  function test_schedule_snapshot_posts_rdfxml_with_a_single_jobtype() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#jobType') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_schedule_snapshot_posts_rdfxml_with_a_single_start_time() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_schedule_snapshot_posts_rdfxml_with_a_type_of_job_request() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://schemas.talis.com/2006/bigfoot/configuration#JobRequest') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_snapshot_posts_rdfxml_with_a_job_type_of_snapshot_job() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#jobType' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://schemas.talis.com/2006/bigfoot/configuration#SnapshotJob') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_snapshot_posts_rdfxml_with_correct_start_time() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  gmmktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime' && $triple['o_type'] == 'literal' && $triple['o'] == '2007-08-06T10:11:00Z') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_snapshot_posts_rdfxml_with_supplied_label() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot(  gmmktime(10, 11, 0, 8, 6, 2007), "My job" );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://www.w3.org/2000/01/rdf-schema#label' && $triple['o_type'] == 'literal' && $triple['o'] == 'My job') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }
  function test_schedule_snapshot_uses_current_time_if_none_supplied() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_snapshot();

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $time = 0;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime' && $triple['o_type'] == 'literal') {
        $time = strtotime($triple['o']);
        break;
      }
    }

    $this->assertTrue( gmmktime() - $time < 5);
  }

  function test_schedule_reindex_posts_to_job_queue_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_schedule_reindex_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( in_array('Content-Type: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_schedule_reindex_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }

  function test_schedule_reindex_uses_auth() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs", new FakeCredentials());
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }


  function test_schedule_reindex_posts_rdfxml_where_triples_all_have_same_subject() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser = ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $subjects = array();
    foreach ($triples as $triple) {
      $subject = $triple['s'];
      $subjects[$subject] = 1;
    }

    $this->assertEquals( 1 , count($subjects));
  }


  function test_schedule_reindex_posts_rdfxml_with_a_single_jobtype() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#jobType') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_schedule_reindex_posts_rdfxml_with_a_single_start_time() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_schedule_reindex_posts_rdfxml_with_a_type_of_job_request() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://schemas.talis.com/2006/bigfoot/configuration#JobRequest') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_reindex_posts_rdfxml_with_a_job_type_of_reindex_job() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#jobType' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://schemas.talis.com/2006/bigfoot/configuration#ReindexJob') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_reindex_posts_rdfxml_with_correct_start_time() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  gmmktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime' && $triple['o_type'] == 'literal' && $triple['o'] == '2007-08-06T10:11:00Z') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_reindex_posts_rdfxml_with_supplied_label() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex(  gmmktime(10, 11, 0, 8, 6, 2007), "My job" );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://www.w3.org/2000/01/rdf-schema#label' && $triple['o_type'] == 'literal' && $triple['o'] == 'My job') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }
  function test_schedule_reindex_uses_current_time_if_none_supplied() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_reindex();

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $time = 0;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime' && $triple['o_type'] == 'literal') {
        $time = strtotime($triple['o']);
        break;
      }
    }

    $this->assertTrue( gmmktime() - $time < 5);
  }

  function test_schedule_restore_posts_to_job_queue_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore( 'http://example.org/snapshot', mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_schedule_restore_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot',  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( in_array('Content-Type: application/rdf+xml', $fake_request->get_headers() ) );
  }

  function test_schedule_restore_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot',  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }

  function test_schedule_restore_uses_auth() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs", new FakeCredentials());
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot',  mktime(10, 11, 0, 8, 6, 2007) );
    $this->assertEquals( "user:pwd", $fake_request->get_auth() );
  }


  function test_schedule_restore_posts_rdfxml_where_triples_all_have_same_subject() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot',  mktime(10, 11, 0, 8, 6, 2007) );

    $parser = ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $subjects = array();
    foreach ($triples as $triple) {
      $subject = $triple['s'];
      $subjects[$subject] = 1;
    }

    $this->assertEquals( 1 , count($subjects));
  }


  function test_schedule_restore_posts_rdfxml_with_a_single_jobtype() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot',  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#jobType') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_schedule_restore_posts_rdfxml_with_a_single_start_time() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot',  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $values = array();
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime') {
        $values[] = $triple['o_type'];
      }
    }

    $this->assertEquals( 1 , count($values));
  }

  function test_schedule_restore_posts_rdfxml_with_a_type_of_job_request() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot',  mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://schemas.talis.com/2006/bigfoot/configuration#JobRequest') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_restore_posts_rdfxml_with_a_job_type_of_restore_job() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore( 'http://example.org/snapshot', mktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#jobType' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://schemas.talis.com/2006/bigfoot/configuration#RestoreJob') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_restore_posts_rdfxml_with_correct_start_time() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore( 'http://example.org/snapshot', gmmktime(10, 11, 0, 8, 6, 2007) );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime' && $triple['o_type'] == 'literal' && $triple['o'] == '2007-08-06T10:11:00Z') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

  function test_schedule_restore_posts_rdfxml_with_supplied_label() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot',  gmmktime(10, 11, 0, 8, 6, 2007), "My job" );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://www.w3.org/2000/01/rdf-schema#label' && $triple['o_type'] == 'literal' && $triple['o'] == 'My job') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }
  function test_schedule_restore_uses_current_time_if_none_supplied() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore('http://example.org/snapshot');

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();
    $time = 0;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#startTime' && $triple['o_type'] == 'literal') {
        $time = strtotime($triple['o']);
        break;
      }
    }

    $this->assertTrue( gmmktime() - $time < 5);
  }


  function test_schedule_restore_posts_rdfxml_with_a_snapshot_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/jobs", $fake_request );

    $queue = new JobQueue("http://example.org/store/jobs");
    $queue->request_factory = $fake_request_factory;

    $response = $queue->schedule_restore( 'http://example.org/snapshot' );

    $parser =ARC2::getRDFXMLParser(array( "bnode_prefix"=>"genid", "base"=> 'http://example.org/'  ));
    $parser->parse('http://example.org/',  $fake_request->get_body() );
    $triples = $parser->getTriples();

    $found_triple = false;
    foreach ($triples as $triple) {
      if ($triple['p'] == 'http://schemas.talis.com/2006/bigfoot/configuration#snapshotUri' && $triple['o_type'] == 'iri' && $triple['o'] == 'http://example.org/snapshot') {
        $found_triple = true;
        break;
      }
    }

    $this->assertTrue( $found_triple);
  }

}
