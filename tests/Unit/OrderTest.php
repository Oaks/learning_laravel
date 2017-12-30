<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class OrderTest extends TestCase
{
  use DatabaseMigrations;

    /** @test  */

  public function creating_an_order_from_tickets_mail_and_amount() {
    $concert = factory(\App\Concert::class)->create()->addTickets(5);
    $this->assertEquals(5, $concert->ticketsRemaining());

    $order = \App\Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

    $this->assertEquals('john@example.com', $order->email);
    $this->assertEquals(3, $order->ticketQuantity());
    $this->assertEquals(2, $concert->ticketsRemaining());
    $this->assertEquals(3600, $order->amount );
  }

    /** @test  */

    public function converting_to_an_array() {
      $concert = factory(\App\Concert::class)->create(['ticket_price' => 1200]);
      $concert->addTickets(5);
      $order = $concert->orderTickets('jane@example.com', 5);

      $result = $order->toArray();

      $this->assertEquals([
        'email' => 'jane@example.com',
        'ticket_quantity' =>5,
        'amount' => 6000
      ], $result);
    }

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
