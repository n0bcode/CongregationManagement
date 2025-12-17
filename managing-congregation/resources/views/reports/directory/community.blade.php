@extends('reports.layouts.pdf')

@section('title', 'Community Report - ' . $community['code'])
@section('subtitle', $community['name'])

@section('content')
<h1>{{ $community['code'] }} - {{ $community['name'] }}</h1>

<div class="community-info mb-4">
    @if($community['location'])
        <div>📍 <strong>Address:</strong> {{ $community['location'] }}</div>
    @endif
    @if($community['phone'])
        <div>☎ <strong>Phone:</strong> {{ $community['phone'] }}</div>
    @endif
    @if($community['email'])
        <div>✉ <strong>Email:</strong> {{ $community['email'] }}</div>
    @endif
    @if($community['patron_saint'])
        <div>⛪ <strong>Patron Saint:</strong> {{ $community['patron_saint'] }}</div>
    @endif
    @if($community['founded_at'])
        <div>📅 <strong>Founded:</strong> {{ $community['founded_at']->format('Y') }}</div>
    @endif
</div>

<h2>Members ({{ $community['member_count'] }})</h2>

@if($community['members']->isNotEmpty())
    @foreach($community['members'] as $member)
    <div class="member-item">
        <div>
            <span class="member-role">{{ $member['role_code'] }}</span>
            <span class="member-name">{{ $member['full_name'] }}</span>
        </div>
        <div class="member-details">
            DOB: {{ $member['dob']->format('d.m.Y') }}
            @if($member['first_profession'])
                | 1st PR: {{ $member['first_profession']->format('d.m.Y') }}
            @endif
            @if($member['ordination'])
                | ORD: {{ $member['ordination']->format('d.m.Y') }}
            @endif
        </div>
        @if($member['phone'] || $member['email'])
        <div class="text-sm text-muted">
            @if($member['phone'])
                ☎ {{ $member['phone'] }}
            @endif
            @if($member['email'])
                @if($member['phone']) | @endif
                ✉ {{ $member['email'] }}
            @endif
        </div>
        @endif
    </div>
    @endforeach
@else
    <div class="text-muted text-sm">No members assigned to this community</div>
@endif
@endsection
