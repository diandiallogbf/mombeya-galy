<?php

namespace App\Http\Controllers;

use App\Models\Showroom;
use Illuminate\View\View;

class ShowroomController extends Controller
{
    public function index(): View
    {
        return view('showrooms', ['showrooms' => Showroom::active()->get()]);
    }
}
