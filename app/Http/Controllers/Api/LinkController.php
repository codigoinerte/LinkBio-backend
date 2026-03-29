<?php

namespace App\Http\Controllers\Api;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $links = DB::table('links')
            ->select('links.*', 'categories.code as category_code', 'categories.icon as category_icon')
            ->join('categories', 'links.category_id', '=', 'categories.id')
            ->where('user_id', auth()->id())
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        // Link::where('user_id', auth()->id())->get();

        return response()->json($links, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $link = new Link($request->all());
        $link->user_id = auth()->id();
        $link->save();

        $link = DB::table('links')
                ->select('links.*', 'categories.code as category_code', 'categories.icon as category_icon')
                ->join('categories', 'links.category_id', '=', 'categories.id')
                ->where('user_id', auth()->id())
                ->where('links.id', $link->id)
                ->first();

        return response()->json($link, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $link = Link::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$link) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'url' => 'sometimes|url',
            'title' => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $link->update($request->all());

        $link = DB::table('links')
                ->select('links.*', 'categories.code as category_code', 'categories.icon as category_icon')
                ->join('categories', 'links.category_id', '=', 'categories.id')
                ->where('user_id', auth()->id())
                ->where('links.id', $id)
                ->first();

        return response()->json($link, 200);
    }

    public function updateState(Request $request, string $id)
    {        
        $link = Link::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$link) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'is_enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $link->is_enabled = $request->input('is_enabled');
        $link->save();

        return response()->json($link, 200);
    }

    public function updateOrders(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:links,id',
            'items.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        foreach ($request->input('items') as $item) {
            $link = Link::where('id', $item['id'])->where('user_id', auth()->id())->first();
            if ($link) {
                $link->order = $item['order'];
                $link->save();
            }
        }

        return response()->json(['message' => 'Orders updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $link = Link::where('id', $id)->where('user_id', auth()->id())->first();
        if (!$link) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $link->delete();

        return response()->json(['message' => 'Successfully deleted'], 200);
    }
}
