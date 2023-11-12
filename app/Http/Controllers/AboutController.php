<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AboutController extends Controller
{

    public function __invoke(Request $request)
    {
        return response()->json(['message' => 'Want to know more about this project? Clicke here!'], Response::HTTP_OK);
    }
}
