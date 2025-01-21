<?php
/**
 * Script de benchmark pour évaluer la latence et le nombre de requêtes par seconde
 * en utilisant PDO avec une base de données MySQL.
 */

require 'vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

try {
    // Connexion à la base de données
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_PERSISTENT => false,
    ];
    $pdo = new PDO(
        'mysql:host=' . $_SERVER['MDEL_DBURL'] . ';port=' . $_SERVER['MDEL_DBPORT'] . ';dbname=' . $_SERVER['MDEL_DBNAME'] . ';charset=utf8mb4',
        $_SERVER['MDEL_DBUSER'],
        $_SERVER['MDEL_DBPASSWORD'] ?? null,
        $options);

    // Création de la table de test
    $pdo->exec("DROP TABLE IF EXISTS benchmark_test");
    $pdo->exec("
        CREATE TABLE benchmark_test (
            id INT AUTO_INCREMENT PRIMARY KEY,
            data VARCHAR(255) NOT NULL
        )
    ");
    echo "Table 'benchmark_test' créée avec succès.<br>";

    // Préparation de l'insertion
    $insertStmt = $pdo->prepare("INSERT INTO benchmark_test (data) VALUES (:data)");

    // Insertion de 100 000 lignes
    $startTime = microtime(true);
    $pdo->beginTransaction();
    for ($i = 1; $i <= 100000; $i++) {
        $insertStmt->execute([':data' => 'Ligne de test ' . $i]);
    }
    $pdo->commit();
    $insertDuration = microtime(true) - $startTime;
    echo "Insertion de 100 000 lignes terminée en " . number_format($insertDuration, 2) . " secondes.<br>";

    // Préparation des requêtes de lecture et d'écriture
    $selectStmt = $pdo->prepare("SELECT * FROM benchmark_test WHERE id = :id");
    $updateStmt = $pdo->prepare("UPDATE benchmark_test SET data = :data WHERE id = :id");

    // Exécution de 100 000 lectures aléatoires
    $startTime = microtime(true);
    for ($i = 1; $i <= 100000; $i++) {
        $randomId = rand(1, 1000000);
        $selectStmt->execute([':id' => $randomId]);
        $selectStmt->fetch();
    }
    $selectDuration = microtime(true) - $startTime;
    echo "100 000 lectures aléatoires terminées en " . number_format($selectDuration, 2) . " secondes.<br>";

    // Exécution de 100 000 écritures aléatoires
    $startTime = microtime(true);
    $pdo->beginTransaction();
    for ($i = 1; $i <= 100000; $i++) {
        $randomId = rand(1, 1000000);
        $updateStmt->execute([':id' => $randomId, ':data' => 'Mise à jour ' . $i]);
    }
    $pdo->commit();
    $updateDuration = microtime(true) - $startTime;
    echo "100 000 écritures aléatoires terminées en " . number_format($updateDuration, 2) . " secondes.<br>";

    // Calcul des performances
    $selectQPS = 100000 / $selectDuration;
    $updateQPS = 100000 / $updateDuration;
    echo "Performances :<br>";
    echo "- Lectures aléatoires : " . number_format($selectQPS, 2) . " requêtes par seconde.<br>";
    echo "- Écritures aléatoires : " . number_format($updateQPS, 2) . " requêtes par seconde.<br>";

    // Phase de nettoyage : suppression de la table de test
    $pdo->exec("DROP TABLE IF EXISTS benchmark_test");
    echo "Table 'benchmark_test' supprimée après le benchmark.<br>";

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
