<?

class gDND extends gPage {
    private $DnDsub;
    private $story_perpage = 5;

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
        <p>It has been several hundred years since the thousand rose war, granting the realm of Faelun a period of relative stability.  In this time, the empire has expanded trade throughout its borders and mended relations with Historia to the north.  However, dark rumblings near Winterhaven, the re-appearance of the Prince of Shadows and the weakening of the Archmage's wards suggest that all is not well with Faelun.  There are those who believe the Emperor has lost his hold on the realm, that his mind has been clouded by an evil presence.  There are those who would suggest the root of that evil to be his newly wed wife and queen, the sorceress Wendolin.</p>

        <p>Pyke Jamb, Lawrence Iferget and Andereth, young pottery students with a knack for adventure, recently embarked on a journey to Winterhaven to further their crockery acumen.  Their arrival, however, would not be marked by terra cotta tutelage.  Unbeknownst to the travellers, their trip to Winterhaven would start a cascade of events that would forever alter their lives and shape the very world in which they live.</p> 

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

    #Pull listed stories - implemented by fct generate_escapades
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
            <i>Adventure started on: &nbsp; <? echo $major_query[$i]['date']; ?></i><br />
            <div id='tab_in'>
            <? #Characters ?>
            <div id='sstitle'>Characters</div>
            <?            
            $CIDs = explode('-', $major_query[$i]['charIDs']);
            echo '<ul>';
            for ($j=0; $j < count($CIDs); $j++)
            {
                $char_name = $this->getCharName($page_db, $CIDs[$j], 2);
                echo '<li>' . $char_name . '</li>';
            }
            echo '</ul>';

            #Brief Description
            ?><div id='sstitle'>Brief Description</div><?
            echo $major_query[$i]['sdesc'] . '<br /><br />';

            $minor_query = $page_db->Select('SELECT SID, sname, sdesc, charIDs, date FROM gdnd_story WHERE majormin=0 AND sparentID=' . $major_query[$i]['SID'] . ' ORDER BY date');

