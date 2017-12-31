<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    /** @test  */

    public function calculating_total_cost()
    {
      $tickets = collect([
        (object) ['price' => 1200],
        (object) ['price' => 1200],
        (object) ['price' => 1200],
      ]);

      $reservation = new \App\Reservation($tickets);
      $this->assertEquals( 3600, $reservation->totalCost());
    }
}
