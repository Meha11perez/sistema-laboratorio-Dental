@extends('layouts.app')

@section('title', 'Nuevo Material | Laboratorio Dental')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <a
            href="{{ route('inventario.index') }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver al inventario
        </a>

        <h1 class="text-3xl font-bold text-slate-900 mt-3">
            Nuevo Material
        </h1>

        <p class="text-slate-500 mt-1">
            Registre un nuevo material para el control de inventario.
        </p>

    </div>


    {{-- ERRORES --}}
    @if ($errors->any())

        <div class="mb-6 bg-red-50 border border-red-200
                    text-red-700 rounded-lg px-5 py-4">

            <p class="font-semibold mb-2">
                Revise los siguientes campos:
            </p>

            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- FORMULARIO --}}
    <form
        method="POST"
        action="{{ route('inventario.store') }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm p-6 md:p-8"
    >

        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- CÓDIGO --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Código *
                </label>

                <input
                    type="text"
                    name="codigo"
                    value="{{ old('codigo') }}"
                    placeholder="Ej. MAT-001"
                    required
                    class="w-full border border-slate-300 rounded-lg
                           px-4 py-3 focus:ring-2 focus:ring-blue-500
                           focus:border-blue-500 outline-none"
                >
            </div>


            {{-- NOMBRE --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Nombre del material *
                </label>

                <input
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    placeholder="Ej. Yeso dental"
                    required
                    class="w-full border border-slate-300 rounded-lg
                           px-4 py-3 focus:ring-2 focus:ring-blue-500
                           focus:border-blue-500 outline-none"
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
                    class="w-full border border-slate-300 rounded-lg
                           px-4 py-3 bg-white"
                >
                    <option value="">Seleccione...</option>

                    <option value="Unidad" @selected(old('unidad_medida') === 'Unidad')>
                        Unidad
                    </option>

                    <option value="Gramo" @selected(old('unidad_medida') === 'Gramo')>
                        Gramo
                    </option>

                    <option value="Kilogramo" @selected(old('unidad_medida') === 'Kilogramo')>
                        Kilogramo
                    </option>

                    <option value="Mililitro" @selected(old('unidad_medida') === 'Mililitro')>
                        Mililitro
                    </option>

                    <option value="Litro" @selected(old('unidad_medida') === 'Litro')>
                        Litro
                    </option>

                    <option value="Caja" @selected(old('unidad_medida') === 'Caja')>
                        Caja
                    </option>

                    <option value="Paquete" @selected(old('unidad_medida') === 'Paquete')>
                        Paquete
                    </option>
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
                        value="{{ old('costo_unitario', 0) }}"
                        min="0"
                        step="0.01"
                        required
                        class="w-full border border-slate-300 rounded-lg
                               pl-9 pr-4 py-3"
                    >

                </div>
            </div>


            {{-- STOCK ACTUAL --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Stock inicial *
                </label>

                <input
                    type="number"
                    name="stock_actual"
                    value="{{ old('stock_actual', 0) }}"
                    min="0"
                    step="0.01"
                    required
                    class="w-full border border-slate-300 rounded-lg px-4 py-3"
                >
            </div>


            {{-- STOCK MÍNIMO --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Stock mínimo *
                </label>

                <input
                    type="number"
                    name="stock_minimo"
                    value="{{ old('stock_minimo', 0) }}"
                    min="0"
                    step="0.01"
                    required
                    class="w-full border border-slate-300 rounded-lg px-4 py-3"
                >
            </div>


            {{-- DESCRIPCIÓN --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Descripción
                </label>

                <textarea
                    name="descripcion"
                    rows="4"
                    placeholder="Descripción u observaciones del material..."
                    class="w-full border border-slate-300 rounded-lg
                           px-4 py-3 resize-none"
                >{{ old('descripcion') }}</textarea>

            </div>

        </div>


        {{-- BOTONES --}}
        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-200">

            <a
                href="{{ route('inventario.index') }}"
                class="px-5 py-3 border border-slate-300
                       text-slate-600 rounded-lg font-semibold"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="px-6 py-3 bg-blue-800 hover:bg-blue-900
                       text-white rounded-lg font-semibold"
            >
                Guardar Material
            </button>

        </div>

    </form>

</div>

@endsection