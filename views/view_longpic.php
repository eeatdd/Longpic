 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Test Longpic</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <style>
	  h3 {display:block; clear:both; padding:10px; margin:10px; border:1px solid #d0d0d0; height:90px;}
	  h3 img { width:70px; padding:2px; margin:2px; border:1px solid #808080;}
  </style>
</head>
<body>
<?php
	foreach ($testurls AS $testurl) {
	?><h3><img src="<?=$this->longpic->thumbnail($testurl);?>"> <?=$testurl?> </h3><?php 
	}
?>
</body>
</html>


