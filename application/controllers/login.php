<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    //Default Constructor 
    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->model('login_model');
        $this->load->helper('url');
        $this->load->library('email');
        $this->load->library('session');

        if ($this->session->userdata('loggedin')) 	//Check to see if user is already logged in
        {
            redirect('chat/index');
        }
    }

    //Default Function 
    public function index()
    {
        //Validate sumbited form data
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|alpha_dash');

        if ($this->form_validation->run() == FALSE) //Invalid Input
        {
            $this->load->view('template/header');
            $this->load->view('login/loginView'); //Re-load the page
        }
        else
        {
            //Check if user exists and password matches
            //1. Get User Data
            $userData = $this->login_model->get_user();
            if ($userData == NULL) {
                $data['message'] = "Invalid Email or Password <br /><br />";
                $this->load->view('template/header');
                $this->load->view('login/loginView', $data);//Reload view with custom error
            } else {

                $sha1_password = sha1($this->input->post('password')); //Encrypts submitted password

                if ($sha1_password == $userData['password']){ //Make sure that password's match
                    $sessoinData = array( //User Authentication Worked!
                        'loggedin' =>  TRUE,
                        'nickname' => $userData['nickname'],
                        'email' => $userData['email'],
                        'id' => $userData['id']
                    );
                    $this->session->set_userdata($sessoinData);
                    redirect('chat/index');
                } else {
                    $data['message'] = "Invalid Email or Password <br /><br />"; 
                    $this->load->view('template/header');
                    $this->load->view('login/loginView', $data);//Reload view with custom error
                }
            }
        }
    }

    //Register a new user
    public function register()
    {
        //1. Validate user input
        $this->form_validation->set_rules('nickname', 'Nickname', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|matches[passwordConfirm]|alpha_dash');	
        $this->form_validation->set_rules('passwordConfirm', 'Password Confirmation', 'required');

        if ($this->form_validation->run() == FALSE) //Registration Failed
        {
            $this->load->view('template/header');
            $this->load->view('login/loginRegister');
        }
        else //Submission sucessfull
        {
            //1. Check to See if User Already Registered 
            $userData = $this->login_model->get_user();//Try to get user from database

            if( $userData == NULL){

                //User is not Registered, Register User
                $this->login_model->register_user(); //Add user into database
                $data['message'] = "You are now registered. Please sign in below.<br /><br />";
                $this->load->view('template/header');
                $this->load->view('login/loginView', $data); //Load login page
            } else {

                //User is Already Registered, display custom error
                $data['message'] = "Email has already been registered previously. Please use Forgot Password link below.<br /><br />"; 
                $this->load->view('template/header');
                $this->load->view('login/loginView', $data); //Load login page
            }
        }
    }

    //Sends Temporary Password to Valid Users
    public function forgot_password()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

        if ($this->form_validation->run() == FALSE) //Invalid Email
        { 
            //Redirect back to forgot password
            $this->load->view('template/header');
            $this->load->view('login/forgotPassword');
        }
        else //See if User Exists 
        {
            $userData = $this->login_model->get_user();//Try to get user from database
            if ($userData == NULL) {

                //Passess error message to the view
                $data['message'] = "Email not found<br /><br />"; 
                $this->load->view('template/header');
                $this->load->view('login/forgotPassword', $data);
            }
            else {
                //1. Load Temporary Password Into Database
                $password = 'Pass' . rand(1000, 9999); //Random Password
                $sha1_password = sha1($password); //Encrypt the password
                $this->login_model->change_password($sha1_password); //Inser updated password into the database

                //2. Send Temporary Password
                $this->email->from('admin@ezchat.nfshost.com','Barack Obama');
                $this->email->to($userData['email']);
                $this->email->subject('Password Recovery');
                $this->email->message($password);
                $data['message']=$this->email->send()?'Please Check your email and Login!<br /><br />':'Error sending email, please contact Administroator<br /><br />';
                $this->load->view('template/header');
                $this->load->view('login/loginView',$data);
            }
        }
    }
}
