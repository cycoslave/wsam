<?php
 // Firewall class 0.3.3 // --> Desc.: This class allows the webmaster to control who can view pages based on its ip address.
 // --> Req.: dbmysql 0.8.5
 //     ** NOTE ** This is in no way to be used as a webhost firewall, this is only to used to 
 //                to filter the content to be display based on the ip address of the visitor.       // (c) 2011 Wicked Software
  
  
 // How-to for firewall rules   <DIRECTIVE> <OBJECT> <PARAMETERS>
 // -------------------------
 // > Directives -> ALLOW, DENY and REDIRECT
 // > Objects -> IP, BLOCK, ALL
 // > Parameters -> IP-one IP, BLOCK-start and end IP, for REDIRECT a TO <REDIRADDR> is required at the end.
 // EXAMPLES:
 //   ALLOW ALL
 //   BLOCK IP 192.168.2.11
 //   REDIRECT BLOCK 192.168.2.1 192.168.2.255 TO www.google.com

 class firewall {
  private $ver = "0.3.3";
  private $test = true;   // works backward, false = test mode  

  private $pkg_wsam = TRUE;
  private $pkg_wsamver = "2.3";
  private $pkg_name = "firewall";
  private $pkg_fullname = "Firewall";
  private $pkg_desc ="This class allows the webmaster to control who can view pages based on its ip address.";
  private $pkg_location = "/class/";  
  private $pkg_upgradable  = 1;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 0, "name" => "", "ver" => "", "sublocation" => FALSE, "hash" => "" ) );
  private $pkg_lastver = "0.3.2";    
  private $pkg_released = "23/09/2012";  
  private $pkg_copyright = "(c) 2012 Wicked Software";
  private $pkg_hasdatabase = FALSE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 0, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ) );          
  private $dbtable = "cFirewall";
 
  private $ip = FALSE;  
  private $host = FALSE;
  private $ref = FALSE;
  private $hasproxy = FALSE;
  private $ipproxy = FALSE;
  
  private $defaultrule = "ALLOW";  //ALLOW or DENY
  
  private $db;
  private $dbtype;
  
  private $defv4 = "00000000000000000000000000000000";
  private $defv6 = "00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000";
    
  function __construct($dbtype="test", &$db=FALSE, $dbtable=FALSE, &$log=FALSE) {
   if ($dbtype == "test") {
    $this->test = false;
   } elseif ($dbtype == "mysql") {
    $this->dbtype = $dbtype;
    if ($db) { 
     $this->db = $db; 
    } else { 
     die("Firewall: Missing argument to contruct class."); 
    }
    
    if ($dbtable) { $this->dbtable = $dbtable; }
    $this->init(); 
   //} elseif ($dbtype == "file") {
   } else {
     die("Firewall: Invalid backend type.");
   }   
  }
  
  function __destruct() {
  }
  
  function ver() { return $this->ver; }
  function pkg_wsamver() { return $this->pkg_wsamver; } 
  function pkg_name() { return $this->pkg_name; }
  function pkg_fullname() { return $this->pkg_fullname; }
  function pkg_released() { return $this->pkg_released; }
  function pkg_desc() { return $this->pkg_desc; }
  function pkg_copyright() { return $this->pkg_copyright; }
  function pkg_hasdb() { return $this->pkg_hasdatabase; }
  function pkg_dbtable() { return $this->dbtable; }
  function pkg_dbstruct() { return $this->pkg_dbstruct; } 
  function pkg_dependencies() { return $this->pkg_dependencies; }
  function pkg_location() { return $this->pkg_location; }
  function pkg_checkdb() { return TRUE; }
  function createdb() {
   $tmp = "CREATE TABLE  `".$this->dbtable."` (
    `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `rule` TEXT NOT NULL ,
    `status` ENUM(  'enabled',  'disabled' ) NOT NULL DEFAULT  'enabled',
    PRIMARY KEY (  `id` )
   ) ENGINE = MYISAM DEFAULT CHARSET = latin1 AUTO_INCREMENT =1;";
   return $this->db->query($tmp);   
  }
  
  private function init() {
   $this->ip = $this->getClientIP();
   $this->host = gethostbyaddr($this->ip); 
   $this->ref = $this->getReferer();
  }  
  
  function hasProxy() { return $this->hasproxy; }
  function showIP() { return $this->ip; }
  function showHost() { return $this->host; }
  function showReferer() { return $this->ref; }
  
  function isValidIP($ip) {
   if (($this->isValidIPv4($ip)) || ($this->isValidIPv6($ip))) { return TRUE; }
   return FALSE;
  }
  
  function isValidIPv4($ip) {
  	$pattern = "/^([1-9]?\d|1\d\d|2[0-4]\d|25[0-5])\.([1-9]?\d|1\d\d|2[0-4]\d|25[0-5])\.([1-9]?\d|1\d\d|2[0-4]\d|25[0-5])\.([1-9]?\d|1\d\d|2[0-4]\d|25[0-5])$/";   
   if (preg_match($pattern, $ip, $result)) { return TRUE; }
   return FALSE;
  }
  
  function isValidIPv6($ip) {
   $pattern = "/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/";
   if (preg_match($pattern, $ip, $result)) { return TRUE; }
   return FALSE;  
  }
  
  private function getClientIP() {
   $ip = $_SERVER['REMOTE_ADDR'];   
   if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    if ($ip != $_SERVER['HTTP_CLIENT_IP']) { 
     $this->hasproxy = TRUE;
     $this->ipproxy = $ip;    
    }    
    $ip = $_SERVER['HTTP_CLIENT_IP'];
   } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    if ($ip != $_SERVER['HTTP_X_FORWARDED_FOR']) { 
     $this->hasproxy = TRUE;
     $this->ipproxy = $ip;    
    }
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   }
   return $ip;
  }
  
  private function getReferer() {
   if (!empty($_SERVER['HTTP_REFERER'])) { return $_SERVER['HTTP_REFERER']; }
   return FALSE; 
  }
  
  private function getAgent() {
   if (!empty($_SERVER['HTTP_USER_AGENT'])) { return $_SERVER['HTTP_USER_AGENT']; }
   return FALSE;  
  }  
  
  private function checkRule($rulestring) {
   $rulearray = array();
   $rulearray['directive'] = "";
   $rulearray['object'] = "";
   $rulearray['ip'] = "";
   $rulearray['ip2'] = "";
   $rulearray['redirect'] = "";
   $rule = explode(" ", $rulestring);
   if ($rule[0] === "ALLOW") {
    $rulearray['directive'] = "ALLOW";
    if ($rule[1] === "IP") {
     $rulearray['object'] = "IP";
     if ($this->isValidIP($rule[2])) {
      $rulearray['ip'] = $rule[2];
     } else {
      return FALSE;
     }
    } elseif ($rule[1] === "BLOCK") {
     $rulearray['object'] = "BLOCK";
     if (($this->isValidIP($rule[2])) && ($this->isValidIP($rule[3]))) {
      $rulearray['ip'] = $rule[2];
      $rulearray['ip2'] = $rule[3];
     } else {
      return FALSE;
     }     
    } elseif ($rule[1] === "ALL") {
     $rulearray['object'] = "ALL";
    } else {
     return FALSE;
    }
   } elseif ($rule[0] === "DENY") {
    $rulearray['directive'] = "DENY";
    if ($rule[1] === "IP") {
     $rulearray['object'] = "IP";
     if ($this->isValidIP($rule[2])) {
      $rulearray['ip'] = $rule[2];
     } else {
      return FALSE;
     }
    } elseif ($rule[1] === "BLOCK") {
     $rulearray['object'] = "BLOCK";
     if (($this->isValidIP($rule[2])) && ($this->isValidIP($rule[3]))) {
      $rulearray['ip'] = $rule[2];
      $rulearray['ip2'] = $rule[3];
     } else {
      return FALSE;
     }     
    } elseif ($rule[1] === "ALL") {
     $rulearray['object'] = "ALL";
    } else {
     return FALSE;
    }  
   } elseif ($rule[0] === "REDIRECT") {
    $rulearray['directive'] = "REDIRECT";
    if ($rule[1] === "IP") {
     $rulearray['object'] = "IP";
     if ($this->isValidIP($rule[2])) {
      $rulearray['ip'] = $rule[2];
      if ($rule[3] === "TO") {
       if ($rule[4] != "") { $rulearray['redirect'] = $rule[4]; }
      } else {
       return FALSE;      
      }
     } else {
      return FALSE;
     }
    } elseif ($rule[1] === "BLOCK") {
     $rulearray['object'] = "BLOCK";
     if (($this->isValidIP($rule[2])) && ($this->isValidIP($rule[3]))) {
      $rulearray['ip'] = $rule[2];
      $rulearray['ip2'] = $rule[3];
      if ($rule[4] === "TO") {
       if ($rule[5] != "") { $rulearray['redirect'] = $rule[5]; }
      } else {
       return FALSE;      
      }
     } else {
      return FALSE;
     }     
    } elseif ($rule[1] === "ALL") {
     $rulearray['object'] = "ALL";
     if ($rule[2] === "TO") {
       if ($rule[3] != "") { $rulearray['redirect'] = $rule[2]; }
      } else {
       return FALSE;      
      }
    } else {
     return FALSE;
    } 
   } else {
    return FALSE;
   }
   return $rulearray;  
  } 
  
  function addRule($rulestring) {
   $rule = $this->checkRule($rulestring);
   if ($rule !== FALSE) {
    if ($this->ruleExist($rulestring)) { return -1; }
    $db =& $this->db;
    $data = "INSERT INTO `".$this->dbtable."` ( `rule` )";
    $data .= " VALUES ( '".$rulestring."' );";  
    $db->query($data);
    return mysql_insert_id($db->dblink());
   } else {
    return FALSE; 
   }
  }
  
  function delRule($rulestring) {
   $rule = $this->checkRule($rulestring);
   if ($rule !== FALSE) {
    $db =& $this->db;
    $data = "DELETE FROM `".$this->dbtable."` WHERE `rule` = '".$rulestring."'";  
    $db->query($data);
    return TRUE;
   } else {
    return FALSE; 
   }  
  }  
  
  function ruleExist($rulestring) {
   $rule = $this->checkRule($rulestring);
   if ($rule !== FALSE) {
    $db =& $this->db;
    $data = "SELECT id FROM `".$this->dbtable."` WHERE `rule` = '".$rulestring."'";
    return mysql_num_rows($db->query($data));
   } else {
    return FALSE; 
   }   
  }
  
  function expand6($ip) {
   if ($this->isValidIPv6($ip)) {
    if (strpos($ip, '::') !== false) {
     $part = explode('::', $ip);
     $part[0] = explode(':', $part[0]);
     $part[1] = explode(':', $part[1]);
     $missing = array();
     for ($i = 0; $i < (8 - (count($part[0]) + count($part[1]))); $i++) {
      array_push($missing, '0000');
     }
     $missing = array_merge($part[0], $missing);
     $part = array_merge($missing, $part[1]);
    } else {
     $part = explode(":", $ip);
    } 
    foreach ($part as &$p) {
     if (strpos($p, '.') !== false) { 
      $p2 = explode(".", $p);
      $p = str_pad(dechex((int) $p2[0]),2,"0",STR_PAD_LEFT);
      $p .= str_pad(dechex((int) $p2[1]),2,"0",STR_PAD_LEFT);
      $p .= ":";
      $p .= str_pad(dechex((int) $p2[2]),2,"0",STR_PAD_LEFT);
      $p .= str_pad(dechex((int) $p2[3]),2,"0",STR_PAD_LEFT);               
     } else {    
      while (strlen($p) < 4) {
       $p = '0' . $p;
      }
     }
    } 
    unset($p);
    $result = implode(':', $part);
    if (strlen($result) == 39) {
     return $result;
    } else {
     return FALSE;
    } 
   } else {
    return FALSE;
   }  
  }   
  
  function compress6($ip) {
   if ($this->isValidIPv6($ip)) {
    return inet_ntop(inet_pton($ip));
   } else {
    return FALSE;
   }
  } 
  
  function ip42bin($ip) {
   $bin = "";   
   if ($this->isValidIPv4($ip)) {
    $ip = explode(".", $ip);
    $bin .= str_pad(decbin((int) $ip[0]),8,"0",STR_PAD_LEFT);
    $bin .= str_pad(decbin((int) $ip[1]),8,"0",STR_PAD_LEFT);
    $bin .= str_pad(decbin((int) $ip[2]),8,"0",STR_PAD_LEFT);
    $bin .= str_pad(decbin((int) $ip[3]),8,"0",STR_PAD_LEFT);
    return $bin;
   } else {
    return FALSE;    
   }
  }
  
  function ip62bin($ip) {
   $bin = "";
   if ($this->isValidIPv6($ip)) {
    $ip = $this->expand6($ip);
    if ($ip !== FALSE) {
     $ip = str_split($ip);
     for ($i = 0; $i < 39; $i++) {
      if ($ip[$i] != ":") {
       switch ($ip[$i]) {
        case "0":
         $bin .= "0000"; break;  
        case "1":
         $bin .= "0001"; break; 
        case "2":
         $bin .= "0010"; break;   
        case "3":
         $bin .= "0011"; break; 
        case "4":
         $bin .= "0100"; break; 
        case "5":
         $bin .= "0101"; break;   
        case "6":
         $bin .= "0110"; break;    
        case "7":
         $bin .= "0111"; break; 
        case "8":
         $bin .= "1000"; break; 
        case "9":
         $bin .= "1001"; break; 
        case "A":
        case "a":
         $bin .= "1010"; break;  
        case "B":
        case "b":
         $bin .= "1011"; break; 
        case "C":
        case "c":
         $bin .= "1100"; break;   
        case "D":
        case "d":
         $bin .= "0000"; break;    
        case "E":
        case "e":
         $bin .= "1110"; break; 
        case "F":
        case "f":
         $bin .= "1111"; break;   
        default:
         break;                                             
       }      
      }     
     }
     return $bin;
    } else {
     return FALSE;    
    } 
   } else {
    return FALSE;    
   }   
  }
  
  function ip2bin($ip, $what="any") {
   $bin = "";   
   if ($what == "v4") {
    $bin = $this->ip42bin($ip);
   } elseif ($what == "v6") {
    $bin = $this->ip62bin($ip);
   } else {
    //any
    if ($this->isValidIPv4($ip)) {
     $bin = $this->ip42bin($ip);
    } elseif ($this->isValidIPv6($ip)) {
     $bin = $this->ip62bin($ip);
    } else {
     return FALSE;    
    }
   }
   return $bin;
  }
  
  function compareip($ip1, $ip2) {
   if (($this->isValidIPv4($ip1)) && ($this->isValidIPv4($ip2))) {
    return strcmp($this->ip2bin($ip1, "v4"), $this->ip2bin($ip2, "v4"));
   } elseif (($this->isValidIPv6($ip1)) && ($this->isValidIPv6($ip2))) {
    return strcmp($this->ip2bin($ip1, "v6"), $this->ip2bin($ip2, "v6"));
   } else {
    return FALSE;   
   }  
  }  
  
  function isipinrange($range1, $range2, $ip) {
   if (($this->compareip($range2, $ip) !== FALSE) && ($this->compareip($ip, $range1) !== FALSE)) {
    if (($this->compareip($range2, $ip) > 0) && ($this->compareip($ip, $range1) > 0)) { return TRUE; } 
   }
   return FALSE;   
  }
  
  function isValidRange($ip1, $ip2) {
   if ($this->compareip($ip2, $ip1) !== FALSE) { 
    if ($this->compareip($ip2, $ip1) > 0) { return TRUE; } 
   }
   return FALSE;   
  }
  
  function isAllowed($ip) {
   if ($this->isValidIP($ip)) {
    if ($this->defaultrule === "ALLOW") {
     $val = 1;
    } else {
     $val = 0;
    }     
    $db =& $this->db;
    $sql = $db->fetch_all_array($db->query("SELECT `rule` FROM ".$this->dbtable));
    if ($sql[0][0] > 0) {
     for ($i=1; $i<=$sql[0][0]; $i++) {
      $tmp = explode(" ", $sql[$i]['rule']);
      if ($tmp[1] == "ALL") {
       if ($tmp[0] == "REDIRECT") {
        $val = -1; /////////////////// NOT CODED YET
       } elseif ($tmp[0] == "ALLOW") {
        $val = 1;
       } else {
        //deny   
        $val = 0;     
       }      
      } elseif ($tmp[1] == "IP") {
       if ($this->isValidIPv6($ip)) {
        $tmp[2] = $this->expand6($tmp[2]);
        $ip = $this->expand6($ip);       
       }
       if ($ip == $tmp[2]) { 
        if ($tmp[0] == "REDIRECT") {
         $val = -1; /////////////////// NOT CODED YET
        } elseif ($tmp[0] == "ALLOW") {
         $val = 1;
        } else {
         //deny   
         $val = 0;     
        }
       }
      } elseif ($tmp[1] == "BLOCK") {
       if ($this->isValidRange($tmp[2], $tmp[3])) {
        if ($this->isValidIPv6($ip)) {
         $tmp[2] = $this->expand6($tmp[2]);
         $tmp[3] = $this->expand6($tmp[3]);
         $ip = $this->expand6($ip);       
        }    
        if ($this->isipinrange($tmp[2], $tmp[3], $ip)) {
         if ($tmp[0] == "REDIRECT") {
          $val = -1; /////////////////// NOT CODED YET
         } elseif ($tmp[0] == "ALLOW") {
          $val = 1;
         } else {
          //deny   
          $val = 0;     
         }         
        }    
       }     
      }
     }
    }
    return $val;
   } else {
    return 0;   
   }
  }
  
  function isPrivateIP($ip) {
  }
  
  function isPublicIP($ip) {
  }
  

/////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
/// The stuff below is for future releases with extra proxy detection
  
/**
     *   Normal Private IP List, to detect if IP is local (and to ignore if reported by proxy)
     *   This is a simple and fixed list, I have compiled according to RFC 3330 (and some other resource)
     *   http://www.rfc-archive.org/getrfc.php?rfc=3330
     */
     var $Private_IP_Normal=  '0.0.0.0/8, 1.0.0.0/8, 2.0.0.0/8, 10.0.0.0/8,
                              127.0.0.0/8, 169.254.0.0/16, 172.16.0.0/12, 192.0.2.0/24,
                              192.168.0.0/16, 198.18.0.0/15, 224.0.0.0/3';
                           /*
                           0.0.0.0/8, "This" Network
                           10.0.0.0/8, Private-Use Networks
                           127.0.0.0/8, Loopback
                           169.254.0.0/16, Link Local
                           172.16.0.0/12, Private-Use Networks
                           192.0.2.0/24, Test-Net
                           192.168.0.0/16, Private-Use Networks
                           198.18.0.0/15, Network Interconnect Device Benchmark Testing
                           */

     /**
     *   Extended Private IP List, to detect if IP is local (and to ignore if reported by proxy)
     *   This is a more extended BOGON IP list, which can change in time according to reservations / allocations by IANA
     *   Last updated 2006.01.05
     *   http://www.cymru.com/Documents/bogon-list.html - Bogon List 3.1 - 05 JAN 2006
     *   BTW you can always download the latest version of list into a file from http://www.cymru.com/Documents/bogon-bn-nonagg.txt
     *   and specify the local file name to variable $exfile below.
     *   Thanks to "Team Cymru Web Site"
     */
     var $Private_IP_Extended=   '0.0.0.0/8, 1.0.0.0/8, 2.0.0.0/8, 5.0.0.0/8,
                                 7.0.0.0/8, 10.0.0.0/8, 23.0.0.0/8, 27.0.0.0/8,
                                 31.0.0.0/8, 36.0.0.0/8, 37.0.0.0/8, 39.0.0.0/8,
                                 42.0.0.0/8, 49.0.0.0/8, 50.0.0.0/8, 77.0.0.0/8,
                                 78.0.0.0/8, 79.0.0.0/8, 92.0.0.0/8, 93.0.0.0/8,
                                 94.0.0.0/8, 95.0.0.0/8, 96.0.0.0/8, 97.0.0.0/8,
                                 98.0.0.0/8, 99.0.0.0/8, 100.0.0.0/8, 101.0.0.0/8,
                                 102.0.0.0/8, 103.0.0.0/8, 104.0.0.0/8, 105.0.0.0/8,
                                 106.0.0.0/8, 107.0.0.0/8, 108.0.0.0/8, 109.0.0.0/8,
                                 110.0.0.0/8, 111.0.0.0/8, 112.0.0.0/8, 113.0.0.0/8,
                                 114.0.0.0/8, 115.0.0.0/8, 116.0.0.0/8, 117.0.0.0/8,
                                 118.0.0.0/8, 119.0.0.0/8, 120.0.0.0/8, 127.0.0.0/8,
                                 169.254.0.0/16, 172.16.0.0/12, 173.0.0.0/8, 174.0.0.0/8,
                                 175.0.0.0/8, 176.0.0.0/8, 177.0.0.0/8, 178.0.0.0/8,
                                 179.0.0.0/8, 180.0.0.0/8, 181.0.0.0/8, 182.0.0.0/8,
                                 183.0.0.0/8, 184.0.0.0/8, 185.0.0.0/8, 186.0.0.0/8,
                                 187.0.0.0/8, 192.0.2.0/24, 192.168.0.0/16, 197.0.0.0/8,
                                 198.18.0.0/15, 223.0.0.0/8, 224.0.0.0/3';
                                 
     var $exfile=''; //Load Extended Private IP Address List from this file (will overwrite existing list)

     var $ex_private=false; //Use Extended List in private IP detection
     var $ex_proxy=false; //Use Extended List in proxy (client IP) detection


     /**
     *   INTERNAL USE - Proxy Evidence Headers
     *   $Proxy_Evidence array is an EXPANDABLE STRUCTURE,
     *   which is EVALUATED on headers to make decisions
     *   on proxy certainty, client IP, proxy name, other info
     *
     *   These decision headers are made according to my small research about proxy
     *   behaviors, proxies do not always behave like RFC's say, so there can be a big
     *   disorder of standard and non standard headers and behaviors.
     */
     var $Proxy_Evidence=array(
         /**
         *   [0]=string HeaderName,[1]=constant HeaderType,[2]=boolean ProxyCertainty, ['value']=string Value
         *
         *   HeaderName    : Header name as string or regular expression
         *   HeaderType    : What kind of info can header contain ?
         *                   (see CONSTANTS section above for explanations...)
         *   ProxyCertainty: Is proxy certainly present if header found?
         *   Value         : Optional parameter must be regular expression,
         *                   Header is only accepted if matches value (regular expression)
         *
         *   Note that headers are written importance ordered, first written header is evaluated first
         */
                    array('HTTP_VIA', 'XIP_HT_PName', true), // example.com:3128 (Squid/2.4.STABLE6)
                    array('HTTP_PROXY_CONNECTION', 'XIP_HT_None', true), //Keep-Alive
                    array('HTTP_XROXY_CONNECTION', 'XIP_HT_None', true), //Keep-Alive
                    array('HTTP_X_FORWARDED_FOR', 'XIP_HT_ClientIP', true), //X.X.X.X, X.X.X.X
                    array('HTTP_X_FORWARDED', 'XIP_HT_PInfo', true), //?
                    array('HTTP_FORWARDED_FOR', 'XIP_HT_ClientIP', true), //?
                    array('HTTP_FORWARDED', 'XIP_HT_PInfo', true), //by http://proxy.example.com:8080 (Netscape-Proxy/3.5)
                    array('HTTP_X_COMING_FROM', 'XIP_HT_ClientIP', true), //?
                    array('HTTP_COMING_FROM', 'XIP_HT_ClientIP', true),
                    /*
                    HTTP_CLIENT_IP can be sometimes wrong (maybe if proxy chains used)
                    First look at HTTP_X_FORWARDED_FOR if exists (it can contain multiple IP addresses comma seperated)
                    (This is why HTTP_CLIENT_IP is written after HTTP_X_FORWARDED_FOR)
                    */
                    array('HTTP_CLIENT_IP', 'XIP_HT_ClientIP', true), //X.X.X.X
                    array('HTTP_PC_REMOTE_ADDR', 'XIP_HT_ClientIP', true), //X.X.X.X
                    array('HTTP_CLIENTADDRESS', 'XIP_HT_ClientIP', true),
                    array('HTTP_CLIENT_ADDRESS', 'XIP_HT_ClientIP', true),
                    array('HTTP_SP_HOST', 'XIP_HT_ClientIP', true),
                    array('HTTP_SP_CLIENT', 'XIP_HT_ClientIP', true),
                    array('HTTP_X_ORIGINAL_HOST', 'XIP_HT_ClientIP', true),
                    array('HTTP_X_ORIGINAL_REMOTE_ADDR', 'XIP_HT_ClientIP', true),
                    array('HTTP_X_ORIG_CLIENT', 'XIP_HT_ClientIP', true),
                    array('HTTP_X_CISCO_BBSM_CLIENTIP', 'XIP_HT_ClientIP', true),
                    array('HTTP_X_AZC_REMOTE_ADDR', 'XIP_HT_ClientIP', true),
                    array('HTTP_10_0_0_0', 'XIP_HT_ClientIP', true),
                    array('HTTP_PROXY_AGENT', 'XIP_HT_PName', true),
                    array('HTTP_X_SINA_PROXYUSER', 'XIP_HT_ClientIP', true),
                    array('HTTP_XXX_REAL_IP', 'XIP_HT_ClientIP', true),
                    array('HTTP_X_REMOTE_ADDR', 'XIP_HT_ClientIP', true),
                    array('HTTP_RLNCLIENTIPADDR', 'XIP_HT_ClientIP', true),
                    array('HTTP_REMOTE_HOST_WP', 'XIP_HT_ClientIP', true),
                    array('HTTP_X_HTX_AGENT', 'XIP_HT_PName', true),
                    array('HTTP_XONNECTION', 'XIP_HT_None', true),
                    array('HTTP_X_LOCKING', 'XIP_HT_None', true),
                    array('HTTP_PROXY_AUTHORIZATION', 'XIP_HT_None', true),
                    array('HTTP_MAX_FORWARDS', 'XIP_HT_None', true),
                    //array('HTTP_FROM', XIP_HT_ClientIP, true,'value'=>'/(\d{1,3}\.){3}\d{1,3}/'), //proxy is detected if header contains IP
                    array('HTTP_X_IWPROXY_NESTING', 'XIP_HT_None', true),
                    array('HTTP_X_TEAMSITE_PREREMAP', 'XIP_HT_None', true), //http://www.example.com/example...
                    array('HTTP_X_SERIAL_NUMBER', 'XIP_HT_None', true),
                    array('HTTP_CACHE_INFO', 'XIP_HT_None', true),
                    array('HTTP_X_BLUECOAT_VIA', 'XIP_HT_PName', true),
                    //search inside REMOTE_HOST
                    /*
                    REMOTE_HOST can always be empty whether or not you have a host name,
                    This is because hostname lookups are turned off by default in many web hosting setups
                    look at here for more info and solutions => http://www.php.net/manual/en/function.gethostbyaddr.php
                    */
                    //Yes, if remote host contains something like proxy123.example.com
                    //array('REMOTE_HOST', 'XIP_HT_Non'e, true, 'value'=>'/proxy.*\..*\..*/'),
                    //Yes, if remote host contains something like cache123.example.com
                    //array('REMOTE_HOST', 'XIP_HT_None', true, 'value'=>'/cache.*\..*\..*/'),
                    //Guess Unknown headers using Regular expressions
                    //array('/^HTTP_X_.*/', 'XIP_HT_None', true),
                    array('/^HTTP_X_.*/', 'XIP_HT_ClientIP', true),
                    array('/^HTTP_PROXY.*/', 'XIP_HT_None', true),
                    array('/^HTTP_XROXY.*/', 'XIP_HT_None', true),
                    array('/^HTTP_XPROXY.*/', 'XIP_HT_None', true),
                    array('/^HTTP_VIA.*/', 'XIP_HT_None', false),
                    array('/^HTTP_XXX.*/', 'XIP_HT_None', false),
                    array('/^HTTP_XCACHE.*/', 'XIP_HT_None', false)
                    );
     /**
     *   HINT ! :
     *   If a HTTP Request Header sent as "tesT-someTHinG_aNYthing: hELLo",
     *   PHP will set $_SERVER['HTTP_TEST_SOMETHING_ANYTHING']=hELLo
     *   (As in PHP 4.3x installed as CGI module Apache)
     */  
  
  
  
 }


?>