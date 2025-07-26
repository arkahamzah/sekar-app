<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('title', 'Login - SEKAR')

@section('content')
<div class="min-h-screen flex">
    <!-- Left Side - Illustration -->
    <div class="hidden lg:flex lg:w-1/2 bg-white items-center justify-center p-8">
        <div class="max-w-lg w-full flex justify-center">
            <!-- Illustration Image -->
            <img src="{{ asset('asset/asset-image-index.png') }}" alt="Login Illustration" class="w-full max-w-md">
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('asset/logo.png') }}" alt="SEKAR Logo" class="h-12">
                </div>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <input 
                        type="text" 
                        name="nik" 
                        placeholder="NIK" 
                        value="{{ old('nik') }}"
                        class="w-full px-4 py-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200 text-gray-700"
                        required
                    >
                </div>

                <div>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Password"
                        class="w-full px-4 py-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200 text-gray-700"
                        required
                    >
                </div>

                <button 
                    type="submit"
                    class="w-full bg-blue-700 text-white py-4 rounded-lg font-medium hover:bg-blue-800 transition duration-200 text-lg"
                >
                    Login
                </button>

                <div class="text-center">
                    <span class="text-gray-600">Ingin menjadi anggota? </span>
                    <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">Daftar Sekar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection