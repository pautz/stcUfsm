<?php
$host = getenv('DB_SERVER') ?: '127.0.0.1';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$db   = getenv('DB_NAME') ?: 'u839226731_farol';

$cx = new mysqli($host, $user, $pass, $db);

if ($cx->connect_error) {
  die('Erro na conexão: ' . $cx->connect_error);
}

$cx->set_charset("utf8mb4");
