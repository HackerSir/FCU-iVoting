<?php

namespace App\Http\Controllers;

class PolicyController extends Controller
{
    protected static $policiesTabs = ['privacy', 'terms', 'FAQ'];

    public function index($tab = null)
    {
        if (!in_array($tab, static::$policiesTabs)) {
            return redirect()->route('policies', static::$policiesTabs[0]);
        }

        return response()->view('policies');
    }
}
