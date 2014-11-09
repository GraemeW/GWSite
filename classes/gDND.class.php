<?

class gDND extends gPage {
	private $DnDsub;
	private	$story_perpage = 5;

	function __construct()
	{
		parent::__construct();
	}


	#------------------------------------------------------------------------------------------------------
	#MAJOR CLASS FUNCTIONS
	#------------------------------------------------------------------------------------------------------
	#Page navigation
	public function generate_content($page_db)
	{	
		?>
		<div id="sidebar">
			<div class="DnDNav">DnD Nav</div>
			<div class="DnDsbar"></div>
			<a href="index.php?id=dnd&sub=story">Story</a> <br>
			<a href="index.php?id=dnd&sub=map">Map</a> <br>
			<a href="index.php?id=dnd&sub=char">PCs/NPCs</a> <br>
			Gear <br>
		</div>
		<?php


		if (isset($_GET['sub'])) 
		{
			$DnDsub = $_GET['sub'];
			if ($DnDsub == '' || $DnDsub == 'home')
			{
				$this->getStory($page_db);
			}
			elseif ($DnDsub == 'map')
			{
				$this->getMap($page_db);
			}
			elseif ($DnDsub == 'char')
			{
				$this->getChar($page_db);
			}
			else
			{
				$this->getStory($page_db);
			}
		}
		else
		{
			$this->getStory($page_db);
		}

	}

	#Story Tab
	private function getStory($page_db)
	{
		if (isset($_GET['SID']))
		{
			$this->getIStory($page_db);
		}
		else
		{	
			$this->getMStory($page_db);
		}
	}

	private function getMap($page_db)
	{
		?>
		<img src="S1-img/dnd/Faelun.png">		
		<?php
	}

	private function getChar($page_db)
	{
		if (isset($_GET['CID']))
		{
			$this->getIChar($page_db);
		}
		else
		{	
			$this->getMChar($page_db);
		}
	}





	#------------------------------------------------------------------------------------------------------
	#MINOR FUNCTIONS
	#------------------------------------------------------------------------------------------------------
	#STORY SPECIFIC
	#------------------------------------------------------------------------------------------------------

	#Major storyline - lists overarching story + all major adventures
	private function getMStory($page_db)
	{
		?>
		<div id="ltitle">The Story as We Know It</div>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tortor est, vehicula vel augue vel, consectetur elementum tortor. Curabitur ultrices metus et nisi tempor, et tincidunt ipsum semper. Duis est mi, tempus id lorem non, ullamcorper ullamcorper lorem. Nulla sit amet molestie enim. Aliquam ac sollicitudin neque. Sed non dapibus velit. Vestibulum pharetra, turpis vitae venenatis bibendum, dui mi consequat orci, vel auctor odio tellus et lorem. Suspendisse eget ipsum nec purus iaculis placerat in non tortor. Quisque mollis porta ultricies. Duis scelerisque magna non lorem ultricies tincidunt in et nibh. Fusce lobortis condimentum dignissim. Etiam eleifend condimentum interdum. Suspendisse ut molestie risus, at auctor quam. Aliquam varius massa vitae ipsum pretium mollis.</p>

		<p>Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec at pellentesque nisi, in faucibus libero. In porta consectetur felis, tempus vehicula est pretium non. Donec a velit et dui convallis cursus in nec ante. Suspendisse accumsan tortor quis ligula gravida lacinia. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean condimentum tellus at purus sagittis mollis. Aliquam consequat magna rhoncus lectus lacinia, ac vulputate erat mollis. Suspendisse pharetra viverra tortor ut dignissim. Praesent a arcu nisl. Mauris venenatis consectetur libero, a suscipit elit. Donec venenatis dui ullamcorper semper ornare. Etiam nec lacus neque. Vestibulum hendrerit elit magna, et lobortis magna blandit sed.</p> 

		<br />
		<?
		$this->generate_escapades($page_db);
	}

