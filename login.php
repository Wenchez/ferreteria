<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesion</title>
    <script src="JS/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="CSS/bootstrap.css">
    <script src="JS/bootstrap.bundle.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Epilogue;
            background: #f4f6fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <link rel="icon" href="/ferreteria/components/favicon.ico" type="image/x-icon">

    <script src="JS/login.js"></script>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100">
            <div class="col-12">
                <div class="row">
                    <div class="card p-4 shadow-lg">
                    <h1 class="card-title text-center mb-4">Iniciar Sesión</h1>
                    <form id="loginForm"> 
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario:</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="tu usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="tu password" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_choice" class="form-label">Usar Base de Datos:</label>
                            <select class="form-select" id="db_choice" name="db_choice">
                                <option value="local">Local</option>
                                <option value="atlas">Atlas (Respaldo)</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button id="submit" type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                <p class="text-center mt-3">
                    <a href="sign_up.html" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
                        Ir a registro
                    </a>
                </p>
                </div>
                </div>
                <div class="row">
                    <div id="loginMessage" class="mt-3 alert alert-warning d-none" role="alert">
                        
                    </div>
                </div>
            </div>
        </div>
    </div> 
</body>
</html>