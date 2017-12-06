<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

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
      $concert->orderTickets('jane@example.com', 3);

      $order = \App\Order::where('email' , 'jane@example.com')->first(); 
      $this->assertNotNull($order);
      $this->assertEquals($order->tickets()->count(), 3);
    }
}
