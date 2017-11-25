<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ConcertTest extends TestCase
{
  use DatabaseMigrations;

    /** @test */

    public function can_get_formated_date()
    {
      $concert = factory(\App\Concert::class)->create([
        'date' => \Carbon\Carbon::parse('2016-12-01 8:00pm')
        ]);

      $date = $concert->formatted_date;
      $this->assertEquals('December 1, 2016', $date );
    }
}
