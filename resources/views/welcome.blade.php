<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TrackIt</title>
        @vite('resources/css/app.css') 

    </head>

        

    <body class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 flex items-center justify-center h-screen transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-md text-center">
            <img src="{{ asset('img/trackit.png') }}" alt="TrackIt Logo" class="mx-auto h-48 w-auto mb-5">
            <p class="mb-6">Simples. Inteligente. Produtivo.</p>
            <div>
                <a href="{{ route('login') }}" class="text-blue-500 hover:underline dark:text-blue-400">Login</a>
                <span class="text-gray-400 mx-2">|</span>
                <a href="{{ route('register') }}" class="text-green-500 hover:underline dark:text-green-400">Registrar</a>
            </div>
            
        </div>
    </body>

    <script>
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const body = document.body;

    darkModeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        // (Opcional) Salvar a preferência no localStorage
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
        } else {
            localStorage.setItem('darkMode', 'disabled');
        }
    });

    // (Opcional) Verificar a preferência do usuário ao carregar a página
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark-mode');
    }
</script>
</html>