<?php
$pdo = new PDO(
    "pgsql:host=localhost;port=5432;dbname=classwall",
    "postgres",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);