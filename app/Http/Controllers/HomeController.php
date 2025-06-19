<?php

namespace App\Http\Controllers;

use App\Settings\GeneralSettings;

class HomeController extends Controller
{
    //
    public function __construct()
    {

    }
    public function index()
    {
        $settings = app(GeneralSettings::class);
        return view('home',compact('settings'));
    }
}
