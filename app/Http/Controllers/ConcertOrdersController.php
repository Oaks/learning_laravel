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
    $this->paymentGateWay->charge(request('ticket_quantity')*$concert->ticket_price,
                                  request('payment_token'));

    $order = $concert->orderTickets(request('email'), request('ticket_quantity'));

    return response()->json([], 201);
  }
}
