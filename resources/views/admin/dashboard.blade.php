@extends('admin.layout.admin')

@section('title', 'Dashboard Super Admin')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Data belum bisa disimpan.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- Banner --}}
    @include('admin.partials.banner')

    <div class="my-4">
        @include('admin.partials.quick-action')
    </div>

    <div class="my-4">
        @include('admin.partials.stats')
    </div>

    <div class="row">

        <div class="col-lg-6 mb-4">
            @include('admin.partials.activities')
        </div>

        <div class="col-lg-6 mb-4">
            @include('admin.partials.progress')
        </div>

    </div>

    <div class="my-4">
        @include('admin.partials.documents')
    </div>

@endsection
