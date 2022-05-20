<?php
require_once 'application/inc/user.php';

if (!empty($_SESSION['IdUser'])){
    //smažeme ze session identifikaci uživatele
    unset($_SESSION['IdUser']);
    unset($_SESSION['Username']);
}

//přesměrujeme uživatele na homepage
header('Location: index.php');