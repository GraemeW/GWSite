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
        if (isset($_GET['CompID'])) 
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


    }

    #Individual computer listing
    private function getIComp($page_db)
    {
        $getvars = array_keys($_GET);
        #Grabbing the SID parameter
        if(($key = array_search('CompID', $getvars)) !== false) 
        {
            $CompID = $_GET['CompID'];
        }
        else
        {
            $CompID = 0;
        }

        #Error checking on input value
        #If legit value not provided, go to the main story page
        $CompID_query = $page_db->Select('SELECT CompID FROM gcomp');
        $CompID_vals = [];
        for ($i = 0; $i < count($CompID_query); $i++)
        {
            array_push($CompID_vals, $CompID_query[$i]['CompID']);
        }
        if(($key = array_search($CompID, $CompID_vals)) !== false)
        {
            $CompID_s = $CompID;
        }
        else
        {
            $CompID_s = 0;
        }

       #Selecting proper page generation based on selection
        if ($CompID_s == 0)
        {
            #Generate main story page when valid selection not provided
            $this->getComps($page_db);
        }
        else
        {

        }

    }

 }
?>
