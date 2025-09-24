<?php
require_once 'License.php';
require_once 'Client.php';

use Licenzo\License;
use Licenzo\Client;

// Create a license instance (hash is null initially)
$license = new License('LICENSE_KEY', 'PRODUCT_NAME', 'VARIATION_ID');

// Create client
$client = new Client('http://localhost/licenzo/wp-json/licenzo/v1');

// Activation
$meta = ['site' => 'example.com', 'device' => 'Windows-PC'];
$hash = $client->activate($license, $meta);
echo "Activation Hash: $hash\n";

// Daily check (using stored hash)
$hash = $client->check($license, $meta);
echo "Check Hash: $hash\n";
