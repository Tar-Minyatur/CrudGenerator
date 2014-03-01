<?php
namespace TSHW\CrudGenerator\Analyzer\Database;

use Analog\Analog;
use TSHW\CrudGenerator\Analyzer\ModelAnalyzer;
use TSHW\CrudGenerator\Config;
use TSHW\CrudGenerator\Exception\AnalysisException;
use TSHW\CrudGenerator\Model\EntityAssociationDescription;
use TSHW\CrudGenerator\Model\EntityDescription;
use TSHW\CrudGenerator\Model\EntityFieldDescription;
use TSHW\CrudGenerator\Model\AssociationType;
use TSHW\CrudGenerator\Model\ModelDescription;

class PDOModelAnalyzer implements ModelAnalyzer {

    function extractModel(Config $config) {

        if (!isset($config->pdo_dsn) ||
            !isset($config->pdo_user) ||
            !isset($config->pdo_password)) {

            Analog::error("PDOModelAnalyzer - Necessary configuration settings ('pdo_dns', 'pdo_user', 'pdo_password') are not defined");
            throw new AnalysisException("You need to configure 'pdo_dns', 'pdo_user' and 'pdo_password'!");
        }

        try {
            Analog::debug("PDOModelAnalyzer - Initializing database connection");
            $model = new ModelDescription();
            $db = new \PDO($config->pdo_dsn, $config->pdo_user, $config->user_password);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            $remainingTables = array();
            $entities = $this->findEntities($db, $remainingTables);
            foreach ($entities as $entity) {
                $this->analyzeEntity($db, $entity);
                $model->addEntity($entity);
            }
            $this->detectAssociations($db, $entities, $remainingTables);

            return $model;
        }
        catch (\Exception $ex) {
            Analog::error("PDOModelAnalyzer - Database operation failed. Exception: " . $ex->getMessage());
            throw new AnalysisException("Unable to analyze the database", 0, $ex);
        }

    }

    private function findEntities(\PDO $db, array &$skippedTables) {
        $entities = array();
        $stmt = $db->query("SHOW TABLES");
        while ($line = $stmt->fetch(\PDO::FETCH_NUM)) {
            $tableName = $line[0];

            // skip association tables
            if (strstr($tableName, '_to_')) {
                $skippedTables[] = $tableName;
                continue;
            }

            $entityName = $this->convertTableName($tableName);
            $entities[$entityName] = new EntityDescription($entityName, $tableName);
        }
        Analog::debug("PDOModelAnalyzer - Found " . $stmt->rowCount() . " table(s), skipped " . count($skippedTables) . " of them");
        return $entities;
    }

    private function analyzeEntity(\PDO $db, EntityDescription $entity) {
        $stmt = $db->query("SHOW FIELDS FROM " . $entity->getTableName());
        while ($field = $stmt->fetch()) {
            $desc = new EntityFieldDescription($field->Field, $field->Type, ($field->Null == 'NO'));
            $entity->addField($desc);
            if ($field->Key == 'PRI') {
                $desc->setEditable(false);
                $entity->setPrimaryKey($desc);
            }
        }
        Analog::debug("PDOModelAnalyzer - Recognized " . count($entity->getFields()) . " field(s) for entity '" . $entity->getName() . "'");
    }

    /**
     * @param \PDO $db
     * @param EntityDescription[] $entities
     * @param String[] $remainingTables
     */
    private function detectAssociations(\PDO $db, array $entities, array $remainingTables) {

        // find references in entites
        $fkeys = array();
        foreach ($entities as $entity) {
            $fkey = lcfirst($entity->getName()) . ucfirst($entity->getPrimaryKey()->getName());
            $fkeys[$fkey] = $entity;
        }
        foreach ($entities as $entity) {
            foreach ($entity->getFields() as $field) {
                if (isset($fkeys[$field->getName()])) {
                    $foreignEntity = $fkeys[$field->getName()];
                    $type = AssociationType::ONE_TO_MANY;
                    $foundAssociation = false;
                    foreach ($foreignEntity->getAssociations() as $a) {
                        if ($a->getTargetEntity() == $entity) {
                            $type = AssociationType::ONE_TO_ONE;
                            $a->setType($type);
                            $foundAssociation = true;
                        }
                    }
                    if (!$foundAssociation) {
                        $association = new EntityAssociationDescription(AssociationType::MANY_TO_ONE, $entity, null);
                        $foreignEntity->addAssociation($association);
                    }
                    $association = new EntityAssociationDescription($type, $foreignEntity, $field);
                    $entity->addAssociation($association);
                    Analog::debug("Detected association between '" . $entity->getName() . "' and '" . $foreignEntity->getName() . "'");
                    break;
                }
            }

        }

        // analyze remaining tables
        foreach ($remainingTables as $table) {
            $participants = explode('_to_', $table);
            $entity1 = $this->convertTableName($participants[0]);
            $entity2 = $this->convertTableName($participants[1]);
            if (isset($entities[$entity1]) && isset($entities[$entity2])) {
                $association = new EntityAssociationDescription(AssociationType::MANY_TO_MANY, $entities[$entity1]);
                $association->setMappingTable($table);
                $entities[$entity2]->addAssociation($association);
                $association = new EntityAssociationDescription(AssociationType::MANY_TO_MANY, $entities[$entity2]);
                $association->setMappingTable($table);
                $entities[$entity1]->addAssociation($association);
                Analog::debug("Detected many-to-many association between '" . $entity1 . "' and '" . $entity2 . "'");
            }
        }
    }

    /**
     * @param $tableName String
     * @return String
     */
    private function convertTableName($tableName) {
        $entityName = ucfirst($tableName);
        if (substr($entityName, -1) == "s") {
            $entityName = substr($entityName, 0, -1);
        }
        return $entityName;
    }

}