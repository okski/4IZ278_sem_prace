<?php
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title id="title">Twice fandom web</title>
    <link rel="stylesheet" href="/~hosj03/sem_prace/resources/css/main.css">
    <link rel="stylesheet" href="/~hosj03/sem_prace/resources/css/print.css" media="print">
    <link rel="stylesheet" href="/~hosj03/sem_prace/resources/css/forming.css">
    <link rel="stylesheet" href="/~hosj03/sem_prace/resources/css/Image_gallery.css">
    <link href="/~hosj03/sem_prace/resources/img_c/favicon/favicon.png" rel="icon">
    <link rel="stylesheet" href="/~hosj03/sem_prace/resources/css/discography.css">
</head>
<body>
<nav>
    <ol>
        <li id="Home">
            <a href="/~hosj03/sem_prace/index.php">Home</a>
        </li>
        <li class="dropdown">
            <p class="dropbtn">Members
                <svg height="10" width="10">
                    <polygon points="10,5 0,10 0,0"/>
                </svg>
            </p>

            <ul class="dropdown-content">
                <li id="Nayeon">
                    <a href="/~hosj03/sem_prace/resources/php/Nayeon.php">Nayeon (나연)</a>
                </li>
                <li id="Jeongyeon">
                    <a href="/~hosj03/sem_prace/resources/php/Jeongyeon.php">Jeongyeon (정연)</a>
                </li>
                <li id="Momo">
                    <a href="/~hosj03/sem_prace/resources/php/Momo.php">Momo (모모)</a>
                </li>
                <li id="Sana">
                    <a href="/~hosj03/sem_prace/resources/php/Sana.php">Sana (사나)</a>
                </li>
                <li id="Jihyo">
                    <a href="/~hosj03/sem_prace/resources/php/Jihyo.php">Jihyo (지효)</a>
                </li>
                <li id="Mina">
                    <a href="/~hosj03/sem_prace/resources/php/Mina.php">Mina (미나)</a>
                </li>
                <li id="Dahyun">
                    <a href="/~hosj03/sem_prace/resources/php/Dahyun.php">Dahyun (다현)</a>
                </li>
                <li id="Chaeyoung">
                    <a href="/~hosj03/sem_prace/resources/php/Chaeyoung.php">Chaeyoung (채영)</a>
                </li>
                <li id="Tzuyu">
                    <a href="/~hosj03/sem_prace/resources/php/Tzuyu.php">Tzuyu (쯔위)</a>
                </li>
            </ul>
        </li>
        <li id="History">
            <a href="/~hosj03/sem_prace/resources/php/forming.php">History</a>
        </li>
        <li id="Discography">
            <a href="/~hosj03/sem_prace/application/view/discography.php">Discography</a>
        </li>
        <li id="JYPE">
            <a href="/~hosj03/sem_prace/resources/php/jype.php">JYPE</a>
        </li>';
        if (isset($_SESSION['Admin']) && $_SESSION['Admin']) {
            echo '<li id="Manage">
            <a href="/~hosj03/sem_prace/application/view/manage.php">Manage</a>';
        }

        echo '</li>
    </ol>
</nav>
<div class="log">';

if (isset($_SESSION['IdUser']) && !empty($_SESSION['IdUser'])) {
    echo '<div class="user">logged in as <span class="username">'.$_SESSION['Username'].'</span></div>';
    echo '<a href="/~hosj03/sem_prace/logout.php" class="btn btn-primary">logout</a>';
} else {
    echo '<a href="/~hosj03/sem_prace/application/view/login.php" class="btn btn-primary">login</a>';
}
echo '</div>';

