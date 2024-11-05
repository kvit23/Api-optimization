<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;

class TicketController extends ApiController
{

    public function index()
    {
        if($this->include('author'))
        {
            return TicketResource::collection(Ticket::with('user')->paginate());
        }
        return TicketResource::collection(Ticket::all());
    }


    public function store(StoreTicketRequest $request)
    {

    }


    public function show(Ticket $ticket)
    {
        if($this->include('author'))
        {
            return new TicketResource($ticket->load('user'));
        }

       return new TicketResource($ticket);
    }



    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {

    }


    public function destroy(Ticket $ticket)
    {

    }
}
