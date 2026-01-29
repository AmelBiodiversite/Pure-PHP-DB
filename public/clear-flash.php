<?php
session_start();
unset($_SESSION['flash_message']);
unset($_SESSION['flash_type']);
header('Location: /');
exit;
