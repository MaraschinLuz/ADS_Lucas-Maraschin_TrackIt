<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Chamado') }} #{{ $chamado->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Título -->
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ __('Título:') }}</h3>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $chamado->titulo }}</p>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ __('Descrição:') }}</h3>
                        <p class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $chamado->descricao }}</p>
                    </div>

                    <!-- Prioridade -->
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ __('Prioridade:') }}</h3>
                        <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full @if($chamado->prioridade == 'alta') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @elseif($chamado->prioridade == 'media') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @endif">
                            {{ ucfirst($chamado->prioridade) }}
                        </span>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ __('Status:') }}</h3>
                        <x-status-badge :value="$chamado->status" />
                    </div>

                    <!-- Equipe Responsável -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ __('Equipe Responsável:') }}</h3>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">
                            {{ $chamado->equipe ? $chamado->equipe->nome : __('Não atribuída') }}
                        </p>
        </div>

        <!-- Histórico de Anexos -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ __('Histórico de Anexos') }}</h3>

                        @if ($chamado->anexos->isEmpty())
                            <p>{{ __('Nenhum anexo ainda.') }}</p>
                        @else
                            <div class="flex flex-wrap gap-3">
                                @foreach($chamado->anexos as $anexo)
                                    @php
                                        $ext = pathinfo($anexo->arquivo, PATHINFO_EXTENSION);
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                        $isImage = in_array(strtolower($ext), $imageExtensions);
                                    @endphp

                                    <a href="{{ asset('storage/' . $anexo->arquivo) }}" target="_blank" class="block" title="Baixar anexo">
                                        @if ($isImage)
                                            <img src="{{ asset('storage/' . $anexo->arquivo) }}" alt="Anexo" class="w-16 h-16 object-cover rounded border border-gray-300 shadow-sm hover:opacity-80 transition" />
                                        @else
                                            <div class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">
                                                {{ basename($anexo->arquivo) }}
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
        @endif

    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isTecnico())
    <div class="mt-6 p-4 bg-white dark:bg-gray-800 rounded shadow">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Notas internas</h3>
            <form method="POST" action="{{ route('chamados.' . ($chamado->seguidores->contains(auth()->id()) ? 'unfollow' : 'follow'), $chamado) }}">
                @csrf
                @if($chamado->seguidores->contains(auth()->id()))
                    @method('DELETE')
                    <button class="text-sm px-3 py-1.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">Deixar de seguir</button>
                @else
                    <button class="text-sm px-3 py-1.5 rounded bg-indigo-600 text-white">Seguir chamado</button>
                @endif
            </form>
        </div>
        <form method="POST" action="{{ route('chamados.notas.store', $chamado) }}" class="mb-3">
            @csrf
            <textarea name="nota" rows="3" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Adicionar nota interna..."></textarea>
            <div class="mt-2 text-right">
                <button class="px-4 py-2 rounded bg-indigo-600 text-white">Salvar nota</button>
            </div>
        </form>
        <div class="space-y-2">
            @forelse($chamado->notasInternas as $n)
                <div class="p-3 rounded border border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $n->created_at->format('d/m/Y H:i') }} • {{ $n->user->name }}</div>
                    <div class="text-gray-800 dark:text-gray-100">{{ $n->nota }}</div>
                    @if(auth()->user()->isAdmin() || auth()->id()===$n->user_id)
                        <form method="POST" action="{{ route('chamados.notas.delete', [$chamado, $n]) }}" class="mt-1 text-right">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-rose-600">Excluir</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="text-sm text-gray-500">Nenhuma nota interna.</div>
            @endforelse
        </div>
    </div>
    @endif

                    <!-- Botões -->
                    <div class="flex justify-between items-center mt-6">
                        <a href="{{ route('chamados.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:bg-gray-400 dark:focus:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Voltar para a Lista') }}
                        </a>
                        <a href="{{ route('chamados.edit', $chamado) }}"
                           class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Editar Chamado') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview para arquivo anexo selecionado (imagem mostra miniatura)
        const inputArquivo = document.getElementById('arquivo');
        if (inputArquivo) {
            inputArquivo.addEventListener('change', function(event) {
                const preview = document.getElementById('preview');
                if (!preview) return;
                preview.innerHTML = '';

                const file = event.target.files[0];
                if (!file) return;

                const imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp'];

                if (imageTypes.includes(file.type)) {
                    const img = document.createElement('img');
                    img.style.maxWidth = '80px';
                    img.style.maxHeight = '80px';
                    img.style.borderRadius = '4px';
                    img.style.boxShadow = '0 0 5px rgba(0,0,0,0.2)';
                    img.alt = 'Pré-visualização do anexo';
                    img.src = URL.createObjectURL(file);
                    preview.appendChild(img);
                } else {
                    const p = document.createElement('p');
                    p.textContent = `Arquivo selecionado: ${file.name}`;
                    preview.appendChild(p);
                }
            });
        }
    </script>
</x-app-layout>
