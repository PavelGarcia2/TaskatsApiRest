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
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    
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
    $query = $db->prepare("SELECT * FROM dailytask JOIN task ON dailytask.task_id = task.task_id JOIN project ON task.project_id=project.project_id");
    $query->execute();
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    

    $tasks = [];
    foreach ($data as $row){
        $tasks[]=[
            "ID"=> $row['daily_task_id'],
            "Name"=> $row['title'], // to implement with join
            "Task_id"=> $row['task_id'],
            "Date"=> $row['date'],
            "Status"=> $row['completed'],
            "Color"=> $row['color'],
            "Punctuation"=> "3" //to implement in database and with join

        ] ;           
    }
    /*
    $array = [
        "texto" => "Hello World",
        "status" => "success"
    ];

    echo json_encode($array);
    */
    Flight::json(["tasks" => $tasks]);
});


Flight::route('GET '.$home.'projects/', function () {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM project ");
    $query->execute();
    $data = $query->fetchAll(PDO::FETCH_ASSOC);
    

    /*
    $array = [
        "texto" => "Hello World",
        "status" => "success"
    ];

    
    */
    
    Flight::json(["projects" => $data]);
});

Flight::start();
