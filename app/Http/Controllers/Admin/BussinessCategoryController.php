<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BussinessCategory;
use Illuminate\Http\Request;

class BussinessCategoryController extends Controller
{
    private $page = "Category";

    public function index() {
        $action = "list";
        $categories = BussinessCategory::where('estatus', 1)->get();
        return view('admin.categories.list', compact('action', 'categories'))->with('page', $this->page);
    }
}
