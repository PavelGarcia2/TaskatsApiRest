<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Users
{
    private $db;

    function __construct()
    {
        $key = 'PASWWORD_SECRETO'; // esto debe ser una variable de entorno ya que es privada
        $host = "localhost";
        $user = "root";
        $password = "";
        $database = "taskats";
        $home = "/taskatsApiRest/";

        Flight::register(
            'db',
            'PDO',
            array('mysql:host=' . $host . ';dbname=' . $database, $user, $password)
            /*
        ,
            function($db){
            $this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        }*/
        );
        $this->db = Flight::db();
    }


    function auth()
    {
        $email = Flight::request()->data->email;
        $password = Flight::request()->data->password;
        $query = $this->db->prepare("SELECT * FROM user WHERE email=:email AND password = :password"); //this is not safe in future implement correctly thanks

        try {

            if ($query->execute([":email" => $email, ":password" => $password])) {
                //the user is allowed authorised
                $user = $query->fetch();
                //we issue the jwt
                //get the current time
                $now = strtotime("now");
                $key = 'PASWWORD_SECRETO';
                $payload = [
                    'exp' => $now + 3600, //3600s = 1h duration till expire
                    'data' => $user['user_id']

                ];

                $jwt = JWT::encode($payload, $key, 'HS256');
                $response = ["token" => $jwt];
            } else {
                $response = [
                    "error" => "No se pudo validar su identidad",
                    "status" => "error"
                ];
            }
        } catch (PDOException $e) {
            // Captura la excepción de la base de datos y maneja el error
            $response = [
                "error" => "Hubo un error en la base de datos: " . $e->getMessage(),
                "status" => "error"
            ];
        }

        Flight::json($response);

        /*
    
        */
    }


    function selectAll()
    {
        if (!$this->validateToken()) {
            Flight::halt(403, json_encode([
                "error" => 'Unauthorized',
                "status" => 'error'
            ]));
        }
        $query = $this->db->prepare("SELECT * FROM user");
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
    }


    function selectOne($id)
    {
        if (!$this->validateToken()) {
            Flight::halt(403, json_encode([
                "error" => 'Unauthorized',
                "status" => 'error'
            ]));
        }

        $query = $this->db->prepare("SELECT * FROM user WHERE user_id= :id");
        $query->execute([":id" => $id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);

        $array = [
            "name" => $data['username'],
            "id" => $data['user_id'],
            "email" => $data['email']
        ];



        Flight::json($array);
    }


    function insert()
    {
        if (!$this->validateToken()) {
            Flight::halt(403, json_encode([
                "error" => 'Unauthorized',
                "status" => 'error'
            ]));
        }

        $name = Flight::request()->data->name;
        $email = Flight::request()->data->email;
        $password = Flight::request()->data->password;

        try {
            $query = $this->db->prepare("INSERT INTO user (username, password, email) VALUES (:username, :pass, :email)");

            if ($query->execute([":username" => $name, ":pass" => $password, ":email" => $email])) {
                $array = [
                    "data" => [
                        "id" => $this->db->lastInsertId(),
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
    }



    function update()
    {
        if (!$this->validateToken()) {
            Flight::halt(403, json_encode([
                "error" => 'Unauthorized',
                "status" => 'error'
            ]));
        }
        $id = Flight::request()->data->id;
        $name = Flight::request()->data->name;
        $email = Flight::request()->data->email;
        $password = Flight::request()->data->password;

        try {
            $query = $this->db->prepare("UPDATE user SET username=:username, password=:pass, email= :email WHERE user_id = :id");

            if ($query->execute([":username" => $name, ":pass" => $password, ":email" => $email, ":id" => $id])) {
                $array = [
                    "data" => [
                        "id" => $id,
                        "name" => $name,
                        "password" => $password,
                        "email" => $email
                    ],
                    "status" => "success"
                ];
            } else {
                $array = [
                    "error" => "Hubo un error al actualizar los registros",
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
    }


    function delete()
    {
        if (!$this->validateToken()) {
            Flight::halt(403, json_encode([
                "error" => 'Unauthorized',
                "status" => 'error'
            ]));
        }
        $id = Flight::request()->data->id;

        try {
            $query = $this->db->prepare("DELETE FROM user WHERE user_id = :id");

            if ($query->execute([":id" => $id])) {
                $array = [
                    "data" => [
                        "id" => $id
                    ],
                    "status" => "success"
                ];
            } else {
                $array = [
                    "error" => "Hubo un error al eliminar los registros",
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
    }









    //AUTHENTICATION
    function getToken()
    {
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            Flight::halt(403, json_encode([
                "error" => 'Unauthenticated',
                "status" => 'error'
            ]));
        }
        $authorization = $headers['Authorization'];
        $authorizationArray = explode(" ", $authorization);
        $token = $authorizationArray[1]; // {[Bearer],[jwt]}
        $key = 'PASWWORD_SECRETO';
        try {
            $decodedToken = JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Throwable $th) {
            Flight::halt(403, json_encode([
                "error" => $th->getMessage(),
                "status" => 'error'
            ]));
        }
        return $decodedToken;
    }

    function validateToken()
    {
        $info = $this->getToken();
        $db = Flight::db();
        $query = $db->prepare("SELECT * FROM user WHERE user_id=:id");
        $query->execute([":id" => $info->data]);
        $rows = $query->fetchColumn();
        return $rows;
    }
}
