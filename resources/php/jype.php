<?php
chdir("../../");
$currentDIR = getcwd();

include  $currentDIR . '/application/inc/header.php';

include $currentDIR . '/resources/html/jype.html';

include  $currentDIR . '/application/inc/footer.php';

chdir(__DIR__);