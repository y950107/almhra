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
        if (request()->getHost() == 'quran.almhrah.com') {
           return view('maqraa_home',compact('settings'));
        } 
        return view('home',compact('settings'));
    }
}
