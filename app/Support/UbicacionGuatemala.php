<?php

namespace App\Support;

use App\Models\Clinica;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UbicacionGuatemala
{
    public static function catalogo(): array
    {
        return config('ubicaciones_guatemala', []);
    }

    public static function normalizar(string $valor): string
    {
        return Str::lower(
            Str::ascii(preg_replace('/\s+/u', ' ', trim($valor)))
        );
    }

    public static function departamento(string $valor): ?string
    {
        $normalizado = self::normalizar($valor);

        foreach (array_keys(self::catalogo()) as $departamento) {
            if (self::normalizar($departamento) === $normalizado) {
                return $departamento;
            }
        }

        return null;
    }

    public static function validar(array $datos, ?Clinica $clinica = null): array
    {
        $departamento = (string) ($datos['departamento'] ?? '');
        $municipio = (string) ($datos['municipio'] ?? '');

        // Conserva la ubicación histórica al editar otros datos.
        if (
            $clinica &&
            $departamento === (string) $clinica->departamento &&
            $municipio === (string) $clinica->municipio
        ) {
            return $datos;
        }

        // Mantiene ambos campos como opcionales.
        if (trim($departamento) === '' && trim($municipio) === '') {
            $datos['departamento'] = null;
            $datos['municipio'] = null;

            return $datos;
        }

        $departamentoValido = self::departamento($departamento);

        if (!$departamentoValido) {
            throw ValidationException::withMessages([
                'departamento' => 'Seleccione un departamento válido de Guatemala.',
            ]);
        }

        foreach (self::catalogo()[$departamentoValido] as $municipioValido) {
            if (self::normalizar($municipioValido) === self::normalizar($municipio)) {
                $datos['departamento'] = $departamentoValido;
                $datos['municipio'] = $municipioValido;

                return $datos;
            }
        }

        throw ValidationException::withMessages([
            'municipio' => 'Seleccione un municipio que pertenezca al departamento elegido.',
        ]);
    }
}