<?

class gResearch extends gPage {
	function __construct()
	{
		parent::__construct();
	}


	#------------------------------------------------------------------------------------------------------
	#MAJOR CLASSES
	#------------------------------------------------------------------------------------------------------
	#Overwriting header generator function from parent
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
			<meta http-equiv="refresh" content="0;URL='http://www.eng.uwaterloo.ca/~g3willia/'" />
		</head>
		<?php
	}

	public function generate_content($page_db)
	{
		echo 'Moving you over to <a href="http://www.eng.uwaterloo.ca/~g3willia/">http://www.eng.uwaterloo.ca/~g3willia/</a>';
	}
}

?>
