@php($user = auth()->user())
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Chat da Equipe</h1>
        <div class="text-sm text-gray-500">Você: {{ $user->name }}</div>
    </div>

    <div id="chat-box" class="border rounded p-3 h-96 overflow-y-auto bg-white"></div>

    <form id="chat-form" class="mt-3 flex gap-2">
        <input type="text" id="msg" name="mensagem" class="flex-1 border rounded px-3 py-2" placeholder="Digite sua mensagem..." required maxlength="2000">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Enviar</button>
    </form>
</div>

<script>
    const box = document.getElementById('chat-box');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('msg');
    let lastId = 0;

    function render(messages) {
        if (!Array.isArray(messages)) return;
        messages.forEach(m => {
            const wrap = document.createElement('div');
            wrap.className = 'mb-2';
            wrap.innerHTML = `<div class="text-sm text-gray-500">${m.created_at} • ${m.user}</div>
                              <div class="text-gray-900">${escapeHtml(m.mensagem)}</div>`;
            box.appendChild(wrap);
            lastId = Math.max(lastId, m.id);
        });
        box.scrollTop = box.scrollHeight;
    }

    function escapeHtml(s) {
        return s
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;');
    }

    async function fetchMessages() {
        try {
            const url = lastId > 0 ? `{{ route('equipe.chat.list') }}?after_id=${lastId}` : `{{ route('equipe.chat.list') }}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (res.ok) {
                const data = await res.json();
                render(data);
            }
        } catch (e) { /* silencioso */ }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = input.value.trim();
        if (!msg) return;
        input.value = '';
        try {
            const res = await fetch(`{{ route('equipe.chat.store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mensagem: msg })
            });
            if (res.ok) {
                const data = await res.json();
                render([data]);
            }
        } catch (e) { /* silencioso */ }
    });

    // Carrega histórico inicial e inicia polling
    (async () => {
        await fetchMessages();
        setInterval(fetchMessages, 4000);
    })();
</script>
@endsection

