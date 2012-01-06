<?php class FB_Controller extends MY_Controller
{

    private $signed_request;
    private $access_token;
    private $uid;
    public $fb_request = false;

    function __construct()
    {
        parent::__construct();
        $this->setHeader();
        $this->loadConfigAndHelpers(); 
        $this->parseSignedRequest();
        $this->setAccessToken();
        $this->setLayout('application');
    }

    public function getUserId()
    {
        return $this->uid;
    }

    private function setHeader()
    {
        $this->output->set_header("Cache-Control: no-cache, must-revalidate");
        $this->output->set_header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    private function loadConfigAndHelpers()
    {
        $this->load->config('facebook');
        $this->load->helper(array('facebook'));
    }

    private function parseSignedRequest()
    {
        if ($signed_request = $this->input->post('signed_request'))
        {
            $this->signed_request = $this->parse_signed_request();
            $this->fb_request = true;
        } else
        {
            $this->signed_request = array();
        }
    }
    private function setAccessToken()
    {
        if ($access_token = $this->getFacebookValue('oauth_token'))
        {
            $this->access_token = $access_token;
            $this->uid = $this->getFacebookValue('user_id');
            return;
        }
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function parse_signed_request()
    {
        list($encoded_sig, $payload) = explode('.', $this->input->post('signed_request'), 2);

        // decode the data
        $sig = $this->base64_url_decode($encoded_sig);
        $data = json_decode($this->base64_url_decode($payload), true);

        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256')
        {
            error_log('Unknown algorithm. Expected HMAC-SHA256');
            return null;
        }

        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $this->config->item('fbSecret'), $raw = true);
        if ($sig !== $expected_sig)
        {
            error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    private function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public function getFacebookValue($key1, $key2 = false, $key3 = false)
    {
        if ($key3)
        {
            return element($key3, element($key2, element($key1, $this->signed_request)));
        } elseif ($key2)
        {
            return element($key2, element($key1, $this->signed_request));
        } else
        {
            return element($key1, $this->signed_request);
        }
    }

    public function apiRequest($url, $args = false, $json_decode = true)
    {
        $this->load->library('curl_http_client');
        $this->curl_http_client->set_cainfo(APPPATH . '/libraries/fb_ca_chain_bundle.crt');

        if (is_array($args))
        {
            $args['access_token'] = $this->getAccessToken();
            $url .= '?' . htmlspecialchars_decode(http_build_query($args));
        }

        try
        {
            return $json_decode ? json_decode($this->curl_http_client->fetch_url($url)) : $this->curl_http_client->fetch_url($url);
        }
        catch (exception $e)
        {
            log_message('error', 'apiRequest: ' . $e->getMessage());
            return false;
        }

    }

    public function apiPostRequest($url, $args = false)
    {
        $this->load->library('curl_http_client');
        $this->curl_http_client->set_cainfo(APPPATH . '/libraries/fb_ca_chain_bundle.crt');

        if (is_array($args))
        {
            $args['access_token'] = $this->getAccessToken();
        }

        try
        {
            return json_decode($this->curl_http_client->send_post_data($url, $args));
        }
        catch (exception $e)
        {
            log_message('error', 'apiPostRequest: ' . $e->getMessage());
            return false;
        }
    }

}
