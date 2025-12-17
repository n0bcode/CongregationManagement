@extends('reports.layouts.pdf')

@section('title', 'AFE Directory Complete')
@section('subtitle', 'DIRECTORY ' . now()->year . '-' . (now()->year + 1))

@section('content')
<h1>AFE SALESIAN PROVINCE DIRECTORY {{ now()->year }}-{{ now()->year + 1 }}</h1>

<div class="mb-4 text-sm text-muted">
    <strong>Total Members:</strong> {{ $stats['total_members'] }} |
    <strong>Communities:</strong> {{ $stats['total_communities'] }} |
    <strong>Deceased:</strong> {{ $stats['deceased_count'] }}
</div>

{{-- TABLE OF CONTENTS --}}
<div class="mb-4" style="page-break-after: always;">
    <h2>TABLE OF CONTENTS</h2>
    <div class="text-sm">
        <div>1. HOUSES/COMMUNITIES (Communion)</div>
        <div>2. INDEX CONFRERES (Alphabetical)</div>
        <div>3. BIRTHDAYS (By Month)</div>
        <div>4. DECEASED SALESIANS</div>
    </div>
</div>

{{-- SECTION 1: COMMUNION --}}
<h1 style="page-break-before: always;">1. HOUSES/COMMUNITIES</h1>

@foreach($communion as $community)
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
    @endif
</div>
@endforeach

{{-- SECTION 2: INDEX --}}
<h1 style="page-break-before: always;">2. INDEX CONFRERES</h1>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Birth</th>
            <th>1st PR</th>
            <th>ORD/PP</th>
            <th>House</th>
        </tr>
    </thead>
    <tbody>
        @foreach($index as $member)
        <tr>
            <td>{{ $member['surname'] }} {{ $member['given_name'] }}</td>
            <td>{{ $member['role_code'] }}</td>
            <td>{{ $member['dob']?->format('d.m.Y') }}</td>
            <td>{{ $member['first_profession']?->format('d.m.Y') }}</td>
            <td>{{ $member['ordination']?->format('d.m.Y') }}</td>
            <td>{{ $member['house_code'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- SECTION 3: BIRTHDAYS --}}
<h1 style="page-break-before: always;">3. BIRTHDAYS</h1>

@foreach($birthdays as $monthData)
<div class="month-section">
    <div class="month-header">
        🎂 {{ strtoupper($monthData['month_name']) }}
    </div>

    @if($monthData['members']->isNotEmpty())
        @foreach($monthData['members'] as $member)
        <div class="member-item">
            <div>
                <strong>{{ $member['day'] }}</strong> - 
                <span class="member-name">{{ $member['surname'] }} {{ $member['given_name'] }}</span>
            </div>
            <div class="member-details">
                {{ $member['dob']->format('d.m.Y') }} (Age: {{ $member['age'] }}) | House: {{ $member['house_code'] }}
            </div>
        </div>
        @endforeach
    @endif
</div>
@endforeach

{{-- SECTION 4: DECEASED --}}
<h1 style="page-break-before: always;">4. DECEASED SALESIANS</h1>

<p class="text-sm text-muted mb-4">May they rest in peace</p>

@foreach($deceased as $member)
<div class="deceased-item">
    <div>
        <span class="member-role">{{ $member['role_code'] }}</span>
        <span class="member-name">{{ $member['surname'] }} {{ $member['given_name'] }}</span>
    </div>
    <div class="member-details">
        ✝ Died: {{ $member['date_of_death']->format('d-m-Y') }}
        @if($member['age_at_death'])
            (Age: {{ $member['age_at_death'] }})
        @endif
        | House: {{ $member['house_code'] }}
    </div>
    @if($member['dob'])
    <div class="text-sm text-muted">
        Born: {{ $member['dob']->format('d.m.Y') }}
    </div>
    @endif
</div>
@endforeach

@endsection
