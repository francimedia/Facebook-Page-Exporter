<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once (APPPATH . '/core/FB_Controller.php');

class Profile extends FB_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->setLayout('/profile');
    }

    public function index()
    {

    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
