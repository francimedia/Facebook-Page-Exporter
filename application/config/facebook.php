<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

 // add to page: https://www.facebook.com/dialog/pagetab?app_id=337897389572823&display=popup&next=http://francimedia.homeip.net/

$prefix = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http';

// badge app
$config['fbAppId'] = '337897389572823';
$config['fbSecret'] = 'ad7826a2d12f583202f3db8ddda4485f';
$config['facebook_url'] = $prefix . '://apps.facebook.com/pageexport/';
$config['facebook_fanpage_id'] = '337897389572823';
$config['facebook_fanpage_url'] = $prefix . '://www.facebook.com/pages/Page-Exporter-Community/130197817097134?sk=app_337897389572823';
 
