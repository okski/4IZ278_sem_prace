<?php
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Twice fandom web</title>
    <link rel="stylesheet" href="/~hosj03/sem_prace/resources/css/main.css">
    <link rel="stylesheet" href="/~hosj03/sem_prace/resources/css/print.css" media="print">
    <link href="/~hosj03/sem_prace/resources/css/Image_gallery.css" rel="stylesheet" media="screen">
    <link href="/~hosj03/sem_prace/resources/img_c/favicon/favicon.png" rel="icon">
</head>
<body>
<nav>
    <ol>
        <li class="active">
            <a href="/~hosj03/sem_prace/index.php">Home</a>
        </li>
        <li class="dropdown">
            <p class="dropbtn">Members
                <svg height="10" width="10">
                    <polygon points="10,5 0,10 0,0"/>
                </svg>
            </p>

            <ul class="dropdown-content">
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Nayeon.php">Nayeon (나연)</a>
                </li>
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Jeongyeon.php">Jeongyeon (정연)</a>
                </li>
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Momo.php">Momo (모모)</a>
                </li>
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Sana.php">Sana (사나)</a>
                </li>
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Jihyo.php">Jihyo (지효)</a>
                </li>
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Mina.php">Mina (미나)</a>
                </li>
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Dahyun.php">Dahyun (다현)</a>
                </li>
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Chaeyoung.php">Chaeyoung (채영)</a>
                </li>
                <li>
                    <a href="/~hosj03/sem_prace/resources/php/Tzuyu.php">Tzuyu (쯔위)</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="/~hosj03/sem_prace/resources/php/forming.php">History</a>
        </li>
        <li>
            <a href="/~hosj03/sem_prace/resources/php/discography.php">Discography</a>
        </li>
        <li>
            <a href="/~hosj03/sem_prace/resources/php/jype.php">JYPE</a>
        </li>
    </ol>
</nav>
<div class="log">';

if (isset($_SESSION['IdUser']) && !empty($_SESSION['IdUser'])) {
    echo '<a href="/~hosj03/sem_prace/logout.php" class="btn btn-primary">odhlásit se</a>';
} else {
    echo '<a href="/~hosj03/sem_prace/login.php" class="btn btn-primary">přihlásit se</a>';
}
echo '</div>';
?>

