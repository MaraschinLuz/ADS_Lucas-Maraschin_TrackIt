@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Membros da Minha Equipe</h1>
        <a href="{{ route('equipe.chat') }}" class="text-blue-600 underline">Ir para o chat</a>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Nome</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Papel</th>
                </tr>
            </thead>
            <tbody>
                @forelse($membros as $m)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $m->name }}</td>
                    <td class="px-4 py-2">{{ $m->email }}</td>
                    <td class="px-4 py-2">{{ $m->role }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-gray-500">Nenhum membro encontrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

