<?php

namespace App\Billing;

interface PaymentGateWay {
  public function charge($amount, $token);
}
