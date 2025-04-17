<?php
// Inclure la connexion à la base de données si elle n'est pas déjà disponible
if (!isset($db)) {
    require_once __DIR__ . '/../includes/dbConnect.php';
}

function sanitizeData($value) {
    if (is_string($value)) {
        return str_replace("'", "''", $value); // Échapper les apostrophes pour SQL
    } elseif (is_null($value)) {
        return 'NULL';
    } elseif (is_bool($value)) {
        return $value ? 1 : 0;
    } elseif (is_array($value)) {
        // Convertir les tableaux en JSON
        return str_replace("'", "''", json_encode($value, JSON_UNESCAPED_UNICODE));
    } else {
        return $value;
    }
}

/**
 * Importe des données JSON dans une table de la base de données
 * 
 * @param string $jsonFile Chemin vers le fichier JSON
 * @param string $tableName Nom de la table cible
 * @param array $mappings Correspondances entre les clés JSON et les colonnes de la table
 * @return array Résultat de l'importation
 */
function importJsonToTable($jsonFile, $tableName, $mappings = []) {
    global $db; // Utiliser la connexion globale à la base de données
    
    if (!file_exists($jsonFile)) {
        return [
            'success' => false,
            'message' => "Le fichier JSON '$jsonFile' n'existe pas."
        ];
    }
    
    // Charger le contenu JSON
    $jsonContent = file_get_contents($jsonFile);
    $data = json_decode($jsonContent, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'message' => "Erreur de décodage JSON: " . json_last_error_msg()
        ];
    }
    
    if (empty($data)) {
        return [
            'success' => false,
            'message' => "Aucune donnée trouvée dans le fichier JSON."
        ];
    }
    
    // Récupérer les colonnes existantes dans la table
    $columnsQuery = $db->query("SHOW COLUMNS FROM $tableName");
    $existingColumns = [];
    while ($column = $columnsQuery->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $column['Field'];
    }
    
    // Commencer une transaction
    $db->beginTransaction();
    
    try {
        $insertedCount = 0;
        $skippedColumns = [];
        
        foreach ($data as $item) {
            // Préparer les colonnes et valeurs pour l'insertion
            $columns = [];
            $values = [];
            
            foreach ($item as $key => $value) {
                // Si un mapping est défini pour cette clé, utiliser le nom de colonne mappé
                $columnName = isset($mappings[$key]) ? $mappings[$key] : $key;
                
                // Vérifier si la colonne existe dans la table
                if (!in_array($columnName, $existingColumns)) {
                    if (!in_array($columnName, $skippedColumns)) {
                        $skippedColumns[] = $columnName;
                    }
                    continue; // Ignorer cette colonne si elle n'existe pas
                }
                
                $columns[] = $columnName;
                
                // Formater la valeur pour SQL
                if (is_string($value)) {
                    $values[] = "'" . str_replace("'", "''", $value) . "'";
                } elseif (is_null($value)) {
                    $values[] = "NULL";
                } elseif (is_array($value)) {
                    // Convertir les tableaux en chaînes JSON
                    $values[] = "'" . str_replace("'", "''", json_encode($value, JSON_UNESCAPED_UNICODE)) . "'";
                } else {
                    $values[] = $value;
                }
            }
            
            // S'assurer qu'il y a des colonnes et des valeurs à insérer
            if (empty($columns)) {
                continue;
            }
            
            // Construire la requête SQL
            $sql = "INSERT INTO $tableName (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";
            
            // Exécuter la requête
            $db->exec($sql);
            $insertedCount++;
        }
        
        // Valider la transaction
        $db->commit();
        
        $message = "$insertedCount enregistrements insérés dans la table '$tableName'.";
        if (!empty($skippedColumns)) {
            $message .= " Colonnes ignorées: " . implode(", ", $skippedColumns);
        }
        
        return [
            'success' => true,
            'message' => $message
        ];
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        $db->rollBack();
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
