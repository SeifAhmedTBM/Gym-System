<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberSuggestion;
use Illuminate\Http\Request;

class MemberSuggestionController extends Controller
{
    public function index()
    {
        $suggestions = MemberSuggestion::with(['member'])->latest()->get();

        return view('admin.member_suggestion.index',compact('suggestions'));
    }
}
