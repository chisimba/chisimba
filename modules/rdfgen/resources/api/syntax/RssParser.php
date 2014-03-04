<?php

// ----------------------------------------------------------------------------------
// Class: RSS/ATOM Parser
// ----------------------------------------------------------------------------------


/**
 * RSS/ATOM Parser
 * 
 * This class uses the MagpieRSS Parser by Kellan Elliott-McCrea <kellan@protest.net>. MagpieRSS is 
 * compatible with RSS 0.9 through RSS 1.0. Also parses RSS 1.0's modules, RSS 2.0, and Atom (with a few exceptions). 
 * Magpie is distributed under the GPL license. (http://magpierss.sourceforge.net/)
 * 
 *
 *
 * @author Tobias Gauﬂ <tobias.gauss@web.de>
 * @version $Id: RssParser.php 301 2006-06-26 10:22:25Z tgauss $
 * @package syntax
 * @access public
 **/

class RssParser extends Object {
	var $type;
	
	/**
	* Main function
	*
	* @param $url location of the document
	* @return parsed model
	*/
	function generateModel($url){
		$rss = null;
		if(substr($url,0,7)=="http://"){
			$rss = fetch_rss($url);
		}else{
				$string = file_get_contents($url);
				$rss = new MagpieRSS($string);	
		}
		
		$model = ModelFactory::getMemModel();	
		$bn = new Resource($url);
		
		$this->type = $rss->feed_type;
		$version = $rss->feed_version;
		
		$nmsp = null;
	 	if($version == "1.0")
	 	 		$nmsp = "http://purl.org/rss/1.0/";
	 	if($version == "0.91")
	 	 		$nmsp = "http://my.netscape.com/publish/formats/rss-spec-0.91.html#";
	 	if($version == "2.0")
	 	 		$nmsp = "http://purl.org/rss/1.0/";
	 	if($this->type == "Atom")
	 	 		$nmsp = "http://www.w3.org/2005/Atom";
	 	 
		if($this->type == "Atom")
			$this->channel($model,$rss,$bn,$nmsp,"feed");
		else 
			$this->channel($model,$rss,$bn,$nmsp,"channel");
		$this->image($model,$rss,$bn,$nmsp);
		$this->items($model,$rss,$bn,$nmsp);
		$model->addNamespace("rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#");
		if($nmsp!= null)
			$model->addNamespace($this->type,$nmsp);
		return $model;
	}

	/**
	* Parses the image if there is one.
	*
	* @param $model the model
	* @param $rss the magpie parser object
	* @param $bnOrg the node which represents the channel
	* @param $nmsp the
	*/
	function image(&$model,&$rss,&$bnOrg,&$nmsp){		
		if(count($rss->image)>0){
			if(isset($rss->image['about'])){
				$bn = new Resource($rss->image['about']);
			}else if(isset($rss->image['url'])){
				$bn = new Resource($rss->image['url']);
			}else{
				$bn = new BlankNode("image");
			}
			$model->add(new Statement($bnOrg,new Resource($nmsp."image"),$bn));
			$model->add(new Statement($bn,new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"),new Resource($nmsp."image")));
		}
		foreach($rss->image as $key => $value){
		$statement = null;	
		switch($key){
					case "dc":
						$this->dc($bn,$model,$value);
					break;
					case "sy":
						$this->sy($bn,$model,$value);
					break;
					default :
						$statement = new Statement($bn,new Resource($nmsp.$key), new Literal($value));
					break;
				}
			if($statement != null){
				$model->add($statement);
			}
		}
	
	}
	
	/**
	* Parses the rss items/ atom entries.
	*
	* @param $model the model
	* @param $rss the magpie parser object
	* @param $bnOrg the node which represents the channel
	* @param $nmsp the
	*/
	function items(&$model,&$rss,&$bnOrg,&$nmsp){		
		$items = new BlankNode("items");
		$model->add(new Statement($bnOrg,new Resource($nmsp."items"),$items));
		$model->add(new Statement($items,new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq")));
		$i = 1;
		foreach($rss->items as $outerKey => $outerValue){
			if(isset($outerValue['about'])){
				$bn = new Resource($outerValue['about']);
			}else if(isset($outerValue['guid'])){
				$bn = new Resource($outerValue['guid']);
			}else if(isset($outerValue['id'])){
				$bn = new Resource($outerValue['id']);
			}else{
				$bn = new Blanknode("item".$i);
			}
			$model->add(new Statement($items,new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#_".$i),$bn));
			if($this->type == "Atom"){
				$model->add(new Statement($bn,new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"),new Resource("http://www.w3.org/2005/Atomentry")));
			}else{
				$model->add(new Statement($bn,new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#type"),new Resource("http://purl.org/rss/1.0/item")));
			}
			foreach($outerValue as $key => $value){
				$statement = null;
				switch($key){
					case "about":
						break;
					case "guid":
						break;
					case "dc":
						$this->dc($bn,$model,$value);
					break;
					case "sy":
						$this->sy($bn,$model,$value);
					break;
					default :
						if($value != null)
						$statement = new Statement($bn,new Resource($nmsp.$key), new Literal($value));
					break;
				}
				if($statement != null){
					$model->add($statement);
				}
			}
			$i++;
		}
		
	}
	
	/**
	* Adds the dc namespace.
	*
	* @param $node the node
	* @param $model the model
	* @param $dc Array which contains the dc objects
	*/
	function dc(&$node,&$model,&$dc){
		$model->addNamespace("dc","http://purl.org/dc/elements/1.1/");
		foreach($dc as $key => $value){
				$statement = null;
				$statement = new Statement($node,new Resource("http://purl.org/dc/elements/1.1/".$key), new Literal($value));
			if($statement != null){
				$model->add($statement);
			}
		}
	}
	
	/**
	* Adds the sy namespace.
	*
	* @param $node the node
	* @param $model the model
	* @param $sy Array which contains the sy objects
	*/
	function sy(&$node,&$model,&$sy){
		$model->addNamespace("sy","http://purl.org/rss/1.0/modules/syndication/");
		foreach($sy as $key => $value){
				$statement = null;
				$statement = new Statement($node,new Resource("http://purl.org/rss/1.0/modules/syndication/".$key), new Literal($value));
			if($statement != null){
				$model->add($statement);
			}
		}
		
	}
	
	
	/**
	* Parses the rss channel/ atom feed.
	*
	* @param $model the model
	* @param $rss the magpie parser object
	* @param $node the node which represents the channel
	* @param $nmsp the
	*/
	function channel(&$model,&$rss,&$node,&$nmsp,$type){
	 	// Channel
	 	$statement = new Statement($node,new Resource('http://www.w3.org/1999/02/22-rdf-syntax-ns#type'), new Resource($nmsp.$type));
		$model->add($statement);
		
	 	foreach($rss->channel as $key => $value){
		$statement = null;	
		switch($key){
					case "dc":
						$this->dc($node,$model,$value);
					break;
					case "sy":
						$this->sy($node,$model,$value);
					break;
					case "items":	
					break;
					case "tagline":	
					break;
					case "items_seq":
					break;
					default :
						$statement = new Statement($node,new Resource($nmsp.$key), new Literal($value));
					break;
				}
		if($statement != null){
			$model->add($statement);
		}
	}
}

  
  
} //end: RssParser

?>
