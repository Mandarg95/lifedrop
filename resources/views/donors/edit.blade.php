@extends('layouts.app')

@section('title', 'Edit donor')
@section('subtitle', 'Update details for '.$donor->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('donors.show', $donor) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to profile
        </a>
    </div>

    <form method="POST" action="{{ route('donors.update', $donor) }}" novalidate>
        @csrf
        @method('PUT')
        @include('donors._form', ['groups' => $groups, 'cancelUrl' => route('donors.show', $donor), 'submitLabel' => 'Save changes'])
    </form>
@endsection
