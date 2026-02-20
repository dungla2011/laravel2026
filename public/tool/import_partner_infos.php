<?php
/**
 * Import Partner Infos from Invoice JSON file
 * Extract unique tax_code, partner_name, address, email
 * Insert into partner_infos table
 */

require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if(!isAdminLrv_()){
    die("You do not have permission to run this script.");
}

use App\Models\PartnerInfo;
use Illuminate\Support\Facades\DB;

// Configuration
$jsonFile = __DIR__ . '/invoices_2years.json';

if (!file_exists($jsonFile)) {
    die("JSON file not found: $jsonFile\n");
}

// Read JSON file
$jsonContent = file_get_contents($jsonFile);
$invoices = json_decode($jsonContent, true);

if (!$invoices) {
    die("Failed to decode JSON file\n");
}

echo "Total invoices: " . count($invoices) . "\n";

// Extract unique partners by tax_code and email
$partners = [];
$duplicates = 0;
$emailDuplicates = 0;

foreach ($invoices as $invoice) {
    if (!isset($invoice['Invoice'])) {
        continue;
    }

    $inv = $invoice['Invoice'];

    // Extract fields
    $taxCode = trim($inv['BuyerTaxCode'] ?? '');
    $partnerName = trim($inv['BuyerUnitName'] ?? '');
    $address = trim($inv['BuyerAddress'] ?? '');
    $email = trim($invoice['ReceiverEmail'] ?? '');

    // Skip if no tax code
    if (empty($taxCode)) {
        continue;
    }

    // Check if tax_code already exists
    if (isset($partners[$taxCode])) {
        $duplicates++;
        continue;
    }

    // Check if email already exists in array (duplicate email)
    $emailExists = false;
    foreach ($partners as $p) {
        if (!empty($email) && $p['email'] === $email) {
            $emailExists = true;
            $emailDuplicates++;
            break;
        }
    }

    if ($emailExists && !empty($email)) {
        continue;
    }

    // Store partner data
    $partners[$taxCode] = [
        'tax_code' => $taxCode,
        'partner_name' => $partnerName,
        'address' => $address,
        'email' => $email,
    ];
}

echo "\nUnique partners found: " . count($partners) . "\n";
echo "Duplicate tax codes skipped: " . $duplicates . "\n";
echo "Duplicate emails skipped: " . $emailDuplicates . "\n";

// Insert into database
$inserted = 0;
$updated = 0;
$errors = 0;

echo "\n--- Importing Partners ---\n";

foreach ($partners as $taxCode => $data) {
    try {
        // Check if partner already exists by tax_code or email
        $existing = PartnerInfo::where('tax_code', $taxCode);
        
        // Also check by email if email is not empty
        if (!empty($data['email'])) {
            $existing = $existing->orWhere('email', $data['email']);
        }
        
        $existing = $existing->first();

        if ($existing) {
            // Update existing
            $existing->update([
                'partner_name' => $data['partner_name'],
                'address' => $data['address'],
                'email' => $data['email'],
            ]);
            $updated++;
            echo "[UPDATE] Tax Code: $taxCode | Email: " . ($data['email'] ?? 'N/A') . " | Partner: " . $data['partner_name'] . "\n";
        } else {
            // Create new
            PartnerInfo::create([
                'tax_code' => $data['tax_code'],
                'partner_name' => $data['partner_name'],
                'address' => $data['address'],
                'email' => $data['email'] ?: null,
            ]);
            $inserted++;
            echo "[INSERT] Tax Code: $taxCode | Email: " . ($data['email'] ?? 'N/A') . " | Partner: " . $data['partner_name'] . "\n";
        }
    } catch (\Exception $e) {
        $errors++;
        echo "[ERROR] Tax Code: $taxCode | Email: " . ($data['email'] ?? 'N/A') . " | Error: " . $e->getMessage() . "\n";
    }
}

echo "\n--- Summary ---\n";
echo "Total inserted: $inserted\n";
echo "Total updated: $updated\n";
echo "Total errors: $errors\n";
echo "Done!\n";
