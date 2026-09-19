@extends('layouts.app')

@section('title', 'Asignar Material | Laboratorio Dental')

@section('content')

<div class="max-w-2xl mx-auto">

    <a
        href="{{ route('inventario.show', $material) }}"
        class="text-sm font-semibold text-blue-700 hover:underline"
    >
        ← Volver al material
    </a>


    <div class="bg-white border border-slate-200
                rounded-xl shadow-sm mt-5 overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200">

            <h1 class="text-xl font-bold text-slate-900">
                Asignar material a técnico
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                {{ $material->nombre }}
            </p>

        </div>


        <div class="p-6">

            <div class="bg-slate-50 rounded-lg p-4 mb-6">

                <p class="text-xs uppercase font-semibold text-slate-400">
                    Stock general disponible
                </p>

                <p class="text-2xl font-bold text-slate-900 mt-1">
                    {{ number_format($material->stock_actual, 2) }}
                    {{ $material->unidad_medida }}
                </p>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'inventario.asignar.store',
                    $material
                ) }}"
                class="space-y-5"
            >

                @csrf


                <div>

                    <label class="block text-sm font-semibold mb-2">
                        Técnico
                    </label>

                    <select
                        name="tecnico_id"
                        required
                        class="w-full rounded-lg border-slate-300"
                    >

                        <option value="">
                            Seleccione...
                        </option>

                        @foreach($tecnicos as $tecnico)

                            <option
                                value="{{ $tecnico->id }}"
                                {{ old('tecnico_id') == $tecnico->id
                                    ? 'selected'
                                    : ''
                                }}
                            >
                                {{ $tecnico->user?->name }}

                                @if($tecnico->especialidad)
                                    — {{ $tecnico->especialidad }}
                                @endif
                            </option>

                        @endforeach

                    </select>

                    @error('tecnico_id')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div>

                    <label class="block text-sm font-semibold mb-2">
                        Cantidad a asignar
                    </label>

                    <input
                        type="number"
                        name="cantidad"
                        min="0.01"
                        step="0.01"
                        value="{{ old('cantidad') }}"
                        required
                        class="w-full rounded-lg border-slate-300"
                    >

                    @error('cantidad')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div>

                    <label class="block text-sm font-semibold mb-2">
                        Observaciones
                    </label>

                    <textarea
                        name="observaciones"
                        rows="3"
                        class="w-full rounded-lg border-slate-300"
                        placeholder="Opcional..."
                    >{{ old('observaciones') }}</textarea>

                </div>


                <div class="flex justify-end">

                    <button
                        type="submit"
                        class="px-5 py-2.5
                               bg-blue-700
                               hover:bg-blue-800
                               text-white
                               rounded-lg
                               font-semibold"
                    >
                        Asignar material
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection