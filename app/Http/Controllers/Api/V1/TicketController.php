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
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{

    protected $policyClass = TicketPolicy::class;


    public function index(TicketFilter $filters)
    {

        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }


    public function store(StoreTicketRequest $request)
    {
        try {

            $this->isAble('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttribute()));

        }catch (AuthorizationException $ex){

            return $this->error('You are not authorized to update this resource', 401);

        }
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

            //policy

            $this->isAble('update', $ticket);

            $ticket->update($request->mappedAttribute());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException){

            return $this->error('Ticket cannot be found.', 404);

        }catch (AuthorizationException $ex){

            return $this->error('You are not authorized to update this resource', 401);

        }


    }

    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {


        try {

            $ticket = Ticket::findOrFail($ticket_id);

            $this->isAble('replace', $ticket);

            $ticket->update($request->mappedAttribute());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException){

            return $this->error('Ticket cannot be found.', 404);

        }

    }


    public function destroy($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            $this->isAble('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket deleted successfully');

        } catch (ModelNotFoundException $exception){

            return $this->error('Ticket cannot be found.', 404);
        }
    }
}
