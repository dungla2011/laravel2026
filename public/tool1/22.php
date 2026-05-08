
<?php


echo "=== MongoDB Driver Test (PHP Driver API) ===\n\n";

try {
    // Use MongoDB\Driver\Manager API
    $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
    echo "âœ“ Connected to MongoDB\n";

    // Test connection with ping command
    $command = new \MongoDB\Driver\Command(["ping" => 1]);
    $result = $manager->executeCommand("admin", $command);
    $resultArray = current($result->toArray());

    if ($resultArray->ok) {
        echo "âœ“ MongoDB is responsive\n";
    }

    // Query databases
    $command = new \MongoDB\Driver\Command(["listDatabases" => 1]);
    $result = $manager->executeCommand("admin", $command);
    $resultArray = current($result->toArray());

    echo "\nAvailable Databases (" . count($resultArray->databases) . "):\n";
    foreach ($resultArray->databases as $db) {
        printf("  - %-20s Size: %s bytes\n", $db->name, number_format($db->sizeOnDisk ?? 0));
    }

    // Insert test data
    echo "\nâœ“ Testing insert...\n";
    $bulk = new \MongoDB\Driver\BulkWrite();
    $id = $bulk->insert(["test" => "hello", "timestamp" => new \MongoDB\BSON\UTCDateTime()]);
    $manager->executeBulkWrite("test_debug.test_collection", $bulk);
    echo "  Document inserted\n";

    // Query test data
    $query = new \MongoDB\Driver\Query([]);
    $cursor = $manager->executeQuery("test_debug.test_collection", $query);
    $documents = $cursor->toArray();
    echo "\nâœ“ Query Results (" . count($documents) . " documents found):\n";
    foreach ($documents as $doc) {
        echo "  Document: " . json_encode($doc, JSON_PRETTY_PRINT) . "\n";
    }

    echo "\nâœ“ All MongoDB tests passed!\n";

} catch (Exception $e) {
    echo "âœ— Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