	#Generate list of adventures	
	private function generate_escapades($page_db)
	{
		#Title + links for chronological / reverse chronological
		?>
		<div id="ltitle">Catalogued Escapades</div>
		
		<a class="tiny" href="<? echo $this->rebuild_url(['order', 'sp']) . '&order=chron';  ?>">Chronological</a> | <a class="tiny" href="<? echo $this->rebuild_url(['order', 'sp']) . '&order=rev'; ?>">Reverse Chronological</a> <br /> <br />
		<?php

		#Initial query to check # of major storyline entries
		$length_query = $page_db->Select('SELECT SID FROM gdnd_story WHERE majormin=1');
		$majstory_length = count($length_query);
		$spn = ceil(($majstory_length / $this->story_perpage));

		$getvars = array_keys($_GET);
		#Setting either chronological or reverse chronological order	
		if(($key = array_search('order', $getvars)) !== false) 
		{
			$chron = $_GET['order'];
		}
		else
		{
			$chron = 'chron';
		}

		#Setting the page #, if a page # is set
		if(($key = array_search('sp', $getvars)) !== false) 
		{
			$sp = $_GET['sp'];
		}
		else
		{
			$sp = 1;
		}

		$this->pull_stories($page_db, $chron, $sp, $spn);

		#Links for # of pages
		echo '<br /><div id="tiny">Pages: &nbsp; </div>';
		for ($i = 1; $i < (count($spn) + 1); $i++)
		{
			?>
			<a class="tiny" href="<? echo $this->rebuild_url(['sp']) . '&sp=' . strval($i); ?>"> <? echo strval($i); ?> </a> &nbsp;. &nbsp; 
			<?
		}
		echo '<br />';
	}

	#Pull individual stories - implemented by fct generate_escapades
	private function pull_stories($page_db, $chron, $sp, $spn)
	{
		#Splitting page into groups of major storylines ($story_perpage)
		#Also taking into account the chronological/reverse for
		#spitting out the data

		if ($chron=='rev')
		{
			$chron_sel = 'DESC ';
		}
		else
		{
			$chron_sel = ' ';
		}

		#verifying that the input sp value is a valid entry
		#otherwise setting the value to 1
		$page_array = range(1,$spn);
		if(($key = array_search((int)$sp, $page_array)) !== false) 
		{
			$limit_sel = 'LIMIT ' . strval((($sp-1)*$this->story_perpage)) . ',' . strval($this->story_perpage);
		}
		else
		{
			$limit_sel = 'LIMIT 1,' . strval($story_perpage);			
		}

		$mq_string = 'SELECT SID, sname, sdesc, charIDs, date FROM gdnd_story WHERE majormin=1 ORDER BY date ' . $chron_sel . $limit_sel;
			
		$major_query = $page_db->Select($mq_string);
		for ($i=0; $i < count($major_query); $i++)
		{
			?>
			<div id='stitle'><? echo '<a href="index.php?id=dnd&sub=story&SID=' . $major_query[$i]['SID'] . '">' . $major_query[$i]['sname']; ?></a></div>
			<div id='tab_in'>
			<i>Adventure started on: &nbsp; <? echo $major_query[$i]['date']; ?></i><br />
			Characters involved: &nbsp; <?
			$CIDs = explode('-', $major_query[$i]['charIDs']);
			echo '<ul>';
			for ($j=0; $j < count($CIDs); $j++)
			{
				echo '<li>' . $CIDs[$j] . '</li>';
			}
			echo '</ul>';
			echo $major_query[$i]['sdesc'] . '<br />';

			$minor_query = $page_db->Select('SELECT SID, sname, sdesc, charIDs, date FROM gdnd_story WHERE majormin=0 AND sparentID=' . $major_query[$i]['SID'] . ' ORDER BY date');

			if (!empty($minor_query))
			{
				echo '<ul>';
				?> <div id='sstitle'>Minor Misadventures</div><?
				for ($j=0; $j<count($minor_query); $j++)
				{
					echo '<li><i>- &nbsp; &nbsp;' . $minor_query[$j]['sname'] . '</i>: &nbsp;' . $minor_query[$j]['sdesc'] . '</li>';
				}
				echo '</ul>';
			}
			echo '</div>';	
		}
	}

