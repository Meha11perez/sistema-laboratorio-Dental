@extends('layouts.app')

@section('title', 'Cancelar Orden | Laboratorio Dental')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="mb-8">

        <p class="text-sm font-semibold text-red-700">
            CANCELACIÓN DE ORDEN
        </p>

        <h1 class="text-3xl font-bold text-slate-900 mt-1">
            {{ $orden->codigo }}
        </h1>

        <p class="text-slate-500 mt-2">
            Esta acción detendrá el proceso de producción de la orden,
            pero conservará toda su información e historial.
        </p>

    </div>


    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="bg-red-50 border-b border-red-200 px-6 py-4">

            <p class="text-red-700 font-semibold">
                ⚠ La orden quedará marcada como Cancelada.
            </p>

            <p class="text-red-600 text-sm mt-1">
                No podrá continuar editándose después de confirmar.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('ordenes.cancelar', $orden) }}"
        >

            @csrf
            @method('PATCH')


            <div class="p-6 space-y-6">

                <div>

                    <label
                        for="motivo"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Motivo de cancelación *
                    </label>

                    <select
                        name="motivo"
                        id="motivo"
                        required
                        class="w-full border border-slate-300 rounded-lg
                               px-4 py-3 bg-white"
                    >

                        <option value="">
                            Seleccione un motivo...
                        </option>

                        <option value="odontologo">
                            Cancelación solicitada por odontólogo
                        </option>

                        <option value="paciente">
                            Cancelación solicitada por paciente
                        </option>

                        <option value="error">
                            Orden registrada por error
                        </option>

                        <option value="duplicada">
                            Orden duplicada
                        </option>

                        <option value="imposibilidad">
                            Imposibilidad de continuar
                        </option>

                        <option value="otro">
                            Otro
                        </option>

                    </select>

                    @error('motivo')
                        <p class="text-red-600 text-sm mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div>

                    <label
                        for="observaciones"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Observaciones
                    </label>

                    <textarea
                        name="observaciones"
                        id="observaciones"
                        rows="4"
                        placeholder="Describa información adicional sobre la cancelación..."
                        class="w-full border border-slate-300
                               rounded-lg px-4 py-3 resize-none"
                    >{{ old('observaciones') }}</textarea>

                </div>

            </div>


            <div class="px-6 py-5 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">

                <a
                    href="{{ route('ordenes.show', $orden) }}"
                    class="px-5 py-3 border border-slate-300
                           rounded-lg text-slate-700 font-semibold"
                >
                    Volver
                </a>

                <button
                    type="submit"
                    class="px-6 py-3 bg-red-600 hover:bg-red-700
                           text-white rounded-lg font-semibold"
                >
                    Confirmar Cancelación
                </button>

            </div>

        </form>

    </div>

</div>

@endsection