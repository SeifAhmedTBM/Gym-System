<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class HelpcenterController extends Controller
{
    public function index()
    {
        $faqCategories = FaqCategory::with('questions')->get();

        return view('admin.help-center.index',compact('faqCategories'));
    }
}