	#Grab an individual story w/ long text description
	private function getIStory($page_db)
	{
		$getvars = array_keys($_GET);
		#Grabbing the SID parameter
		if(($key = array_search('SID', $getvars)) !== false) 
		{
			$SID = $_GET['SID'];
		}
		else
		{
			$SID = 0;
		}

		#Error checking on input value
		#If legit value not provided, go to the main story page
		$SID_query = $page_db->Select('SELECT SID FROM gdnd_story');
		$SID_vals = [];
		for ($i = 0; $i < count($SID_query); $i++)
		{
			array_push($SID_vals, $SID_query[$i]['SID']);
		}
		if(($key = array_search($SID, $SID_vals)) !== false)
		{
			$SID_s = $SID;
		}
		else
		{
			$SID_s = 0;
		}

		#Selecting proper page generation based on selection
		if ($SID_s == 0)
		{
			#Generate main story page when valid selection not provided
			$this->getMStory($page_db);
		}
		else
		{
			#DB select for individual character information
			$SID_Iquery = $page_db->Select('SELECT SID, sname, majormin, sdesc, ldesc, charIDs, date FROM gdnd_story WHERE SID = ' . $SID_s . ' OR sparentID = ' . $SID_s);

			#Identifying position of the SID entry in the query array
			#This should be first position, but just making sure	
			for ($i=0; $i < count($SID_Iquery); $i++)
			{
				if($SID_Iquery[$i]['majormin']==1)
				{
					$major_index = $i;
				}	
			}

			#Displaying all the char info prettily
			?>
			<div id='ltitle'><? echo $SID_Iquery[$major_index]['sname']; ?></a></div>
			<div id='tab_in'>
			<i>Adventure started on: &nbsp; <? echo $SID_Iquery[$major_index]['date']; ?></i><br />
			Characters involved: &nbsp; <?
			$CIDs = explode('-', $SID_Iquery[$major_index]['charIDs']);
			echo '<ul>';
			for ($j=0; $j < count($CIDs); $j++)
			{
				echo '<li>' . $CIDs[$j] . '</li>';
			}
			echo '</ul>';
			echo $SID_Iquery[$major_index]['sdesc'] . '<br />';

			$minor_query = $page_db->Select('SELECT SID, sname, sdesc, charIDs, date FROM gdnd_story WHERE majormin=0 AND sparentID=' . $major_query[$i]['SID'] . ' ORDER BY date');

			if (!empty($minor_query))
			{
				echo '<ul>';
				?> <div id='sstitle'>Minor Misadventures</div><?
				for ($j=0; $j<count($minor_query); $j++)
				{
					echo '<li><i>- &nbsp; &nbsp;' . $minor_query[$j]['sname'] . '</i>: &nbsp;' . $minor_query[$j]['sdesc'] . '</li>';
				}
				echo '</ul>';
			}
			echo '</div>';	
		<div id="ltitle">The Story as We Know It</div>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin tortor est, vehicula vel augue vel, consectetur elementum tortor. Curabitur ultrices metus et nisi tempor, et tincidunt ipsum semper. Duis est mi, tempus id lorem non, ullamcorper ullamcorper lorem. Nulla sit amet molestie enim. Aliquam ac sollicitudin neque. Sed non dapibus velit. Vestibulum pharetra, turpis vitae venenatis bibendum, dui mi consequat orci, vel auctor odio tellus et lorem. Suspendisse eget ipsum nec purus iaculis placerat in non tortor. Quisque mollis porta ultricies. Duis scelerisque magna non lorem ultricies tincidunt in et nibh. Fusce lobortis condimentum dignissim. Etiam eleifend condimentum interdum. Suspendisse ut molestie risus, at auctor quam. Aliquam varius massa vitae ipsum pretium mollis.</p>

		<p>Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec at pellentesque nisi, in faucibus libero. In porta consectetur felis, tempus vehicula est pretium non. Donec a velit et dui convallis cursus in nec ante. Suspendisse accumsan tortor quis ligula gravida lacinia. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean condimentum tellus at purus sagittis mollis. Aliquam consequat magna rhoncus lectus lacinia, ac vulputate erat mollis. Suspendisse pharetra viverra tortor ut dignissim. Praesent a arcu nisl. Mauris venenatis consectetur libero, a suscipit elit. Donec venenatis dui ullamcorper semper ornare. Etiam nec lacus neque. Vestibulum hendrerit elit magna, et lobortis magna blandit sed.</p> 

		<br />
		<?
		}
	}
	
	#------------------------------------------------------------------------------------------------------
	#MAP SPECIFIC
	#------------------------------------------------------------------------------------------------------
	
	#------------------------------------------------------------------------------------------------------
	#CHARACTER SPECIFIC
	#------------------------------------------------------------------------------------------------------
	
