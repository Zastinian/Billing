<?php

namespace App\Http\Controllers\Api\Client;


use App\Http\Controllers\Api\ApiController;
use App\Models\Ticket;
use App\Models\TicketContent;
use App\Rules\Captcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends ApiController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:50',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        $ticket = Ticket::create([
            'client_id' => $request->user()->id,
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

        if ($ticket->is_locked) {
            return $this->respondJson(['error' => 'The ticket has been locked.']);
        } elseif ($request->has('solved')) {
            $ticket->status = 0;
            $ticket->save();
            return $this->respondJson(['success' => 'You have marked the ticket as solved successfully!']);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:50',
        ]);

        if ($validator->fails())
            return $this->respondJson(['errors' => $validator->errors()->all()]);

        TicketContent::create([
            'ticket_id' => $id,
            'replier_id' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        $ticket->status = 1;
        $ticket->save();

        return $this->respondJson(['success' => 'You have made a reply to the ticket successfully!']);
    }
}
