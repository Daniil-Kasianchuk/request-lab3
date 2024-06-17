<?php
include("connect.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $department_id = $_GET['department'];

    $stmt = $dbh->prepare('
        SELECT id_nurse, name, shift, date
        FROM nurse
        WHERE department = :department_id
    ');
    $stmt->bindValue(":department_id", $department_id);
    $stmt->execute();

    $nurses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($nurses);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Помилка: ' . $e->getMessage()]);
}
?>
