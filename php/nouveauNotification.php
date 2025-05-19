<?php
require_once 'config.php';  // Connexion à la base de données 

if (empty($_GET['since'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètre since manquant']);
    exit();
}
$since = str_replace('T',' ', $_GET['since']); // ISO → DATETIME

$sql = "
    SELECT
        rv.id_rdv AS id,
        CONCAT(p.nom, ' ', p.prenom) AS patient, type_rdv AS rdvtype,
        DATE_FORMAT(rv.date_rdv, '%Y-%m-%dT%H:%i:%s') AS date_rdv
    FROM rendez_vous rv
    JOIN patients p USING(id_patient)
    WHERE rv.date_creation > :since
    ORDER BY rv.date_creation ASC
    LIMIT 50
";


$stmt = $pdo->prepare($sql);
$stmt->execute(['since' => $since]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Envoi JSON
header('Content-Type: application/json');
echo json_encode(['new' => $notifications]);

