@extends('layouts.app')

@section('title', 'Movimiento de Inventario')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="mb-8">

        <a
            href="{{ route('inventario.show', $material) }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver al material
        </a>

        <h1 class="text-3xl font-bold text-slate-900 mt-3">
            Movimiento de Inventario
        </h1>

        <p class="text-slate-500 mt-1">
            {{ $material->codigo }} — {{ $material->nombre }}
        </p>

    </div>


    <div class="grid grid-cols-2 gap-4 mb-6">

        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="text-xs uppercase font-semibold text-slate-400">
                Stock actual
            </p>

            <p class="text-3xl font-bold mt-2">
                {{ number_format($material->stock_actual, 2) }}
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-5">
            <p class="text-xs uppercase font-semibold text-slate-400">
                Stock mínimo
            </p>

            <p class="text-3xl font-bold mt-2">
                {{ number_format($material->stock_minimo, 2) }}
            </p>
        </div>

    </div>


    @if ($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">

            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('inventario.movimiento.store', $material) }}"
        class="bg-white border border-slate-200 rounded-xl shadow-sm p-6"
    >

        @csrf

        <div class="space-y-6">

            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Tipo de movimiento *
                </label>

                <select
                    name="tipo_movimiento"
                    required
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                >

                    <option value="">
                        Seleccione...
                    </option>

                    <option value="Entrada" @selected(old('tipo_movimiento') === 'Entrada')>
                        Entrada
                    </option>

                    <option value="Salida" @selected(old('tipo_movimiento') === 'Salida')>
                        Salida
                    </option>

                    <option value="Devolución" @selected(old('tipo_movimiento') === 'Devolución')>
                        Devolución
                    </option>

                    <option value="Ajuste" @selected(old('tipo_movimiento') === 'Ajuste')>
                        Ajuste
                    </option>

                </select>

            </div>


            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Cantidad *
                </label>

                <input
                    type="number"
                    name="cantidad"
                    value="{{ old('cantidad') }}"
                    min="0.01"
                    step="0.01"
                    required
                    class="w-full border border-slate-300 rounded-lg px-4 py-3"
                >

                <p class="text-xs text-slate-400 mt-2">
                    En un ajuste, la cantidad indicada será el nuevo stock total.
                </p>

            </div>


            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Observaciones
                </label>

                <textarea
                    name="observaciones"
                    rows="4"
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 resize-none"
                    placeholder="Motivo o detalle del movimiento..."
                >{{ old('observaciones') }}</textarea>

            </div>

        </div>


        <div class="flex justify-end gap-3 mt-8 pt-6 border-t">

            <a
                href="{{ route('inventario.show', $material) }}"
                class="px-5 py-3 border border-slate-300 rounded-lg font-semibold"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="px-6 py-3 bg-blue-800 hover:bg-blue-900 text-white rounded-lg font-semibold"
            >
                Registrar Movimiento
            </button>

        </div>

    </form>

</div>

@endsection