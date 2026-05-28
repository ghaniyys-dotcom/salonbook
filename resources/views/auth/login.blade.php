<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Glow Studio</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500;1,600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        body { background-color: #0c0a1a; color: #ffffff; font-family: 'Inter', sans-serif; }
        
        /* Glassmorphism utility */
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        /* Glowing text gradient */
        .gradient-text {
            background: linear-gradient(135deg, #7c3aed 0%, #db2777 50%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Floating background blobs */
        @keyframes float-blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 30px) scale(0.9); }
        }
        .animate-blob { animation: float-blob 10s infinite ease-in-out; }
        .animate-blob-delay { animation: float-blob 10s infinite ease-in-out 3s; }
    </style>
</head>
<body class="min-h-[100svh] flex items-center justify-center p-4 relative overflow-hidden select-none">

    {{-- Glowing background orbs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-10 -left-20 w-[450px] h-[450px] rounded-full opacity-20 animate-blob" style="background: radial-gradient(circle, #7c3aed, transparent 70%); filter: blur(80px);"></div>
        <div class="absolute bottom-10 -right-20 w-[450px] h-[450px] rounded-full opacity-15 animate-blob-delay" style="background: radial-gradient(circle, #db2777, transparent 70%); filter: blur(80px);"></div>
    </div>

    <div class="relative z-10 w-full max-w-md mx-auto" x-data="loginHelper()">
        
        {{-- Brand Logo and Title --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2 mb-4 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-fuchsia-500 flex items-center justify-center shadow-lg shadow-violet-500/20 group-hover:scale-105 transition-transform duration-300">
                    <span class="text-white font-bold text-sm">G</span>
                </div>
                <span class="font-bold text-xl tracking-tight text-white" style="font-family: 'Playfair Display', serif;">Glow Studio</span>
            </a>
            <p class="text-slate-400 text-xs tracking-wider uppercase font-semibold">Ruang Kendali Manajemen</p>
        </div>

        {{-- Main Glass Card --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 shadow-2xl relative">
            
            {{-- Session Status --}}
            @if(session('status'))
                <div class="mb-5 bg-emerald-500/10 border border-emerald-500/30 text-emerald-200 px-4 py-3 rounded-xl text-xs backdrop-blur-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Error Alerts --}}
            @if($errors->any())
                <div class="mb-5 bg-red-500/10 border border-red-500/30 text-red-200 px-4 py-3 rounded-xl text-xs backdrop-blur-lg">
                    @foreach($errors->all() as $error)
                        <p class="flex items-center gap-1.5">⚠️ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @auth
                {{-- User is already authenticated view --}}
                <div class="space-y-6 text-center py-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-violet-600/20 to-fuchsia-600/20 border border-violet-500/30 rounded-2xl flex items-center justify-center mx-auto shadow-lg animate-pulse">
                        <span class="text-2xl">⚡</span>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-bold text-white leading-tight">Sesi Anda Aktif</h3>
                        <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                            Anda terdeteksi sedang masuk sebagai:<br>
                            <span class="font-semibold text-violet-400">{{ auth()->user()->name }}</span> 
                            (<span class="text-slate-300 font-mono text-[11px]">{{ auth()->user()->email }}</span>)
                        </p>
                    </div>

                    <div class="space-y-3 pt-2">
                        @if(auth()->user()->canAccessAdmin())
                            <a href="{{ route('admin.dashboard') }}" 
                                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white py-3.5 rounded-2xl font-bold hover:from-violet-700 hover:to-fuchsia-700 transition-all shadow-lg shadow-violet-500/25">
                                Masuk ke Dashboard →
                            </a>
                        @else
                            <a href="{{ route('home') }}" 
                                class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white py-3.5 rounded-2xl font-bold hover:from-violet-700 hover:to-fuchsia-700 transition-all shadow-lg shadow-violet-500/25">
                                Masuk ke Beranda →
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 bg-white/5 border border-white/10 text-rose-400 py-3.5 rounded-2xl font-bold hover:bg-white/10 hover:border-white/20 transition-all">
                                Keluar (Logout) Sesi Ini
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Standard Login Form --}}
                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    <div class="space-y-5">
                        {{-- Email input --}}
                        <div>
                            <label class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-2 block">Alamat Email</label>
                            <input type="email" name="email" x-model="email" required autofocus autocomplete="username"
                                placeholder="admin@demo.com"
                                style="background-color: #16132d !important; color: #ffffff !important; color-scheme: dark !important;"
                                class="w-full rounded-2xl border border-white/10 px-5 py-4 bg-white/5 text-white focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 outline-none transition-all placeholder-slate-600 text-sm">
                        </div>

                        {{-- Password input --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 block">Password</label>
                                @if (Route::has('password.request'))
                                    <a class="text-[9px] font-bold text-violet-400 hover:underline" href="{{ route('password.request') }}">
                                        Lupa Password?
                                    </a>
                                @endif
                            </div>
                            <input type="password" name="password" x-model="password" required autocomplete="current-password"
                                placeholder="••••••••"
                                style="background-color: #16132d !important; color: #ffffff !important; color-scheme: dark !important;"
                                class="w-full rounded-2xl border border-white/10 px-5 py-4 bg-white/5 text-white focus:ring-2 focus:ring-violet-500/50 focus:border-violet-500 outline-none transition-all placeholder-slate-600 text-sm">
                        </div>

                        {{-- Remember me checkbox --}}
                        <div class="flex items-center">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                                <input id="remember_me" type="checkbox" name="remember" class="w-4.5 h-4.5 rounded border-white/10 bg-white/5 text-violet-600 focus:ring-violet-500/50 focus:ring-offset-0 outline-none cursor-pointer">
                                <span class="ms-2.5 text-xs text-slate-400 font-medium">Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        {{-- Submit button --}}
                        <button type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white py-4 rounded-2xl font-bold hover:from-violet-700 hover:to-fuchsia-700 transition-all shadow-lg shadow-violet-500/25">
                            ⚡ Masuk Sekarang
                        </button>
                    </div>
                </form>
            @endauth

            {{-- Quick Demo Login Helper Card --}}
            <div class="mt-8 pt-6 border-t border-white/5 text-center">
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-500 block mb-3.5">AKSES KILAT DEMO (1-KLIK)</span>
                
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" @click="quickLogin('admin@demo.com')"
                        class="bg-violet-600/10 hover:bg-violet-600/20 border border-violet-500/20 text-violet-300 text-xs py-3 px-3 rounded-xl font-bold transition-all hover:scale-[1.02] flex flex-col items-center gap-0.5">
                        <span>🔑 Super Admin</span>
                        <span class="text-[8px] font-normal text-violet-400/80">admin@demo.com</span>
                    </button>
                    <button type="button" @click="quickLogin('staff@demo.com')"
                        class="bg-fuchsia-600/10 hover:bg-fuchsia-600/20 border border-fuchsia-500/20 text-fuchsia-300 text-xs py-3 px-3 rounded-xl font-bold transition-all hover:scale-[1.02] flex flex-col items-center gap-0.5">
                        <span>✨ Staff Salon</span>
                        <span class="text-[8px] font-normal text-fuchsia-400/80">staff@demo.com</span>
                    </button>
                </div>
            </div>

        </div>
        
        <div class="text-center mt-6">
            <a href="/" class="text-xs text-slate-500 hover:text-white transition-colors">
                ← Kembali ke Website Utama
            </a>
        </div>
    </div>

    {{-- Interactive Alpine JS controller for quick demo logins --}}
    <script>
        function loginHelper() {
            return {
                email: '',
                password: '',
                
                quickLogin(targetEmail) {
                    this.email = targetEmail;
                    this.password = 'password';
                    
                    // Auto submit after values are assigned in DOM
                    this.$nextTick(() => {
                        document.getElementById('login-form').submit();
                    });
                }
            }
        }
    </script>
</body>
</html>
