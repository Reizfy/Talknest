<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nest;

class NestController extends Controller
{
    public function index($name)
    {
        $nest = Nest::with(['owner', 'users', 'posts'])->where('name', $name)->firstOrFail();
        return view('nests.nests', compact('nest'));
    }

}
