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
        $this->load->helper('smiley');
        $this->load->library('table');

        //Make sure user is logged in
        if (!$this->session->userdata('loggedin'))  //Check to see if user is already logged in
        {
            redirect('login/index');
        }

    }

    //Default Function Expect Chat ID or defaults to 1
    public function index($chatid = 1){

        $data['chatid'] = $chatid;
        $data['nickname'] = $this->session->userdata('nickname');
        //Get Room as array 
        $room = $this->login_model->get_a_room($chatid);

        //Make sure room name exists
        if ($room != NULL ){
            //And set the room variable
            $data['chatname'] = $room['chat_name']; 
        } 

        else {
            //This was an attempt to enter not existing room, redirect to list of rooms
            redirect('secure/index');

        }

        //TODO if needed, I can move this to login area to keep track of converstatinos since login until logout

        //******** Let's Find Out Last Message posted BEFORE user has joined the chat
        //1. Get all messages for that chat room
        $allMessages = $this->chatmodel->getChatMessages($data['chatid'], 0);
        //2. Calculate Number of Rows
        $numberRows = $allMessages->num_rows();
        //3. Get ID for the last Row
        $lastRow = $allMessages->row((int)$numberRows - 1);
        //4. Store that ID in user session

        //Check to make sure database is not empty
        if ($lastRow != NULL) {
            $this->session->set_userdata('lastMessageToPost', $lastRow->message_id);
        }
        else {
            $this->session->set_userdata('lastMessageToPost', 0);          
        }
        //*******Now lastMessageToPost points to first message that the user should see


        //Prepare Smileys 

        $image_array = get_clickable_smileys(base_url() . '/images/smileys/', 'chatline');

        $col_array = $this->table->make_columns($image_array, 20);

        $data['smiley_table'] = $this->table->generate($col_array);

        //Load Default View
        $this->load->view('secure/chatview', $data);
    }



    //Gets Called by Ajax Request to insert message
    public function ajax_call_insertMessage(){
        $username = $this->input->post('username');
        $chatid = $this->input->post('chatid');
        $chatmessage = $this->input->post('chatmessage');

        //1. Insert into local database first
        $smileymessage = parse_smileys($chatmessage, base_url() . '/images/smileys/', 'chatline');
        $this->chatmodel->addChatMessage($username, $chatid, $smileymessage); 

        //2. Insert to all of the other sites
        $result = $this->insertRemoteMessages($username, $chatid, $chatmessage);

        echo json_encode("Insert Complete " . $username . " Result " . $result); //Let front end know that the function was sucessful
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


    /*****************************************************************i*****/    
    /******************** Remote Access Function Block *********************/

    //Driver to Insert Remote Chat Message
    private function insertRemoteMessages($username, $chatid, $chatmessage){
        //1. Define Sites to Post To
        $Alex = 'http://cmpe208alexkras.com/';
            //$Wilson = 'http://wjtsang208.com/';
            //$Melisa = 'http://';
            //$Alouise = 'http://';
        $allUrls = array (
            'Alex' => ''. $Alex . 'index.php/backend/insertMessage/'//,
            //'Alouise' => ''. $Alouise . 'index.php/backend/insertMessage/',
            //'Melisa' => '' . $Melisa . 'index.php/backend/insertMessage/',
            //'Wilson' => '' . $Wilson . 'index.php/backend/insertMessage/'       
        );

        //2. Post Data to Remote Sites
        foreach ($allUrls as $url) {
            $data = array ('username' => $username,
                'chatid' => $chatid,
                'chatmessage' => $chatmessage);
            //open connection
            $ch = curl_init($url);

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);
            $result = $allUrls['Alex'] . $result;
            return $result;
        }
    }

    /****************** End of Remote Function Block ****************************/
}
