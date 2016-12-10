<?php

$oDb = new mysqli('percona57', 'root', 'root', 'smartapp');
$oDb->set_charset("utf8");

$srcPath = __DIR__ . '/data';
$srcFile = $srcPath . '/' . 'evenements.csv';

$srcFileContent = explode("\n", file_get_contents($srcFile));
unset($srcFileContent[0]);

try {
    foreach ($srcFileContent as $row) {

        $cols = array_map('trim', explode(',', $row));

        $stmt = $oDb->stmt_init();
        $stmt->prepare("INSERT INTO `evenement_divers` (`date_debut`, `date_fin`, `type`, `label`) VALUES (?, ?, ?, ?);");

        $dateDebut = DateTime::createFromFormat('d/m/Y', $cols[0]);
        if($dateDebut === false) {
            var_dump($dateDebut, $cols[0]);
            die;
        }
        $dateDebut = $dateDebut->format('Y-m-d');

        $dateFin = DateTime::createFromFormat('d/m/Y', $cols[1]);
        if($dateFin === false) {
            var_dump($dateFin, $cols[0]);
            die;
        }
        $dateFin = $dateFin->format('Y-m-d');

        $type = mb_convert_encoding($cols[3], 'UTF-8');
        $libelle = mb_convert_encoding($cols[2], 'UTF-8');

        $stmt->bind_param('ssss'
            , $dateDebut
            , $dateFin
            , $type
            , $libelle
        );

        $stmt->execute();
    }

    $oDb->close();

} catch (Exception $e) {
    $error = $e->getMessage();
    print("\n==== Erreur ====\n\n" . $error . "\n\n");
}