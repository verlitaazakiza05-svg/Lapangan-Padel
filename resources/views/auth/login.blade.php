<x-guest-layout>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-purple-50 relative overflow-hidden">

    <!-- Animated Background Blur Circles -->
    <div class="absolute w-96 h-96 bg-purple-300 rounded-full blur-3xl opacity-30 top-10 left-10 animate-pulse"></div>
    <div class="absolute w-96 h-96 bg-blue-300 rounded-full blur-3xl opacity-30 bottom-10 right-10 animate-pulse animation-delay-1000"></div>
    <div class="absolute w-64 h-64 bg-pink-300 rounded-full blur-3xl opacity-20 top-40 right-40 animate-float"></div>
    <div class="absolute w-64 h-64 bg-indigo-300 rounded-full blur-3xl opacity-20 bottom-40 left-40 animate-float animation-delay-2000"></div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden">
        @for($i = 0; $i < 20; $i++)
            <div class="absolute w-1 h-1 bg-indigo-300 rounded-full animate-twinkle"
                 style="top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 5) }}s;"></div>
        @endfor
    </div>

    <!-- Card -->
    <div class="relative w-full max-w-md p-10 bg-white/80 backdrop-blur-xl border border-white/50 rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 group">

        <!-- Decorative Corner -->
        <div class="absolute -top-3 -right-3 w-16 h-16 bg-gradient-to-br from-indigo-400 to-purple-400 rounded-full opacity-20 blur-sm"></div>
        <div class="absolute -bottom-3 -left-3 w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full opacity-20 blur-sm"></div>

        <!-- Logo with Animation -->
        <div class="flex justify-center mb-6 transform group-hover:scale-110 transition-transform duration-500">
            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 p-4 rounded-2xl shadow-lg rotate-3 group-hover:rotate-6 transition-all duration-500">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>

        <!-- Title with Gradient -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">
                Selamat Datang
            </h2>
            <p class="text-gray-500 mt-2">Silakan masuk ke akun Anda</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div class="group/input">
                <label class="text-sm font-medium text-gray-600 ml-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within/input:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input
                        type="email"
                        name="email"
                        required
                        value="{{ old('email') }}"
                        class="w-full pl-10 pr-4 py-3.5 rounded-xl border border-gray-200 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all duration-300 bg-white/50 backdrop-blur-sm"
                        placeholder="email@example.com"
                    >
                </div>
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="group/input">
                <label class="text-sm font-medium text-gray-600 ml-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within/input:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full pl-10 pr-12 py-3.5 rounded-xl border border-gray-200 focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100 focus:outline-none transition-all duration-300 bg-white/50 backdrop-blur-sm"
                        placeholder="••••••••"
                    >
                    <button type="button"
                        onclick="togglePassword()"
                        class="absolute right-3 top-3 text-gray-400 hover:text-indigo-500 transition-colors duration-300">
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember & Forgot -->
            <div class="flex items-center justify-between text-sm py-2">
                <label class="flex items-center gap-2 text-gray-600 cursor-pointer group/checkbox">
                    <div class="relative">
                        <input type="checkbox" name="remember" class="peer sr-only">
                        <div class="w-4 h-4 border border-gray-300 rounded bg-white peer-checked:bg-gradient-to-r peer-checked:from-indigo-500 peer-checked:to-purple-500 peer-checked:border-transparent transition-all duration-300"></div>
                        <svg class="absolute top-0.5 left-0.5 w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="group-hover/checkbox:text-indigo-600 transition-colors">Ingat saya</span>
                </label>

                <a href="{{ route('password.request') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline transition-colors flex items-center gap-1">
                    Lupa Password?
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

            <!-- Button -->
            <button
                type="submit"
                class="group/btn relative w-full overflow-hidden rounded-xl">
                <!-- Gradient Background -->
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600"></div>
                <!-- Shine Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                <!-- Button Content -->
                <div class="relative py-3.5 text-white font-bold tracking-wide flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 animate-bounce-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>MASUK</span>
                    <svg class="w-5 h-5 animate-bounce-slow animation-delay-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
            </button>

            <!-- Register -->
            <p class="text-center text-sm text-gray-600 pt-4 border-t border-gray-100">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-pink-600 hover:to-indigo-600 transition-all duration-300">
                    Daftar sekarang
                </a>
            </p>

        </form>

    </div>

</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-20px) scale(1.1); }
}
@keyframes twinkle {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
.animate-float { animation: float 8s ease-in-out infinite; }
.animate-twinkle { animation: twinkle 3s ease-in-out infinite; }
.animate-bounce-slow { animation: bounce-slow 2s ease-in-out infinite; }
.animation-delay-500 { animation-delay: 0.5s; }
.animation-delay-1000 { animation-delay: 1s; }
.animation-delay-2000 { animation-delay: 2s; }
</style>

<script>
function togglePassword(){
    const input = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");

    if(input.type === "password"){
        input.type = "text";
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
        `;
    }else{
        input.type = "password";
        eyeIcon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}
</script>

</x-guest-layout>
