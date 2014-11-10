<?

class gHome extends gPage {
	private $select_array;

	function __construct()
	{
		parent::__construct();
	}

	public function generate_content($page_db)
	{	
		?>
		<div id="home-container">
		<?php

		$total_query = $page_db->Select('SELECT gcode, title, external, sdesc, gimg FROM gpage WHERE 1');
		shuffle($total_query);

		for ($i=0; $i < count($total_query); $i++)
		{
		?>
			<div id="home-split">
				<?
                if($total_query[$i]['external']==1)
                {
                ?>
                <div class="overtop">
                <a href="index.php?id=<? echo $total_query[$i]['gcode']; ?>">
                <img src="S1-img/collage/<? echo $total_query[$i]['gimg']; ?>" alt="<? echo $total_query[$i]["gcode"]; ?>" title="<? echo $total_query[$i]["title"]; ?>">
                <h2><span>External <br /> Website</span></h2>
                </a>
                </div>
                <?
                }
                else 
                {
                ?>
                <a href="index.php?id=<? echo $total_query[$i]['gcode']; ?>">
                <img src="S1-img/collage/<? echo $total_query[$i]['gimg']; ?>" alt="<? echo $total_query[$i]["gcode"]; ?>" title="<? echo $total_query[$i]["title"]; ?>">
                </a>
                <?    
                }
                ?>
			</div>
		<? } ?>
		
		</div>
		<?php
	}
}

