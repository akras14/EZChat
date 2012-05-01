<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chatmodel extends CI_Model {

    public function __construct()
    {
        $this->load->database(); //Loads database
    }

    //Insert chat message into table
    public function addChatMessage($userid, $chatid, $messagecontent){
        $data = array ( 
            'user_id' => $userid,
            'chat_id' => $chatid,
            'message_content' => $messagecontent
        );
        return $this->db->insert('chatmessages', $data);
    }
    
    //Return chat messages from the table
    public function getChatMessages($chatid) {
        $query = $this->db->get_where('chatmessages', array('chat_id' =>$chatid));
        return $query;        
    }
}

?>
