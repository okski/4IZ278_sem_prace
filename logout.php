<?php
require_once __DIR__ . '/application/inc/user.php';

if (!empty($_SESSION['IdUser'])){
    //smažeme ze session identifikaci uživatele
    unset($_SESSION['IdUser']);
    unset($_SESSION['Username']);
    unset($_SESSION['Admin']);
}

//přesměrujeme uživatele na homepage
header('Location: index.php');