<?php
header("Content-Type:text/html;charset=UTF-8");
class jsonApi {

	public $wikiRoot; 
	public $apiUrl;
	public $reqVars;
	public function __construct() {
		$this->wikiRoot = 'http://10.171.26.240/wiki';
		$this->apiUrl = $this->wikiRoot.'/api.php?format=json';
	}
	
	function getImgList($reqVars) {
		$fullUrl = $this->apiUrl.$this->parseReqVars($reqVars);
		$imgJson = file_get_contents($fullUrl);
		$imgArr = json_decode($imgJson, true);
		$imgDB = $imgArr['query']['allimages'];
		return $imgDB;
	}
	
	function getPageList($reqVars) {
		$fullUrl = $this->apiUrl.$this->parseReqVars($reqVars);
		$pageJson = file_get_contents($fullUrl);
		$pageArr = json_decode($pageJson, true);
		$pageDB = $pageArr['query']['allpages'];
		return $pageDB;
	}
	
	function getImg($reqVars, $pageID) {
		$fullUrl = $this->apiUrl.$this->parseReqVars($reqVars);
		$imgJson = file_get_contents($fullUrl);
		$imgArr = json_decode($imgJson, true);
		return $imgArr['query']['pages'][$pageID]['imageinfo'][0]['url'];
	}
	
	function parseReqVars($reqVars) {
		$reqStr = '';
		foreach($reqVars as $key=>$var) {
			$reqStr .= '&'.$key.'='.$var;
		}
		return $reqStr;
	}
}

//$myimg = new jsonApi();
/*
图片列表调用
$reqArr['action'] = 'query';
$reqArr['list'] = 'allimages';
$reqArr['aifrom'] = 'B';
$imgdb = $myimg->getImgList($reqArr);
foreach($imgdb as $img) {
	echo $img['url'];
}
*/
/*
单图片调用
$reqArr = array();
$reqArr['action'] = 'query';
$reqArr['titles'] = 'Image:Login.png';
$reqArr['prop'] = 'imageinfo';
$reqArr['iiprop'] = 'timestamp|user|url';
$imgdb = $myimg->getImg($reqArr, 4);
*/
?>