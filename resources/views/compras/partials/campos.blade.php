@php
    $actual = isset($compra) ? $compra->instantanea() : [];
    $iniciales = array_map(fn ($d) => ['tipo'=>$d['tipo'], 'material_id'=>$d['material_id'] ?? (!empty($d['material_solicitado']) ? 'nuevo' : ''), 'material_nombre'=>$d['material_solicitado'] ?? '', 'material_unidad'=>$d['unidad'], 'descripcion'=>$d['descripcion'], 'cantidad'=>\App\Support\ImporteCajaChica::decimal($d['cantidad_centesimas']), 'precio'=>\App\Support\ImporteCajaChica::decimal($d['precio_centavos'])], $actual['detalles'] ?? []);
    $lineas = old('detalles', $iniciales ?: [['tipo'=>'Material', 'material_id'=>'', 'descripcion'=>'', 'cantidad'=>'1', 'precio'=>'']]);
@endphp
<div class="cc-grid">
    @foreach(['fecha'=>'Fecha de factura', 'proveedor'=>'Proveedor', 'nit'=>'NIT del proveedor', 'serie'=>'Serie (si aparece)', 'numero_factura'=>'Número de factura'] as $campo=>$etiqueta)
    <div><label for="{{ $campo }}">{{ $etiqueta }}{{ $campo !== 'serie' ? ' *' : '' }}</label><input id="{{ $campo }}" name="{{ $campo }}" type="{{ $campo === 'fecha' ? 'date' : 'text' }}" value="{{ old($campo, $actual[$campo] ?? ($campo === 'fecha' ? now('America/Guatemala')->toDateString() : '')) }}" maxlength="{{ ['proveedor'=>150,'nit'=>30,'serie'=>50,'numero_factura'=>100][$campo] ?? 10 }}" @required($campo !== 'serie') @if($campo === 'fecha') max="{{ now('America/Guatemala')->toDateString() }}" @endif></div>
    @endforeach
    <div><label for="metodo_pago">Método de pago informado *</label><select id="metodo_pago" name="metodo_pago" required>@foreach(['Efectivo','Transferencia','Depósito','Cheque','Otro'] as $metodo)<option @selected(old('metodo_pago', $actual['metodo_pago'] ?? '') === $metodo)>{{ $metodo }}</option>@endforeach</select></div>
    <div><label for="referencia">Referencia de transferencia, depósito o cheque</label><input id="referencia" name="referencia" value="{{ old('referencia', $actual['referencia'] ?? '') }}" maxlength="150"></div>
    <div><label for="factura">{{ isset($compra) ? 'Reemplazar factura (opcional)' : 'Adjuntar factura *' }}</label><input id="factura" type="file" name="factura" accept=".pdf,.jpg,.jpeg,.png" @required(!isset($compra))><p class="cc-muted">PDF, JPG o PNG; hasta 5 MB. Se conserva la evidencia anterior.</p></div>
</div>
<p class="cc-muted my-4">No vuelva a registrar esta factura como gasto de Caja chica. El método informado no crea pagos ni abonos de odontólogos.</p>
@if(isset($compra) && $compra->fecha_recepcion)<p class="cc-notice cc-notice-warning my-4">Esta compra ya fue recibida. Conserve el orden de las líneas, sus tipos, materiales y cantidades. Puede solicitar corregir precios, descripciones y datos de factura.</p>@endif
<div data-compra-lineas>
    @foreach($lineas as $indice=>$linea)
        @include('compras.partials.linea', ['indice'=>$indice, 'linea'=>$linea])
    @endforeach
</div>
<button type="button" class="cc-btn my-4" data-compra-agregar @disabled(isset($compra) && $compra->fecha_recepcion)>Agregar material o servicio</button>
<p class="my-4"><strong>Total calculado: <span data-compra-total>Q 0.00</span></strong></p>
<div><label for="observaciones">Observaciones</label><textarea id="observaciones" name="observaciones" rows="3" maxlength="1000">{{ old('observaciones', $actual['observaciones'] ?? '') }}</textarea></div>
<template id="compra-linea-nueva">@include('compras.partials.linea', ['indice'=>'__I__', 'linea'=>['tipo'=>'Material','material_id'=>'','descripcion'=>'','cantidad'=>'1','precio'=>'']])</template>
<script src="{{ asset('js/compras.js') }}" defer></script>
