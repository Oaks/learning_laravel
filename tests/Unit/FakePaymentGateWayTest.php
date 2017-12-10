<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Billing\PaymentFailedException;

class FakePaymentGateWayTest extends TestCase
{
    /** @test */

    public function charges_with_a_valid_payment_token_are_valid()
    {
      $paymentGateWay = new \App\Billing\FakePaymentGateWay;

      $paymentGateWay->charge(2500, $paymentGateWay->getValidTestToken());
      $this->assertEquals(2500, $paymentGateWay->totalCharges());
    }
    
    /** @test */

    public function charges_with_invalid_payment_token_fail() {
      try {
        $paymentGateWay = new \App\Billing\FakePaymentGateWay;
        $paymentGateWay->charge(2500, 'invalid-payment-token');
      }
      catch (PaymentFailedException $e) {
        $this->assertEquals(0, 0);
        return;
      }
      $this->fail();
    }

}
