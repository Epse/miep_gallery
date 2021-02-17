<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use App\MiepClient\MiepProvider;

class UpdateProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $provider = new MiepProvider([
            "clientId" =>  "viewmedia_248b3rbsdzhckw8c8kk40s4wgo08ckwcw0oco040w4wow8cks4",
            "clientSecret" => "vyqjpgl7m74gg4og408sk4ggw8so4ooo0o4k8cc8so0c0o4os",
            "urlAuthorize" => "https://ep.max-immo.be/api/oauth",
            "urlAccessToken" => "https://ep.max-immo.be/api/oauth",
            "urlResourceOwnerDetails" => "https://ep.max-immo.be/api/oauth",
        ]);

        try {
            $accessToken = $provider->getAccessToken('client_credentials');
        } catch (IdentityProviderException $e) {
            $this->error($e->getMessage());
            return -1;
        }

        $berno_id = 2232;
        $request = $provider->getAuthenticatedRequest('GET', 'https://ep.max-immo.be/api/brokers/2232/real-estate/properties/2292313', $accessToken);
        $response = $provider->getParsedResponse($request);

        return 0;
    }
}
