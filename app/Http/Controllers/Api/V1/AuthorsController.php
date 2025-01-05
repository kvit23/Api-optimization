<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;

class AuthorsController extends ApiController
{

    public function index(AuthorFilter $filters)
    {
        // if ($this->include('tickets')){
        //     return UserResource::collection(User::with('tickets')->paginate());
        // }

        return UserResource::collection(User::filter($filters)->paginate());
    }




    public function store(StoreUserRequest $request)
    {
        //
    }


    public function show(User $author)
    {
        if ($this->include('tickets')){
            return new UserResource($author->load('tickets'));
        }

        return new UserResource($author);
    }




    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }

    public function destroy(User $user)
    {
        //
    }
}
