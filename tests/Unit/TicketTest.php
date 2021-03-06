<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TicketTest extends TestCase
{
  use DatabaseMigrations;
    /** @test */

    public function a_ticket_can_be_released()
    {
      $concert = factory(\App\Concert::class)->create();
      $concert->addTickets(1);
      $order = $concert->orderTickets('jane@example.com', 1);
      $ticket = $order->tickets()->first();
      $this->assertEquals($order->id, $ticket->order_id);
      $ticket->release();

      $this->assertNull($ticket->fresh()->order_id);
    }
}
