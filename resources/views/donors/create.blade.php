@extends('layouts.app')

@section('title', 'Add donor')
@section('subtitle', 'Register a new donor in the system')

@section('content')
    <div class="mb-6">
        <a href="{{ route('donors.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-800">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-4"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to donors
        </a>
    </div>

    <form method="POST" action="{{ route('donors.store') }}" novalidate>
        @csrf
        @include('donors._form', ['groups' => $groups, 'cancelUrl' => route('donors.index'), 'submitLabel' => 'Create donor'])
    </form>
@endsection
