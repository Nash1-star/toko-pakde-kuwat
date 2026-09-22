@extends('layouts.app')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 space-y-8">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-4">
                <i class="fas fa-store text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Login Operator</h2>
            <p class="mt-2 text-sm text-gray-600">Toko Pakde Kuwat</p>
        </div>
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Nama Anda</label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($users as $user)
                        <label class="cursor-pointer">
                            <input type="radio" name="user_id" value="{{ $user->id }}" class="peer sr-only" required>
                            <div class="rounded-lg border border-gray-200 bg-white p-5 hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500 peer-checked:bg-blue-50 transition-all text-center">
                                <span class="block font-semibold text-gray-900">{{ $user->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label for="pin" class="block text-sm font-medium text-gray-700">PIN (4 Digit)</label>
                <input id="pin" name="pin" type="password" required class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm mt-1" placeholder="Masukkan PIN">
            </div>
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection