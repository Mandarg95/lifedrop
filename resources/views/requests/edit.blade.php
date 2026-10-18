@extends('layouts.app')

@section('title', 'Edit request')
@section('subtitle', 'Update the request for '.$bloodRequest->patient_name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('requests.show', $bloodRequest) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to request
        </a>
    </div>

    <form method="POST" action="{{ route('requests.update', $bloodRequest) }}" novalidate>
        @csrf
        @method('PUT')
        @include('requests._form', ['groups' => $groups, 'cancelUrl' => route('requests.show', $bloodRequest), 'submitLabel' => 'Save changes'])
    </form>
@endsection
