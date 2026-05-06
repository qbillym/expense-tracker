@extends('layouts.clean')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center mb-4">Login</h3>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.perform') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address or Username</label>
                            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                                   class="form-control" placeholder="Enter your email or 'admin' for admin login">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password" required
                                   class="form-control" placeholder="Enter your password">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <p class="mb-0">
                            Don't have an account?
                            <a href="{{ route('register') }}">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection