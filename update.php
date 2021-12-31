<?php
require ('../headers/header.php');
//connection to the db
$pdo;

//target id

$target_id;
//in coming id
$id=$_GET[$target_id];

//table name
$table_name;

//updating($pdo, $d = array(), $id, $page_location)

//redirecting page
$redirect_page;
//locating values in atable and storing values in a dataling variable
//$access->locate($pdo,$id,'products');
$dataling=$access->locate($pdo,$target_id,$id,$table_name);

//updating the values to the db
$access->updating($pdo,$d,$id,$redirect_page);

// require ('../headers/header.php');
// $id=$_GET['id'];
// $access->locate($pdo,$id,'products');
// $dataling=$access->locate($pdo,$id,'products');