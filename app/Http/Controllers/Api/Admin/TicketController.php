<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\TicketContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client' => 'required|string|exists:clients,email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $ticket = Ticket::create([
            'client_id' => Client::where('email', $request->input('client'))->value('id'),
            'subject' => $request->input('subject'),
        ]);

        TicketContent::create([
            'ticket_id' => $ticket->id,
            'replier_id' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        return $this->respondJson(['success' => 'You have created a support ticket successfully!']);
    }
    
    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);

        if ($request->has('close')) {
            $ticket->status = 3;
            $ticket->save();
            return $this->respondJson(['success' => 'You have closed the ticket successfully!']);
        } elseif ($request->has('lock')) {
            $ticket->status = 3;
            $ticket->is_locked = true;
            $ticket->save();
            return $this->respondJson(['success' => 'You have locked the ticket successfully!']);
        } elseif ($request->has('unlock')) {
            $ticket->is_locked = false;
            $ticket->save();
            return $this->respondJson(['success' => 'You have unlocked the ticket successfully!']);
        } elseif ($ticket->is_locked) {
            return $this->respondJson(['error' => 'The ticket has been locked. You must unlock it before making new replies.']);
        }
        
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        TicketContent::create([
            'ticket_id' => $id,
            'replier_id' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        $ticket->status = 2;
        $ticket->save();
        
        return $this->respondJson(['success' => 'You have made a reply to the ticket successfully!']);
    }
}
