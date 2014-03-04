<?php

class rpcdbcmsadmin extends object
{
	public $objDbCmsAdmin;

	public function init()
	{
		$this->objDbCmsAdmin = $this->getObject('dbcmsadmin', 'cmsadmin');
	}

	/**
     * Method to get the list of sections
     *
     * @access public
     * @param bool $isPublished TRUE | FALSE To get published sections
     * @return array An array of associative arrays of all sections
     */
	public function getSections($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$published = $param->scalarval();

		$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$filter = $param->scalarval();

		$return = $this->objDbCmsAdmin->getSections($published, $filter);

		foreach($return as $ret)
		{
			$sectionStruct[] = new XML_RPC_Value(array(
			"id" => new XML_RPC_Value($ret['id'], "string"),
			"rootid" => new XML_RPC_Value($ret['rootid'], "string"),
			"parentid" => new XML_RPC_Value($ret['parentid'], "string"),
			"title" => new XML_RPC_Value($ret['title'], "string"),
			"menutext" => new XML_RPC_Value($ret['menutext'], "string"),
			"description" => new XML_RPC_Value($ret['description'], "string"),
			"published" => new XML_RPC_Value($ret['published'], "integer"),
			"showdate" => new XML_RPC_Value($ret['showdate'], "integer"),
			"showintroduction" => new XML_RPC_Value($ret['showintroduction'], "integer"),
			"hidetitle" => new XML_RPC_Value($ret['hidetitle'], "integer"),
			"numpagedisplay" => new XML_RPC_Value($ret['numpagedisplay'], "integer"),
			"checked_out" => new XML_RPC_Value($ret['checked_out'], "integer"),
			"checked_out_time" => new XML_RPC_Value($ret['checked_out_time'], "string"),
			"ordering" => new XML_RPC_Value($ret['ordering'], "integer"),
			"ordertype" => new XML_RPC_Value($ret['ordertype'], "string"),
			"access" => new XML_RPC_Value($ret['access'], "integer"),
			"trash" => new XML_RPC_Value($ret['trash'], "integer"),
			"nodelevel" => new XML_RPC_Value($ret['nodelevel'], "integer"),
			"params" => new XML_RPC_Value($ret['params'], "string"),
			"layout" => new XML_RPC_Value($ret['layout'], "string"),
			"link" => new XML_RPC_Value($ret['link'], "string"),
			"userid" => new XML_RPC_Value($ret['userid'], "string"),
			"datecreated" => new XML_RPC_Value($ret['datecreated'], "string"),
			"lastupdatedby" => new XML_RPC_Value($ret['lastupdatedby'], "string"),
			"updated" => new XML_RPC_Value($ret['updated'], "string"),
			"startdate" => new XML_RPC_Value($ret['startdate'], "string"),
			"finishdate" => new XML_RPC_Value($ret['finishdate'], "string"),
			"contextcode" => new XML_RPC_Value($ret['contextcode'], "string"),
			), "struct");
		}
		$sectarr = new XML_RPC_Value($sectionStruct, 'array');
		return new XML_RPC_Response($sectarr);

	}

	public function getFilteredSecs($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$text = $param->scalarval();

		$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$publish = $param->scalarval();

		$return = $this->objDbCmsAdmin->getFilteredSections($text, $publish);

		foreach($return as $ret)
		{
			$sectionStruct[] = new XML_RPC_Value(array(
			"id" => new XML_RPC_Value($ret['id'], "string"),
			"rootid" => new XML_RPC_Value($ret['rootid'], "string"),
			"parentid" => new XML_RPC_Value($ret['parentid'], "string"),
			"title" => new XML_RPC_Value($ret['title'], "string"),
			"menutext" => new XML_RPC_Value($ret['menutext'], "string"),
			"description" => new XML_RPC_Value($ret['description'], "string"),
			"published" => new XML_RPC_Value($ret['published'], "integer"),
			"showdate" => new XML_RPC_Value($ret['showdate'], "integer"),
			"showintroduction" => new XML_RPC_Value($ret['showintroduction'], "integer"),
			"hidetitle" => new XML_RPC_Value($ret['hidetitle'], "integer"),
			"numpagedisplay" => new XML_RPC_Value($ret['numpagedisplay'], "integer"),
			"checked_out" => new XML_RPC_Value($ret['checked_out'], "integer"),
			"checked_out_time" => new XML_RPC_Value($ret['checked_out_time'], "string"),
			"ordering" => new XML_RPC_Value($ret['ordering'], "integer"),
			"ordertype" => new XML_RPC_Value($ret['ordertype'], "string"),
			"access" => new XML_RPC_Value($ret['access'], "integer"),
			"trash" => new XML_RPC_Value($ret['trash'], "integer"),
			"nodelevel" => new XML_RPC_Value($ret['nodelevel'], "integer"),
			"params" => new XML_RPC_Value($ret['params'], "string"),
			"layout" => new XML_RPC_Value($ret['layout'], "string"),
			"link" => new XML_RPC_Value($ret['link'], "string"),
			"userid" => new XML_RPC_Value($ret['userid'], "string"),
			"datecreated" => new XML_RPC_Value($ret['datecreated'], "string"),
			"lastupdatedby" => new XML_RPC_Value($ret['lastupdatedby'], "string"),
			"updated" => new XML_RPC_Value($ret['updated'], "string"),
			"startdate" => new XML_RPC_Value($ret['startdate'], "string"),
			"finishdate" => new XML_RPC_Value($ret['finishdate'], "string"),
			"contextcode" => new XML_RPC_Value($ret['contextcode'], "string"),
			), "struct");
		}
		$sectarr = new XML_RPC_Value($sectionStruct, 'array');
		return new XML_RPC_Response($sectarr);
	}

