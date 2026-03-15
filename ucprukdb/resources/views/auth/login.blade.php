<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - UCPRUK Database</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Google reCAPTCHA v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
    
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-6 bg-slate-50">
    
    <div class="w-full max-w-md relative">
        <!-- Abstract shapes for glass visibility -->
        <div class="absolute -top-12 -left-12 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 -right-12 w-32 h-32 bg-accent/30 rounded-full blur-3xl"></div>
        
        <!-- Logo -->
        <div class="text-center mb-10 relative z-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-white shadow-xl mb-4 border border-white/50">
                <h1 class="text-3xl font-black text-primary">U</h1>
            </div>
            <h1 class="text-4xl font-black text-gray-800 tracking-tight">UCPRUK <span class="text-primary">DB</span></h1>
            <p class="text-gray-500 font-bold uppercase tracking-[0.2em] text-xs mt-2">Management Portal</p>
        </div>
        
        <!-- Login Card (Glass) -->
        <div class="glass p-10 relative z-10">
            <h2 class="text-2xl font-black text-gray-800 mb-8 tracking-tight">Selamat Datang</h2>
            
            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-50/50 backdrop-blur-sm border border-red-200 text-red-700 p-4 rounded-xl mb-8 font-medium text-sm">
                    @foreach($errors->all() as $error)
                        <p class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif
            
            <form action="{{ route('login') }}" method="POST" id="loginForm" class="space-y-6">
                @csrf
                
                <!-- reCAPTCHA v3 Token -->
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                
                <!-- Username -->
                <div>
                    <label for="username" class="label">Username</label>
                    <div class="relative">
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}"
                               class="input @error('username') border-red-300 @enderror"
                               placeholder="admin"
                               required
                               autofocus>
                    </div>
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="label">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="input @error('password') border-red-300 @enderror"
                           placeholder="••••••••"
                           required>
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <div class="relative w-6 h-6">
                            <input type="checkbox" name="remember" value="1" class="peer absolute opacity-0 w-full h-full cursor-pointer">
                            <div class="w-6 h-6 border-2 border-gray-200 rounded-lg group-hover:border-primary transition-colors peer-checked:bg-primary peer-checked:border-primary"></div>
                            <svg class="absolute top-1 left-1 w-4 h-4 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-gray-600 font-bold text-sm tracking-tight capitalize">Ingat saya</span>
                    </label>
                </div>

                @error('g-recaptcha-response')
                    <p class="text-red-600 text-[10px] font-black uppercase text-center tracking-wider">{{ $message }}</p>
                @enderror
                
                <!-- Submit Button -->
                <button type="submit" class="btn-primary w-full py-4 text-lg">
                    Masuk Sekarang
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-10">
            <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">
                &copy; {{ date('Y') }} United Cerebral Palsy RUK
            </p>
        </div>
    </div>

    <!-- reCAPTCHA Execution -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('recaptcha.site_key') }}', {action: 'login'}).then(function(token) {
                    if (token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    } else {
                        alert('Gagal mendapatkan token reCAPTCHA. Silakan segarkan halaman.');
                    }
                });
            });
        });
    </script>
</body>
</html>