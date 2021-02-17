<?php

namespace App\MiepClient;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;

class MiepClient
{

    const BASE_URL = 'https://ep.max-immo.be/api/';

    protected MiepProvider $provider;
    protected AccessTokenInterface|null $token = null;
    protected string $brokerId = "";

    public function __construct(MiepProvider $provider = null) {
        if (is_null($provider)) { // This allows for easier testing
            $this->provider = new MiepProvider([
                "clientId" =>  env('MIEP_CLIENT_ID'),
                "clientSecret" => env('MIEP_CLIENT_SECRET'),
                "urlAuthorize" => "https://ep.max-immo.be/api/oauth",
                "urlAccessToken" => "https://ep.max-immo.be/api/oauth",
                "urlResourceOwnerDetails" => "https://ep.max-immo.be/api/oauth",
            ]);
        } else {
            $this->provider = $provider;
        }
    }

    public function authorize(): self
    {
        if ($this->token !== null) {
            return $this;
        }

        try {
            $this->token = $this->provider->getAccessToken('client_credentials');
        } catch (IdentityProviderException $e) {
            Log::error($e);
            throw $e;
        }

        return $this;
    }

    protected function get(string $url, array $options = []): ResponseInterface
    {
        if ($this->token === null) {
            throw new UnauthorizedException("No token set, did you call authorize() ?");
        }

        $request = $this->provider->getAuthenticatedRequest('GET', $url, $this->token, $options);
        return $this->provider->getResponse($request);
    }

    protected function getParsed(string $url, array $options = []): mixed
    {
        if ($this->token === null) {
            throw new UnauthorizedException("No token set, did you call authorize() ?");
        }

        $request = $this->provider->getAuthenticatedRequest('GET', $url, $this->token, $options);
        return $this->provider->getParsedResponse($request);
    }

    protected function assertHasBroker(): void
    {
        if ($this->brokerId === "") {
            throw new \BadMethodCallException('No broker id set, did you forget withBroker($id) ?');
        }
    }

    public function withBroker(string $broker): self
    {
        $this->brokerId = $broker;
        return $this;
    }

    public function brokers(): mixed
    {
        return $this->getParsed(self::BASE_URL . "brokers");
    }

    public function broker(): mixed
    {
        $this->assertHasBroker();
        return $this->getParsed(self::BASE_URL . "brokers/" . $this->brokerId);
    }

    public function real_estate(): mixed
    {
        $this->assertHasBroker();
        return $this->getParsed(self::BASE_URL . "brokers/" . $this->brokerId . "/real-estate");
    }

    public function project(string $project_id): mixed
    {
        $this->assertHasBroker();
        return $this->getParsed(self::BASE_URL . "brokers/" . $this->brokerId . "/real-estate/projects/" . $project_id);
    }

    public function property(string $property_id): mixed
    {
        $this->assertHasBroker();
        return $this->getParsed(self::BASE_URL . "brokers/" . $this->brokerId . "/real-estate/properties/" . $property_id);
    }
}
