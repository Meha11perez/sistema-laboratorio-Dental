@extends('layouts.app')
@section('title', 'Registrar gasto | Caja chica')
@section('content')
@include('caja_chica.partials.estilos')
<div class="cc" style="max-width:1000px">
    <header class="cc-header"><div><p class="cc-kicker">Caja chica</p><h1>Registrar gasto</h1><p class="cc-muted mt-2"></p></div><a class="cc-btn" href="{{ route('caja-chica.index') }}">Volver</a></header>
    @include('caja_chica.partials.mensajes')
    <p class="cc-notice">El límite mensual es <strong>Q5,000.00</strong></p>
    <form action="{{ route('caja-chica.store') }}" method="POST" enctype="multipart/form-data" class="cc-card cc-padding" data-envio-caja>
        @csrf
        <input type="hidden" name="clave_registro" value="{{ old('clave_registro', (string) \Illuminate\Support\Str::uuid()) }}">
        @include('caja_chica.partials.campos')
        <div class="cc-footer"><a class="cc-btn" href="{{ route('caja-chica.index') }}">Cancelar</a><button type="submit" class="cc-btn cc-btn-primary">Guardar gasto</button></div>
    </form>
</div>
@include('caja_chica.partials.envio')
@endsection
