<?php
require_once 'config.php';  // Connexion à la base de données
// Récupération des paramètres 'start' et 'end' envoyés par FullCalendar
$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

if (!$start || !$end) {
    http_response_code(400);
    echo json_encode(['error' => 'Paramètres start et end requis.']);
    exit();
}

// Requête SQL pour récupérer les rendez-vous dans la plage de dates
$sql = "
    SELECT 
        rv.id_rdv AS id,
        CONCAT(p.nom, ' ', p.prenom, ' – ', rv.type_rdv) AS title,
        DATE_FORMAT(rv.date_rdv, '%Y-%m-%dT%H:%i:%s') AS start,
        DATE_FORMAT(DATE_ADD(rv.date_rdv, INTERVAL rv.duree_min MINUTE), '%Y-%m-%dT%H:%i:%s') AS end,
        rv.statut AS className
    FROM rendez_vous rv
    JOIN patients p ON p.id_patient = rv.id_patient
    WHERE rv.date_rdv BETWEEN :date_debut AND :date_fin
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':date_debut', $start);
$stmt->bindParam(':date_fin', $end);
$stmt->execute();

$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Envoi des résultats au format JSON
header('Content-Type: application/json');
echo json_encode($evenements);
?>
