<?php   
   $content = "";
   $debug = TRUE;
   $debug_array = TRUE;
   if ($debug) { 
    if ($debug_array) {
     $content .= "Got a resquest.<br>".var_export($_SERVER, TRUE);
    } else {
     $content .= "Got a resquest.<br>";
     $content .= " from: ".$_SERVER['REMOTE_ADDR']." port ".$_SERVER['REMOTE_PORT']."<br>";
     $content .= " using: ".$_SERVER['HTTP_USER_AGENT']."<br>";
     $content .= "<br>";
     $content .= "URL requested: ".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."<br>";
     $content .= " located at ".$_SERVER['SERVER_ADDR']."<br>";
     $content .= " protocol: ".$_SERVER['SERVER_PROTOCOL']."<br>";
     $content .= " method: ".$_SERVER['REQUEST_METHOD']."<br>";
     
    } 
   }
   //print_r($_SERVER);

   echo $content; 
?>