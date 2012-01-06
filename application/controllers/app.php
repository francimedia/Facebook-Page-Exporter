<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once (APPPATH . '/core/FB_Controller.php');

class App extends FB_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!getFacebookUserId())
        {
            echo ("<script> top.location.href = 'https://www.facebook.com/dialog/oauth?scope=manage_pages&client_id=" . $this->config->item('fbAppId') . "&redirect_uri=" . site_url('app', false, true) .
                "'; </script>");
            exit;
        }
    }

    public function index()
    {
        if ($album_id = $this->input->post('album_id'))
        {
            if ($this->getPictures($album_id))
            {
                return;
            }
        }
        if ($page_id = $this->input->post('page_id'))
        {
            $this->getAlbums($page_id);
            return;
        }
        $this->getPages();
    }

    private function getPages()
    {

        $accounts = $this->apiRequest('https://graph.facebook.com/me/accounts', array());

        if (isset($accounts->data) && $accounts->data)
        {
            $accounts_options = array();
            foreach ($accounts->data as $account)
            {
                $accounts_options[$account->id] = $account->name . ' (ID: ' . $account->id . ' / Category: ' . $account->category . ')';
            }
            natcasesort($accounts_options);
            $this->setData('accounts_options', form_dropdown('page_id', $accounts_options));
        }
    }

    private function getAlbums($page_id)
    {

        $albums = $this->apiRequest('https://graph.facebook.com/' . $page_id . '/albums', array());

        if (isset($albums->data) && $albums->data)
        {
            $albums_options = array();
            foreach ($albums->data as $album)
            {
                $albums_options[$album->id] = $album->name . ' (ID: ' . $album->id . ' / Pictures: ' . (isset($album->count) ? $album->count : 0) . ')';
            }
            natcasesort($albums_options);
            $this->setData('albums_options', form_dropdown('album_id', $albums_options));
        }
    }

    private function getPictures($album_id)
    {
        $this->_getPictures('https://graph.facebook.com/' . $album_id . '/photos');

        if (count($this->photos))
        {
            $this->load->library('zip');
            $data = array();
            foreach ($this->photos as $photo)
            {
                $data[basename($photo)] = file_get_contents($photo);
            }

            $this->zip->add_data($data);
            $this->zip->download('facebook_album_' . $album_id . '.zip');
        }
    }

    private $photos = false;
    private function _getPictures($url)
    {

        $photos = $this->apiRequest($url, array());

        if (isset($photos->data) && $photos->data)
        {
            foreach ($photos->data as $photo)
            {
                $this->photos[] = $photo->source;
            }
        }

        if (isset($photos->paging) && isset($photos->paging->next))
        {
            $this->_getPictures($photos->paging->next);
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/app.php */
