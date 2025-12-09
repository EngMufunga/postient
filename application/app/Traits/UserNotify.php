<?php
namespace App\Traits;

use App\Constants\Status;

trait UserNotify
{
    public static function notifyToUser(){
        return [
            'list'              => 'All Users',
            'selectedUsers'         => 'Selected Users',
            'withBalance'           => 'With Balance Users',
            'emptyBalanceUsers'     => 'Empty Balance Users',
            'twoFaDisableUsers'     => '2FA Disable User',
            'twoFaEnableUsers'      => '2FA Enable User',
            'hasDepositedUsers'       => 'Deposited Users',
            'notDepositedUsers'       => 'Not Deposited Users',
            'pendingDepositedUsers'   => 'Pending Deposited Users',
            'rejectedDepositedUsers'  => 'Rejected Deposited Users',
            'pendingTicketUser'     => 'Pending Ticket Users',
            'answerTicketUser'      => 'Answer Ticket Users',
            'closedTicketUser'      => 'Closed Ticket Users',
        ];
    }

    public function scopeSelectedUsers($query)
    {
        return $query->whereIn('id', request()->user ?? []);
    }

    public function scopeList($query)
    {
        return $query;
    }

    public function scopeEmptyBalanceUsers($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableUsers($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableUsers($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedUsers($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }


    public function scopePendingTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

}
