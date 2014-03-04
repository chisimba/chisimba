<?PHP

// ----------------------------------------------------------------------------------
// RAP_Pubby - A Linked Data Frontend for RAP
// ----------------------------------------------------------------------------------

/**
 * Installation information is found in the RAP_Pubby documentation.
 *
 * @author  Radoslaw Oldakowski <radol@gmx.de>
 * @version 1.0, 19.12.2007
 * @package rap-pubby
 */


include_once('config.php');


$ds = new RAPpubbyDataset();

$http_reqest_URI = "http://" .$_SERVER['SERVER_NAME'] .$_SERVER['REQUEST_URI'];

// index resource
if ($http_reqest_URI == PUBBY_WEBBASE) {
	$http_reqest_URI = RAPpubbyURIrewriter::datasetURItoPubbyURI(PUBBY_INDEX_RESOURCE);
}

switch (substr($http_reqest_URI, strlen(PUBBY_WEBBASE), 4)) {
	
	case 'data':
	
		$resURI = RAPpubbyURIrewriter::dataURItoResURI($http_reqest_URI, $_SERVER['QUERY_STRING']);
		
		$rd = & $ds->getResDescr($resURI, $_PUBBY_DATASET['rdfDocumentMetadata']);
		
		if ($rd->isEmpty()) {
			header('HTTP/1.1 404 Not Found');
			header('Content-type: text/html;charset=utf-8');
			$c = "Nothing known about <$resURI>";
			header('Content-length: ' .strlen($c));
			echo $c;
			break;		
		}	
			
		// valid query string: 'output='(xml|n3)
		if (!strcasecmp($_SERVER['QUERY_STRING'], 'output=n3')) {
			 	
		  	header('Content-type: application/n3');
		 	$c = $rd->serialize("n3"); 	
		}
		elseif (!strcasecmp($_SERVER['QUERY_STRING'], 'output=xml')) {
		
			header('Content-type: application/rdf+xml');
			$c = $rd->serialize("rdf/xml");
		}
		// default output
		else {
					
			header('Content-type: application/rdf+xml');
			
			$c = $rd->serialize("rdf/xml");
		}		
		header('Content-length: ' .strlen($c));
		echo $c;
		break;
	
	case 'page':
	
		$resURI = RAPpubbyURIrewriter::pageURItoResURI($http_reqest_URI);	
		$dataURI = RAPpubbyURIrewriter::resURItoDataURI($resURI);
	
		$rd = & $ds->getResDescr($resURI);
		
		header('Content-type: text/html;charset=utf-8');
		if ($rd->isEmpty()) {
			header('HTTP/1.1 404 Not Found');	
		}			
		$c = $rd->serialize("html");		
		header('Content-length: ' .strlen($c));
		echo $c;
		break;
	
	default: 

		if (strpos($_SERVER['HTTP_ACCEPT'], "application/rdf+xml") !==  false) {
			
			$dataURI = RAPpubbyURIrewriter::resURItoDataURI($http_reqest_URI);	
				
			header('HTTP/1.1 303 See Other');
			header('Location: ' .$dataURI);
			header('Content-type: text/plain');
			$c = "303 See Other: For a description of this item, see <$dataURI>";
		}
		// n3
		elseif (strpos($_SERVER['HTTP_ACCEPT'], "application/n3") !== false) {
			
			$dataURI = RAPpubbyURIrewriter::resURItoDataURI($http_reqest_URI, 'n3');
	
			header('HTTP/1.1 303 See Other');
			header('Location: ' .$dataURI);
			header('Content-type: text/plain');
			$c = "303 See Other: For a description of this item in n3, see <$dataURI>";			
		}
		else {
			
			$pageURI = RAPpubbyURIrewriter::resURItoPageURI($http_reqest_URI);
		
			header('HTTP/1.1 303 See Other');		
			header('Location: ' .$pageURI);
			header('Content-type: text/plain');			
			header('Content-length: 33');
			$c = "303 See Other: For a description of this item see <$pageURI>";					
			
		} 
		
		header('Content-length: ' .strlen($c));
		echo $c;				
}


?>