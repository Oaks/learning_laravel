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

    /** @test */

    public function running_hook_before_the_first_charge( ) {
      $paymentGateWay = new \App\Billing\FakePaymentGateWay;
      $callbackRan = false;

      $paymentGateWay->beforeFirstCharge( function ($paymentGateWay) use (&$callbackRan) {
        $callbackRan = true;
        $this->assertEquals(0, $paymentGateWay->totalCharges());
        }
      );
      $paymentGateWay->charge(2500, $paymentGateWay->getValidTestToken());

      $this->assertEquals(true, $callbackRan);
      $this->assertEquals(2500, $paymentGateWay->totalCharges());
    }
}
