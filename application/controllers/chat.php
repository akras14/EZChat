<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat extends CI_Controller {

    //Default Constructor 
    function __construct(){
        parent::__construct();

        $this->load->model('chatmodel');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('login_model');  

        //Make sure user is logged in
        if (!$this->session->userdata('loggedin'))  //Check to see if user is already logged in
        {
            redirect('login/index');
        }

    }

    //Default Function
    public function index(){

        $data['chatid'] = 1;
        $data['nickname'] = $this->session->userdata('nickname');

        //TODO if needed, I can move this to login area to keep track of converstatinos since login until logout
        //******** Let's Find Out Last Message posted BEFORE user has joined the chat
        //1. Get all messages
        $allMessages = $this->chatmodel->getChatMessages($data['chatid'], 0);
        //2. Calculate Number of Rows
        $numberRows = $allMessages->num_rows();
        //3. Get ID for the last Row
        $lastRow = $allMessages->row((int)$numberRows - 1);
        //4. Store that ID in user session
        //Check to make sure database is not empty
        if ($lastRow != NULL) 
            $this->session->set_userdata('lastMessageToPost', $lastRow->message_id);
        else
            $this->session->set_userdata('lastMessageToPost', 0);          
        //*******Now lastMessageToPost points to first message that the user should see

        //Load Default View
        $this->load->view('secure/chatview', $data);
    }

    //Log User Out
    public function logout(){
        $sessionData = array( //Logout User
            'loggedin' =>  FALSE
        );
        $this->session->set_userdata($sessionData);
        redirect('login/index');

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
            $userData = $this->login_model->get_user();

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
                        $this->login_model->change_mynickname( $this->input->post('nickname'));
                    }

                    // Load chat view TODO update to home page
                    $data['nickname'] = $this->input->post('nickname');
                    $data['chatid']=1;

                    $this->load->view('template/header');
                    $this->load->view('secure/chatview', $data);

                } 
                // Else check if user want's to change just the nickname
                else if ($this->input->post('nickname') != NULL){
                    //Update Nickname
                    $this->login_model->change_mynickname( $this->input->post('nickname'));
                    
                    // Load chat view TODO update to home page
                    $data['nickname'] = $this->input->post('nickname');
                    $data['chatid']=1;

                    $this->load->view('template/header');
                    $this->load->view('secure/chatview', $data);


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

    //Gets Called by Ajax Request to insert message
    public function ajax_call_insertMessage(){
        $username = $this->input->post('username');
        $chatid = $this->input->post('chatid');
        $chatmessage = $this->input->post('chatmessage');
        $this->chatmodel->addChatMessage($username, $chatid, $chatmessage); 
        echo json_encode("Insert Complete " . $username ); //Let front end know that the function was sucessful
    }

    //Gets Called by Ajax Request to Return all Messages for thsi Chat id
    public function ajax_call_getMessages(){
        //Get ChatID 
        $chatid = $this->input->post('chatid');

        //Get Last Message ID to Post for this user
        $lastMessageToPost = $this->session->userdata('lastMessageToPost');

        //Access database to see if any new messages are posted
        $chatmessages = $this->chatmodel->getChatMessages($chatid, $lastMessageToPost);

        //If querry return any new results and lastRow ID is Greater Previous Post ID
        if ($chatmessages->num_rows() > 0 )
        {
            //Get All of New Messages to Post
            $chathtml ='';
            foreach( $chatmessages->result() as $chatmessage)
            {
                $chathtml .= '&#60;'. $chatmessage->user_name. '&#62; ' . $chatmessage->message_content . '<br />'; 
            }

            $result = array('status' => 'ok', 'content' => $chathtml);


        } else { //No New Results
            $result = array('status' => 'No New Messages' . $lastMessageToPost, 'content' => '');
        }

        //Send result out
        echo json_encode($result);
    }
}
