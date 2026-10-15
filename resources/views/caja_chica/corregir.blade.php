@extends('layouts.app')
@section('title', 'Solicitar corrección | Caja chica')
@section('content')
@include('caja_chica.partials.estilos')
<div class="cc" style="max-width:1000px">
    <header class="cc-header"><div><p class="cc-kicker">Gasto #{{ $gasto->id }}</p><h1>Solicitar corrección</h1></div><a class="cc-btn" href="{{ route('caja-chica.show', $gasto) }}">Volver al gasto</a></header>
    @include('caja_chica.partials.mensajes')
    <p class="cc-notice">El gasto y sus totales conservarán sus valores actuales hasta que el Administrador apruebe la solicitud. El historial guardará los valores anteriores, los propuestos y la decisión.</p>
    <form action="{{ route('caja-chica.correcciones.store', $gasto) }}" method="POST" enctype="multipart/form-data" class="cc-card cc-padding" data-envio-caja>
        @csrf
        <input type="hidden" name="clave_registro" value="{{ old('clave_registro', (string) \Illuminate\Support\Str::uuid()) }}">
        <input type="hidden" name="version_gasto" value="{{ old('version_gasto', $gasto->version) }}">
        <div class="mb-5"><label for="tipo">Tipo de solicitud *</label><select id="tipo" name="tipo" required><option value="Corregir" @selected(old('tipo', 'Corregir') === 'Corregir')>Corregir información del gasto</option><option value="Anular" @selected(old('tipo') === 'Anular')>Solicitar anulación del gasto</option></select></div>
        <fieldset id="campos_correccion" @disabled(old('tipo') === 'Anular') @if(old('tipo') === 'Anular') hidden @endif>
            <legend class="sr-only">Valores propuestos para el gasto</legend>
            @include('caja_chica.partials.campos')
        </fieldset>
        <div class="mt-5"><label for="motivo_solicitud">Motivo de la solicitud *</label><textarea id="motivo_solicitud" name="motivo_solicitud" required rows="3" maxlength="1000" placeholder="Explique qué se registró por error y por qué debe corregirse o anularse.">{{ old('motivo_solicitud') }}</textarea></div>
        <div class="cc-footer"><a class="cc-btn" href="{{ route('caja-chica.show', $gasto) }}">Cancelar</a><button type="submit" class="cc-btn cc-btn-primary">Enviar solicitud al Administrador</button></div>
    </form>
</div>
<script>
(() => {
    const tipo = document.getElementById('tipo');
    const campos = document.getElementById('campos_correccion');
    const actualizar = () => { campos.hidden = tipo.value === 'Anular'; campos.disabled = campos.hidden; };
    tipo.addEventListener('change', actualizar);
    actualizar();
})();
</script>
@include('caja_chica.partials.envio')
@endsection
