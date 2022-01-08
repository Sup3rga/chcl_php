<?php

require_once "self_autoload.php";
require "./vendor/autoload.php";

(new Route())
->setDefault("akademy/404.html")
->setPublic("css/")
->setPublic("js/")
->setPublic("assets/")
->redirect("", "index.html")
->redirect("akademy/", "akademy/index.html")
->delegate("akademy$", "akademy.php")
->delegate("signin$", "login.php")
->delegate("submit$", "main.php")
->watch();