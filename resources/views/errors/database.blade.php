@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-database-exclamation text-danger" style="font-size: 4rem;"></i>
                </div>
                <h2 class="h4 mb-3">Database Connection Issue</h2>
                <p class="text-muted mb-4">
                    We're having trouble connecting to our database. This is usually a temporary issue.
                    Please try again in a few moments.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus me-2"></i>Register
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
