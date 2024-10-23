<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Ticket::all();
    }


    public function store(StoreTicketRequest $request)
    {

    }


    public function show(Ticket $ticket)
    {

    }



    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {

    }


    public function destroy(Ticket $ticket)
    {

    }
}
