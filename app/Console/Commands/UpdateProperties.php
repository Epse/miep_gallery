<?php

namespace App\Console\Commands;

use App\MiepClient\MiepClient;
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
        $client = new MiepClient();

        $response = $client->authorize()->withBroker(env('BROKER_ID'))->real_estate();
        $properties = collect($response['properties']);

        $properties = $properties->map(function($property) use (&$client) {
            return $client->property($property['id']);
        });

        dd($properties);

        return 0;
    }
}
