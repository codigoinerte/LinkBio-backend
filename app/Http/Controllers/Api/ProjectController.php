<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\GaleryModelController;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::query()
            ->where('user_id', auth()->id())
            ->with([
                'galery' => function ($query) {
                    $query->select('id', 'origin_id', 'name', 'image_path');
                }
            ])
            ->orderBy('order')
            ->get();

        return response()->json($projects, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->boolean('debug_files')) {
            $uploadedFiles = $request->file('files', []);

            return response()->json([
                'has_files' => $request->hasFile('files'),
                'files_count' => count($uploadedFiles),
                'files' => collect($uploadedFiles)->map(function ($file) {
                    return [
                        'original_name' => $file->getClientOriginalName(),
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'is_valid' => $file->isValid(),
                    ];
                })->values(),
                'name_received' => $request->filled('name'),
            ], 200);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'is_enabled' => 'boolean',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = auth()->id();

        try {
            $project = Project::create(array_merge(
                $request->only('name', 'description', 'short_description', 'link', 'location', 'from', 'to', 'is_enabled'),
                ['user_id' => $userId]
            ));

            if ($request->hasFile('files')) {
                $newGalery = new GaleryModelController();
                $newGalery->procesar('projects', $project->id, $request->file('files'));
            }

            $project = $this->getDetail($project->id);

            return response()->json([
                'ok' => true,
                'message' => 'Project created successfully',
                'project' => $project
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'message' => 'An error occurred while creating the project',
                'error' => $th->getMessage()
            ], 500);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string|max:255',
                'link' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'from' => 'nullable|date',
                'to' => 'nullable|date|after_or_equal:from',
                'is_enabled' => 'boolean',
                'files' => 'nullable|array',
                'files.*' => 'file|mimes:jpg,jpeg,png,webp|max:2048'
            ]);            
    
            if ($validator->fails()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            try {
                $project = Project::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
                $project->update($request->only('name', 'description', 'short_description', 'link', 'location', 'from', 'to', 'is_enabled'));

                if ($request->hasFile('files')) {
                    $newGalery = new GaleryModelController();
                    $newGalery->procesar('projects', $project->id, $request->file('files'));
                }

                $project = $this->getDetail($project->id);
    
                return response()->json([
                    'ok' => true,
                    'message' => 'Project updated successfully',
                    'project' => $project
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'ok' => false,
                    'message' => 'An error occurred while updating the project',
                    'error' => $th->getMessage()
                ], 500);
            }
    }

    public function updateState(Request $request, string $id)
    {        
        $project = Project::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$project) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'is_enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $project->is_enabled = $request->input('is_enabled');
        $project->save();

        $project = $this->getDetail($project->id);

        return response()->json($project, 200);
    }

    public function updateOrders(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:projects,id',
            'items.*.order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        foreach ($request->input('items') as $item) {
            $project = Project::where('id', $item['id'])->where('user_id', auth()->id())->first();
            if ($project) {
                $project->order = $item['order'];
                $project->save();
            }
        }

        return response()->json(['message' => 'Orders updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $newGalery = new GaleryModelController();
            
            $project = Project::findOrFail($id);
            $newGalery->deleteImageBydTableOrigin('projects', $project->id);
            $project->delete();

            return response()->json([
                'ok' => true,
                'message' => 'Project deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'ok' => false,
                'message' => 'An error occurred while deleting the project',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getDetail($id){
        $project = Project::query()
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->with([
                'galery' => function ($query) {
                    $query->select('id', 'origin_id', 'name', 'image_path');
                }
            ])
            ->first();

        return $project;
    }
}
