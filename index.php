<?php


include 'admin/views.php';
include 'admin/exp.php';


header('Location: login.php?auth='.md5('flashcoder'));
    exit;
?>


