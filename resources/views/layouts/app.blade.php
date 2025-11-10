<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white dark:from-gray-900 dark:to-gray-900">
            @include('layouts.navigation')

            {{-- Admin Toolbar: aparece apenas para administradores --}}
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="bg-indigo-50 dark:bg-indigo-900/40 border-b border-indigo-200/70 dark:border-indigo-800">
                        <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div class="text-sm font-medium text-indigo-700 dark:text-indigo-200">
                                Você está logado como <span class="font-semibold">Administrador</span>.
                            </div>
                            <div class="flex flex-wrap gap-2">
                                {{-- Atalhos de administração (adicione mais quando criar as telas) --}}
                                <a href="{{ route('admin.equipes.index') }}"
                                   class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium
                                          bg-white text-indigo-700 ring-1 ring-inset ring-indigo-200 hover:bg-indigo-50
                                          dark:bg-indigo-800/60 dark:text-indigo-100 dark:hover:bg-indigo-800">
                                    Gerir Equipes
                                </a>
                                {{-- Exemplo de futuros atalhos:
                                <a href="{{ route('admin.usuarios.index') }}" class="inline-flex ...">Usuários</a>
                                <a href="{{ route('admin.config') }}" class="inline-flex ...">Configurações</a>
                                --}}
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @include('components.toast')
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
        <script>
            (function(){
                const holder = document.createElement('div');
                holder.className = 'fixed z-50 top-4 right-4 space-y-2';
                document.addEventListener('DOMContentLoaded', ()=> document.body.appendChild(holder));

                function showToast(msg){
                    const wrap = document.createElement('div');
                    wrap.className = 'rounded-md shadow px-4 py-3 bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200';
                    wrap.textContent = msg;
                    holder.appendChild(wrap);
                    setTimeout(()=>{ wrap.remove(); }, 4000);
                }

                function getSeen(){
                    try { return JSON.parse(localStorage.getItem('notif_seen')||'[]'); } catch(e){ return []; }
                }
                function addSeen(id){
                    const seen = new Set(getSeen());
                    seen.add(id);
                    localStorage.setItem('notif_seen', JSON.stringify(Array.from(seen)));
                }
                async function updateBell(){
                    try {
                        const res = await fetch('{{ route('notifications.index') }}', { headers: { 'Accept':'application/json' } });
                        if(!res.ok) return;
                        const data = await res.json();
                        const countEl = document.getElementById('notif-count');
                        if(countEl){
                            const c = (data||[]).length;
                            countEl.textContent = c > 99 ? '99+' : String(c);
                            countEl.classList.toggle('hidden', c === 0);
                        }
                    } catch(e){}
                }
                async function poll(){
                    try {
                        const res = await fetch('{{ route('notifications.index') }}', { headers: { 'Accept':'application/json' } });
                        if(!res.ok) return;
                        const data = await res.json();
                        const seen = new Set(getSeen());
                        for(const n of data){
                            if(!seen.has(n.id)){
                                showToast(n.message);
                                addSeen(n.id);
                            }
                        }
                        updateBell();
                    } catch(e){}
                }
                setInterval(poll, 12000);
                document.addEventListener('DOMContentLoaded', ()=>{
                    updateBell();
                    const list = document.getElementById('notif-list');
                    const bell = document.getElementById('notif-bell');
                    if(bell && list){
                        bell.addEventListener('click', async ()=>{
                            try{
                                list.innerHTML = '<div class="text-gray-500 dark:text-gray-400">Carregando...</div>';
                                const res = await fetch('{{ route('notifications.index') }}', { headers: { 'Accept':'application/json' } });
                                const data = res.ok ? await res.json() : [];
                                if(!data.length){ list.innerHTML = '<div class="text-gray-500 dark:text-gray-400">Sem novas notificações.</div>'; return; }
                                list.innerHTML = '';
                                data.forEach(n=>{
                                    const item = document.createElement('div');
                                    item.className = 'px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 cursor-default';
                                    item.textContent = `${n.created_at} • ${n.message}`;
                                    list.appendChild(item);
                                });
                            }catch(e){}
                        });
                    }
                });
            })();
        </script>
    </body>
</html>
