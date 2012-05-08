<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chatmodel extends CI_Model {

    public function __construct()
    {
        $this->load->database(); //Loads database
    }

    //Insert chat message into table
    public function addChatMessage($username, $chatid, $messagecontent){
        $data = array ( 
            'user_name' => $username,
            'chat_id' => $chatid,
            'message_content' => $messagecontent
        );
        return $this->db->insert('chatmessages', $data);
    }

    //Return chat messages from the table
    public function getChatMessages($chatid, $messageid) { 

        $queryStr = "SELECT * 
            FROM  `chatmessages` 
            WHERE chat_id = ?
            AND  message_id > ?
            ORDER BY  `chatmessages`.`message_id` ASC ";

        $query = $this->db->query($queryStr, array($chatid, $messageid));
        return $query;        
    }
}

?>
