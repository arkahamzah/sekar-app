{{-- resources/views/profile/change-password.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('profile.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Profile</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Ubah Password</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2a2 2 0 01-2-2m0 0a2 2 0 012-2m0 0a2 2 0 00-2 2m-6 8a6 6 0 1112 0 6 6 0 01-12 0zm6-3a1 1 0 011-1h.01a1 1 0 110 2H13a1 1 0 01-1-1z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Ubah Password</h1>
                    <p class="mt-1 text-sm text-gray-600">Ubah password akun Anda untuk keamanan yang lebih baik</p>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Formulir Ubah Password</h2>
                        <p class="mt-1 text-sm text-gray-600">Masukkan password saat ini dan password baru yang diinginkan</p>
                    </div>

                    <form method="POST" action="{{ route('profile.change-password.update') }}" class="p-6 space-y-6" id="changePasswordForm">
                        @csrf

                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password Saat Ini <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="current_password" 
                                    name="current_password" 
                                    required
                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror"
                                    placeholder="Masukkan password saat ini"
                                >
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('current_password')">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="current_password_icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="new_password" 
                                    name="new_password" 
                                    required
                                    minlength="6"
                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('new_password') border-red-500 @enderror"
                                    placeholder="Masukkan password baru (minimal 6 karakter)"
                                >
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('new_password')">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="new_password_icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('new_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Password Strength Indicator -->
                            <div class="mt-2">
                                <div class="text-xs text-gray-500 mb-1">Kekuatan Password:</div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="password-strength-bar" class="bg-red-400 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <div id="password-strength-text" class="text-xs mt-1 text-gray-500">Masukkan password</div>
                            </div>
                            
                            <div class="mt-2 text-xs text-gray-500">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Minimal 6 karakter</li>
                                    <li>Disarankan menggunakan kombinasi huruf besar, kecil, angka, dan simbol</li>
                                    <li>Hindari menggunakan informasi pribadi</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="new_password_confirmation" 
                                    name="new_password_confirmation" 
                                    required
                                    minlength="6"
                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Ulangi password baru"
                                >
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('new_password_confirmation')">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="new_password_confirmation_icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <div id="password-match-feedback" class="mt-1 text-sm hidden"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 pt-6 border-t border-gray-200">
                            <a 
                                href="{{ route('profile.index') }}" 
                                class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Profile
                            </a>

                            <button 
                                type="submit" 
                                id="submitBtn"
                                class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2a2 2 0 01-2-2m0 0a2 2 0 012-2m0 0a2 2 0 00-2 2m-6 8a6 6 0 1112 0 6 6 0 01-12 0zm6-3a1 1 0 011-1h.01a1 1 0 110 2H13a1 1 0 01-1-1z"></path>
                                </svg>
                                <span id="submitBtnText">Ubah Password</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Account Info -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">NIK: {{ Auth::user()->nik }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Email:</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Terakhir Login:</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if(Auth::user()->pengurus && Auth::user()->pengurus->role)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Role:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ Auth::user()->pengurus->role->NAME }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Security Tips -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Tips Keamanan</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Gunakan password yang unik dan kuat</li>
                                    <li>Jangan bagikan password Anda</li>
                                    <li>Ganti password secara berkala</li>
                                    <li>Logout dari perangkat umum</li>
                                    <li>Aktifkan notifikasi keamanan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Activity -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Aktivitas Terakhir</h3>
                    <div class="text-sm text-gray-600">
                        <p>Anda login terakhir kali pada:</p>
                        <p class="font-medium text-gray-900 mt-1">{{ Auth::user()->updated_at->format('l, d F Y') }}</p>
                        <p class="font-medium text-gray-900">{{ Auth::user()->updated_at->format('H:i') }} WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('changePasswordForm');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('new_password_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    const matchFeedback = document.getElementById('password-match-feedback');

    // Password strength checker
    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        updatePasswordStrength(strength);
        checkPasswordMatch();
    });

    // Password confirmation checker
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // Form submission
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtnText.textContent = 'Memproses...';
    });

    function calculatePasswordStrength(password) {
        let score = 0;
        let feedback = '';

        if (password.length === 0) {
            return { score: 0, feedback: 'Masukkan password' };
        }

        if (password.length >= 6) score += 1;
        if (password.length >= 8) score += 1;
        if (/[a-z]/.test(password)) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^A-Za-z0-9]/.test(password)) score += 1;

        if (score < 2) {
            feedback = 'Lemah';
        } else if (score < 4) {
            feedback = 'Sedang';
        } else if (score < 6) {
            feedback = 'Kuat';
        } else {
            feedback = 'Sangat Kuat';
        }

        return { score, feedback };
    }

    function updatePasswordStrength(strength) {
        const percentage = (strength.score / 6) * 100;
        strengthBar.style.width = percentage + '%';
        strengthText.textContent = strength.feedback;

        // Update color based on strength
        if (strength.score < 2) {
            strengthBar.className = 'bg-red-400 h-2 rounded-full transition-all duration-300';
            strengthText.className = 'text-xs mt-1 text-red-600';
        } else if (strength.score < 4) {
            strengthBar.className = 'bg-yellow-400 h-2 rounded-full transition-all duration-300';
            strengthText.className = 'text-xs mt-1 text-yellow-600';
        } else if (strength.score < 6) {
            strengthBar.className = 'bg-green-400 h-2 rounded-full transition-all duration-300';
            strengthText.className = 'text-xs mt-1 text-green-600';
        } else {
            strengthBar.className = 'bg-green-500 h-2 rounded-full transition-all duration-300';
            strengthText.className = 'text-xs mt-1 text-green-700';
        }
    }

    function checkPasswordMatch() {
        const password = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (confirmPassword === '') {
            matchFeedback.classList.add('hidden');
            confirmPasswordInput.setCustomValidity('');
            return;
        }

        if (password === confirmPassword) {
            matchFeedback.textContent = '✓ Password sesuai';
            matchFeedback.className = 'mt-1 text-sm text-green-600';
            matchFeedback.classList.remove('hidden');
            confirmPasswordInput.setCustomValidity('');
        } else {
            matchFeedback.textContent = '✗ Password tidak sesuai';
            matchFeedback.className = 'mt-1 text-sm text-red-600';
            matchFeedback.classList.remove('hidden');
            confirmPasswordInput.setCustomValidity('Password tidak sesuai');
        }
    }
});

// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
        `;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}
</script>
@endsection