<?php
/**
 * Agents Controller
 * 
 *  
 *
 */

App::uses('Controller', 'Controller');

class AgentsController extends Controller {
    
    /**
     * loadfile method
     * Reads the content of the contacts files and returns an array with the contents of the file
     */
    private function loadFile(){
        
        $contacts = array();

        $fname = "contacts.csv";
        $output_dir = ROOT.DS.APP_DIR."/tmp/";
        // Read the contents of the file and storages at $myFile
        $myFile = file_get_contents($output_dir. $fname); 

        // The contents of the file it's converted to an array 
        $lines = explode(PHP_EOL, $myFile);
        
        // Process every line of the array and split every line in a Contact Name and Contact Zip Code
        foreach ($lines as $line){
            $r = explode(',',$line);
            
            // Excludes the line that includes the tags: name and zipcode
            if ($r[0] != 'name' and $r[1] != 'zipcode'){
                // build a "named key" array with the info of every contact
                $c = array(
                    'name' => $r[0],
                    'zipcode' => $r[1]
                );
                // store it on the contacts array
                $contacts[] = $c;
            }
        }
        // once the file it's processed it's returned
        return $contacts;
    }

    /**
     * groupContacts method
     * Get the contents of the file and returns it to the view
     */
    public function groupContacts(){
        $contacts = $this->loadFile();
        $this->set('contacts',$contacts);
    }

    /**
     * getDistance method
     * @param $zipa: Agent location info
     * @param $zipc: Contact Location info 
     * @param $unit: 'K' Kilometers, 'N' Nautic Miles, null Miles
     * @return distance
     *
     * Computes the distance between to geographical point given it's latitude and longitude 
     * using the Haversine Formula
     *
     */
    private function getDistance($zipa, $zipc, $unit=null){
        
        //Get latitude and longitude from geo data
        $latitudea = $zipa['location']['lat'];
        $longitudea = $zipa['location']['lng'];
        $latitudec = $zipc['location']['lat'];;
        $longitudec = $zipa['location']['lng'];
        
        //Calculate distance from latitude and longitude
        $theta = $longitudea - $longitudec;
        $dist = sin(deg2rad($latitudea)) * sin(deg2rad($latitudec)) +  cos(deg2rad($latitudea)) * cos(deg2rad($latitudec)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     * calculateHaversine method
     * @param $agents: Agents information array
     * @param $contacts: Contacts information array
     * @return array Distances btween the agemnts and every Contact 
     * 
     */
    private function calculateHaversine($agents,$contacts){
        // get the location info of the agents
        $zipa1 = $agents[0];
        $zipa2 = $agents[1];

        // Get every contact info and calculate the distance between the agents
        foreach ($contacts as $c){
            $nc = array(
                'name' => $c['name'],
                'zipcode' => $c['zipcode'],
                'da1' => $this->getDistance($zipa1,$c),
                'da2' => $this->getDistance($zipa2,$c)
            );
            $distances[] = $nc;
        }
        // return the computed distances
        return $distances;
    }

    // Comparisson function used to sort the contacts distances
    private function distanceCmp($a,$b){
        if ($a['distance'] == $b['distance']) {
            return 0;
        }
        return ($a['distance'] < $b['distance']) ? -1 : 1;
    }

    /**
     * group method
     * Received the data sent by the view via an Ajax call, computes the distance between contacts and agents,
     * groups the nearest contacts for the agents
     *
     * The result it's sent back to the view as a JSON stream
     *
     */
    public function group(){
        // tells the framwork to use the ajax layout
        $this->layout = 'ajax';
        // do not render the view, also don't look for the group.ctp view file
        $this->autoRender = false;

        // get the data on the Ajax request
        $data = $this->request->data;
        // Contacts array
        $contacts = $data['c'];
        // Agents array
        $agents = $data['a'];


        // calculates the distances
        $d = $this->calculateHaversine($agents,$contacts);

        // group the contacts according the distance to the agents
        foreach($d as $dis){
            // decides who contact it's closed to the agents
            if ($dis['da1'] <= $dis['da2']){
                $agent1[] = array(
                    'name' => $dis['name'],
                    'zipcode' => $dis['zipcode'],
                    'distance' => $dis['da1']
                );
            } else {
                $agent2[] = array(
                    'name' => $dis['name'],
                    'zipcode' => $dis['zipcode'],
                    'distance' => $dis['da2']
                );
            }
        }
        // sorts the contacts list for every agent
        usort($agent1,array('AgentsController',"distanceCmp"));
        usort($agent2,array('AgentsController',"distanceCmp"));

        // build the result array
        $result = array(
            'agent1' => $agent1,
            'agent2' => $agent2
        );

        // sent the result to the view as a JSON array
        echo json_encode($result);

    }
}
