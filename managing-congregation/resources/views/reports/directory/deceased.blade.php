@extends('reports.layouts.pdf')

@section('title', 'Deceased Salesians')
@section('subtitle', 'IN MEMORIAM')

@section('content')
<h1>DECEASED SALESIANS</h1>

<p class="text-sm text-muted mb-4">May they rest in peace</p>

@foreach($members as $member)
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

<div class="text-sm text-muted mb-4">
    <strong>Total Deceased:</strong> {{ $members->count() }}
</div>
@endsection
