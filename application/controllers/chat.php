<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat extends CI_Controller {
    //Default Constructor 
    function __construct(){
        parent::__construct();

        $this->load->model('chatmodel');
        $this->load->helper('url');
        $this->load->library('session');
    }

    //Default Function
    public function index(){
        $data['chatid'] = 1;
        $data['userid'] = 1;

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
        $this->load->view('chatview', $data);
    }

    //Gets Called by Ajax Request to insert message
    public function ajax_call_insertMessage(){
        $username = $this->input->post('username');
        $chatid = $this->input->post('chatid');
        $chatmessage = $this->input->post('chatmessage');
        $this->chatmodel->addChatMessage($username, $chatid, $chatmessage); 
        echo json_encode("Insert Complete"); //Let front end know that the function was sucessful
    }

    //Gets Called by Ajax Request to Return all Messages for thsi Chat id
    public function ajax_call_getMessages(){	
        //Get ChatID and Last Message ID before user joined
        $chatid = $this->input->post('chatid');
        $lastMessageToPost = $this->session->userdata('lastMessageToPost');
        //echo "In ajax call and lastMessageToPost is " . $lastMessageToPost;

        //Access database to see if any new messages are posted
        $chatmessages = $this->chatmodel->getChatMessages($chatid, $lastMessageToPost);


        //If querry return any new results
        if ($chatmessages->num_rows() > 0)
        {
            //1. Update Last Message that was posted
            $numRows = $chatmessages->num_rows(); 
            $lastRow = $chatmessages->row((int)$numRows - 1);

            $this->session->set_userdata('lastMessageToPost', $lastRow->message_id);

            //2. Get All of New Messages to Post
            $chathtml ='';
            foreach( $chatmessages->result() as $chatmessage)
            {
                $chathtml .= '&#60;'. $chatmessage->user_name. '&#62; ' . $chatmessage->message_content . '<br />'; 
            }

            $result = array('status' => 'ok', 'content' => $chathtml);


        } else { //No New Results
            $result = array('status' => 'No New Messages', 'content' => '');
        }

        //Send result out
        echo json_encode($result);
    }
}
