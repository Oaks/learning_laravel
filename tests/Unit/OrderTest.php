<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class OrderTest extends TestCase
{
  use DatabaseMigrations;

    /** @test  */

    public function tickets_are_released_when_an_order_is_cancelled() {
      $concert = factory(\App\Concert::class)->create();
      $concert->addTickets(10);

      $order = $concert->orderTickets('jane@example.com', 5);
      $this->assertEquals(5, $concert->ticketsRemaining());
      $order->cancel();
      $this->assertEquals(10, $concert->ticketsRemaining());
      $this->assertNull(\App\Order::find($order->id));

    }
}
