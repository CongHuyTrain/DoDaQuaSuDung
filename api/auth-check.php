<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

if(isset($_SESSION["user_id"])){

    echo json_encode([
        "success"=>true,
        "logged_in"=>true,
        "user"=>[
            "id"=>$_SESSION["user_id"],
            "username"=>$_SESSION["username"] ?? "",
            "fullname"=>$_SESSION["fullname"] ?? ""
        ]
    ]);

}else{

    echo json_encode([
        "success"=>true,
        "logged_in"=>false
    ]);

}