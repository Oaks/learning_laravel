<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FakePaymentGateWayTest extends TestCase
{
    /** @test */

    public function charges_with_a_valid_payment_token_are_valid()
    {
      $paymentGateWay = new \App\Billing\FakePaymentGateWay;

      $paymentGateWay->charge(2500, $paymentGateWay->getValidTestToken());
      $this->assertEquals(2500, $paymentGateWay->totalCharges());
    }
}
