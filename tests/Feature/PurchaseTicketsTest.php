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

  protected function setUp() {
    parent::setUp();

    $this->paymentGateWay = new FakePaymentGateWay;
    $this->app->instance(PaymentGateWay::class, $this->paymentGateWay);
  }

  private function orderTickets($concert, $params) {
      return $this->json('POST', "/concerts/{$concert->id}/orders", $params);
  }

  private function assertValidationError($response, $field) {
    $response->assertStatus(422);
    $this->assertArrayHasKey('errors', $response->decodeResponseJson());
    $this->assertArrayHasKey($field, $response->decodeResponseJson()['errors']);
  }

    /** @test */

    public function customer_can_purchase_concert_tickets()
    {

      $concert = factory(\App\Concert::class)->create([ 'ticket_price' => 3250 ]);

      $response = $this->orderTickets($concert,  [
        'email' => 'john@example.com',
        'ticket_quantity' => 3,
        'payment_token' => $this->paymentGateWay->getValidTestToken(),
        ]);
      $response->assertStatus(201);

      $this->assertEquals(9750, $this->paymentGateWay->totalCharges());
      $order = $concert->orders()->where('email', 'john@example.com')->first();
      $this->assertNotNull($order);
      $this->assertEquals(3, $order->tickets()->count());
    }

  /** @test */

  public function email_field_required_to_purchase_tickets() {

//      $this->disableExceptionHandling();

      $concert = factory(\App\Concert::class)->create();

      
      $response = $this->orderTickets($concert,  [
        'ticket_quantity' => 3,
        'payment_token' => $this->paymentGateWay->getValidTestToken(),
        ]);

      $this->assertValidationError($response, 'email');
  }

  /** @test */

  public function email_must_be_valid_to_purchase_tickets() {

//      $this->disableExceptionHandling();

      $concert = factory(\App\Concert::class)->create();

      $response = $this->orderTickets($concert,  [
        'email'=> 'not-email-address',
        'ticket_quantity' => 3,
        'payment_token' => $this->paymentGateWay->getValidTestToken(),
        ]);

      $this->assertValidationError($response, 'email');
  }

  /** @test */

  public function ticket_quantity_is_required_to_purchase_tickets() {

//      $this->disableExceptionHandling();

      $concert = factory(\App\Concert::class)->create();

      $response = $this->orderTickets($concert,  [
        'email'=> 'john@example.com',
        'payment_token' => $this->paymentGateWay->getValidTestToken(),
        ]);

      $this->assertValidationError($response, 'ticket_quantity');
  }

  /** @test */

  public function ticket_quantity_must_be_at_least_1_to_purchase_tickets() {

//      $this->disableExceptionHandling();

      $concert = factory(\App\Concert::class)->create();

      $response = $this->orderTickets($concert,  [
        'email'=> 'john@example.com',
        'ticket_quantity'=> 0,
        'payment_token' => $this->paymentGateWay->getValidTestToken(),
        ]);

      $this->assertValidationError($response, 'ticket_quantity');
  }

  /** @test */

  public function payment_token_is_required_to_purchase_tickets() {

      $concert = factory(\App\Concert::class)->create();

      $response = $this->orderTickets($concert,  [
        'email'=> 'john@example.com',
        'ticket_quantity'=> 3,
        ]);

      $this->assertValidationError($response, 'payment_token');
  }

  /** @test */

  public function an_order_is_not_created_if_payment_fails() {
      $concert = factory(\App\Concert::class)->create();

      $response = $this->orderTickets($concert,  [
        'email'=> 'john@example.com',
        'ticket_quantity'=> 3,
        'payment_token'=> 'invalid-payment_tocken'
        ]);

      $response->assertStatus(422);

      $order = $concert->orders()->where('email', 'john@example.com')->first();
      $this->assertNull($order);
  }
}
