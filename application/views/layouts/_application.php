<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"
    xmlns:og="http://opengraphprotocol.org/schema/"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <title><?php echo $page_title; ?></title>
    
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="" />
    
    <link href="<?php echo base_url(); ?>public/css/screen.css" rel="stylesheet" type="text/css" />    
    <script src="<?php echo base_url(); ?>public/js/jquery.min.js"></script> 
    
</head>

<body id="body" class="<?php echo !getFacebookUserId() ? 'guest' : 'user'; ?> <?php echo $body_class; ?>">

    <?php include (dirname(__file__) . '/_body.php'); ?>   
 
  <div id="fb-root"></div>
  <?php $prefix = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https' : 'http'; ?>
 <script src="<?php echo $prefix; ?>://connect.facebook.net/de_DE/all.js"></script>
 <script>
   FB.init({
     appId  : '<?php echo $this->config->item('fbAppId'); ?>',
     status : true, // check login status
     cookie : false, // enable cookies to allow the server to access the session
     xfbml  : true,  // parse XFBML,
     oauth: true 
   });
    
    //this resizes the the i-frame
    //on an interval of 100ms
    FB.Canvas.setAutoGrow(100);
 
  </script> 
</body>
</html>