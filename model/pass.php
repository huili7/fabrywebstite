<?php
require_once('connection.php');

$name=$_POST['name'];
$email=$_POST['email'];
$phone=$_POST['phone'];
$message=$_POST['message'];
$subject=$_POST['subject'];
$institute=$_POST['institute'];
$street=$_POST['street'];
$city=$_POST['city'];


$query=" insert into CONTRIBUTE(name,email,phone,subject,message,institute,street,city)
values(";
$query=$query."'".$name."',";
$query=$query."'".$email."',";
$query=$query."'".$phone."',";
$query=$query."'".$subject."',";
$query=$query."'".$message."',";
$query=$query."'".$institute."',";
$query=$query."'".$street."',";
$query=$query."'".$city."')";


$pdo = Db::getInstance();
$stmt = $pdo->prepare($query);
    try {
     	$stmt->execute();

	} catch (PDOException $e) {
     	echo $e->getMessage();
}







?>
