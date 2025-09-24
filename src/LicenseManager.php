<?php
namespace Licenzo;

use Licenzo\License;
use Licenzo\Client;

class LicenseManager {
    private $license;
    private $client;
    private $option;

    public function __construct($key, $product, $variation, $api, $option = 'license_hash') {
        $this->license = new License($key, $product, $variation);
        $this->client = new Client($api);
        $this->option = $option;
    }

    public function activate() {
        $hash = $this->client->activate($this->license, ['site'=>get_site_url(), 'device'=>php_uname('n')]);
        update_option($this->option, $hash);
        return $hash;
    }

    public function check() {
        $hash = get_option($this->option);
        if (!$hash) return false;
        $this->license->setHash($hash);
        $hash = $this->client->check($this->license, ['site'=>get_site_url(), 'device'=>php_uname('n')]);
        update_option($this->option, $hash);
        return $hash;
    }
}
