<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Http\Requests\UserRequest;
use App\Http\Resources\User as UserResource;

use App\Models\User;

class UserController extends Controller
{
    public $successStatus = 200;


     /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Register"},
     *     summary="Register a new user",
     *     description="Register a new user with email and password",
     *     operationId="register",
     *      @OA\RequestBody(
     *      required=true,   
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
	 *						@OA\Property(
     *                          property="password",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "email":"example@email.com",
	 *                     "password":"abc123"
     *                }
     *             )
     *         )
     *      ),
     * 
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *      
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in registering user"
     *     )
     * )
     */
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email|unique:users',
            'password' => 'required',
            
        ]);

        if ($validator->fails()) { 
             return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input); 
        $success['token'] =  $user->createToken('TanzoApp')-> accessToken; 
        $success['email'] =  $user->email;
        
        return response()->json(['success'=>$success], $this-> successStatus); 
    }


        /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Login"},
     *     summary="Login an existing user",
     *     description="Login an existing user",
     *     operationId="login",
     *      @OA\RequestBody(
     *      required=true,   
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
	 *						@OA\Property(
     *                          property="password",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "email":"example@email.com",
	 *                     "password":"abc123"
     *                }
     *             )
     *         )
     *      ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *      
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in loggin in"
     *     )
     * )
     */
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('TanzoApp')-> accessToken; 
            $success['userId'] = $user->id;

            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }


    /**
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"Profile"},
     *     summary="Get logged in user details",
     *     description="Get logged in user details",
     *     operationId="userDetails",
     *     security={{"passport": {}},},
     *     @OA\Response(
     *         response=200,
     *         description="User details fetched successfully",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in fetching user details"
     *     )
     * )
     */
    public function userDetails() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    }


/**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get all users",
     *     description="Get all users",
     *     operationId="getAllUsers",
     *     security={{"passport": {}},},
     *     @OA\Response(
     *         response=200,
     *         description="All users fetched successfully",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in fetching users"
     *     )
     * )
     */

     public function getAllUsers()
     {
         $users = User::all();
 
         return response()->json([
             'status'=>true,
             'users'=> $users
         ]);
     }


    /**
     * @OA\Get(
     *     path="/api/users/{userId}",
     *     tags={"Users"},
     *     summary="Get a user by ID",
     *     description="Get a user by ID",
     *     security={{"passport": {}},},
     *     @OA\Parameter(
    *       description="ID of User",
    *       in="path",
    *       name="userId",
    *       required=true,
    *       example="1",
    *       @OA\Schema(
    *       type="integer",
    *       format="int64"
    *       )
    *       ),
     *     operationId="getOneUser",
     *     @OA\Response(
     *         response=200,
     *         description="User details fetched successfully",
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
    public function getOneUser($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                'message'=>'User does not exist',
            ], 404);
        }

        return response()->json([
            'status'=>true,
            'message'=>'User loaded succesfully',
            'user'=>new UserResource($user)
        ], 200);
    }




 /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     description="Create a new user",
     *     operationId="createNewUser",
     *     security={{"passport": {}},},
     *      @OA\RequestBody(
     *      required=true,   
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "email":"testing@email.com",
     *                     "password":"testing@123*"
     *                }
     *             )
     *         )
     *      ),
     * 
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in creating user"
     *     )
     * )
     */
    public function createNewUser(UserRequest $request)
    {
        $user = User::create($request->all());
        return response()->json([
            'status'=>true,
            'message'=>'User created succesfully',
            'user'=>$user
        ], 201);
    }


     /**
     * @OA\Put(
     *     path="/api/users/{userId}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     description="Update a user",
     *     operationId="updateUser",
     *     security={{"passport": {}},},
     *      @OA\Parameter(
    *       description="ID of User",
    *       in="path",
    *       name="userId",
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
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "email":"changed_example@email.com",
     *                     "password":"abc123",
     *                }
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *      
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in updating user"
     *     )
     * )
     */
    public function updateUser(UserRequest $request, User $user, $id)
    {
        $users = User::all();
        $user = User::find($id);
        
        if (is_null($user)) {
             return response()->json([
            'error'=>'User does not exist',
            'users'=> $users
        ], 401);
    }
        $user->update($request->all());
        return response()->json([
            'status'=>true,
            'message'=>'User updated succesfully',
            'user'=>$user
        ], 200);
        
    }



        /**
     * @OA\Delete(
     *     path="/api/users/{userId}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     description="Delete a user",
     *     operationId="deleteUser",
     *     security={{"passport": {}},},
     *     @OA\Parameter(
    *       description="ID of User",
    *       in="path",
    *       name="userId",
    *       required=true,
    *       example="1",
    *       @OA\Schema(
    *       type="integer",
    *       format="int64"
    *       )
    *       ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in deleting user"
     *     )
     * )
     */
    public function deleteUser(User $user, $id)
    {
        $user = User::find($id);
        $users = User::all();

        if (is_null($user)) {
             return response()->json([
            'error'=>'User does not exist',
            'users'=> $users
        ], 401);

        }

        $user->delete();
        return response()->json([
            'status'=>true,
            'message'=>'User deleted succesfully',
        ], 200);
    }

}