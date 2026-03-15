@extends('layouts.app')

@section('content')
    <div class="auth-container">
        <div class="card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p class="text-muted">Sign in to your trading journal</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <span style="color: var(--error); font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn" style="width: 100%;">Sign In</button>
            </form>

            <div
                style="margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1rem; font-size: 0.8rem; color: var(--text-muted);">
                <p>Demo Login:</p>
                <p>Admin: admin@example.com / password</p>
                <p>Guest: guest@example.com / password</p>
            </div>
        </div>
    </div>
@endsection