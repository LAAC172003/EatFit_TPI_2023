<?php

namespace Eatfit\Site\Controllers;


use Eatfit\Site\Core\Controller;

class AboutController extends Controller
{
    public function index()
    {
        return $this->render('about');
    }
}