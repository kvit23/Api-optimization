<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorTicketsController extends ApiController
{

    protected $policyClass = TicketPolicy::class;

    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filters)->paginate()
        );
    }

    public function store($author_id, StoreTicketRequest $request)
    {

        try {

            $this->isAble('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttribute([
                'author' => 'user_id'
            ])));

        }catch (AuthorizationException $ex){

            return $this->error('You are not authorized to create this resource', 401);

        }

    }

    public function update(UpdateTicketRequest $request, $author_id, $ticket_id)
    {

        try {

            $ticket = Ticket::where('id', $ticket_id)
                            ->where('user_id', $author_id)
                            ->firstOrFail();


            $this->isAble('update', $ticket);

            $ticket->update($request->mappedAttribute());

            return new TicketResource($ticket);


        } catch (ModelNotFoundException){

            return $this->error('Ticket cannot be found.', 404);

        } catch (AuthorizationException $ex){

            return $this->error('You are not authorized to update this resource', 401);

        }

    }


    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {

        try {

            $ticket = Ticket::where('id', $ticket_id)
                            ->where('user_id', $author_id)
                            ->firstOrFail();


            $this->isAble('replace', $ticket);

            $ticket->update($request->mappedAttribute());

            return new TicketResource($ticket);



        } catch (ModelNotFoundException){

            return $this->error('Ticket cannot be found.', 404);

        } catch (AuthorizationException $ex){

            return $this->error('You are not authorized to update this resource', 401);

        }

    }


    public function destroy($author_id, $ticket_id)
    {
        try {

            $ticket = Ticket::where('id', $ticket_id)
                            ->where('user_id', $author_id)
                            ->firstOrFail();

            $this->isAble('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket deleted successfully');


        } catch (ModelNotFoundException $exception){

            return $this->error('Ticket cannot be found.', 404);
        }  catch (AuthorizationException $ex){

            return $this->error('You are not authorized to delete this resource', 401);

        }

    }

}
