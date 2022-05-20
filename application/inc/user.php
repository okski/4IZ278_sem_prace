<?php

  session_start();

  require_once 'db.php';

  require_once __DIR__ . '/../vendor/autoload.php';

  if (!empty($_SESSION['IdUser'])){
    $userQuery=$db->prepare('SELECT IdUser FROM hosj03.user WHERE IdUser=:id LIMIT 1;');
    $userQuery->execute([
      ':id'=>$_SESSION['IdUser']
    ]);
    if ($userQuery->rowCount()!=1){
      unset($_SESSION['IdUser']);
      unset($_SESSION['Username']);
      header('Location: index.php');
      exit();
    }
  }