	public function getArcSections($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$filter = $param->scalarval();

		$return = $this->objDbCmsAdmin->getArchiveSections($filter);

		foreach($return as $ret)
		{
			$sectionStruct[] = new XML_RPC_Value(array(
			"id" => new XML_RPC_Value($ret['id'], "string"),
			"rootid" => new XML_RPC_Value($ret['rootid'], "string"),
			"parentid" => new XML_RPC_Value($ret['parentid'], "string"),
			"title" => new XML_RPC_Value($ret['title'], "string"),
			"menutext" => new XML_RPC_Value($ret['menutext'], "string"),
			"description" => new XML_RPC_Value($ret['description'], "string"),
			"published" => new XML_RPC_Value($ret['published'], "integer"),
			"showdate" => new XML_RPC_Value($ret['showdate'], "integer"),
			"showintroduction" => new XML_RPC_Value($ret['showintroduction'], "integer"),
			"hidetitle" => new XML_RPC_Value($ret['hidetitle'], "integer"),
			"numpagedisplay" => new XML_RPC_Value($ret['numpagedisplay'], "integer"),
			"checked_out" => new XML_RPC_Value($ret['checked_out'], "integer"),
			"checked_out_time" => new XML_RPC_Value($ret['checked_out_time'], "string"),
			"ordering" => new XML_RPC_Value($ret['ordering'], "integer"),
			"ordertype" => new XML_RPC_Value($ret['ordertype'], "string"),
			"access" => new XML_RPC_Value($ret['access'], "integer"),
			"trash" => new XML_RPC_Value($ret['trash'], "integer"),
			"nodelevel" => new XML_RPC_Value($ret['nodelevel'], "integer"),
			"params" => new XML_RPC_Value($ret['params'], "string"),
			"layout" => new XML_RPC_Value($ret['layout'], "string"),
			"link" => new XML_RPC_Value($ret['link'], "string"),
			"userid" => new XML_RPC_Value($ret['userid'], "string"),
			"datecreated" => new XML_RPC_Value($ret['datecreated'], "string"),
			"lastupdatedby" => new XML_RPC_Value($ret['lastupdatedby'], "string"),
			"updated" => new XML_RPC_Value($ret['updated'], "string"),
			"startdate" => new XML_RPC_Value($ret['startdate'], "string"),
			"finishdate" => new XML_RPC_Value($ret['finishdate'], "string"),
			"contextcode" => new XML_RPC_Value($ret['contextcode'], "string"),
			), "struct");
		}
		$sectarr = new XML_RPC_Value($sectionStruct, 'array');
		return new XML_RPC_Response($sectarr);
	}

