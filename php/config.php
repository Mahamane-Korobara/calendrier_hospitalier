<?php

// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=hopital_demo;charset=utf8';
$nomUtilisateur = 'root';
$motdepasse = '';

try {
    $pdo = new PDO($dsn, $nomUtilisateur, $motdepasse);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion : ' . $e->getMessage()]);
    exit();
}
