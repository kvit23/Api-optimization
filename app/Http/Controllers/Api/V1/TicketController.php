<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{


    public function index(TicketFilter $filters)
    {
//        [
//            'include' => 'author',
//            'filter' => [
//                'status' => 'C',
//                'title' => 'title filter',
//                'created_at' => ''
//            ],
//        ]



        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }


    public function store(StoreTicketRequest $request)
    {
        try {
            $user = User::findOrFail($request->input('data.relationships.author.data.id'));
        } catch (ModelNotFoundException){
            return $this->ok('User not found',[
                'error' => 'The provided user id does not exists'
            ]);
        }

        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $request->input('data.relationships.author.data.id'),
        ];

        return new TicketResource(Ticket::create($model));

    }


    public function show($ticket_id)
    {
        try{

            $ticket = Ticket::findOrFail($ticket_id);

            if($this->include('author'))
            {
                return new TicketResource($ticket->load('user'));
            }

           return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {

            return $this->error('Ticket cannot be found.', 404);

        }

    }



    public function update(UpdateTicketRequest $request, $ticket_id)
    {

        try {

            $ticket = Ticket::findOrFail($ticket_id);

            $ticket->update($request->mappedAttribute());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException){

            return $this->error('Ticket cannot be found.', 404);

        }


    }

    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {

        try {

            $ticket = Ticket::findOrFail($ticket_id);

            $model = [
                'title' => $request->input('data.attributes.title'),
                'description' => $request->input('data.attributes.description'),
                'status' => $request->input('data.attributes.status'),
                'user_id' => $request->input('data.relationships.author.data.id'),
            ];

            $ticket->update($model);

            return new TicketResource($ticket);

        } catch (ModelNotFoundException){

            return $this->error('Ticket cannot be found.', 404);

        }

    }


    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();

            return $this->ok('Ticket deleted successfully');

        } catch (ModelNotFoundException $exception){

            return $this->error('Ticket cannot be found.', 404);
        }
    }
}
