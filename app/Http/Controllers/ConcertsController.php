<?php

use Illuminate\Http\Request;

namespace App\Http\Controllers;

class ConcertsController extends Controller
{
  public function show($id) {
    $concert = \App\Concert::published()->findOrFail($id);
    return view('concerts.show', ['concert' => $concert]);
  }
}
