<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Balai - SITABA</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="min-h-screen flex flex-col items-center justify-center bg-[#161446] px-4 py-12">

        <div class="flex items-center gap-3 mb-8">
            <img src="{{ asset('logositaba.png') }}" alt="Logo" class="w-12 h-12 object-contain">
            <span class="text-white text-2xl font-bold tracking-wide">SITABA</span>
        </div>

        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">

            <h1 class="text-center text-xl font-bold text-gray-900 mb-6">
                Login Balai
            </h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('balai.login.authenticate') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="username" class="block text-base text-gray-900 mb-1">
                        Username
                    </label>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full rounded-lg bg-blue-50/70 border border-blue-50 px-4 py-3 text-gray-900
                               focus:outline-none focus:ring-2 focus:ring-[#3B39C4] focus:border-transparent"
                    />
                </div>

                <div>
                    <label for="password" class="block text-base text-gray-900 mb-1">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-lg bg-blue-50/70 border border-blue-50 px-4 py-3 text-gray-900
                               focus:outline-none focus:ring-2 focus:ring-[#3B39C4] focus:border-transparent"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-[#3B39C4] hover:bg-[#302ea3] transition-colors
                           text-white font-semibold text-lg py-3.5"
                >
                    Submit
                </button>
            </form>
        </div>
    </div>
</body>
</html>