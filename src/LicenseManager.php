<?php

namespace Licenzo;

use Licenzo\License;
use Licenzo\Client;

class LicenseManager
{
    private $license;
    private $client;
    private $option;
    private $response = [];
    private array $meta = [];

    public function __construct($key, $product, $variation, $api, $option = 'license_hash')
    {
        $this->license = new License($key, $product, $variation);
        $this->client = new Client($api);
        $this->option = $option;
    }

    /**
     * Set custom activation meta
     */
    public function setMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    public function activate()
    {
        $this->response = $this->client->activate($this->license,  $this->getMeta());
        update_option($this->option, $this->response['hash']);
        return $this->response;
    }

    public function check()
    {
        $hash = get_option($this->option);
        if (!$hash) return false;
        $this->license->setHash($hash);
        $this->response = $this->client->check($this->license, $this->getMeta());
        update_option($this->option, $this->response['hash']);
        return $this->response['hash'];
    }

    /**
     * Deactivate license
     */
    public function deactivate(): bool
    {
        $hash = get_option($this->option);
        if (!$hash) return false;

        $this->license->setHash($hash);
        $this->response = $this->client->deactivate($this->license);

        if (!empty($this->response['success'])) {
            delete_option($this->option);
            $this->license->setHash(null);
            return true;
        }

        return false;
    }

    /**
     * Is the license a trial?
     */
    public function istrail(): bool
    {
        return !empty($this->response['trial']);
    }

    /**
     * Has the support period ended?
     */
    public function issupportEnded(): bool
    {
        return !empty($this->response['support_ended']);
    }

    /**
     * Is the license active?
     */
    public function isactive(): bool
    {
        return !empty($this->response['active']);
    }
    /**
     * Get meta to use: custom if set, otherwise default
     */
    private function getMeta(): array
    {
        return !empty($this->meta) ? $this->meta : $this->defaultMeta();
    }

    /**
     * Default meta values
     */
    private function defaultMeta(): array
    {
        return [
            'site'   => get_site_url(),
            'domain' => $_SERVER['SERVER_NAME'] ?? '',
            'device' => php_uname('n'),
        ];
    }
}
