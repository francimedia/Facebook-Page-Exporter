<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

 // add to page: https://www.facebook.com/dialog/pagetab?app_id=YOUR_APP_ID&display=popup&next=http://testdomain.homeip.net/

$prefix = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http';

$config['fbAppId'] = 'YOUR_APP_ID';
$config['fbSecret'] = 'YOUR_APP_SECRET';
$config['facebook_url'] = $prefix . '://apps.facebook.com/pageexport/';
$config['facebook_fanpage_url'] = $prefix . '://www.facebook.com/pages/Page-Exporter-Community/130197817097134?sk=app_337897389572823';
 
