<?php
$stationId = '000322'; //RENNES-ST JACQUES

$oDb = new mysqli('percona57', 'root', 'root', 'smartapp');
$oDb->set_charset("utf8");


$srcPath = __DIR__ . '/data';
$fileTempMax = sprintf('TX_STAID%s.txt', $stationId);
$fileTempMin = sprintf('TN_STAID%s.txt', $stationId);


$tempFileContent = explode("\n", file_get_contents($srcPath . '/' . $fileTempMax));
foreach($tempFileContent as $row) {

    if(0 === preg_match(',(2015|2016)([0-9]{2})([0-9]{2}),', $row, $matches)) {
        continue;
    }
    if(!in_array($matches[1], [2015,2016])) {
        continue;
    }

    $date = DateTime::createFromFormat('Ymd', $matches[0])->format('Y-m-d');
    $cols = explode(',', $row);

    if(!isset($data[$date])) {
        $data[$date] = [];
    }

    $data[$date]['max'] = (float)((int)$cols[3]/10);
}


$tempFileContent = explode("\n", file_get_contents($srcPath . '/' . $fileTempMin));

foreach($tempFileContent as $row) {

    if(0 === preg_match(',(2015|2016)([0-9]{2})([0-9]{2}),', $row, $matches)) {
        continue;
    }
    if(!in_array($matches[1], [2015,2016])) {
        continue;
    }

    $date = DateTime::createFromFormat('Ymd', $matches[0])->format('Y-m-d');
    $cols = explode(',', $row);

    if(!isset($data[$date])) {
        $data[$date] = [];
    }
    $data[$date]['min'] = (float)((int)$cols[3]/10);
}

try {

    foreach ($data as $date => $temperatures) {
        $stmt = $oDb->stmt_init();
        $stmt->prepare("INSERT INTO `evenement_meteo` (`date`, `temperature_max`, `temperature_min`) VALUES (?, ?, ?);");

        $stmt->bind_param('sdd', $date, $temperatures['max'], $temperatures['min']);

        $stmt->execute();
    }

    $oDb->close();

} catch (Exception $e) {
    $error = $e->getMessage();
    print("\n==== Erreur ====\n\n" . $error . "\n\n");
}