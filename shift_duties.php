<?php
include("connect.php");

header('Content-Type: text/xml; charset=utf-8');

try {
    $shift = $_GET['shift'];

    $stmt = $dbh->prepare('
        SELECT n.id_nurse, n.name, w.name AS ward_name, n.date
        FROM nurse n
        JOIN nurse_ward nw ON n.id_nurse = nw.fid_nurse
        JOIN ward w ON nw.fid_ward = w.id_ward
        WHERE n.shift = :shift
    ');
    $stmt->bindValue(":shift", $shift);
    $stmt->execute();

    $duties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $xml = new SimpleXMLElement('<duties/>');

    foreach ($duties as $duty) {
        $dutyXml = $xml->addChild('duty');
        $dutyXml->addChild('id', $duty['id_nurse']);
        $dutyXml->addChild('name', $duty['name']);
        $dutyXml->addChild('ward', $duty['ward_name']);
        $dutyXml->addChild('date', $duty['date']);
    }

    echo $xml->asXML();
} catch (PDOException $e) {
    $xml = new SimpleXMLElement('<error/>');
    $xml->addChild('message', 'Помилка: ' . $e->getMessage());
    echo $xml->asXML();
}
?>
