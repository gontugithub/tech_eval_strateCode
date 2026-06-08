<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Traits\TraitApiResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use TraitApiResponse;

    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page  = $request->query('page', 1);   
        $projects = Project::where('user_id', auth('api')->id())
                        ->paginate($limit, ['*'], 'page', $page);

        return $this->successResponse($projects, 'Proyectos obtenidos', 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string'],
            'description' => ['nullable', 'string']
        ]);

        $project = Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'user_id'     => auth('api')->id()
        ]);

        return $this->successResponse($project, 'Proyecto creado', 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => ['sometimes', 'string'],
            'description' => ['sometimes', 'nullable', 'string']
        ]);

        $project = Project::where('id', $id)
                        ->where('user_id', auth('api')->id())
                        ->firstOrFail();

        $project->update($request->only(['name', 'description']));

        return $this->successResponse($project, 'Proyecto actualizado', 200);
    }

    public function destroy($id)
    {
        $project = Project::where('id', $id)
                        ->where('user_id', auth('api')->id())
                        ->firstOrFail();

        $project->delete();

        return $this->successResponse(null, 'Proyecto eliminado', 200);
    }
}