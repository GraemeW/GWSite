<?php

class gPage
{
	private $pagetype;
	private $pagetitle;
	private $pagedesc;
	private $pageimage;
	private $pageID;

	public function setPageType($pagetype) {
		$this->pagetype = $pagetype;
		return TRUE;
	}
	public function getPageType() {
		return $this->pagetype;
	}
	public function readPageTitle() {
		return $this->pagetitle;
	}
	public function getPageDetails($db_handle) {
		$page_query = $db_handle->Select('SELECT title,sdesc,gimg FROM gpage WHERE gcode = ?', $this->pagetype);
		$this->pagetitle = $page_query[0]["title"];
		$this->pagedesc = $page_query[0]["sdesc"];
		$this->pageimage = $page_query[0]["gimg"];
	}	


	public function queryPageID($db_handle) {
		$pageID = $_GET['id'];
		if ($this->setPageType($pageID)) {
			$this->getPageDetails($db_handle);
		}
		return $pageID;	
	}

	function __construct() {
	}

	function generate_htmlhead() {
		?>
		<head>
			<title>
			<?php
				echo "Lunchtime is an Illusion: &nbsp;" . $this->readPageTitle();
			?>
			</title>
			<script type="text/javascript"></script>
			<style type="text/css" media="all">
				@import "S1.css";
			</style>
		</head>
		<?php
	}

	function generate_header() {
		?>
		<div id="header">
			<img src="S1-img/header.jpg">
			<div class="titletext">Lunchtime is an Illusion</div>
		</div>

		<div id="quickbar">
			| <a href="index.php">Home</a> 
			| <a href="http://www.eng.uwaterloo.ca/~g3willia/">graeme@uwaterloo</a> 
			| <a href="http://www.facebook.com/graeme.m.williams">Facebook</a> 
			| <a href="http://www.youtube.com/user/UWEngBlog">Youtube</a> 
			|
		</div>

		<?php
	}

	function generate_footer() {
		?>

		<p id="footer">&copy; 2009-2014 Graeme Williams</p>

		<?php
	}


	public function rebuild_url($disallowed = []) {
		#grabbing current $_GET variables for re-use in URLs
		#$disallowed variable is an array that contains any $_GET fields that you want to remove from the URL
		
		$getvars = array_keys($_GET);
		$index_base = '?';

		for ($i=0; $i < count($disallowed); $i++)
		{
			if(($key = array_search($disallowed[$i], $getvars)) !== false) 
			{
		    		unset($getvars[$key]);
			}
		}

		$URL_array = [];
		for ($i=0; $i < count($getvars); $i++)
		{
			$URL_array[$getvars[$i]] = $_GET[$getvars[$i]];
		}

		$index_base = $index_base . http_build_query($URL_array);

		return $index_base;
	}

	public function get_val($array,$path) {
		for($i=$array; $key=array_shift($path); $i=$i[$key]) {
			if(!isset($i[$key])) return null;
		}
		return $i;
	}

	public function set_val (&$array,$path,$val) {
		for($i=&$array; $key=array_shift($path); $i=&$i[$key]) {
			if(!isset($i[$key])) $i[$key] = array();
		}
		$i = $val;
	}
}	
