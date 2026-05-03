<?php
session_start();
$_SERVER["REQUEST_METHOD"] = "POST";
$_SESSION["user_id"] = 1;
$_SESSION["user_role"] = "admin";
define("CSRF_TOKEN_NAME", "_csrf_token");
$_SESSION[CSRF_TOKEN_NAME] = "12345";
$_POST[CSRF_TOKEN_NAME] = "12345";
$_POST["name"] = "PHP Test Client";
$_POST["email"] = "teste@teste.com";
$_POST["status"] = "ativo";
require_once "config/app.php";
require_once "config/database.php";
require_once "includes/helpers.php";
require_once "controllers/ClientController.php";
$ctrl = new ClientController();
$ctrl->store();

