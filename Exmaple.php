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





// Wordpress.org Example

require_once __DIR__ . '/vendor/autoload.php';

$license = new LicenseManager(
    'YOUR_KEY', 
    'YOUR_PRODUCT', 
    'YOUR_VARIATION', 
    'http://localhost/licenzo/wp-json/licenzo/v1'
);

// Activate on plugin activation
register_activation_hook(__FILE__, fn() => $license->activate());

// Daily check via cron
if (!wp_next_scheduled('daily_license_check')) {
    wp_schedule_event(time(), 'daily', 'daily_license_check');
}
add_action('daily_license_check', fn() => $license->check());

// Admin notice if license invalid
add_action('admin_notices', fn() => 
    !$license->check() ? print('<div class="notice notice-error"><p>License invalid!</p></div>') : null
);
