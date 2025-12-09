<?php

namespace App\Http\Controllers;


abstract class Controller
{
    public $activeTemplate;

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

}
