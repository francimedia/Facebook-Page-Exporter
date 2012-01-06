<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('isFbRequest'))
{
    function isFbRequest()
    {
        $CI = &get_instance();
        return $CI->fb_request;
    }
}

if (!function_exists('site_url'))
{
    function site_url($uri = '', $force_default = false, $force_facebook = false)
    {
        $CI = &get_instance();

        if ($force_facebook)
        {
            $base_url = $CI->config->item('facebook_url');
        } elseif (!$force_default && inFacebook())
        {
            $base_url = $CI->config->item('facebook_url');
        } else
        {
            $base_url = $CI->config->item('base_url');
        }

        if (is_array($uri))
        {
            $uri = implode('/', $uri);
        }

        if ($uri == '')
        {
            return slash_str($base_url) . $CI->config->item('index_page');
        } else
        {
            $suffix = ($CI->config->item('url_suffix') == false) ? '' : $CI->config->item('url_suffix');
            return slash_str($base_url) . slash_str($CI->config->item('index_page')) . trim($uri, '/') . $suffix;
        }

    }

}

if (!function_exists('slash_str'))
{
    function slash_str($pref)
    {
        if ($pref != '' && substr($pref, -1) != '/')
        {
            $pref .= '/';
        }

        return $pref;
    }
}
 