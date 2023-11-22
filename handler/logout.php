<?php
session_destroy();
setcookie('remember','', 1,'/');
header("Location: /");
exit;