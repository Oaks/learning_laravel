<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Billing\PaymentGateWay;
use App\Billing\PaymentFailedException;
use App\Concert;
use App\Exceptions\NotEnoughTicketsException;

class ConcertOrdersController extends Controller
{
  private $paymentGateWay;

  public function __construct( PaymentGateWay $paymentGateWay) {
    $this->paymentGateWay = $paymentGateWay;
  }

  public function store($concertId) {

    $concert = Concert::published()->findOrFail($concertId);

    $this->validate(request(), [
      'email'=>['required', 'email'],
      'ticket_quantity'=>['required', 'integer', 'min:1'],
      'payment_token'=>['required']
    ]);

    try {
      $order = $concert->orderTickets(request('email'), request('ticket_quantity'));
      $this->paymentGateWay->charge(request('ticket_quantity')*$concert->ticket_price,
                                    request('payment_token'));


      return response()->json($order->toArray(), 201);
    }
    catch (PaymentFailedException $e) {
      $order->cancel();
      return response()->json([], 422);
    }
    catch (NotEnoughTicketsException $e) {
      return response()->json([], 422);
    }
  }
}
