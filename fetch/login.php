<?php
    require_once 'config.php';
    session_start();

    $errors = [
        'login'=>$_SESSION['login_error'] ?? '',
        'register'=>$_SESSION['register_error'] ?? '',
    ];
    $activeForm = $_SESSION['active_form'] ?? 'login';

    session_unset();

    function ShowError($error){
        return !empty($error) ? "<p class='error_message'>$error</p>" : '';
    }

    function IsActiveForm($formName, $activeForm){
        return $formName === $activeForm ? 'active' : '';
    }
    
        
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>fetch</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/general_css.css">
    <script src="js/dark_mode.js" defer></script>
    <link rel="shortcut icon" type="image/x-icon" href="img/map.png" />
</head>

<body>
    

    <div class="wrapper">
    <header>
        <div class="header__container">
        <nav class="navMenu">
            <div class="headerLogo">
                <a class = "logo" href="api/index.php">
                    fetch
                </a>
            </div>
            <ul class="headerList">
                
               
                <li class="mainList themeSwitchButton">
                    <button id="theme-switch">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000"><path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Z"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000"><path d="M480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480q0 83-58.5 141.5T480-280ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Z"/></svg>
                    </button>
                </li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>
        </div>
    </header>
    <main class="main">
        <div class="main__container formContainer">
            
            <div class="formBox addItem <?= IsActiveForm('login', $activeForm); ?>" id="loginForm">
                <form action="login_register.php" method="post">
                    <h2 class="formTitle">Login</h2>
                    <?= ShowError($errors['login']); ?>
                    <input type="username" class="loginInput" name="username" placeholder="Username" required>
                    <input type="password" class="loginInput" name="password" placeholder="Password" required>
                    <button type="submit" class="button" name="login">Log In</button>
                    <p class="accountNotification">Don't have an account? <a href="#" onclick="ShowForm('registerForm')">Register</a></p>
                </form>
            </div>
    
            <div class="formBox  addItem <?= IsActiveForm('register', $activeForm); ?>" id="registerForm">
                <form action="login_register.php" method="post">
                    <h2 class="formTitle">Register</h2>
                    <?= ShowError($errors['register']); ?>
                    <input type="username" class="loginInput" name="username" placeholder="Username" required>
                    <input type="password" class="loginInput" name="password" placeholder="Password" required>
                    <button type="submit" class="button" name="register">Register</button>
                    <p class="accountNotification">Already have an account? <a href="login.php" onclick="ShowForm('loginForm')">Log In</a></p>
                </form>
            </div>
            
        </div>
    
    </main>
    <footer class="footer">
        <div class="footer__container">
            
        </div>
    </footer>
    </div>
    <script src="js/script.js"></script>
</body>

</html>