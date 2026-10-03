<?php
$host = '127.0.0.1';
$user = 'u839226731_farol';
$pass = 'Meta6595869!';
$db   = 'u839226731_farol';

$cx = new mysqli($host, $user, $pass, $db);

if ($cx->connect_error) {
  die('Erro na conexão: ' . $cx->connect_error);
}

$cx->set_charset("utf8mb4");
