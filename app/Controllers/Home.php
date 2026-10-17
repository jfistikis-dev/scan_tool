<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data['title'] = "scan-ME :: Κεντρικό Μενου";
        return view('homeScreen', $data);
    }
}
