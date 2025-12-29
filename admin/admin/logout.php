<?php
require_once '../../config/config.php';
require_once '../../classes/Admin.php';

$admin = new Admin();
$admin->logout();

header('Location: index.php');
exit;
?>
