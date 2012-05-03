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
        $this->session->set_userdata('lastMessageBeforeUser', $lastRow->message_id);
        //*******Now lastMessageBeforeUser points to first message that the user should see

        //Load Default View
        $this->load->view('chatview', $data);
    }

    //Gets Called by Ajax Request to insert message
    public function ajax_call_insertMessage(){
        $userid = $this->input->post('userid');
        $chatid = $this->input->post('chatid');
        $chatmessage = $this->input->post('chatmessage');
        $this->chatmodel->addChatMessage($userid, $chatid, $chatmessage); 
    }

    //Gets Called by Ajax Request to Return all Messages for thsi Chat id
    public function ajax_call_getMessages(){	
        //Get ChatID and Last Message ID before user joined
        $chatid = $this->input->post('chatid');
        $lastMessageBefore = $this->session->userdata('lastMessageBeforeUser');

        //Access database to see if any new messages are posted
        $chatmessages = $this->chatmodel->getChatMessages($chatid, $lastMessageBefore);

        //If querry return any new results
        if ($chatmessages->num_rows() > 0)
        {
            $chathtml ='';
            foreach( $chatmessages->result() as $chatmessage)
            {
                $chathtml .= $chatmessage->message_content . '<br />'; 

            }

            $result = array('status' => 'ok', 'content' => $chathtml);

        } else { //No New Results
            $result = array('status' => 'No New Messages', 'content' => '');
        }

        //Send result out
        echo json_encode($result);
    }
}
