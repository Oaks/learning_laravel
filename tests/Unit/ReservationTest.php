<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReservationTest extends TestCase
{
  use DatabaseMigrations;

    /** @test  */

    public function calculating_total_cost()
    {
      $concert = factory(\App\Concert::class)->create(['ticket_price'=> 1200])->addTickets(3);
      $tickets = $concert->findTickets(3);

      $reservation = new \App\Reservation($tickets);
      $this->assertEquals( 3600, $reservation->totalCost());
    }
}
