<?php

session_start();
require_once("../classes/class.user.php");
$logout = new USER();
$logout->doLogout();