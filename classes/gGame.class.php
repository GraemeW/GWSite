<?

class gGame extends gPage {
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
			<meta http-equiv="refresh" content="0;URL='http://steamcommunity.com/profiles/76561198015203000'" />
		</head>
		<?php
	}

	public function generate_content($page_db)
	{
		echo 'Moving you over to <a href="http://steamcommunity.com/profiles/76561198015203000">http://steamcommunity.com/profiles/76561198015203000</a>';
	}
}

?>
