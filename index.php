<?php include('functions.php'); ?>

<!DOCTYPE html>
<html>
  <head>
    <title>Get Social Feeds</title>
    <style>
		*, *:before, *:after
		{
			-moz-box-sizing: border-box;
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
			-webkit-font-smoothing: antialiased;
		}    
    	HTML {
    		font-family:"arial";
    		padding:0;    		
    	}
    	BODY {
    		width:980px;
    		padding:20px;
    		margin:auto;
    	}
    </style>
  </head>
  <body>
    <h1>Get Social Feeds</h1>
    <h2>Tweets</h2>    
    <?php 
      $tweets = get_social_channel('twitter'); 
      //var_dump($tweets);
    ?>
    <h2>Pinterest</h2>    
    <?php 
      $pinterest = get_social_channel('pinterest'); 
      //var_dump($pinterest);
    ?> 
    <h2>Facebook</h2>    
    <?php 
      $facebook = get_social_channel('facebook'); 
      //var_dump($facebook);
    ?>        
    <h2>Instagram</h2>    
    <?php 
      $instagram = get_social_channel('instagram'); 
      //var_dump($instagram);
    ?> 
    <h2>YouTube</h2>    
    <?php 
      $youtube = get_social_channel('youtube'); 
      //var_dump($youtube);
    ?>           
  </body>
</html>
