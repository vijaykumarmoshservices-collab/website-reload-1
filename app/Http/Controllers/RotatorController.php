<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RotatorController extends Controller
{
    public function index()
    {
        $websites = [
            'https://www.wikipedia.org',
            'https://www.mongodb.com',
            'https://laravel.com',
            'https://openai.com',
        ];

        return view('websites', compact('websites'));
    }

    public function viewer(Request $request)
    {
        $url = $request->query('url', '');
        return view('viewer', compact('url'));
    }
}
