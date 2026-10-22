<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MenuController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $branch = $request->user()->branch;

        $items = $branch->menuItems()
            ->available()
            ->with('category')
            ->orderBy('sort_order')
            ->get();

        return MenuItemResource::collection($items);
    }
}
