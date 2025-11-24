<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TrackIt - Login</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center h-screen transition-colors duration-300">
    <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-md w-96">
        <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800 dark:text-white">Login - TrackIt</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            

            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Email</label>
                <input id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Senha</label>
                <input id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:border-gray-600" type="password" name="password" required autocomplete="current-password">
                @error('password')
                    <p class="text-red-500 text-xs italic mt-2 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Entrar
                </button>
                @if (Route::has('password.request'))
                    <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" href="{{ route('password.request') }}">
                        Esqueceu sua senha?
                    </a>
                @endif
            </div>
        </form>

        
        <div class="mt-4 text-center">
                <a href="{{ url('/') }}" class="text-blue-500 hover:underline dark:text-blue-400">Voltar à Página Inicial</a>
            </div>

        
        <button id="dark-mode-toggle" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-full h-8 w-8 focus:outline-none mt-4" style="position: absolute; top: 20px; right: 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5 mx-auto my-auto">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354a9 9 0 01-12.728 0 9 9 0 0112.728 0zM8 8.009c.193.006.382.02.576.043m4.464 1.202a9.095 9.095 0 01-4.316 4.315m4.464-1.202a9.095 9.095 0 014.316-4.314M12 8.009c.405 0 .808.04 1.202.117M12 8.009a9.094 9.094 0 01-8.43 8.43m16.863-1.019C16.946 16.979 16.168 18 15.354 18M3.636 9.86A9.092 9.092 0 014.05 6.047M12 20.009c-1.662 0-3.191-.407-4.464-1.085M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
            </svg>
        </button>
    </div>

    <script>
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const body = document.body;

        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark'); // Tailwind usa 'dark' por padrão
            if (body.classList.contains('dark')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        });

        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark');
        }
    </script>
</body>

</html>