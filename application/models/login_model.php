<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model {

    public function __construct()
    {
        $this->load->database(); //Loads database
    }

    //Register's new user
    public function register_user() 
    {
        $sha1_password = sha1($this->input->post('password')); //Encrypts password
        $data = array( //Creates user aray to put into database
            'nickname' => $this->input->post('nickname'),
            'email' => $this->input->post('email'),
            'password' => $sha1_password
        );

        return $this->db->insert('users', $data);
    }

    //Gets user's email from form post an returns entire row from users table
    public function get_user() 
    {
        $query = $this->db->get_where('users', array('email' => $this->input->post('email')));
        return $query->row_array();
    }

    //Gets user's email from form post an returns entire row from users table
    public function get_user_from_session() 
    {
        $query = $this->db->get_where('users', array('email' => $this->session->userdata('email')));
        return $query->row_array();
    }

    //Receives password and updates it for user who is traying to retrieve his password
    public function change_password($password) 
    {
        $sql = "UPDATE users SET password='" . $password ."' WHERE email='" . $this->input->post('email') . "'"; //Create SQL query
        $this->db->query($sql); //Execute the query
    }

    //Receives password and updates it for user who is traying change his password after sucsefull signin
    public function change_mypassword($password) 
    {
        //Create SQL query
        $sql = "UPDATE users SET password='" . $password ."' WHERE email='" . $this->session->userdata('email') . "'"; 
        //Execute the query
        $this->db->query($sql); 
    }


    //Receives nickname and updates it for current user
    public function change_mynickname($nickname) 
    {
        //Create SQL query
        $sql = "UPDATE users SET nickname='" . $nickname ."' WHERE email='" . $this->session->userdata('email') . "'"; 
        //Execute the query
        $this->db->query($sql); 
    }

    //Check to see if room with provided name exists
    public function check_room($newRoomName){
        $query = $this->db->get_where('chatrooms', array('chat_name' => $newRoomName));
        return $query->row_array();
    }

    //Get's a signle room
    public function get_a_room($roomid){
        $query = $this->db->get_where('chatrooms', array('chat_id' => $roomid));
        return $query->row_array();
    }
    //Add New Chatroom into the database
    public function add_room($newRoomName, $userid){
        //Creates room aray to put into database
        $data = array( 
            'chat_name' => $newRoomName,
            'user_id' => $userid
        );

        return $this->db->insert('chatrooms', $data);
    }

    //Get All Available rooms
    public function get_rooms(){
       $this->db->order_by("chat_name", "asc"); 
        return $this->db->get('chatrooms');
    }

    //Delete Chat Room
    public function delete_room($roomid){
        return $this->db->delete('chatrooms', array('chat_id' => $roomid)); 
    }
}
?>
