<?php

use Faker\Generator as Faker;

$factory->define(App\Concert::class, function (Faker $faker) {
    return [
        'title'    => 'Example Band',
        'subtitle' => 'with The Fake Openers',
        'date'     => \Carbon\Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue'    => 'The Example Theatre',
        'venue_address' => '123 Example Lane',
        'city'     => 'Fakeville',
        'state'    => 'ON',
        'zip'      => '90210',
        'additional_information' => 'Some sample additional information.',
    ];
});

$factory->state(App\Concert::class, 'published', function(Faker $faker) {
  return [
    'published_at' => \Carbon\Carbon::parse('-1 week')
  ];
});

$factory->state(App\Concert::class, 'unpublished', function(Faker $faker) {
  return [
    'published_at' => null
  ];
});
