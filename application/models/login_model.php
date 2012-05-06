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


}
?>
