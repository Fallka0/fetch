<?php

session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkUsername = $conn->query("SELECT username FROM users WHERE username = '$username'");

    if ($checkUsername->num_rows > 0){
        $_SESSION['register_error'] = 'Username is already registered';
        $_SESSION['active_form'] = 'register';
    }else{
        $conn->query("INSERT INTO users(username, password) VALUES('$username', '$password')");
    }

    header("Location: login.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$username' ");
    if ($result->num_rows > 0){
        $user = $result-> fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id']; // Make sure you fetch 'id' in SELECT *

            header("Location: index.php");
            exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect Username or Password';
    $_SESSION['active_form'] = 'login';
    header("Location: login.php");
    exit();
   
}

   
?>
