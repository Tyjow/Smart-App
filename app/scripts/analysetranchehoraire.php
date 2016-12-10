<?php

$annees = [2015, 2016];

$oDb = new mysqli('percona57', 'root', 'root', 'smartapp');
$csvSrcPath     = __DIR__ . '/data';

foreach($annees as $annee) {

    $csvSrcFile     = sprintf('analysetranchehoraire%d.csv', $annee);
    $csvSrcFilepath = $csvSrcPath . '/' . $csvSrcFile;

    $csvRows         = explode("\n", trim(file_get_contents($csvSrcFilepath)));
    $headers = explode(';', $csvRows[0]);
    $nbCol   = sizeof($headers);
    unset($csvRows[0]);

    printf("[!] %d colonnes entête\n", $nbCol);

    foreach($csvRows as $rowIndex => $row){

        $rowCols    = explode(';', trim($row));
        $nbRowCols  = sizeof($rowCols);

        if($nbRowCols!== $nbCol) {
            var_dump($row);
            var_dump($rowCols);
            printf("[!] %d colonnes sur la ligne : %d\n", $nbRowCols, $rowIndex);
            die("\n=== Fin du script===\n");
        }

        // Nettoyage des valeurs
        foreach($rowCols as $colIndex => $colValue) {
            $rowCols[$colIndex] = trim($colValue, '"');
        }


        //On zap les lignes "Total" 0, 2,4 ,5
        if($rowCols[0] == 'Total'
            /*|| $rowCols[2] == 'Total'
            || $rowCols[4] == 'Total'
            || $rowCols[5] == 'Total'*/) {
            continue;
        }


        if($rowCols[0] !== '' && $rowCols[1] !== '') {
            $marcheCode = (int)$rowCols[0];
            $marcheLabel = mb_strtolower($rowCols[1]);
            continue;
        }

        if($rowCols[2] !== '' && $rowCols[3] !== '') {
            $jourDate = implode('-', array_reverse(explode('/', $rowCols[3])));
            continue;
        }

        if($rowCols[4] !== '') {
            $jourLibelle = mb_strtolower($rowCols['4']);
            continue;
        }


        $horaires   = str_replace('heures', '', $rowCols[5]);
        $horaires   = array_map('trim', explode('-', $horaires));
        $heureDebut = $horaires[0] . ':00';
        $heureFin   = $horaires[1] . ':00';

        $poidCa         = str_replace(',', '.', trim($rowCols[6], '%'));
        $ticketsNb      = (int)$rowCols[7];
        $panierMoyen    = str_replace(',', '.', $rowCols[8]);
        $qttVendues     = (int)$rowCols[9];

        $sqlQuery = "INSERT INTO `activite_horaire` (`marche_code`, `marche_label`, `jour_date`, `jour_libelle`, `heure_debut`, `heure_fin`, `poid_ca`, `tickets_nb`, `panier_moyen`, `quantites_vendues`) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');";

        $sqlQuery = sprintf($sqlQuery
            , $marcheCode
            , $marcheLabel
            , $jourDate
            , $jourLibelle
            , $heureDebut
            , $heureFin
            , $poidCa
            , $ticketsNb
            , $panierMoyen
            , $qttVendues
        );

        $oDb->query($sqlQuery);

    }
}