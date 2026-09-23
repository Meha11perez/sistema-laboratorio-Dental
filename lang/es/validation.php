<?php

return [

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other sea :value.',
    'active_url' => 'El campo :attribute debe contener una URL válida.',

    'after' => 'El campo :attribute debe contener una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe contener una fecha posterior o igual a :date.',

    'alpha' => 'El campo :attribute solo puede contener letras.',
    'alpha_dash' => 'El campo :attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo puede contener letras y números.',

    'array' => 'El campo :attribute debe ser un arreglo.',

    'before' => 'El campo :attribute debe contener una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe contener una fecha anterior o igual a :date.',

    'between' => [
        'array' => 'El campo :attribute debe contener entre :min y :max elementos.',
        'file' => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe contener entre :min y :max caracteres.',
    ],

    'boolean' => 'El campo :attribute debe ser verdadero o falso.',

    'confirmed' => 'La confirmación de :attribute no coincide.',

    'current_password' => 'La contraseña es incorrecta.',

    'date' => 'El campo :attribute debe contener una fecha válida.',

    'date_equals' =>
        'El campo :attribute debe contener una fecha igual a :date.',

    'date_format' =>
        'El campo :attribute no tiene el formato :format.',

    'decimal' =>
        'El campo :attribute debe tener :decimal decimales.',

    'different' =>
        'Los campos :attribute y :other deben ser diferentes.',

    'digits' =>
        'El campo :attribute debe contener :digits dígitos.',

    'digits_between' =>
        'El campo :attribute debe contener entre :min y :max dígitos.',

    'distinct' =>
        'El campo :attribute contiene un valor duplicado.',

    'email' =>
        'El campo :attribute debe contener un correo electrónico válido.',

    'exists' =>
        'El valor seleccionado para :attribute no es válido.',

    'file' =>
        'El campo :attribute debe ser un archivo.',

    'filled' =>
        'El campo :attribute debe contener un valor.',

    'image' =>
        'El campo :attribute debe ser una imagen.',

    'in' =>
        'El valor seleccionado para :attribute no es válido.',

    'integer' =>
        'El campo :attribute debe ser un número entero.',

    'json' =>
        'El campo :attribute debe contener una cadena JSON válida.',

    'max' => [
        'array' =>
            'El campo :attribute no puede contener más de :max elementos.',

        'file' =>
            'El archivo :attribute no puede pesar más de :max kilobytes.',

        'numeric' =>
            'El campo :attribute no puede ser mayor que :max.',

        'string' =>
            'El campo :attribute no puede contener más de :max caracteres.',
    ],

    'min' => [
        'array' =>
            'El campo :attribute debe contener al menos :min elementos.',

        'file' =>
            'El archivo :attribute debe pesar al menos :min kilobytes.',

        'numeric' =>
            'El campo :attribute debe ser al menos :min.',

        'string' =>
            'El campo :attribute debe contener al menos :min caracteres.',
    ],

    'not_in' =>
        'El valor seleccionado para :attribute no es válido.',

    'numeric' =>
        'El campo :attribute debe ser un valor numérico.',

    'present' =>
        'El campo :attribute debe estar presente.',

    'regex' =>
        'El formato del campo :attribute no es válido.',

    'required' =>
        'El campo :attribute es obligatorio.',

    'required_if' =>
        'El campo :attribute es obligatorio cuando :other es :value.',

    'required_unless' =>
        'El campo :attribute es obligatorio a menos que :other sea :values.',

    'required_with' =>
        'El campo :attribute es obligatorio cuando :values está presente.',

    'required_with_all' =>
        'El campo :attribute es obligatorio cuando :values están presentes.',

    'required_without' =>
        'El campo :attribute es obligatorio cuando :values no está presente.',

    'required_without_all' =>
        'El campo :attribute es obligatorio cuando ninguno de :values está presente.',

    'same' =>
        'Los campos :attribute y :other deben coincidir.',

    'size' => [
        'array' =>
            'El campo :attribute debe contener :size elementos.',

        'file' =>
            'El archivo :attribute debe pesar :size kilobytes.',

        'numeric' =>
            'El campo :attribute debe ser :size.',

        'string' =>
            'El campo :attribute debe contener :size caracteres.',
    ],

    'string' =>
        'El campo :attribute debe contener texto.',

    'unique' =>
        'El valor de :attribute ya se encuentra registrado.',

    'uploaded' =>
        'No se pudo cargar el archivo :attribute.',

    'url' =>
        'El campo :attribute debe contener una URL válida.',


    /*
    |--------------------------------------------------------------------------
    | Mensajes personalizados
    |--------------------------------------------------------------------------
    */

    'custom' => [],


    /*
    |--------------------------------------------------------------------------
    | Nombres de campos
    |--------------------------------------------------------------------------
    */

    'attributes' => [

        // USUARIOS
        'name' => 'nombre',
        'nombre' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
        'role_id' => 'rol',

        // ÓRDENES
        'codigo_caja' => 'código de caja',
        'odontologo_id' => 'odontólogo',
        'paciente_id' => 'paciente',
        'tipo_protesis_id' => 'tipo de prótesis',
        'estado_orden_id' => 'estado de la orden',
        'etapa_actual_id' => 'etapa de producción',
        'tecnico_actual_id' => 'técnico',
        'fecha_ingreso' => 'fecha de ingreso',
        'fecha_entrega_estimada' => 'fecha estimada de entrega',
        'fecha_entrega_real' => 'fecha real de entrega',
        'cantidad' => 'cantidad',
        'especificaciones' => 'especificaciones',
        'observaciones' => 'observaciones',
        'color' => 'color',
        'prioridad' => 'prioridad',
        'total' => 'precio del trabajo',
        'tipo_orden' => 'tipo de orden',
        'orden_origen_id' => 'orden de origen',

        // PAGOS
        'monto' => 'monto del abono',
        'monto_total' => 'monto total',
        'metodo_pago' => 'método de pago',
        'fecha_abono' => 'fecha del abono',
        'referencia' => 'referencia o número de boleta',
        'fecha_vencimiento' => 'fecha de vencimiento',

        // MENSAJERÍA
        'orden_trabajo_id' => 'orden de trabajo',
        'tipo_movimiento' => 'tipo de movimiento',
        'orden_visita' => 'orden de visita',
        'direccion_referencia' => 'dirección de referencia',
        'recibido_por' => 'nombre de quien recibe',

        // INVENTARIO
        'material_id' => 'material',
        'tecnico_id' => 'técnico',
        'stock' => 'existencia',
        'stock_minimo' => 'existencia mínima',
        'costo_unitario' => 'costo unitario',

        // CRÉDITOS
        'modalidad_pago' => 'modalidad de pago',
        'limite_credito' => 'límite de crédito',
        'saldo_pendiente' => 'saldo pendiente',
    ],

];