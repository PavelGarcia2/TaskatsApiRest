<?php
//importing for authentication with jwt
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


require 'vendor/autoload.php';
require 'classes/Users.php';


$key = 'PASWWORD_SECRETO'; // esto debe ser una variable de entorno ya que es privada
$host = "localhost";
$user = "root";
$password = "";
$database = "taskats";
$home = "/taskatsApiRest/";
$users = new Users();




//ENDPOINT for authentication a user
Flight::route('POST ' . $home . 'auth/', [$users,'auth']);

//ENDPOINT USERS 
//get info of all users56
Flight::route('GET ' . $home . 'users/', [$users, 'selectAll']);

//inserting a user
Flight::route('POST ' . $home . 'users/',[$users,'insert']);

Flight::route('PUT ' . $home . 'users/', [$users,'update']);

Flight::route('DELETE ' . $home . 'users/', [$users,'delete']);

//get info of a single user
Flight::route('GET ' . $home . 'users/@id',[$users,'selectOne']);


//ENDPOINT projects
/*
Flight::route('GET ' . $home . 'projects/', function () {
    if (!validateToken()) {
        Flight::halt(403, json_encode([
            "error" => 'Unauthorized',
            "status" => 'error'
        ]));
    }
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM project ");
    $query->execute();
    $data = $query->fetchAll(PDO::FETCH_ASSOC);


    
    $array = [
        "texto" => "Hello World",
        "status" => "success"
    ];

    
   

    Flight::json(["projects" => $data]);
});


//ENDPOINT tasks


//ENDPOINT DailyTasks

Flight::route('GET ' . $home . 'dailytasks/', function () {
    if (!validateToken()) {
        Flight::halt(403, json_encode([
            "error" => 'Unauthorized',
            "status" => 'error'
        ]));
    }
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM dailytask JOIN task ON dailytask.task_id = task.task_id JOIN project ON task.project_id=project.project_id");
    $query->execute();
    $data = $query->fetchAll(PDO::FETCH_ASSOC);


    $tasks = [];
    foreach ($data as $row) {
        $tasks[] = [
            "ID" => $row['daily_task_id'],
            "Name" => $row['title'],
            "Task_id" => $row['task_id'],
            "Date" => $row['date'],
            "Status" => $row['completed'],
            "Color" => $row['color'],
            "Punctuation" => "3" //to implement in database and with join

        ];
    }

    Flight::json(["tasks" => $tasks]);
});


 */

Flight::start();
