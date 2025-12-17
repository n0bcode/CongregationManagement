@extends('reports.layouts.pdf')

@section('title', 'Directory Communion')
@section('subtitle', 'COMMUNION - ' . now()->year)

@section('content')
<h1>DIRECTORY COMMUNION {{ now()->year }}</h1>

@foreach($communities as $community)
<div class="community-block">
    <div class="community-header">
        <div class="community-code">{{ $community['code'] }}</div>
        <div class="community-name">{{ $community['name'] }}</div>
        @if($community['location'] || $community['phone'] || $community['email'])
        <div class="community-info">
            @if($community['location'])
                📍 {{ $community['location'] }}
            @endif
            @if($community['phone'])
                | ☎ {{ $community['phone'] }}
            @endif
            @if($community['email'])
                | ✉ {{ $community['email'] }}
            @endif
        </div>
        @endif
    </div>

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
        </div>
        @endforeach
    @else
        <div class="text-muted text-sm">No members assigned</div>
    @endif
</div>
@endforeach

<div class="mb-4"></div>
<div class="text-sm text-muted">
    <strong>Total Communities:</strong> {{ $communities->count() }} |
    <strong>Total Members:</strong> {{ $communities->sum(fn($c) => $c['members']->count()) }}
</div>
@endsection
