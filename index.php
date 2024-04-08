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

Flight::route('GET ' . $home . 'users/', function () {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM user");
    $query->execute();
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    $array = [];
    foreach ($data as $row) {
        $array[] = [
            "id" => $row['user_id'],
            "name" => $row['username'],
            "email" => $row['email']
        ];
    }


    Flight::json([
        "total_rows" => $query->rowCount(),
        "rows" => $array
    ]);
});


Flight::route('GET ' . $home . 'users/@id', function ($id) {
    $db = Flight::db();
    $query = $db->prepare("SELECT * FROM user WHERE user_id= :id");
    $query->execute([":id" => $id]);
    $data = $query->fetch(PDO::FETCH_ASSOC);

    $array = [
        "name" => $data['username'],
        "id" => $data['user_id'],
        "email" => $data['email']
    ];



    Flight::json($array);
});


Flight::route('POST ' . $home . 'users/', function () {
    $db = Flight::db();

    $name = Flight::request()->data->name;
    $email = Flight::request()->data->email;
    $password = Flight::request()->data->password;

    try {
        $query = $db->prepare("INSERT INTO user (username, password, email) VALUES (:username, :pass, :email)");

        if ($query->execute([":username" => $name, ":pass" => $password, ":email" => $email])) {
            $array = [
                "data" => [
                    "id" => $db->lastInsertId(),
                    "name" => $name,
                    "password" => $password,
                    "email" => $email
                ],
                "status" => "success"
            ];
        } else {
            $array = [
                "error" => "Hubo un error al insertar los registros",
                "status" => "error"
            ];
        }
    } catch (PDOException $e) {
        // Captura la excepción de la base de datos y maneja el error
        $array = [
            "error" => "Hubo un error en la base de datos: " . $e->getMessage(),
            "status" => "error"
        ];
    }

    Flight::json($array);
});


Flight::route('GET ' . $home . 'dailytasks/', function () {
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
    /*
    $array = [
        "texto" => "Hello World",
        "status" => "success"
    ];

    echo json_encode($array);
    */
    Flight::json(["tasks" => $tasks]);
});


Flight::route('GET ' . $home . 'projects/', function () {
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
