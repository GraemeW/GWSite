
<?

class gRead extends gPage {

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
		<div id='ltitle'>Books on the Bookshelf!</div>
		<?

		$getvars = array_keys($_GET);
		#Setting how the data is ordered 
		if(($key = array_search('sort', $getvars)) !== false) 
		{
			if ($_GET['sort']=='BID' || $_GET['sort']=='Name' || $_GET['sort']=='Author' || $_GET['sort']=='GR' || $_GET['sort']=='KR')
			{
				$sort = $_GET['sort'];
			}
			else
			{
				$sort = 'Author';
			}
		}
		else
		{
			$sort = 'Author';
		}

		if(($key = array_search('order', $getvars)) !== false)
		{
			if ($_GET['order']=='fwd')
			{
				$order = '';
			}
			elseif ($_GET['order']=='bkwd')
			{
				$order = ' DESC';
			}
			else
			{
				$order = '';
			}
		}
		else
		{
			$order = '';
		}

		$bq_params = array('Name', 'Author', 'GR', 'KR');
		$bq_string = 'SELECT ' . implode(', ', $bq_params) . ' FROM gread ORDER BY ' . $sort . $order;
		$book_query = $page_db->Select($bq_string);
		echo '<table id="tab-base"><thead><tr>';
		for ($i = 0; $i < count($bq_params); $i++)
		{
			echo '<th scope="col">';
			echo '<a href="?id=read&sort=' . $bq_params[$i];
			if ($sort==$bq_params[$i] && $order=='')
			{
				echo '&order=bkwd';
			}
			echo '">';
			if ($bq_params[$i]=='GR')
			{
				echo 'Graeme';
			}
			elseif ($bq_params[$i]=='KR')
			{
				echo 'Kati';
			}
			else
			{
				echo $bq_params[$i];
			}
			echo '</a>';
			echo '</th>';
		}
		echo '</tr></thead><tbody>';
		for ($i = 0; $i < count($book_query); $i++)
		{
			echo '<tr>';
			for($j = 0; $j < count($bq_params); $j++)
			{
				echo '<td>';
				$path = array($bq_params[$j]); 
				if ($bq_params[$j]=='GR' || $bq_params[$j]=='KR')
				{
					if ($this->get_val($book_query[$i], $path)==1)
					{
						echo 'Read';
					}
					elseif ($this->get_val($book_query[$i], $path)==0)
					{
						echo 'Not Read';
					}
					else
					{
						echo '??';
					}
				}
				else
				{
					echo $this->get_val($book_query[$i], $path);
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table><br />';
	}

}

?>
