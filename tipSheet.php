<?php
//create new file
$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file

//open file
$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //open file for writing ('w','r','a')...

//read a file
$my_file = 'file.txt';
$handle = fopen($my_file, 'r');
$data = fread($handle,filesize($my_file));

//Write to a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
$data = 'This is the data';
fwrite($handle, $data);

//Append to a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file);
$data = 'New data line 1';
fwrite($handle, $data);
$new_data = "\n".'New data line 2';
fwrite($handle, $new_data);

//Close a File
$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
//write some data here
fclose($handle);

//Delete a File
$my_file = 'file.txt';
unlink($my_file);

//xml to sql
header('Content-Type: application/json');
function mysqlDBConnection(){
	$connection = mysqli_connect("localhost","root","root");
		if (!$connection) {
			die("database connection failed: " . mysqli_connect_error());
		}
		// Select a database to use
		$db_select = mysqli_select_db($connection,"configuration_xml");
		
		if (!$db_select) {
			die("database selection failed: " . mysqli_connect_error());
	}
	return $connection;
}
function  mysql_connection_insert($name,$module,$value,$connection){	
	
	
	$query ='INSERT INTO `mod_config_xml`( `Name`, `Module`, `Value`) VALUES ("'.$name.'","'.$module.'","'.$value.'") ON DUPLICATE KEY UPDATE `Module`="'.$module.'";';
   	
   	/*-------Print INSERT & UPDATE Query-------*/
   	echo $query ; echo "\n";
   	
   	/*---------------Insert Record in MySQL Database Base---------------- /	
   /* if(mysqli_query($connection,$query)){
      $data = array("status"=>"success", "message"=>"data submited!");
    }
    else{
    	 $data = array("status"=>"error", "message"=>"database query failed !");
	
    }
    return $data;*/
}
function configXmlReader($Config_file_path){
	$connection = array();
	/*------MySQL DATABASE Connnection Function---*/
	//$connection = mysqlDBConnection();
	$xml   = simplexml_load_file($Config_file_path, 'SimpleXMLElement', LIBXML_NOCDATA);
	$array = json_decode(json_encode($xml), TRUE);
	$array_value= array_values($array);
	$module_array = array_keys($array);
	for($i=0;$i<count($module_array);$i++){
		$name_array[$i] = array_keys($array_value[$i]);
	
		$value_array[$i] = array_values($array_value[$i]);
	
		for($j=0;$j<count($name_array[$i]);$j++){
	
			if($name_array[$i][$j]!="@attributes"){
	
			  $output[$module_array[$i]][$name_array[$i][$j]] = mysql_connection_insert($name_array[$i][$j],$module_array[$i],$value_array[$i][$j],$connection);
	
			}
			
		}
		
	}
	/* COMMENT REQUIRE JSON OUTPUT */
	exit();
	/****---------------MYSQL DATABASE INSERT SUCCESSFULL JSON OUTPUT RETURN------*/
	return $output;
}
/*
  TERMINAL COMMAND :
  php configxml.php  '/var/www/html/a.xml' 
*/
parse_str($argv[1], $_GET);
parse_str($argv[1], $_POST);
/*print_r($argv);*/
$post_array=explode("&", $argv[1]);
$Config_file_path  =  $post_array[0];  /*"/var/www/html/a.xml";*/
$output = configXmlReader($Config_file_path);
/**----------JSON Output----------------***/
echo json_encode($output,JSON_PRETTY_PRINT); echo "\n";

//xml to sql end
?>
