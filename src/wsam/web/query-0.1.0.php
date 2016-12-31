<?php 
 
  function getResponse() {
   $content = "";
   if ($this->debug) { 
    if ($this->debug_array) {
     $this->addlog("Got a resquest. \n".var_export($_SERVER, TRUE));
    } else {
     $this->addlog("Got a resquest. \n");
    } 
   }
  	if ((array_key_exists('CONTENT_TYPE', $_SERVER)) && ($_SERVER['CONTENT_TYPE'] == "application/ocsp-request")) {
    $hexarr = str_split($this->string2hex(file_get_contents("php://input")), 2);  	
    $hex = implode(" ", $hexarr);
    if ($this->debug) { 
  	  $this->addlog("Got OCSP request. (".$hex.")");
  	 } else {
  	  $this->addlog("Got OCSP request.");
  	 }
  	 $hex = str_replace(" ", "", $hex);
  	 header("Content-Transfer-Encoding: Binary");
    #header("Content-Length: 5");
    header("Content-type: application/ocsp-response");
    echo $this->buildResponse($hex); return;
  	} else {
  	 // if not ocsp-request, assume its a normal HTTP
  	 if ($this->debug) { 
     $this->addlog("Got HTTP request. (".$_SERVER['REQUEST_URI'].")");
    } else {
     $this->addlog("Got HTTP request.");
    }
  	 $content .= $this->showHomepage();
  	}
   return $content;  	
  }
  
?>