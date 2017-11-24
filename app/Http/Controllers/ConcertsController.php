<?php

use Illuminate\Http\Request;

namespace App\Http\Controllers;

class ConcertsController extends Controller
{
  public function show($id) {
    $concert = \App\Concert::find($id);
    \Log::debug($concert->id);
    return view('concerts.show', ['concert' => $concert]);
  }
}
