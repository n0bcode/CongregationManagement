<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $member = \App\Models\Member::where('email', $user->email)->first();

        if (!$member) {
            return view('my-tasks.index', ['tasks' => collect()]);
        }

        $perPage = request()->input('perPage', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $tasks = \App\Models\Task::where('assignee_id', $member->id)
            ->with('project')
            ->orderBy('due_date', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        return view('my-tasks.index', compact('tasks'));
    }
}
