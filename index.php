<?php
require_once('init.php');

$gpage_db = new Database();


if (isset($_GET['id'])) {
	$pageID = $_GET['id'];

	if ($pageID == '' || $pageID == 'home')
	{
		$gPage = new gHome();
		$gPage->setPageType("home");
		$gPage->getPageDetails($gpage_db);
	}
	elseif ($pageID == 'dnd')
	{
		$gPage = new gDND();
		$gPage->setPageType("dnd");
		$gPage->getPageDetails($gpage_db);
	}
	elseif ($pageID == 'read')
	{
		$gPage = new gRead();
		$gPage->setPageType("read");
		$gPage->getPageDetails($gpage_db);
	}
	elseif ($pageID == 'research')
	{
		$gPage = new gResearch();
		$gPage->setPageType("research");
		$gPage->getPageDetails($gpage_db);
	}
	elseif ($pageID == 'fit')
	{
		$gPage = new gFit();
		$gPage->setPageType("fit");
		$gPage->getPageDetails($gpage_db);
	}
	else
	{
		$pageID = 'home';
		$gPage = new gHome();
		$gPage->setPageType("home");
		$gPage->getPageDetails($gpage_db);
	}
}
else
{
	$pageID = 'home';
	$gPage = new gHome();
	$gPage->setPageType("home");
	$gPage->getPageDetails($gpage_db);
}

?>
<!DOCTYPE html> 
<?php
	$gPage->generate_htmlhead();
?>

<body>
<div id="container">
	<?php
		$gPage->generate_header();
	?>
	<div id="main">
		<?php
		$gPage->generate_content($gpage_db);

		?>
	</div>

	<?php
		$gPage->generate_footer();	
	?>

</div>

</body>

</html>

