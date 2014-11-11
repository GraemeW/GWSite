<?

class gComp extends gPage {
    function __construct()
    {
        parent::__construct();
    }


    #------------------------------------------------------------------------------------------------------
    #MAJOR CLASS FUNCTIONS
    #------------------------------------------------------------------------------------------------------
    #Overwriting header generator function from parent
    public function generate_content($page_db)
    {
        if (isset($_GET['compID'])) 
        {
            $this->getIComp($page_db);
        }
        else
        {
            $this->getComps($page_db);
        }
    }

    #Overall listing of computers
    private function getComps($page_db)
    {
        $allcomp_string = 'SELECT compID, compname, builddate, CPU, CPUcooler, motherboard, memory, storage1, storage2, storage3, videocard, compcase, PSU, opticaldrive, OS, monitor, misc1, misc2 FROM gcomp WHERE 1';
        $allcomp_query = $page_db->Select($allcomp_string);

        ?><div id="ltitle">My Computers</div>
        I love playing with and building computers.  Right now I have 2 custom-built PCs and a beautiful ThinkPad.  The details are below: <br /><br />
        <?
       
        for ($i=0; $i<count($allcomp_query); $i++)
        {
            echo '<div id="stitle"><a href="index.php?id=comp&compID=' .  $allcomp_query[$i]['compID'] . '">Computer Name: &nbsp;' . $allcomp_query[$i]['compname'] . '</a></div>';
            echo '<div id="tab_in">';
            ?><table id='tab-minimalist' summary='Computer_<? echo $allcomp_query[$i]['compID']; ?>'> <?    
            echo '<tr><th>Build Date:</th><td>' . $allcomp_query[$i]['builddate'] . '</td></tr>';
            echo '<tr><th>CPU:</th><td>' . $allcomp_query[$i]['CPU'] . '</td></tr>';
            echo '<tr><th>CPU Cooler:</th><td>' . $allcomp_query[$i]['CPUcooler'] . '</td></tr>';
            echo '<tr><th>Motherboard:</th><td>' . $allcomp_query[$i]['motherboard'] . '</td></tr>';
            echo '<tr><th>Memory:</th><td>' . $allcomp_query[$i]['memory'] . '</td></tr>';
            echo '<tr><th>Storage 1:</th><td>' . $allcomp_query[$i]['storage1'] . '</td></tr>';
            echo '<tr><th>Storage 2:</th><td>' . $allcomp_query[$i]['storage2'] . '</td></tr>';
            echo '<tr><th>Storage 3:</th><td>' . $allcomp_query[$i]['storage3'] . '</td></tr>';
            echo '<tr><th>Video Card:</th><td>' . $allcomp_query[$i]['videocard'] . '</td></tr>';
            echo '<tr><th>Case:</th><td>' . $allcomp_query[$i]['compcase'] . '</td></tr>';
            echo '<tr><th>PSU:</th><td>' . $allcomp_query[$i]['PSU'] . '</td></tr>';
            echo '<tr><th>Optical Drive:</th><td>' . $allcomp_query[$i]['opticaldrive'] . '</td></tr>';
            echo '<tr><th>Operating System:</th><td>' . $allcomp_query[$i]['OS'] . '</td></tr>';
            echo '<tr><th>Monitor:</th><td>' . $allcomp_query[$i]['monitor'] . '</td></tr>';
            echo '<tr><th>Miscellaneous 1:</th><td>' . $allcomp_query[$i]['misc1'] . '</td></tr>';
            echo '<tr><th>Miscellaneous 2:</th><td>' . $allcomp_query[$i]['misc2'] . '</td></tr>';
            echo '</table></div> <br />';
        }
    }

    #Individual computer listing
    private function getIComp($page_db)
    {
        $getvars = array_keys($_GET);
        #Grabbing the SID parameter
        if(($key = array_search('compID', $getvars)) !== false) 
        {
            $compID = $_GET['compID'];
        }
        else
        {
            $compID = 0;
        }

        #Error checking on input value
        #If legit value not provided, go to the main story page
        $compID_query = $page_db->Select('SELECT compID FROM gcomp');
        $compID_vals = [];
        for ($i = 0; $i < count($compID_query); $i++)
        {
            array_push($compID_vals, $compID_query[$i]['compID']);
        }
        if(($key = array_search($compID, $compID_vals)) !== false)
        {
            $compID_s = $compID;
        }
        else
        {
            $compID_s = 0;
        }

       #Selecting proper page generation based on selection
        if ($compID_s == 0)
        {
            #Generate main story page when valid selection not provided
            $this->getComps($page_db);
        }
        else
        {
            $Icomp_string = 'SELECT compID, compname, builddate, CPU, CPUcooler, motherboard, memory, storage1, storage2, storage3, videocard, compcase, PSU, opticaldrive, OS, monitor, misc1, misc2 FROM gcomp WHERE compID = ' . strval($compID_s);
            $Icomp_query = $page_db->Select($Icomp_string);

            echo '<div id="stitle"><a href="index.php?id=comp&compID=' .  $Icomp_query[0]['compID'] . '">Computer Name: &nbsp;' . $Icomp_query[0]['compname'] . '</a></div>';
            echo '<div id="tab_in">';
            ?><table id='tab-minimalist' summary='Computer_<? echo $Icomp_query[0]['compID']; ?>'> <?    
            echo '<tr><th>Build Date:</th><td>' . $Icomp_query[0]['builddate'] . '</td></tr>';
            echo '<tr><th>CPU:</th><td>' . $Icomp_query[0]['CPU'] . '</td></tr>';
            echo '<tr><th>CPU Cooler:</th><td>' . $Icomp_query[0]['CPUcooler'] . '</td></tr>';
            echo '<tr><th>Motherboard:</th><td>' . $Icomp_query[0]['motherboard'] . '</td></tr>';
            echo '<tr><th>Memory:</th><td>' . $Icomp_query[0]['memory'] . '</td></tr>';
            echo '<tr><th>Storage 1:</th><td>' . $Icomp_query[0]['storage1'] . '</td></tr>';
            echo '<tr><th>Storage 2:</th><td>' . $Icomp_query[0]['storage2'] . '</td></tr>';
            echo '<tr><th>Storage 3:</th><td>' . $Icomp_query[0]['storage3'] . '</td></tr>';
            echo '<tr><th>Video Card:</th><td>' . $Icomp_query[0]['videocard'] . '</td></tr>';
            echo '<tr><th>Case:</th><td>' . $Icomp_query[0]['compcase'] . '</td></tr>';
            echo '<tr><th>PSU:</th><td>' . $Icomp_query[0]['PSU'] . '</td></tr>';
            echo '<tr><th>Optical Drive:</th><td>' . $Icomp_query[0]['opticaldrive'] . '</td></tr>';
            echo '<tr><th>Operating System:</th><td>' . $Icomp_query[0]['OS'] . '</td></tr>';
            echo '<tr><th>Monitor:</th><td>' . $Icomp_query[0]['monitor'] . '</td></tr>';
            echo '<tr><th>Miscellaneous 1:</th><td>' . $Icomp_query[0]['misc1'] . '</td></tr>';
            echo '<tr><th>Miscellaneous 2:</th><td>' . $Icomp_query[0]['misc2'] . '</td></tr>';
            echo '</table></div> <br />';
            echo '<div id="tiny">Navigate to:  <a class="tiny" href="index.php?id=comp">Computer Listing</a></div>';
        }

    }

 }
?>
