<?php 
//change the values with your own hosting setting
 $mysql_host = "127.0.0.1";
 $mysql_database = "dbname";
 $mysql_user = "root";
 $mysql_password = "";

 $db = mysql_connect($mysql_host,$mysql_user,$mysql_password);
 mysql_connect($mysql_host,$mysql_user,$mysql_password);
 mysql_select_db($mysql_database);
?>