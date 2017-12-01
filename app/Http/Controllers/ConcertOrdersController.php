<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Billing\PaymentGateWay;
use App\Concert;

class ConcertOrdersController extends Controller
{
  private $paymentGateWay;

  public function __construct( PaymentGateWay $paymentGateWay) {
    $this->paymentGateWay = $paymentGateWay;
  }

  public function store($concertId) {

    $concert = Concert::find($concertId);
    $ticketQuantity = request('ticket_quantity');
    $amount = $ticketQuantity * $concert->ticket_price;
    $token = request('payment_tcken');
    $this->paymentGateWay->charge($amount, $token);
    return response()->json([], 201);
  }
}