            if (!empty($minor_query))
            {
                ?> <div id='sstitle'>Minor Misadventures</div><?
                echo '<ul>';
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
        $SID_query = $page_db->Select('SELECT SID FROM gdnd_story WHERE majormin = 1');
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
            $SID_Iquery = $page_db->Select('SELECT SID, sname, majormin, sdesc, ldesc, charIDs, date FROM gdnd_story WHERE SID = ' . $SID_s . ' OR sparentID = ' . $SID_s . ' ORDER BY date');

            #Identifying position of the SID entry in the query array
            #This should be first position, but just making sure
            $minor_SIDs = [];    
            for ($i=0; $i < count($SID_Iquery); $i++)
            {
                if($SID_Iquery[$i]['majormin']==1)
                {
                    $major_index = $i;
                }
                else
                {
                    array_push($minor_SIDs,$i); 
                }                       
            }

            #Displaying all story info
            ?>
            <div id='ltitle'><? echo $SID_Iquery[$major_index]['sname']; ?></a></div>
            <table id='tab-minimalist' summary='Adventure Info'>
            <?
            echo '<tr><th>Adventure Start Date:</th><td>' . $SID_Iquery[$major_index]['date'] . '</td></tr>';
            echo '<tr><th>Brief Description:</th><td>' . $SID_Iquery[$major_index]['sdesc'] . '</td></tr>';
               
            echo '<tr><th>Characters:</th><td>';
            $CIDs = explode('-', $SID_Iquery[$major_index]['charIDs']);
            for ($i=0; $i < count($CIDs); $i++)
            {
                $char_name = $this->getCharName($page_db, $CIDs[$i], 2);
                echo $char_name;
                if ($i < count($CIDs) - 1)
                {
                    echo '<br />';
                }
            }
            echo '</td></tr>';
            
            #Saving detailed description for later 
            $long_desc = $SID_Iquery[$major_index]['ldesc'];
            #Kicking out the major query entry from the array
            unset($SID_Iquery[$major_index]);
            
            echo '<tr><th>Minor Misadventures:</th><td>';
            for ($i=0; $i < count($minor_SIDs); $i++)
            {
                echo '<i>' . $SID_Iquery[$minor_SIDs[$i]]['sname'] . '</i>: &nbsp;' . $SID_Iquery[$minor_SIDs[$i]]['sdesc'] . ' <br />';
                ?> <div id='tab_in'>Details: <?
                echo $SID_Iquery[$minor_SIDs[$i]]['ldesc'] . '</div>';
            }
            echo '</td></tr>';
            echo '</table>';
            
            #Printing long description of story
            ?>
            <div id='sstitle'>Long Description</div>  
            <br />
            <?
            echo $long_desc;
        }
    }
    
    #------------------------------------------------------------------------------------------------------
    #MAP SPECIFIC
    #------------------------------------------------------------------------------------------------------
    
    #------------------------------------------------------------------------------------------------------
    #CHARACTER SPECIFIC
    #------------------------------------------------------------------------------------------------------
    
    private function getCharName($page_db, $CID, $linkset)
    {
        #Input parameters:  Database parameter, character ID, linkset
        #Output parameter:  Character name
        
        #linkset:  0 for no link, 1 for standard link, 2 for table font link
        $cname_query = $page_db->Select('SELECT cname FROM gdnd_char WHERE CID = ' . $CID);
        if ($linkset==0)
        {
            return $cname_query[0]['cname'];
        }
        elseif($linkset==1)
        {
            $linked_char = '<a href="index.php?id=dnd&sub=char&CID=' . strval($CID) . '">' . $cname_query[0]['cname'] . '</a>';
            return $linked_char;
        }
        elseif($linkset==2)
        {
            $linked_char = '<a href="index.php?id=dnd&sub=char&CID=' . strval($CID) . '" class="table">' . $cname_query[0]['cname'] . '</a>';
            return $linked_char;
        }
        else
        {
            $linked_char = '<a href="index.php?id=dnd&sub=char&CID=' . strval($CID) . '">' . $cname_query[0]['cname'] . '</a>';
            return $linked_char;
        }
    }
    
    private function getMChar($page_db)
    {
        echo '<div id="ltitle">Characters of Relative Importance</div>';

        #Grabbing all of the PCs
        $PC_query = $page_db->Select('SELECT CID, cname, pname, sdesc, status FROM gdnd_char WHERE pc=1');

        echo '<div id="stitle">Player Characters</div>';
        echo '<table id="tab-base"><thead><tr><th scope="col">Name</th><th scope="col">Player</th><th scope="col">Description</th></tr></thead><tbody>';
        for ($i = 0; $i < count($PC_query); $i++)
        {
            echo '<tr>';
            #Changing font colour depending on character status - 0 is dead, 1 is alive, 2 is missing
            if ($PC_query[$i]['status']==0)
            {
                $font_class = 'table-red';
            }
            elseif ($PC_query[$i]['status']==2) 
            {
	            $font_class = 'table-purple';
            }
            else
            {
                $font_class = 'table';    
            }
            echo '<td><a href="index.php?id=dnd&sub=char&CID=' . strval($PC_query[$i]['CID']) . '" class="' . $font_class . '">' . $PC_query[$i]['cname'] . '</a></td>';
            echo '<td>' . $PC_query[$i]['pname'] . '</td>';
            echo '<td>' . $PC_query[$i]['sdesc'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table><br /><br />';

        #Grabbing all of the NPCs
        $NPC_query = $page_db->Select('SELECT CID, cname, ally, sdesc, status FROM gdnd_char WHERE pc=0 ORDER BY ally');


        echo '<div id="stitle">Non-Playable Characters</div>';
        echo '<table id="tab-base"><thead><tr><th scope="col">Name</th><th scope="col">Loyalty</th><th scope="col">Description</th><tr><thead><tbody>';
        for ($i = 0; $i < count($NPC_query); $i++)
        {
            echo '<tr>';
            #Changing font colour depending on character status - 0 is dead, 1 is alive, 2 is missing
            if ($NPC_query[$i]['status']==0)
            {
                $font_class = 'table-red';
            }
            elseif ($NPC_query[$i]['status']==2) 
            {
                $font_class = 'table-purple';
            }
            else
            {
                $font_class = 'table';    
            }
            echo '<td><a href="index.php?id=dnd&sub=char&CID=' . strval($NPC_query[$i]['CID']) . '" class="' . $font_class . '">' . $NPC_query[$i]['cname'] . '</a></td>';
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
            $CID_Iquery = $page_db->Select('SELECT cname, pc, pname, ally, sdesc, status, height, weight, haircol, eyecol, clothes, location, backstory FROM gdnd_char WHERE CID = ' . $CID_s);
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
                echo '<tr><th>Character Status:</th><td>';
                if ($CID_Iquery[0]['status']==1)
                {
                    echo 'Alive and well';
                }
                elseif($CID_Iquery[0]['status']==0)
                {
                    echo 'Deceased';
                }
                elseif($CID_Iquery[0]['status']==2)
                {
                    echo 'Unknown';
                }
                else
                {
                    echo 'Unknown';
                }
                echo '</td></tr>';
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

