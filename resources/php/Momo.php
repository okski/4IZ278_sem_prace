<?php
chdir("../../");
$currentDIR = getcwd();

//var_dump(__DIR__);
//var_dump($currentDIR);

include  $currentDIR . '/application/inc/header.php';

include $currentDIR . '/resources/html/Momo.html';

include  $currentDIR . '/application/inc/footer.php';

chdir(__DIR__);