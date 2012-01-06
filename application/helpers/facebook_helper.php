<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('getFacebookUserId'))
{
    function getFacebookUserId()
    {
        $CI = &get_instance();
        if ($user_id = $CI->getFacebookValue('user_id'))
        {
            return $user_id;
        }
        return $CI->uid;
    }
}

if (!function_exists('getUserId'))
{
    function getUserId()
    {
        $CI = &get_instance();
        return $CI->getUserId();
    }
}

if (!function_exists('getUserName'))
{
    function getUserName()
    {
        $CI = &get_instance();
        return $CI->getUserName();
    }
}

if (!function_exists('getAccessToken'))
{
    function getAccessToken()
    {
        $CI = &get_instance();
        return $CI->getAccessToken();
    }
}

if (!function_exists('getFacebookValue'))
{
    function getFacebookValue($key1, $key2 = false, $key3 = false)
    {
        $CI = &get_instance();
        return $CI->getFacebookValue($key1, $key2, $key3);
    }
}
