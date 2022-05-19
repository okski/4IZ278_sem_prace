<?php

  session_start(); //spustíme session

  require_once 'db.php'; //načteme připojení k databázi

  require_once __DIR__ . '/../vendor/autoload.php';//načtení class loaderu vytvořeného composerem

  #region kontrola, jestli je přihlášený uživatel platný
  if (!empty($_SESSION['IdUser'])){
    $userQuery=$db->prepare('SELECT user_id FROM hosj03.user WHERE IdUser=:id LIMIT 1;');
    $userQuery->execute([
      ':id'=>$_SESSION['IdUser']
    ]);
    if ($userQuery->rowCount()!=1){
      //uživatel už není v DB, nebo není aktivní => musíme ho odhlásit
      unset($_SESSION['IdUser']);
      unset($_SESSION['Username']);
      header('Location: index.php');
      exit();
    }
  }
  #endregion kontrola, jestli je přihlášený uživatel platný