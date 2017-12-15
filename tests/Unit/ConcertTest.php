<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Exceptions\NotEnoughTicketsException;

class ConcertTest extends TestCase
{
  use DatabaseMigrations;

    /** @test */

    public function can_get_formatted_date()
    {
      $concert = factory(\App\Concert::class)->make([
        'date' => \Carbon\Carbon::parse('2016-12-01 8:00pm')
        ]);

      $date = $concert->formatted_date;
      $this->assertEquals('December 1, 2016', $date );
    }

    /** @test */

    public function can_get_formatted_start_time() {
      $concert = factory(\App\Concert::class)->make([
        'date' => \Carbon\Carbon::parse('2016-12-01 17:00:00')
        ]);

      $this->assertEquals('5:00pm', $concert->formatted_start_time );
    }

    /** @test */

    public function can_get_ticket_price_in_dollars() {
      $concert = factory(\App\Concert::class)->make([
        'ticket_price' => 6750,
        ]);

      $this->assertEquals('67.50', $concert->ticket_price_in_dollars );
    }

    /** @test */

    public function concert_with_a_published_at_date_are_published() {
      $publishedConcertA = factory(\App\Concert::class)->create([
        'published_at' => \Carbon\Carbon::parse('-1 week'),
        ]);
      $publishedConcertB = factory(\App\Concert::class)->create([
        'published_at' => \Carbon\Carbon::parse('-1 week'),
        ]);
      $unpublishedConcert = factory(\App\Concert::class)->create([
        'published_at' => null,
        ]);

      $publishedConcerts = \App\Concert::published()->get();

      $this->assertTrue($publishedConcerts->contains($publishedConcertA));
      $this->assertTrue($publishedConcerts->contains($publishedConcertB));
      $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */

    public function can_order_tickets() {
      $concert = factory(\App\Concert::class)->create();
      $concert->addTickets(3);
      $concert->orderTickets('jane@example.com', 3);

      $order = \App\Order::where('email' , 'jane@example.com')->first(); 
      $this->assertNotNull($order);
      $this->assertEquals($order->tickets()->count(), 3);
    }

    /** @test */

    public function can_add_tickets() {
      $concert = factory(\App\Concert::class)->create();
      $concert->addTickets(50);

      $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */

    public function tickets_remaining_does_not_include_tickets_associated_with_an_order() {
      $concert = factory(\App\Concert::class)->create();
      $concert->addTickets(50);
      $concert->orderTickets('jane@example.com', 30);

      $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */

    public function trying_to_purchase_more_tickets_than_remain_throws_an_exception() {
      $concert = factory(\App\Concert::class)->create();
      $concert->addTickets(10);

      try {
        $concert->orderTickets('jane@example.com', 11);
      }
      catch (NotEnoughTicketsException $e) {
        $order= $concert->orders()->where('email', 'jane@example.com')->first();  
        $this->assertNull($order);
        $this->assertEquals(10, $concert->ticketsRemaining());
        return;

      }
      $this->fail("Order succeeded even though there were not enough tickets remaining");
    }

    /** @test */

    public function cannot_order_tickets_that_have_already_purchase() {
      $concert = factory(\App\Concert::class)->create();
      $concert->addTickets(10);
      $concert->orderTickets('jane@example.com', 8);

      try {
        $concert->orderTickets('john@example.com', 3);
      }
      catch (NotEnoughTicketsException $e) {
        $johnOrder= $concert->orders()->where('email', 'john@example.com')->first();  
        $this->assertNull($johnOrder);
        $this->assertEquals(2, $concert->ticketsRemaining());
        return;

      }
      $this->fail("Order succeeded even though there were not enough tickets remaining");
    }
}
