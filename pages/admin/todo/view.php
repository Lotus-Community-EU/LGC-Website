<?php

if(!isset($GET[3])) {
    $_SESSION['error_title'] = 'Not existing!';
    $_SESSION['error_message'] = 'To-Do List doesn\'t exist (unset)!';
    header("Location: /admin/todo/list");
    exit;
}
$id = $GET[3];

$todo = new ToDo($id);
if($todo == null) {
    echo 'bla';
    $_SESSION['error_title'] = 'Not existing!';
    $_SESSION['error_message'] = 'To-Do List doesn\'t exist!';
    header("Location: /admin/todo/list");
    exit;
}

if($todo->canAccess($user->getMainRank(), $user->getSecondaryRank()) || $user->hasPermission('admin_todo_master')) {
    echo 'Yes';
}
else {
    echo 'No';
}
?>