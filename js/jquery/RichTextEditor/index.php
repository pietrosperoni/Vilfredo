<?php
$language="enus";
include_once("locale/".$language.".php");
function getLabel($key,$language){
   global $label;
   return $label[$language][$key];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
<title>jQuery WYSIWYG Rich Text Editor (jQRTE) - Demo</title>
<link rel="Stylesheet" type="text/css" href="css/jqrte.css" />
<link type="text/css" href="css/jqpopup.css" rel="Stylesheet"/>
<link rel="stylesheet" href="css/jqcp.css" type="text/css"/>

<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="js/jqDnR.min.js"></script>
<script type="text/javascript" src="js/jquery.jqpopup.min.js"></script>
<script type="text/javascript" src="js/jquery.jqcp.min.js"></script>
<script type="text/javascript" src="js/jquery.jqrte.min.js"></script>
</head>
<body>
   <h2>jQRTE Demostration</h2>

   <form method="post" action="test.php">
   <div id="demo">
      <textarea id="content_test" name="content_test" class="jqrte_popup" rows="5" cols="5">rich text editor with &lt;b&gt;Content&lt;/b&gt;</textarea>
      <?php
         include_once("content_editor.php");
         include_once("editor.php");
      ?>
   </div>
<input type="submit">
   </form>

   <div id="demo">
      <textarea id="content" name="content" class="jqrte_popup" rows="5" cols="5">rich text editor with &lt;b&gt;Content&lt;/b&gt;</textarea>
      <?php
         include_once("content_editor.php");
         include_once("editor.php");
      ?>
   </div>

<script type="text/javascript">
   window.onload = function(){ 
      try{
         $("#content_rte").jqrte();
      }
      catch(e){}
   } 

   $(document).ready(function() {
         $("#content_rte").jqrte_setIcon();
         $("#content_rte").jqrte_setContent();
   });
</script>
</body>
</html>