	private function getMChar($page_db)
	{
		echo '<div id="ltitle">Characters of Relative Importance</div>';

		#Grabbing all of the PCs
		$PC_query = $page_db->Select('SELECT CID, cname, pname, sdesc FROM gdnd_char WHERE pc=1');

		echo '<div id="stitle">Player Characters</div>';
		echo '<table id="tab-base"><thead><tr><th scope="col">Name</th><th scope="col">Player</th><th scope="col">Description</th></tr></thead><tbody>';
		for ($i = 0; $i < count($PC_query); $i++)
		{
			echo '<tr>';
			echo '<td><a href="index.php?id=dnd&sub=char&CID=' . strval($PC_query[$i]['CID']) . '" class="table">' . $PC_query[$i]['cname'] . '</a></td>';
			echo '<td>' . $PC_query[$i]['pname'] . '</td>';
			echo '<td>' . $PC_query[$i]['sdesc'] . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table><br /><br />';

		#Grabbing all of the NPCs
		$NPC_query = $page_db->Select('SELECT CID, cname, ally, sdesc FROM gdnd_char WHERE pc=0 ORDER BY ally');


		echo '<div id="stitle">Non-Playable Characters</div>';
		echo '<table id="tab-base"><thead><tr><th scope="col">Name</th><th scope="col">Loyalty</th><th scope="col">Description</th><tr><thead><tbody>';
		for ($i = 0; $i < count($NPC_query); $i++)
		{
			echo '<tr>';
			echo '<td><a href="index.php?id=dnd&sub=char&CID=' . strval($NPC_query[$i]['CID']) . '" class="table">' . $NPC_query[$i]['cname'] . '</a></td>';
			echo '<td>';
			if ($NPC_query[$i]['ally']==1)
			{
				echo 'Lib. Army';
			}
			elseif ($NPC_query[$i]['ally']==3)
			{
				echo '???';
			}
			elseif ($NPC_query[$i]['ally']==2)
			{
				echo 'Emperor';
			}
			else
			{
				echo '???';
			}
			echo '</td>';
			echo '<td>' . $NPC_query[$i]['sdesc'] . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table><br />';
	}

	private function getIChar($page_db)
	{

		$getvars = array_keys($_GET);
		#Determining if the user requested a single character sheet,
		#or the general character page - the latter lists all
		#characters in the game
		if(($key = array_search('CID', $getvars)) !== false) 
		{
			$CID = $_GET['CID'];
		}
		else
		{
			$CID = 0;
		}


		#Error checking on input value
		#If legit value not provided, go to the generic CID=0 page
		$CID_query = $page_db->Select('SELECT CID FROM gdnd_char');
		$CID_vals = [];
		for ($i = 0; $i < count($CID_query); $i++)
		{
			array_push($CID_vals, $CID_query[$i]['CID']);
		}
		if(($key = array_search($CID, $CID_vals)) !== false)
		{
			$CID_s = $CID;
		}
		else
		{
			$CID_s = 0;
		}

		#Selecting proper page generation based on selection
		if ($CID_s == 0)
		{
			$this->getMChar($page_db);
		}
		else
		{
			#DB select for individual character information
			$CID_Iquery = $page_db->Select('SELECT cname, pc, pname, ally, sdesc, height, weight, haircol, eyecol, clothes, location, backstory FROM gdnd_char WHERE CID = ' . $CID_s);
			#Displaying all the char info prettily
			?>
				<div id='ltitle'>Character Info:  <? echo $CID_Iquery[0]["cname"]; ?></div>
				<table id='tab-minimalist' summary='Character Info'>
				<?
					echo '<tr><th>Full Name:</th><td>' . $CID_Iquery[0]['cname'] . '</td></tr>';
					if ($CID_Iquery[0]['pc']==1)
					{
						echo '<tr><th>Played by:</th><td>' . $CID_Iquery[0]['pname'] . '</td></tr>';
					}
					echo '<tr><th>Brief Description:</th><td>' . $CID_Iquery[0]['sdesc'] . '</td></tr>';
					echo '</table>';
					if ($CID_Iquery[0]['ally']==1)
					{
						echo '<div id="small"><i>Friend of the liberation army</i></div>';
					}
					echo '<div id="niceline"></div>';
				?>
				<div id='sstitle2'>Character Traits</div>	
				<table id='tab-minimalist-s' summary='Character Info 2'>
				<?
					echo '<tr><th>Height:</th><td>' . $CID_Iquery[0]['height'] . ' cm</td></tr>';
					echo '<tr><th>Weight:</th><td>' . $CID_Iquery[0]['weight'] . ' kg</td></tr>';
					echo '<tr><th>Hair Colour:</th><td>' . $CID_Iquery[0]['haircol'] . '</td></tr>';
					echo '<tr><th>Eye Colour:</th><td>' . $CID_Iquery[0]['eyecol'] . '</td></tr>';
					echo '<tr><th>Clothes:</th><td>' . $CID_Iquery[0]['clothes'] . '</td></tr>';
					echo '<tr><th>Location:</th><td>' . $CID_Iquery[0]['location'] . '</td></tr>';
					echo '<tr><th>Backstory:</th></td>' . $CID_Iquery[0]['backstory'] . '</td></tr>';		
				?>
				</table>
				<div id='sstitle2'>Abilities</div>	
				<br />
				<div id='sstitle2'>Inventory</div>
				<br />
				<div id='niceline'></div>		
				<div id='tiny'>Navigate to:</div>
				<a class='tiny' href='index.php?id=dnd&sub=char'>Full PC/NPC List</a> <br />
				
			<?
		}
	}
}

