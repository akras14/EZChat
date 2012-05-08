<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//Used for remote calls into the database
class Backend extends CI_Controller {

    //Default Constructor 
    function __construct() {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->helper('url');
        $this->load->library('session');

    }

    //Default Function 
    public function index()
    {
        //No need to access this area, since it's only the drive for backend calls to insert data
        show_404(); 
    }


    //Add New Chatroom
    public function createNewRoom($newRoomName){
        echo "In the Function";

        //1. Remote user = -1 
        $userid = -1;
        //2. Get New Room Name
        
        // Passed on as variable

        //3. Check that room is not taken
        $result = $this->login_model->check_room($newRoomName);

        //If Room Name is Available
        if ($result == NULL) {
            //4. Add New Room
            $userData = $this->login_model->add_room($newRoomName, $userid);
            //Add room to remote servers
        }

        return true;
    }
}
