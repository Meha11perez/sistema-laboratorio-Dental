@extends('layouts.app')

@section('title', 'Cuenta del Odontólogo | Laboratorio Dental')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- ENCABEZADO --}}
    <div class="mb-8">

        <a
            href="{{ route('pagos.index') }}"
            class="text-sm font-semibold text-blue-700 hover:underline"
        >
            ← Volver a Pagos y Créditos
        </a>

        <h1 class="text-3xl font-bold text-slate-900 mt-3">
            Cuenta del Odontólogo
        </h1>

        <p class="text-slate-500 mt-1">
            Configure la modalidad de pago y crédito.
        </p>

    </div>


    {{-- ODONTÓLOGO --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">

        <p class="text-xs uppercase font-semibold text-slate-400">
            Odontólogo
        </p>

        <p class="text-xl font-bold text-slate-900 mt-1">
            {{ $odontologo->nombre }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Saldo pendiente actual
                </p>

                <p class="text-lg font-bold text-amber-700 mt-1">
                    Q {{ number_format($cuenta->saldo_pendiente, 2) }}
                </p>
            </div>


            <div>
                <p class="text-xs uppercase font-semibold text-slate-400">
                    Estado
                </p>

                <p class="font-semibold mt-1">
                    {{ $cuenta->estado ? 'Activa' : 'Inactiva' }}
                </p>
            </div>

        </div>

    </div>


    @if(session('success'))

        <div class="mb-6 bg-emerald-50 border border-emerald-200
                    text-emerald-700 rounded-xl px-5 py-4">
            {{ session('success') }}
        </div>

    @endif


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
        action="{{ route('cuentas-odontologos.update', $odontologo) }}"
        class="bg-white border border-slate-200
               rounded-xl shadow-sm overflow-hidden"
    >

        @csrf
        @method('PUT')


        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- MODALIDAD --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Modalidad de pago *
                </label>

                <select
                    name="modalidad_pago"
                    id="modalidad_pago"
                    required
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 bg-white"
                >

                    <option
                        value="Contado"
                        @selected(
                            old('modalidad_pago', $cuenta->modalidad_pago)
                            === 'Contado'
                        )
                    >
                        Contado
                    </option>

                    <option
                        value="Semanal"
                        @selected(
                            old('modalidad_pago', $cuenta->modalidad_pago)
                            === 'Semanal'
                        )
                    >
                        Semanal
                    </option>

                    <option
                        value="Crédito"
                        @selected(
                            old('modalidad_pago', $cuenta->modalidad_pago)
                            === 'Crédito'
                        )
                    >
                        Crédito
                    </option>

                </select>

            </div>


            {{-- LÍMITE --}}
            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Límite de crédito
                </label>

                <div class="relative">

                    <span class="absolute left-4 top-3 text-slate-500">
                        Q
                    </span>

                    <input
                        type="number"
                        name="limite_credito"
                        value="{{ old(
                            'limite_credito',
                            $cuenta->limite_credito
                        ) }}"
                        min="0"
                        step="0.01"
                        class="w-full border border-slate-300
                               rounded-lg pl-9 pr-4 py-3"
                    >

                </div>

                <p class="text-xs text-slate-400 mt-2">
                    Se utiliza únicamente cuando la modalidad es Crédito.
                </p>

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
                        @selected(
                            old('estado', $cuenta->estado ? '1' : '0') === '1'
                        )
                    >
                        Activa
                    </option>

                    <option
                        value="0"
                        @selected(
                            old('estado', $cuenta->estado ? '1' : '0') === '0'
                        )
                    >
                        Inactiva
                    </option>

                </select>

            </div>


            {{-- OBSERVACIONES --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Observaciones
                </label>

                <textarea
                    name="observaciones"
                    rows="4"
                    class="w-full border border-slate-300
                           rounded-lg px-4 py-3 resize-none"
                >{{ old('observaciones', $cuenta->observaciones) }}</textarea>

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
                Guardar Configuración
            </button>

        </div>

    </form>

</div>

@endsection