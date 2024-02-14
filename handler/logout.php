<?php
die(var_dump("Logout"));
$user->logout();
header("Location: /");
exit;