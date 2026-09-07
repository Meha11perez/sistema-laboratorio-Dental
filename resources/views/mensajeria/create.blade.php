@extends('layouts.app')

@section('title', 'Nueva Ruta de Mensajería')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="mb-8">

        <a
            href="{{ route('mensajeria.index') }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver a Mensajería
        </a>

        <h1 class="text-3xl font-bold text-slate-900 mt-3">
            Nueva Ruta de Mensajería
        </h1>

        <p class="text-slate-500 mt-1">
            Registre la ruta y asigne un mensajero.
        </p>

    </div>


    @if($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200
                    text-red-700 rounded-xl p-4">

            <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('mensajeria.store') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm overflow-hidden"
    >

        @csrf

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Mensajero *
                </label>

                <select
                    name="mensajero_id"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        Seleccione...
                    </option>

                    @foreach($mensajeros as $mensajero)

                        <option
                            value="{{ $mensajero->id }}"
                            @selected(old('mensajero_id') == $mensajero->id)
                        >
                            {{ $mensajero->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Fecha *
                </label>

                <input
                    type="date"
                    name="fecha"
                    value="{{ old('fecha', now()->toDateString()) }}"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3"
                >

            </div>
            <div class="md:col-span-2">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Observaciones
                </label>

                <textarea
                    name="observaciones"
                    rows="4"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 resize-none"
                >{{ old('observaciones') }}</textarea>

            </div>

        </div>


        <div class="px-6 py-5 bg-slate-50
                    border-t border-slate-200
                    flex justify-end">

            <button
                type="submit"
                class="px-6 py-3 bg-blue-800
                       hover:bg-blue-900 text-white
                       rounded-lg font-semibold"
            >
                Guardar Ruta
            </button>

        </div>

    </form>

</div>

@endsection