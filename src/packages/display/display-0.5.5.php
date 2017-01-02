<?php
 // Display class 0.5.5
 // --> Desc.: creates an object to display the dynamic content
 //            of a web site. Allows the programmer to define a
 //            doctype, title, cssfile, favicon and add meta tags.
 // --> WARNING!: This class is not backward compatible with display below version 0.5.0.
 // (c) 2008-2016 Wicked Software


 class display {
  private $ver = "0.5.5";
  private $test = true;   // works backward, false = test mode
  
  private $pkg_wsam = TRUE;
  private $pkg_wsamver = "2.3";
  private $pkg_name = "display";
  private $pkg_fullname = "Display";
  private $pkg_desc ="Creates an object that build a webpage before sending it to the browser.";
  private $pkg_location = "/src/class/";  
  private $pkg_upgradable  = FALSE;
  private $pkg_priority = 1;
  private $pkg_dependencies = array ( 0 => array ( 0 => 0, "name" => "", "ver" => "", "sublocation" => FALSE, "hash" => "" ) );
  private $pkg_lastver = "0.5.4";    
  private $pkg_released = "07/03/2016";  
  private $pkg_copyright = "(c) 2016 Wicked Software";
  private $pkg_hasdatabase = FALSE;
  private $pkg_dbstruct = array ( 0 => array( 0 => 0, "Field" => "", "Type" => "", "Null" => "NO", "Key" => "", "Default" => "", "Extra" => "" ) );
  private $dbtable = "";   
  
  private $content = "";  // The content of the page.
  

  function __construct($type="test", $html="xhtml", $ver="1.0", $doctype="transitional") {
   if ($type == "test") {
    $this->clear();
    $this->test = false; 
   } else {
    $this->clear();
    $this->addDoctype($html, $ver, $doctype);   
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
  function pkg_createdb() { return FALSE; }
  
  function show() { if ($this->test) { echo $this->content; } }
  function add($data=FALSE) { if ($this->test) { $this->content .= "{$data}\n"; } }
  function clear() { if ($this->test) { $this->content = ""; } }

  function addDoctype($type, $ver, $doctype=FALSE) {
   if ($this->test) {
    $content = "";
    if ($type == "xhtml") {
     if ($ver = "1.0") {
      if ($doctype == "strict") {
       $content .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
      } elseif ($doctype == "transitional") {
       $content .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
      } elseif ($doctype == "frameset") {
       $content .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">>";
      }     
     } elseif ($ver == "1.1") {
      $content .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">";
     }
    } elseif ($type == "html") {     if ($ver == "4.01") {
      if ($doctype == "strict") {
       $content .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">";
      } elseif ($doctype == "transitional") {
       $content .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
      } elseif ($doctype == "frameset") {
       $content .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">";
      }      } elseif ($ver == "5.0") {
      $content .= "<!DOCTYPE HTML>";
     } 
    }
    return $content;
   }
   return FALSE;
  }
  
  function startHTML() { 
   if ($this->test) { return "<html>"; } 
   return FALSE;
  }
  
  function endHTML() { 
   if ($this->test) { return "</html>"; }
   return FALSE;
  }  

  function startHead() { 
   if ($this->test) { return "<head>"; }
   return FALSE;
  }
  
  function endHead() { 
   if ($this->test) { return "</head>"; } 
   return FALSE;
  }

  function startBody() { 
   if ($this->test) { return "<body>"; } 
   return FALSE;
  }
  
  function endBody() { 
   if ($this->test) { return "</body>"; }
   return FALSE;
  }  
  
  function addTitle($title=FALSE) { 
   if (($this->test) && ($title)) { return "<title>".$title."</title>"; }
   return FALSE; 
  }
  
  function addCharset($charset="ISO-8859-1") {
   if ($this->test) {
    $content = "";
    if ($charset) { $content .= "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset={$charset}\">"; }
    return $content;
   }
  }
  function addContentlang($lang="en-US") {
   if ($this->test) {
    $content = "";
    if ($lang) { $content .= "<META HTTP-EQUIV=\"Content-Language\" CONTENT=\"{$lang}\">"; }
    return $content;
   }
  }

  function addLink($type=false,$arg=false) {
   if ($this->test) {
    if (($type == "css") && ($arg)) { 
     return "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$arg}\" />";
    } elseif (($type == "icon") && ($arg)) {
     return "<link rel=\"shortcut icon\" href=\"".$arg."\" />";
    }   
   }  
  }
  
  function addScript($type=false,$arg=false) {
   if ($this->test) {
    if (($type == "javascript") && ($arg)) { 
     return "<script type=\"text/javascript\" src=\"{$arg}\" />"; 
    }
   }  
  }
  
  function addMeta($name=false,$arg) {
   if ($this->test) {
    if (($name == "robots") && ($arg)) {
     return "<META NAME=\"ROBOTS\" CONTENT=\"{$arg}\">";
    } elseif (($name == "description") && ($arg)) {
     return "<META NAME=\"description\" CONTENT=\"{$arg}\">";
    } elseif (($name == "keywords") && ($arg)) {
     return "<META NAME=\"keywords\" CONTENT=\"{$arg}\">";
    } elseif (($name == "author") && ($arg)) {
     return "<META NAME=\"author\" CONTENT=\"{$arg}\">";
    } elseif (($name == "copyright") && ($arg)) {
     return "<META NAME=\"copyright\" CONTENT=\"{$arg}\">";
    }
   }
   return FALSE;
  }  
  
 }

?>