<?

class gFit extends gPage {
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
			<meta http-equiv="refresh" content="0;URL='https://www.fitocracy.com/profile/GraemeW/'" />
		</head>
		<?php
	}

	public function generate_content($page_db)
	{
		echo 'Moving you over to <a href="https://www.fitocracy.com/profile/GraemeW/">https://www.fitocracy.com/profile/GraemeW/</a>';
	}
}

?>
