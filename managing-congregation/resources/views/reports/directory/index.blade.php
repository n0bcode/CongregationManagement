@extends('reports.layouts.pdf')

@section('title', 'Member Index')
@section('subtitle', 'INDEX CONFRERES')

@section('content')
<h1>INDEX CONFRERES</h1>

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
        @foreach($members as $member)
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

<div class="text-sm text-muted">
    <strong>Total Members:</strong> {{ $members->count() }}
</div>
@endsection