	public function getSectionRootNodes($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$published = $param->scalarval();

		$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$contextcode = $param->scalarval();

		$return = $this->objDbCmsAdmin->getRootNodes($published, $contextcode);

		foreach($return as $ret)
		{
			$sectionStruct[] = new XML_RPC_Value(array(
			"id" => new XML_RPC_Value($ret['id'], "string"),
			"rootid" => new XML_RPC_Value($ret['rootid'], "string"),
			"parentid" => new XML_RPC_Value($ret['parentid'], "string"),
			"title" => new XML_RPC_Value($ret['title'], "string"),
			"menutext" => new XML_RPC_Value($ret['menutext'], "string"),
			"description" => new XML_RPC_Value($ret['description'], "string"),
			"published" => new XML_RPC_Value($ret['published'], "integer"),
			"showdate" => new XML_RPC_Value($ret['showdate'], "integer"),
			"showintroduction" => new XML_RPC_Value($ret['showintroduction'], "integer"),
			"hidetitle" => new XML_RPC_Value($ret['hidetitle'], "integer"),
			"numpagedisplay" => new XML_RPC_Value($ret['numpagedisplay'], "integer"),
			"checked_out" => new XML_RPC_Value($ret['checked_out'], "integer"),
			"checked_out_time" => new XML_RPC_Value($ret['checked_out_time'], "string"),
			"ordering" => new XML_RPC_Value($ret['ordering'], "integer"),
			"ordertype" => new XML_RPC_Value($ret['ordertype'], "string"),
			"access" => new XML_RPC_Value($ret['access'], "integer"),
			"trash" => new XML_RPC_Value($ret['trash'], "integer"),
			"nodelevel" => new XML_RPC_Value($ret['nodelevel'], "integer"),
			"params" => new XML_RPC_Value($ret['params'], "string"),
			"layout" => new XML_RPC_Value($ret['layout'], "string"),
			"link" => new XML_RPC_Value($ret['link'], "string"),
			"userid" => new XML_RPC_Value($ret['userid'], "string"),
			"datecreated" => new XML_RPC_Value($ret['datecreated'], "string"),
			"lastupdatedby" => new XML_RPC_Value($ret['lastupdatedby'], "string"),
			"updated" => new XML_RPC_Value($ret['updated'], "string"),
			"startdate" => new XML_RPC_Value($ret['startdate'], "string"),
			"finishdate" => new XML_RPC_Value($ret['finishdate'], "string"),
			"contextcode" => new XML_RPC_Value($ret['contextcode'], "string"),
			), "struct");
		}
		$sectarr = new XML_RPC_Value($sectionStruct, 'array');
		return new XML_RPC_Response($sectarr);

	}

	public function getSectionId($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$id = $param->scalarval();

		$ret = $this->objDbCmsAdmin->getSection($id);

		$sectionStruct[] = new XML_RPC_Value(array(
		"id" => new XML_RPC_Value($ret['id'], "string"),
		"rootid" => new XML_RPC_Value($ret['rootid'], "string"),
		"parentid" => new XML_RPC_Value($ret['parentid'], "string"),
		"title" => new XML_RPC_Value($ret['title'], "string"),
		"menutext" => new XML_RPC_Value($ret['menutext'], "string"),
		"description" => new XML_RPC_Value($ret['description'], "string"),
		"published" => new XML_RPC_Value($ret['published'], "integer"),
		"showdate" => new XML_RPC_Value($ret['showdate'], "integer"),
		"showintroduction" => new XML_RPC_Value($ret['showintroduction'], "integer"),
		"hidetitle" => new XML_RPC_Value($ret['hidetitle'], "integer"),
		"numpagedisplay" => new XML_RPC_Value($ret['numpagedisplay'], "integer"),
		"checked_out" => new XML_RPC_Value($ret['checked_out'], "integer"),
		"checked_out_time" => new XML_RPC_Value($ret['checked_out_time'], "string"),
		"ordering" => new XML_RPC_Value($ret['ordering'], "integer"),
		"ordertype" => new XML_RPC_Value($ret['ordertype'], "string"),
		"access" => new XML_RPC_Value($ret['access'], "integer"),
		"trash" => new XML_RPC_Value($ret['trash'], "integer"),
		"nodelevel" => new XML_RPC_Value($ret['nodelevel'], "integer"),
		"params" => new XML_RPC_Value($ret['params'], "string"),
		"layout" => new XML_RPC_Value($ret['layout'], "string"),
		"link" => new XML_RPC_Value($ret['link'], "string"),
		"userid" => new XML_RPC_Value($ret['userid'], "string"),
		"datecreated" => new XML_RPC_Value($ret['datecreated'], "string"),
		"lastupdatedby" => new XML_RPC_Value($ret['lastupdatedby'], "string"),
		"updated" => new XML_RPC_Value($ret['updated'], "string"),
		"startdate" => new XML_RPC_Value($ret['startdate'], "string"),
		"finishdate" => new XML_RPC_Value($ret['finishdate'], "string"),
		"contextcode" => new XML_RPC_Value($ret['contextcode'], "string"),
		), "struct");

		$sectarr = new XML_RPC_Value($sectionStruct, 'array');
		return new XML_RPC_Response($sectarr);

	}

