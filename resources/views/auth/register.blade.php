@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="h4 fw-bold">Create an Account</h2>
                        <p class="text-muted mb-0">Register to start tracking your expenses.</p>
                    </div>

                    <form method="POST" action="{{ route('register.perform') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password" required
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   class="form-control">
                        </div>

                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-person-plus me-2"></i>Register
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">
                            Already have an account?
                            <a href="{{ route('login') }}" class="link-primary">Log in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection