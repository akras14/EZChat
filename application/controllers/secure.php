<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Secure extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->model('login_model');
        $this->load->library('email');

        if (!$this->session->userdata('loggedin')) 	//Check to see if user is already logged in
        {
            redirect('login/index');
        }

    }

    public function index()
    {
        //1. Get list of all chatrooms
        $allRooms = $this->login_model->get_rooms();

        //If No Rooms Are Created
        if ($allRooms->num_rows() == 0) {

            //Populate Blank Container
            $data['container'] = "No Rooms Created Yet, You can Create a New Room";

        }
        //Else There are Chat Rooms Available
        else {
            $userid = $this->session->userdata('id');
            //Populate Chat Room Container
            $roomhtml ='<h2>List of Rooms</h2><div id=\'roomList\'>';
            //Populate table to contain list of rooms
            $roomhtml .= '<table border="1">';

            foreach( $allRooms->result() as $oneRoom)
            {
                $roomhtml .= '<tr><td width="200px"><a class=\'roomlink\' href=\''. site_url('chat/index'). '/' 
                    . $oneRoom->chat_id .'\'>'  . $oneRoom->chat_name . '</a></td>'; 

                //Check if user has created the room and can delete it
                if ($userid == $oneRoom->user_id){

                    //Add Delete Link
                    $roomhtml .= '<td width="100px"><a href=\''. site_url('secure/deleteRoom'). '/' 
                        . $oneRoom->chat_id  . '\'>Delete</a></td></tr>';
                }
                //Else add blank table cell
                else {
                    $roomhtml .= '<td width="100px"></td></tr>';

                } 
            }

            $roomhtml .= '</table></div>';
            $data['container'] = $roomhtml;

        }

        //Load Home View
        $this->load->view('template/header');
        $this->load->view('secure/homeview', $data);
    }


    //Changes User Password or Nickname
    public function changeInfo(){ 

        //Validate user input
        $this->form_validation->set_rules('nickname', 'Nickname', 'trim|xss_clean|alpha_dash');
        $this->form_validation->set_rules('oldpassword', 'Current Password', 'trim|xss_clean|required|alpha_dash');
        $this->form_validation->set_rules('password', 'Password', 'trim|xss_clean|matches[passwordConfirm]|alpha_dash');

        //If user input is not valid
        if ($this->form_validation->run() == FALSE ) { 
            $this->load->view('template/header');
            $this->load->view('secure/changeInfoView');
        } else {
            //1. Get user data            
            $userData = $this->login_model->get_user_from_session();

            //2. Encrypt Old Password
            $sha1_oldpassword = sha1($this->input->post('oldpassword'));


            //3. If old password matches -> Process Other Fields 
            if( $sha1_oldpassword == $userData['password'] ) {

                //If User Wants to Change Password
                if($this->input->post('password') != NULL) {

                    // Encryp New password
                    $sha1_password = sha1($this->input->post('password'));

                    //Update Password
                    $this->login_model->change_mypassword($sha1_password);

                    //Check to make Sure if User wants to Chagne Password AND Update Nickname
                    if($this->input->post('nickname') != NULL){
                        //Update Nickname
                        $nickname = $this->input->post('nickname');
                        $this->session->set_userdata('nickname', $nickname);
                        $this->login_model->change_mynickname($nickname);
                    }

                    //Redirect home
                    redirect('secure/index');
                } 
                // Else check if user want's to change just the nickname
                else if ($this->input->post('nickname') != NULL){
                    //Update Nickname
                    $this->login_model->change_mynickname( $this->input->post('nickname'));

                    $nickname = $this->input->post('nickname');
                    $this->session->set_userdata('nickname', $nickname);
                    //Redirect home
                    redirect('secure/index');

                } 
                // User didn't submit any data to change -> why submit form than?
                else {

                    $data['message'] = "Pleae enter data to change or press cancel to return home<br /><br />";
                    $this->load->view('template/header');
                    $this->load->view('secure/changeInfoView', $data);
                } 

            } 

            //4. Else Current Password Didn't Match -> Display Error
            else {

                //Reload view with custom error message
                $data['message'] = "Pleae enter valid current password <br /> <br />";
                $this->load->view('template/header');
                $this->load->view('secure/changeInfoView', $data);

            }
        }
    }

    //Log User Out
    public function logout(){
        $sessionData = array( //Logout User
            'loggedin' =>  FALSE
        );
        $this->session->set_userdata($sessionData);
        $this->session->sess_destroy(); 
        redirect('login/index');

    }




    //Add New Chatroom
    public function createNewRoom(){

        //Validate user input
        $this->form_validation->set_rules('roomname', 'Room Name', 'required|trim|xss_clean|alpha_dash');

        //If user input is not valid
        if ($this->form_validation->run() == FALSE ) {

            $this->load->view('template/header');
            $this->load->view('secure/newRoomView');
        } 
        //Else create New Room
        else {

            //1. Get UserID
            $userid = $this->session->userdata('id');
            //2. Get New Room Name
            $newRoomName = $this->input->post('roomname');
            //3. Check that room is not taken
            $result = $this->login_model->check_room($newRoomName);

            //If Room Name is Available
            if ($result == NULL) {
                //4. Add New Room
                $userData = $this->login_model->add_room($newRoomName, $userid);
                //Add room to remote servers
                $this->createRemoteRooms($newRoomName );
                redirect('secure/index');
            }
            //5. Else Let user know that the room name is taken
            else {
                //Set Custom Error Message
                $data['message'] = 'Sorry room name ' . $newRoomName . ' is already taken.<br/>';
                //Load message for the user
                $this->load->view('template/header');
                $this->load->view('secure/newRoomView', $data);

            }
        }
    }

    public function deleteRoom($roomid){

        //Delete Remote Rooms
        $this->removeRemoteRooms($roomid);

        //Delete the room
        $result = $this->login_model->delete_room($roomid);
        redirect('secure/index');
    }

    /*****************************************************************i*****/    
    /******************** Remote Access Function Block *********************/

    //Driver to Create Remote Room
    private function createRemoteRooms($newRoomName){

        //1. Define Sites to Post To
        $Alex = 'http://cmpe208alexkras.com/';
        //$Wilson = 'http://wjtsang208.com/';
        //$Melisa = 'http://mahleesa.com/';
        //$Alouise = 'http://';
        $allUrls = array (
            'Alex' => ''. $Alex . 'index.php/backend/createNewRoom/'//,
            //'Alouise' => ''. $Alouise . 'index.php/backend/createNewRoom/',
            //'Melisa' => '' . $Melisa . 'index.php/backend/createNewRoom/',
            //'Wilson' => '' . $Wilson . 'index.php/backend/createNewRoom/'       
        );

        //2. Post data to remote sites
        foreach ($allUrls as $url) {
            $data = array ('newRoomName' => $newRoomName);

            //open connection
            $ch = curl_init($url);

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);
            return $result;
        }
    }

    //Driver to Remove Remote Rooms
    private Function removeRemoteRooms($roomid){

        //1. Define Sites to Post To
        $Alex = 'http://cmpe208alexkras.com/';
        //$Wilson = 'http://wjtsang208.com/';
        //$Melisa = 'http://mahleesa.com/';
        //$Alouise = 'http://';
        $allUrls = array (
            'Alex' => ''. $Alex . 'index.php/backend/deleteRoom/'//,
            //'Alouise' => ''. $Alouise . 'index.php/backend/deleteRoom/',
            //'Melisa' => '' . $Melisa . 'index.php/backend/deleteRoom/',
            //'Wilson' => '' . $Wilson . 'index.php/backend/deleteRoom/'       
        );

        foreach ($allUrls as $url) {
            $data = array ('roomid' => $roomid);
            //open connection
            $ch = curl_init($url);

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);
            return $result;
        }
    }

    /****************** End of Remote Function Block ****************************/
}
