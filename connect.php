<?php

//establish connection with the database

$con;
try{
    $con = new PDO("mysql:host=localhost;dbname=assignment", 'root', '');
}
catch(PDOException $e){
    $con = null;
}

?>