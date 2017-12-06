<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Billing\FakePaymentGateWay;
use App\Billing\PaymentGateWay;

class PurchaseTicketsTest extends TestCase
{
  use DatabaseMigrations;

    /** @test */

    public function customer_can_purchase_concert_tickets()
    {
      $paymentGateWay = new FakePaymentGateWay;
      $this->app->instance(PaymentGateWay::class, $paymentGateWay);

      $concert = factory(\App\Concert::class)->create([ 'ticket_price' => 3250 ]);

      $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
        'email' => 'john@example.com',
        'ticket_quantity' => 3,
        'payment_token' => $paymentGateWay->getValidTestToken(),
        ]);
      $response->assertStatus(201);

      $this->assertEquals(9750, $paymentGateWay->totalCharges());
      $order = $concert->orders()->where('email', 'john@example.com')->first();
      $this->assertNotNull($order);
      $this->assertEquals(3, $order->tickets()->count());
    }
}
