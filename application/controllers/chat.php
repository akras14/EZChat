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

        //1. Validate user input
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|matches[passwordConfirm]|alpha_dash');

        if ($this->form_validation->run() == FALSE ) { //If user input is not valid
            $this->load->view('template/header');
            $this->load->view('secure/changePasswordView');
        } else {
            $sha1_password = sha1($this->input->post('password')); //Encryp the password
            $this->login_model->change_mypassword($sha1_password); //Enter Password into database
            redirect('login/index');
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
