<?php
require_once __DIR__ . '/includes/app.php';
session_destroy();
redirect('index.php');
