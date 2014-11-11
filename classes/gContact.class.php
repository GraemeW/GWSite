
<?

class gContact extends gPage {

	function __construct()
	{
		parent::__construct();
	}


	#------------------------------------------------------------------------------------------------------
	#MAJOR CLASSES
	#------------------------------------------------------------------------------------------------------
	public function generate_content($page_db)
	{
        ?>
        <div id="ltitle">Contact Info</div><br />
        Please feel free to contact me by either of the following emails. I strive to answer emails on the day I receive them or shortly after.<br />
                
        <ul>
        <li>g3willia@uwaterloo.ca</li>
        <li>graeme.m.williams@gmail.com</li>
        </ul>
        <?
        
	}

}

?>
