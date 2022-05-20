<?php
chdir("../../");
$currentDIR = getcwd();

require_once $currentDIR .'/application/inc/user.php';

include  $currentDIR . '/application/inc/header.php';

include $currentDIR . '/resources/html/Tzuyu.html';

include  $currentDIR . '/application/inc/footer.php';

chdir(__DIR__);