@extends('layouts.app')
@section('title', 'Solicitar corrección de compra')
@section('content')
<div class="cc compras">@include('compras.partials.cabecera', ['titulo'=>'Solicitar corrección de compra #'.$compra->id])
    <p class="cc-muted mb-4">Los cambios se aplican únicamente con aprobación del Administrador. No existe edición directa del registro.</p>
    <form class="cc-card" action="{{ route('compras.solicitar', $compra) }}" method="POST" enctype="multipart/form-data" data-compra-form>
        @csrf<input type="hidden" name="tipo" value="Corregir"><input type="hidden" name="version_compra" value="{{ $compra->version }}"><input type="hidden" name="clave_registro" value="{{ old('clave_registro', (string) \Illuminate\Support\Str::uuid()) }}">
        @include('compras.partials.campos')
        <div class="mt-4"><label for="motivo">Motivo de la solicitud *</label><textarea id="motivo" name="motivo" minlength="5" maxlength="1000" required>{{ old('motivo') }}</textarea></div>
        <div class="cc-actions mt-5"><a class="cc-btn" href="{{ route('compras.show', $compra) }}">Cancelar</a><button class="cc-btn cc-btn-primary">Enviar solicitud</button></div>
    </form>
</div>
@endsection
