<?php

namespace App\Support;

use InvalidArgumentException;

class ImporteCajaChica
{
    public static function cantidadMilesimas(string|int|float $cantidad): int
    {
        $cantidad = trim((string) $cantidad);
        if (!preg_match('/^\d{1,6}(?:\.\d{1,3})?$/D', $cantidad)) {
            throw new InvalidArgumentException('La cantidad admite hasta tres decimales.');
        }
        [$entero, $decimal] = array_pad(explode('.', $cantidad, 2), 2, '');
        $milesimas = ((int) $entero * 1000) + (int) str_pad($decimal, 3, '0');
        if ($milesimas <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a cero.');
        }
        return $milesimas;
    }

    public static function cantidadDecimal(int $milesimas): string
    {
        return rtrim(rtrim(intdiv($milesimas, 1000).'.'.str_pad((string) ($milesimas % 1000), 3, '0', STR_PAD_LEFT), '0'), '.');
    }

    public static function total(int $milesimas, int $precioCentavos): int
    {
        if ($milesimas < 1 || $milesimas > 999999999 || $precioCentavos < 1 || $precioCentavos > 999999999999) {
            throw new InvalidArgumentException('La cantidad o el precio unitario no son válidos.');
        }
        // Se separa la parte entera para evitar desbordamientos. Redondeo a centavos, mitad hacia arriba.
        $total = intdiv($milesimas, 1000) * $precioCentavos
            + intdiv(($milesimas % 1000) * $precioCentavos + 500, 1000);
        if ($total < 1 || $total > 999999999999) {
            throw new InvalidArgumentException('El total debe estar entre Q0.01 y Q9,999,999,999.99.');
        }
        return $total;
    }

    public static function centavos(string|int|float $importe): int
    {
        $importe = trim((string) $importe);
        if (!preg_match('/^\d{1,10}(?:\.\d{1,2})?$/D', $importe)) {
            throw new InvalidArgumentException('Importe inválido: utilice hasta dos decimales y punto decimal.');
        }
        [$entero, $decimal] = array_pad(explode('.', $importe, 2), 2, '');

        return ((int) $entero * 100) + (int) str_pad($decimal, 2, '0');
    }

    public static function decimal(int $centavos): string
    {
        return intdiv($centavos, 100).'.'.str_pad((string) ($centavos % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function formato(int $centavos): string
    {
        return number_format($centavos / 100, 2, '.', ',');
    }
}
