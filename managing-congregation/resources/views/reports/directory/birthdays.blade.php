@extends('reports.layouts.pdf')

@section('title', 'Birthdays')
@section('subtitle', 'BIRTHDAYS - ' . now()->year)

@section('content')
<h1>BIRTHDAYS {{ now()->year }}</h1>

@foreach($months as $monthData)
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

<div class="text-sm text-muted">
    <strong>Total Birthdays:</strong> {{ $months->sum(fn($m) => $m['members']->count()) }}
</div>
@endsection
