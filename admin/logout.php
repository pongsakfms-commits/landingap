<?php
/**
 * Admin Logout
 */
require_once __DIR__ . '/../config/config.php';

session_destroy();
redirect('/admin/login.php');
