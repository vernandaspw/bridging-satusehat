<?php

namespace App\Services\SatuSehat;

use Dotenv\Dotenv;

class ConfigSatuSehat
{

    protected $baseUrl;
    protected $authUrl;
    protected $clientId;
    protected $clientSecret;
    protected $organizationId;

    public function __construct()
    {
        $dotenv = Dotenv::createUnsafeImmutable(getcwd());
        $dotenv->safeLoad();
        $this->baseUrl = env('SATU_SEHAT_BASE_URL');
        $this->authUrl = env('SATU_SEHAT_AUTH_URL');
        $this->clientId = env('SATU_SEHAT_CLIENT_ID');
        $this->clientSecret = env('SATU_SEHAT_CLIENT_SECRET');
        $this->organizationId = env('SATU_SEHAT_ORGANIZATION_ID');
    }

    public static function setUrl()
    {
        return env('SATU_SEHAT_BASE_URL');
    }

    public static function setAuthUrl()
    {
        return env('SATU_SEHAT_AUTH_URL');
    }

    public static function setClientId()
    {
        return env('SATU_SEHAT_CLIENT_ID');
    }

    public static function setClientSecret()
    {
        return env('SATU_SEHAT_CLIENT_SECRET');
    }

    public static function setOrganizationId()
    {
        return env('SATU_SEHAT_ORGANIZATION_ID');
    }
}
