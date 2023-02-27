<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
/**
     * @OA\Get(
     *     path="/api/projects",
     *     tags={"Projects"},
     *     summary="Get all projects of the logged in user",
     *     description="Get all projects of the logged in user",
     *     operationId="getAllProjects",
     *     security={{"passport": {}},},
     *     @OA\Response(
     *         response=200,
     *         description="All projects fetched successfully",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in fetching projects"
     *     )
     * )
     */
    public function getAllProjects()
    {

        $user = Auth::user(); 
        $userId = $user['id'];

        $projects = Project::where('user_id', $userId)->get();

        if (count($projects) == 0) {
            return  response()->json([
                'message'=>'No projects for the logged in user',
            ], 404);
        }

        else{

            return response()->json([
                'status'=>true,
                'user_id'=>$userId,
                'projects'=> $projects
            ]);
         }
    }


 /**
     * @OA\Post(
     *     path="/api/projects",
     *     tags={"Projects"},
     *     summary="Create a new project",
     *     description="Create a new project",
     *     operationId="createProject",
     *     security={{"passport": {}},},
     *      @OA\RequestBody(
     *      required=true,   
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="user_id",
     *                          type="int64"
     *                      ),
     *                 ),
     *                 example={
     *                     "name":"Project name",
     *                     "description":"Project description",
     *                     "user_id":1
     *                }
     *             )
     *         )
     *      ),
     * 
     *     @OA\Response(
     *         response=201,
     *         description="Project created successfully",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in creating project"
     *     )
     * )
     */
    public function createProject(Request $request)
    {

        $validator = Validator::make($request->all(), [ 
            'name'=>'required|max:50',
            'description'=>'required',
            'user_id'=>'required'
            
        ]);

        if ($validator->fails()) { 
             return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();

        $project = Project::create($input); 

        $project = Project::create($request->all());
        return response()->json([
            'status'=>true,
            'message'=>'Project created succesfully',
            'project'=>$project
        ], 201);
    }

/**
     * @OA\Get(
     *     path="/api/projects/{projectId}",
     *     tags={"Projects"},
     *     summary="Get a project by ID",
     *     description="Get a project by ID",
     *     security={{"passport": {}},},
     *     @OA\Parameter(
    *       description="ID of Project",
    *       in="path",
    *       name="projectId",
    *       required=true,
    *       example="1",
    *       @OA\Schema(
    *       type="integer",
    *       format="int64"
    *       )
    *       ),
     *     operationId="getOneProject",
     *     @OA\Response(
     *         response=200,
     *         description="All projects details fetched successfully",
     *               @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in fetching user details"
     *     )
     * )
     */
    public function getOneProject($id)
    {
        $project = Project::find($id);
        if (is_null($project)) {
            return  response()->json([
                'message'=>'Project does not exist',
            ], 404);
        }

        $user = Auth::user(); 
        $userId = $user['id'];

        if ($project['user_id'] == $userId) {

            return response()->json([
                'status'=>true,
                'message'=>'Project loaded succesfully',
                'project'=> $project
            ], 200);

        }

        else{
            return  response()->json([
                'message'=>'The project does not belong to the logged in user',
            ], 404);
        }

    }


 /**
     * @OA\Put(
     *     path="/api/projects/{projectId}",
     *     tags={"Projects"},
     *     summary="Update a project",
     *     description="Update a project",
     *     operationId="updateProject",
     *     security={{"passport": {}},},
     *      @OA\Parameter(
    *       description="ID of Project",
    *       in="path",
    *       name="projectId",
    *       required=true,
    *       example="1",
    *       @OA\Schema(
    *       type="integer",
    *       format="int64"
    *       )
    *       ),
     *      @OA\RequestBody(
     *      required=true,   
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="user_id",
     *                          type="int64"
     *                      ),
     *                 ),
     *                 example={
     *                     "name":"New Project name",
     *                     "description":"New Project description",
     *                     "user_id":1
     *                }
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Project updated successfully",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *      
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in updating project"
     *     )
     * )
     */
    public function updateProject(Request $request, Project $project, $id)
    {
        $validator = Validator::make($request->all(), [ 
            'name'=>'required|max:50',
            'description'=>'required',
            'user_id'=>'required'
            
        ]);

        if ($validator->fails()) { 
             return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();


        $projects = Project::all();
        $project = Project::find($id);
        
        if (is_null($project)) {
             return response()->json([
            'error'=>'Project does not exist',
            'projects'=> $projects
        ]);
    }
        $project->update($request->all());
        return response()->json([
            'status'=>true,
            'message'=>'Project updated succesfully',
            'project'=>$project
        ], 200);
        
    }


    /**
     * @OA\Delete(
     *     path="/api/projects/{projectId}",
     *     tags={"Projects"},
     *     summary="Delete a project",
     *     description="Delete a project",
     *     operationId="deleteProject",
     *     security={{"passport": {}},},
     *     @OA\Parameter(
    *       description="ID of Project",
    *       in="path",
    *       name="projectId",
    *       required=true,
    *       example="1",
    *       @OA\Schema(
    *       type="integer",
    *       format="int64"
    *       )
    *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Project deleted successfully",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in deleting project"
     *     )
     * )
     */
    public function deleteProject(Project $project, $id)
    {
        $project = Project::find($id);
        $projects = Project::all();

        if (is_null($project)) {
             return response()->json([
            'error'=>'Project does not exist',
            'projects'=> $projects
        ]);

        }

        $project->delete();
        return response()->json([
            'status'=>true,
            'message'=>'Project deleted succesfully',
        ], 200);
    }
}
