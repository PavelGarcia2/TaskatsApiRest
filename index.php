<?php

require 'vendor/autoload.php';


$host = "localhost";
$user = "root";
$password = "";
$database = "taskats";
$home = "/taskatsApiRest/";

Flight::register('db', 'PDO', array('mysql:host=' . $host . ';dbname=' . $database, $user, $password));

Flight::route('GET /', function () {

    $array = [
        "texto" => "Hello World",
        "status" => "success"
    ];

    echo json_encode($array);

});

Flight::route('GET '.$home.'users/', function () {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM user");
    $query->execute();
    $data = $query->fetchAll();
    
    $array = [];
    foreach ($data as $row){
        $array[]=[
            "id" => $row['user_id'],
            "name" => $row['username'],
            "email" => $row['email']
        ] ;           
    }
    
    /*
    $array = [
        "texto" => "Hello World",
        "status" => "success"
    ];

    echo json_encode($array);
    */
    Flight::json([
        "total_rows" => $query->rowCount(),
        "rows" => $array
    ]);
});


Flight::route('GET '.$home.'dailytasks/', function () {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM dailytask");
    $query->execute();
    $data = $query->fetchAll();
    

    $tasks = [];
    foreach ($data as $row){
        $tasks[]=[
            "daily_task_id"=> $row['daily_task_id'],
            "task_id"=> $row['task_id'],
            "date"=> $row['date'],
            "completed"=> $row['completed']
        ] ;           
    }
    /*
    $array = [
        "texto" => "Hello World",
        "status" => "success"
    ];

    echo json_encode($array);
    */
    Flight::json([
        "total_rows" => $query->rowCount(),
        "rows" => $tasks
    ]);
});

Flight::start();
