<?php
require_once __DIR__ . '/includes/auth.php';
log_out_user();
header('Location: ' . BASE_URL . '/index.php');
exit;
