<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//Used for remote calls into the database
class Backend extends CI_Controller {

    //Default Constructor 
    function __construct() {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('chatmodel');
        $this->load->helper('url');
        $this->load->library('session');

    }

    //Default Function 
    public function index()
    {
        //No need to access this area, since it's only the drive for backend calls to insert data
        show_404();//Default function to show page not found error 
    }

    //**********************************************************************
    //
    //      Following Functions Should Not be KNOW to outside users
    //      And are being used for php Curl calls to post data to cross 
    //      site databasess
    //
    //*********************************************************************

    //Add New Chatroom to All Chats
    public function createNewRoom(){
        $newRoomName = $this->input->post('newRoomName');
        //1. Remote user = -1 
        $userid = -1;
        //2. New Room Name Passed on as variable

        //3. Check that room is not taken (Should Never Happend)
        $result = $this->login_model->check_room($newRoomName);

        //If Room Name is Available
        if ($result == NULL) {
            //4. Add New Room
            $userData = $this->login_model->add_room($newRoomName, $userid);
            //Add room to remote servers
        }

        return true;
    }

    //Delete a Chatroom from All Sites
    public function deleteRoom(){
        $roomid = $this->input->post('roomid');
        //Delete the room
        $result = $this->login_model->delete_room($roomid);
        redirect('secure/index');
    }

    //Insert Chat Messages into All Sites
    public function insertMessage(){
        $username = $this->input->post('username');
        $chatid = $this->input->post('chatid');
        $chatmessage = $this->input->post('chatmessage');

        $result = $this->chatmodel->addChatMessage($username, $chatid, $chatmessage); 
    }

}
