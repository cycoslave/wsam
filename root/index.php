<?php
 // Wicked Software Application Manager - Launcher
 // (c) 2017 Wicked Software

 require __DIR__."/../src/autoload.php";
   
 $wsam = new wsam\core\kernel("web");
 echo $wsam->response();
 
?>