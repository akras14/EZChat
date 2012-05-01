<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat extends CI_Controller {
    //Default Constructor 
    function __construct(){
        parent::__construct();

        $this->load->model('chatmodel');
        $this->load->helper('url');
    }

    //Default Function
    public function index(){
        $data['chatid'] = 1;
        $data['userid'] = 1;
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
        $chatid = $this->input->post('chatid');
        $chatmessages = $this->chatmodel->getChatMessages($chatid); 
        $chathtml ='';
        foreach( $chatmessages->result() as $chatmessage)
        {
            $chathtml .= $chatmessage->message_content . '<br />'; 

        }

        $result = array('status' => 'ok', 'content' => $chathtml);
        echo json_encode($result);
    }
}
