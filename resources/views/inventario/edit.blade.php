@extends('layouts.app')

@section('title', 'Editar Material | Laboratorio Dental')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <a
            href="{{ route('inventario.show', $material) }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver al material
        </a>

        <h1 class="text-3xl font-bold text-slate-900 mt-3">
            Editar Material
        </h1>

        <p class="text-slate-500 mt-1">
            Actualice la información general del material.
        </p>

    </div>


    {{-- ERRORES --}}
    @if ($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200
                    rounded-xl p-4 text-red-700">

            <p class="font-semibold mb-2">
                Revise los siguientes campos:
            </p>

            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('inventario.update', $material) }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm overflow-hidden"
    >

        @csrf
        @method('PUT')


        {{-- INFORMACIÓN --}}
        <div class="p-6 md:p-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- CÓDIGO --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Código *
                    </label>

                    <input
                        type="text"
                        name="codigo"
                        value="{{ old('codigo', $material->codigo) }}"
                        required
                        class="w-full border border-slate-300
                               rounded-lg px-4 py-3"
                    >

                </div>


                {{-- NOMBRE --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        value="{{ old('nombre', $material->nombre) }}"
                        required
                        class="w-full border border-slate-300
                               rounded-lg px-4 py-3"
                    >

                </div>


                {{-- UNIDAD --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Unidad de medida *
                    </label>

                    <select
                        name="unidad_medida"
                        required
                        class="w-full border border-slate-300
                               rounded-lg px-4 py-3 bg-white"
                    >

                        @foreach([
                            'Unidad',
                            'Gramo',
                            'Kilogramo',
                            'Mililitro',
                            'Litro',
                            'Caja',
                            'Paquete'
                        ] as $unidad)

                            <option
                                value="{{ $unidad }}"
                                @selected(
                                    old('unidad_medida', $material->unidad_medida)
                                    === $unidad
                                )
                            >
                                {{ $unidad }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- COSTO --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Costo unitario *
                    </label>

                    <div class="relative">

                        <span class="absolute left-4 top-3 text-slate-500">
                            Q
                        </span>

                        <input
                            type="number"
                            name="costo_unitario"
                            value="{{ old('costo_unitario', $material->costo_unitario) }}"
                            min="0"
                            step="0.01"
                            required
                            class="w-full border border-slate-300
                                   rounded-lg pl-9 pr-4 py-3"
                        >

                    </div>

                </div>


                {{-- STOCK ACTUAL --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Stock actual
                    </label>

                    <input
                        type="text"
                        value="{{ number_format($material->stock_actual, 2) }}"
                        disabled
                        class="w-full bg-slate-100 border border-slate-300
                               rounded-lg px-4 py-3 text-slate-500"
                    >

                    <p class="text-xs text-slate-400 mt-2">
                        El stock solo puede cambiar mediante movimientos de inventario.
                    </p>

                </div>


                {{-- STOCK MÍNIMO --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Stock mínimo *
                    </label>

                    <input
                        type="number"
                        name="stock_minimo"
                        value="{{ old('stock_minimo', $material->stock_minimo) }}"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border border-slate-300
                               rounded-lg px-4 py-3"
                    >

                </div>


                {{-- ESTADO --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Estado *
                    </label>

                    <select
                        name="estado"
                        required
                        class="w-full border border-slate-300
                               rounded-lg px-4 py-3 bg-white"
                    >

                        <option
                            value="1"
                            @selected(old('estado', $material->estado) == 1)
                        >
                            Activo
                        </option>

                        <option
                            value="0"
                            @selected(old('estado', $material->estado) == 0)
                        >
                            Inactivo
                        </option>

                    </select>

                </div>


                {{-- DESCRIPCIÓN --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        rows="4"
                        class="w-full border border-slate-300
                               rounded-lg px-4 py-3 resize-none"
                    >{{ old('descripcion', $material->descripcion) }}</textarea>

                </div>

            </div>

        </div>


        {{-- BOTONES --}}
        <div class="bg-slate-50 border-t border-slate-200
                    px-6 py-5 flex justify-end gap-3">

            <a
                href="{{ route('inventario.show', $material) }}"
                class="px-5 py-3 border border-slate-300
                       rounded-lg font-semibold text-slate-600"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="px-6 py-3 bg-blue-800
                       hover:bg-blue-900 text-white
                       rounded-lg font-semibold"
            >
                Guardar Cambios
            </button>

        </div>

    </form>

</div>

@endsection