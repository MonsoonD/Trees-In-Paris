<?php
require_once __DIR__ . '/../../includes/dbConnect.php';

function createMissingArrondissements($db, $jsonFile, $mappings) {
    if (!file_exists($jsonFile)) {
        echo "ERREUR: Le fichier JSON '$jsonFile' n'existe pas.\n";
        return false;
    }

    $jsonContent = file_get_contents($jsonFile);
    $data = json_decode($jsonContent, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "ERREUR: Erreur de décodage JSON: " . json_last_error_msg() . "\n";
        return false;
    }
    $arrondKey = array_search('arrondissement_id', $mappings) ?: 'arrond';
    $arrondissements = [];
    
    foreach ($data as $item) {
        if (isset($item[$arrondKey]) && !empty($item[$arrondKey])) {
            $arrondissements[] = $item[$arrondKey];
        }
    }
    
    $arrondissements = array_unique($arrondissements);
    
    if (empty($arrondissements)) {
        echo "AVERTISSEMENT: Aucun arrondissement trouvé dans le fichier JSON.\n";
        return false;
    }
    
    echo "Arrondissements trouvés: " . implode(", ", $arrondissements) . "\n";
    
    $columnsQuery = $db->query("SHOW COLUMNS FROM arrondissements");
    $existingColumns = [];
    while ($column = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $column['Field'];
    }
    
    echo "Colonnes disponibles dans la table arrondissements: " . implode(", ", $existingColumns) . "\n";
    
    $insertedCount = 0;
    
    foreach ($arrondissements as $arrond) {
        $stmt = $db->prepare("SELECT id FROM arrondissements WHERE id = ? OR name = ?");
        $stmt->execute([$arrond, "Arrondissement $arrond"]);
        
        if ($stmt->rowCount() == 0) {
            $columns = ['id'];
            $values = [$arrond];
            $placeholders = ['?'];
            
            if (in_array('name', $existingColumns)) {
                $columns[] = 'name';
                $values[] = "Arrondissement $arrond";
                $placeholders[] = '?';
            }
            
            if (in_array('code', $existingColumns)) {
                $columns[] = 'code';
                $values[] = $arrond;
                $placeholders[] = '?';
            }
            
            $sql = "INSERT INTO arrondissements (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            $insertedCount++;
        }
    }
    
    echo "SUCCESS: $insertedCount arrondissements insérés dans la table 'arrondissements'.\n";
    return true;
}
$imports = [
    [
        'file' => 'admin/database/arbres-plantes-par-projet.json',
        'table' => 'planting_projects',
        'mappings' => [
            'arrond' => 'arrondissement_id',
            'date_deb_t' => 'date_debut', 
            'date_fin_t' => 'date_fin',
            'ope_statut' => 'statut',
            'ope_type' => 'type_operation',
            'ind_arb_plant' => 'nombre_arbres_plantes',
            'st_area_shape' => 'surface_m2',
            'st_perimeter_shape' => 'perimetre_m',
        ]
    ],
];

function sanitizeData($value) {
    if (is_string($value)) {
        return str_replace("'", "''", $value); 
    } elseif (is_null($value)) {
        return 'NULL';
    } elseif (is_bool($value)) {
        return $value ? 1 : 0;
    } elseif (is_array($value)) {
        return str_replace("'", "''", json_encode($value, JSON_UNESCAPED_UNICODE));
    } else {
        return $value;
    }
}

function importJsonToTable($db, $jsonFile, $tableName, $mappings = []) {
    if (!file_exists($jsonFile)) {
        echo "ERREUR: Le fichier JSON '$jsonFile' n'existe pas.\n";
        return false;
    }

    $jsonContent = file_get_contents($jsonFile);
    $data = json_decode($jsonContent, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "ERREUR: Erreur de décodage JSON: " . json_last_error_msg() . "\n";
        return false;
    }
    
    if (empty($data)) {
        echo "ERREUR: Aucune donnée trouvée dans le fichier JSON.\n";
        return false;
    }

    $columnsQuery = $db->query("SHOW COLUMNS FROM $tableName");
    $existingColumns = [];
    while ($column = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $column['Field'];
    }
    
    echo "Colonnes disponibles dans la table: " . implode(", ", $existingColumns) . "\n";

    $db->beginTransaction();
    
    try {
        $insertedCount = 0;
        $skippedColumns = [];
        
        foreach ($data as $item) {
            
            $columns = [];
            $values = [];
            
            $usedColumns = []; 
            
            foreach ($item as $key => $value) {
                $columnName = isset($mappings[$key]) ? $mappings[$key] : $key;
                
                if (!in_array($columnName, $existingColumns)) {
                    if (!in_array($columnName, $skippedColumns)) {
                        echo "AVERTISSEMENT: La colonne '$columnName' n'existe pas dans la table et sera ignorée.\n";
                        $skippedColumns[] = $columnName;
                    }
                    continue;
                }
                
                if (in_array($columnName, $usedColumns)) {
                    echo "AVERTISSEMENT: La colonne '$columnName' est déjà spécifiée et sera ignorée pour la clé '$key'.\n";
                    continue;
                }
                
                $usedColumns[] = $columnName; 
                $columns[] = $columnName;

                if (is_string($value)) {
                    $values[] = "'" . sanitizeData($value) . "'";
                } elseif (is_null($value)) {
                    $values[] = "NULL";
                } elseif (is_array($value)) {
                    $values[] = "'" . sanitizeData($value) . "'";
                } else {
                    $values[] = sanitizeData($value);
                }
            }

            if (empty($columns)) {
                echo "AVERTISSEMENT: Aucune colonne valide trouvée pour l'insertion.\n";
                continue;
            }
            
            $sql = "INSERT INTO $tableName (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";

            $db->exec($sql);
            $insertedCount++;
        }

        $db->commit();
        
        echo "SUCCESS: $insertedCount enregistrements insérés dans la table '$tableName'.\n";
        return true;
    } catch (PDOException $e) {
        $db->rollBack();
        echo "ERREUR: " . $e->getMessage() . "\n";
        return false;
    }
}

echo "Début de l'importation des données...\n";

foreach ($imports as $import) {
    if ($import['table'] == 'planting_projects') {
        echo "Création des arrondissements manquants à partir de {$import['file']}...\n";
        createMissingArrondissements($db, $import['file'], $import['mappings']);
        echo "---------------------------------------------\n";
    }
}

foreach ($imports as $import) {
    echo "Importation de {$import['file']} vers la table {$import['table']}...\n";
    $result = importJsonToTable($db, $import['file'], $import['table'], $import['mappings']);
    
    if ($result) {
        echo "Importation réussie pour {$import['table']}.\n";
    } else {
        echo "Échec de l'importation pour {$import['table']}.\n";
    }
    
    echo "---------------------------------------------\n";
}

echo "Processus d'importation terminé.\n";
