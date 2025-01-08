<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends ApiController
{

    protected $policyClass = UserPolicy::class;

    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(
            User::filter($filters)->paginate()
        );
    }




    public function store(StoreUserRequest $request)
    {
        try {

            $this->isAble('store', User::class);

            return new UserResource(User::create($request->mappedAttribute()));

        }catch (AuthorizationException $ex){

            return $this->error('You are not authorized to update this resource', 401);

        }
    }


    public function show(User $user)
    {
        if ($this->include('tickets')){
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }




    public function update(UpdateUserRequest $request, $user_id)
    {
        try {

            $user = User::findOrFail($user_id);

            //policy


            $this->isAble('update', $user);

            $user->update($request->mappedAttribute());

            return new UserResource($user);

        } catch (ModelNotFoundException){

            return $this->error('User cannot be found.', 404);

        }catch (AuthorizationException $ex){

            return $this->error('You are not authorized to update this resource', 401);

        }

    }


    public function replace(ReplaceUserRequest $request, $user_id)
    {


        try {

            $user = User::findOrFail($user_id);

            $this->isAble('replace', $user);

            $user->update($request->mappedAttribute());

            return new userResource($user);

        } catch (ModelNotFoundException){

            return $this->error('User cannot be found.', 404);

        }

    }

    public function destroy($user_id)
    {
        try {

            $user = User::findOrFail($user_id);

            $this->isAble('delete', $user);

            $user->delete();

            return $this->ok('User deleted successfully');

        } catch (ModelNotFoundException $exception){

            return $this->error('User cannot be found.', 404);
        }
    }
}
