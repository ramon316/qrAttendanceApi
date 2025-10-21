<?php

namespace App\Livewire\User;

use App\Models\Attendance;
use Livewire\Component;
use Livewire\WithPagination;

class MyAttendances extends Component
{
    use WithPagination;

    public function render()
    {
        $attendances = Attendance::with(['event'])
            ->where('user_id', auth()->id())
            ->orderBy('checked_in_at', 'desc')
            ->paginate(9);

        $totalAttendances = Attendance::where('user_id', auth()->id())->count();

        return view('livewire.user.my-attendances', [
            'attendances' => $attendances,
            'totalAttendances' => $totalAttendances,
        ]);
    }
}
