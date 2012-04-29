<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chat extends CI_Controller {
    //Default Constructor 
    function __construct(){
        parent::__construct();
    }

    //Default Function
    public function index(){
        $this->load->view('chatview');
    }
}
