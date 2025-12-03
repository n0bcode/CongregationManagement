<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = \App\Models\Member::paginate(20);
        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(\App\Http\Requests\StoreMemberRequest $request)
    {
        $data = $request->validated();
        
        // Handle community_id
        // If User is Director/Member: Use Auth::user()->community_id
        // If User is Super Admin: Require community_id from input (not implemented in form yet, assuming Director for now as per MVP decision)
        // AC3: "And the new member is automatically assigned to my community_id."
        
        $data['community_id'] = \Illuminate\Support\Facades\Auth::user()->community_id;

        $member = \App\Models\Member::create($data);

        return redirect()->route('members.show', $member)->with('status', 'Member created successfully.');
    }

    public function show(\App\Models\Member $member): View
    {
        return view('members.show', compact('member'));
    }
}
