<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Traits\SupportTicketManager;
use Illuminate\Http\Request;
class SupportTicketController extends Controller
{
    use SupportTicketManager;


    public function __construct()
    {
        $this->userType = 'admin';
        $this->column = 'admin_id';
        $this->user = Admin::first();
    }


    public function tickets(Request $request, $status = 'all')
    {
        $search = $request->search;
        $query = SupportTicket::with('user');

        switch ($status) {
            case 'pending':
                $query->whereIn('status', [0, 2]);
                break;
            case 'closed':
                $query->where('status', 3);
                break;
            case 'answered':
                $query->where('status', 1);
                break;
            case 'all':
                $query->whereIn('status', [0, 1, 2, 3]);
                break;
            default:

                break;
        }

        $items = $query->searchable(['subject', 'ticket'])->dateFilter()->latest()->paginate(getPaginate());

        if ($request->ajax()) {
            return response()->json([
                'html' => view('Admin::components.tables.ticket_list', compact('items'))->render(),
                'pagination' => $items->hasPages() ? view('Admin::components.pagination', compact('items'))->render() : '',
            ]);
        }

        $pageTitle = ucfirst($status) . ' Tickets';
        return view('Admin::support.tickets', compact('items', 'pageTitle'));
    }


    public function ticketReply($id)
    {
        $ticket = SupportTicket::with('user')->where('id', $id)->firstOrFail();
        $pageTitle = 'Reply Ticket';
        $messages = SupportMessage::with('ticket','admin','attachments')->where('support_ticket_id', $ticket->id)->orderBy('id','desc')->get();
        return view('Admin::support.reply', compact('ticket', 'messages', 'pageTitle'));
    }

    public function ticketDelete($id)
    {
        $message = SupportMessage::findOrFail($id);
        $path = getFilePath('ticket');
        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $attachment) {
                fileManager()->removeFile($path.'/'.$attachment->attachment);
                $attachment->delete();
            }
        }
        $message->delete();
        $notify[] = ['success', "Support ticket has been deleted successfully"];
        return back()->withNotify($notify);

    }

}
