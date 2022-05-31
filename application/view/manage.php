<?php
//načteme připojení k databázi a inicializujeme session
require_once __DIR__ . '/../inc/user.php';

if (!isset($_SESSION['IdUser']) || $_SESSION['Admin'] != 1) {
    include __DIR__ . '/../../error/400.html';
    exit();
}


include __DIR__ . '/../inc/header.php';
?>

<div class="breadcrumb_div">
    <ul class="breadcrumb_ul">
        <li class="breadcrumb_li">
            <a href="./../../index.php">Home</a>
            <p class="arrow">→</p>
        </li>
        <li class="breadcrumb_li">
            <p>Manage</p>
        </li>
    </ul>
</div>

<h1>Admin page</h1>

<?php
include __DIR__ . '/../inc/footer.php';