	public function getFirstSectionId($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$published = $param->scalarval();
		
		$ret = $this->objDbCmsAdmin->getFirstSectionId($published);

		$sectarr = new XML_RPC_Value($ret, 'string');
		return new XML_RPC_Response($sectarr);
	}

	public function addSec($params)
	{
		
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$title = $param->scalarval();
		
		$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$menutext = $param->scalarval();
		
		$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$access = $param->scalarval();
		
		$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$layout = $param->scalarval();
		
		$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$description = $param->scalarval();
		
		$param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$published = $param->scalarval();
		
		$param = $params->getParam(6);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$hidetitle = $param->scalarval();
		
		$param = $params->getParam(7);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$showdate = $param->scalarval();
		
		$param = $params->getParam(8);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$showintroduction = $param->scalarval();
		
		$param = $params->getParam(9);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$ordertype = $param->scalarval();
		
		$param = $params->getParam(10);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$userid = $param->scalarval();
		
		$param = $params->getParam(11);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$link = $param->scalarval();
		
		$param = $params->getParam(12);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$contextcode = $param->scalarval();
		
		$param = $params->getParam(13);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$userid = $param->scalarval();
		
		$param = $params->getParam(14);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$parentselected = $param->scalarval();
		
		$secArr = array(
			'title' => $title,
			'menutext' => $menutext,
			'access' => $access,
			'layout' => $layout,
			'description' => $description,
			'published' => $published,
			'hidetitle' => $hidetitle,
			'showdate' => $showdate,
			'showintroduction' => $showintroduction,
			'ordertype' => $ordertype,
			'userid' => $userid,
			'link' => $link,
			'contextcode' => $contextcode,
			'parentselected' => $parentselected
			);
		$ret = $this->objDbCmsAdmin->addSection($secArr);

		$sectarr = new XML_RPC_Value($ret, 'string');
		return new XML_RPC_Response($sectarr);
	}
	
	public function addPg($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$title = $param->scalarval();
		
		$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$sectionid = $param->scalarval();
		
		$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$introtext = $param->scalarval();
		
		$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$body = $param->scalarval();
		
		$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$access = $param->scalarval();
		
		$param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$published = $param->scalarval();
		
		$param = $params->getParam(6);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$hide_title = $param->scalarval();
		
		$param = $params->getParam(7);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$post_lic = $param->scalarval();
		
		$param = $params->getParam(8);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$override_date = $param->scalarval();
		
		$param = $params->getParam(9);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$creatorid = $param->scalarval();
		
		$param = $params->getParam(10);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$created_by = $param->scalarval();
		
		$param = $params->getParam(11);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$meta_key = $param->scalarval();
		
		$param = $params->getParam(12);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$meta_desc = $param->scalarval();
		
		$param = $params->getParam(13);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$start_publish = $param->scalarval();
		
		$param = $params->getParam(14);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$end_publish = $param->scalarval();
		
		$param = $params->getParam(15);
		if (!XML_RPC_Value::isValue($param)) {
			log_debug($param);
		}
		$isfrontpage = $param->scalarval();
		
		$newArr = array(
		'title' => $title ,
		'sectionid' => $sectionid,
		'introtext' => $introtext,
		'body' => $body,
		'access' => $access,
		'published' => $published,
		'hide_title' => $hide_title,
		'post_lic' => $post_lic,
		'created' =>$override_date,
		'created_by' => $creatorid,
		'creatorid' => $creatorid,
		'created_by_alias'=>$created_by,
		'checked_out'=> $creatorid,
		'metakey'=>$meta_key,
		'metadesc'=>$meta_desc,
		'start_publish'=>$start_publish,
		'end_publish'=>$end_publish,
		'isfrontpage' => $isfrontpage,
		);
		
		$ret = $this->objDbCmsAdmin->addContent($newArr);
		$sectarr = new XML_RPC_Value($ret, 'string');
		return new XML_RPC_Response($sectarr);
	}
	
	/**
     * Method to dynamically switch tables
     *
     * @param  string  $table
     * @return boolean
     * @access private
     */
	private function _changeTable($table)
	{
		try {
			parent::init($table);
			return TRUE;
		}
		catch(customException $e) {
			customException::cleanUp();
			exit;
		}
	}
}
?>