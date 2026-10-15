<?php

namespace App\Services;

use App\Support\ImporteCajaChica;
use Carbon\CarbonImmutable;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Phar;
use PharData;
use RuntimeException;

class ExportarCajaChicaExcel
{
    private const NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    public static function generar(string $plantilla, iterable $gastos, string $mes, array $resumen): string
    {
        if (!class_exists(PharData::class) || !class_exists(DOMDocument::class)) {
            throw new RuntimeException('La exportación Excel requiere las extensiones PHP Phar y DOM.');
        }
        if (!is_file($plantilla)) {
            throw new RuntimeException('No se encontró la plantilla Excel de Caja chica.');
        }
        $base = tempnam(sys_get_temp_dir(), 'caja_excel_');
        if ($base === false) {
            throw new RuntimeException('No se pudo preparar el archivo temporal de Excel.');
        }
        $ruta = $base.'.zip';
        $archivo = null;
        try {
            if (!rename($base, $ruta) || !copy($plantilla, $ruta)) {
                throw new RuntimeException('No se pudo copiar la plantilla Excel.');
            }
            $archivo = new PharData($ruta);
            $hoja = self::xml($archivo['xl/worksheets/sheet1.xml']->getContent());
            $x = self::xpath($hoja);
            $datos = $x->query('/s:worksheet/s:sheetData')->item(0);
            $plantillas = [$x->query('//s:row[@r="10"]')->item(0), $x->query('//s:row[@r="11"]')->item(0)];
            if (!$datos || !$plantillas[0] || !$plantillas[1]) {
                throw new RuntimeException('La plantilla Excel no tiene las filas de formato esperadas.');
            }
            $filas = [];
            foreach ($gastos as $gasto) {
                $filas[] = $gasto;
            }
            if (count($filas) > 1048567) {
                throw new RuntimeException('El mes supera la cantidad de filas que admite una hoja de Excel.');
            }
            foreach (iterator_to_array($datos->childNodes) as $fila) {
                if ($fila instanceof DOMElement && (int) $fila->getAttribute('r') >= 10) {
                    $datos->removeChild($fila);
                }
            }
            $cantidadFilas = max(1, count($filas));
            for ($i = 0; $i < $cantidadFilas; $i++) {
                $numero = 10 + $i;
                $fila = $plantillas[$i % 2]->cloneNode(true);
                $fila->setAttribute('r', (string) $numero);
                $gasto = $filas[$i] ?? null;
                $valores = $gasto ? [
                    $gasto->id, self::fechaExcel($gasto->fecha_gasto->toDateString()), $gasto->numero_documento,
                    $gasto->concepto, $gasto->beneficiario, $gasto->cantidad, $gasto->precio_unitario, $gasto->monto,
                    $gasto->responsable, $gasto->estado, $gasto->metodo_pago, $gasto->tipo_comprobante,
                    $gasto->nit, $gasto->referencia, $gasto->usuarioRegistro?->name,
                ] : array_fill(0, 15, '');
                $lineas = 1;
                foreach ([2 => 24, 3 => 44, 4 => 30, 8 => 26, 9 => 28, 10 => 19, 11 => 23, 12 => 20, 13 => 30, 14 => 27] as $indice => $ancho) {
                    $texto = (string) ($valores[$indice] ?? '');
                    $lineas = max($lineas, (int) ceil(mb_strlen($texto) / $ancho), substr_count($texto, "\n") + 1);
                }
                $fila->setAttribute('ht', (string) max(34, min(160, 16 * $lineas + 10)));
                $fila->setAttribute('customHeight', '1');
                foreach (range(0, 14) as $columna) {
                    $letra = chr(65 + $columna);
                    $celda = null;
                    foreach ($fila->childNodes as $hija) {
                        if ($hija instanceof DOMElement && preg_replace('/\d+$/', '', $hija->getAttribute('r')) === $letra) {
                            $celda = $hija;
                            break;
                        }
                    }
                    if (!$celda) {
                        throw new RuntimeException('La plantilla Excel tiene una columna incompleta.');
                    }
                    $celda->setAttribute('r', $letra.$numero);
                    self::valor($celda, (string) ($valores[$columna] ?? ''), $gasto && in_array($columna, [0, 1, 5, 6, 7], true));
                }
                $datos->appendChild($fila);
            }
            $ultima = 9 + $cantidadFilas;
            self::actualizar($x, 'A3', 'Mes: '.CarbonImmutable::createFromFormat('!Y-m', $mes)->locale('es')->translatedFormat('F \d\e Y'));
            self::actualizar($x, 'I3', 'Emitido: '.now('America/Guatemala')->format('d/m/Y H:i'));
            self::actualizar($x, 'I6', 'Registros del mes: '.count($filas));
            self::actualizar($x, 'D5', ImporteCajaChica::decimal($resumen['limite']), true);
            self::actualizar($x, 'H5', ImporteCajaChica::decimal($resumen['gastado']), true,
                'ROUND(SUMIFS(H10:H'.$ultima.',J10:J'.$ultima.',"Registrado")+SUMIFS(H10:H'.$ultima.',J10:J'.$ultima.',"Autorizado"),2)');
            self::actualizar($x, 'M5', ImporteCajaChica::decimal($resumen['disponible']), true, 'MAX(0,ROUND(D5-H5,2))');
            self::actualizar($x, 'D6', ImporteCajaChica::decimal($resumen['pendiente']), true,
                'ROUND(SUMIFS(H10:H'.$ultima.',J10:J'.$ultima.',"Pendiente de autorización"),2)');
            self::actualizar($x, 'H6', ImporteCajaChica::decimal($resumen['exceso']), true, 'MAX(0,ROUND(H5-D5,2))');
            $x->query('/s:worksheet/s:dimension')->item(0)?->setAttribute('ref', 'A1:O'.$ultima);
            foreach ($x->query('//s:autoFilter') as $filtro) {
                $filtro->setAttribute('ref', 'A9:O'.$ultima);
            }
            $reemplazos = ['xl/worksheets/sheet1.xml' => $hoja->saveXML()];
            $tabla = self::xml($archivo['xl/tables/table1.xml']->getContent());
            $tabla->documentElement->setAttribute('ref', 'A9:O'.$ultima);
            foreach (self::xpath($tabla)->query('//s:autoFilter') as $filtro) {
                $filtro->setAttribute('ref', 'A9:O'.$ultima);
            }
            $reemplazos['xl/tables/table1.xml'] = $tabla->saveXML();
            $libro = self::xml($archivo['xl/workbook.xml']->getContent());
            $calc = self::xpath($libro)->query('//s:calcPr')->item(0);
            if ($calc) {
                $calc->setAttribute('fullCalcOnLoad', '1');
                $calc->setAttribute('forceFullCalc', '1');
            }
            $reemplazos['xl/workbook.xml'] = $libro->saveXML();
            // Phar se utiliza solo para leer. Su escritor ZIP genera cabeceras
            // con versión 0, incompatibles con lectores estrictos de Office.
            $partes = [];
            foreach (new \RecursiveIteratorIterator($archivo) as $entrada) {
                $nombre = substr($entrada->getPathname(), strlen('phar://'.$ruta.'/'));
                $partes[$nombre] = $reemplazos[$nombre] ?? $entrada->getContent();
            }
            return self::empaquetar($partes);
        } finally {
            unset($archivo);
            if (is_file($ruta)) {
                Phar::unlinkArchive($ruta);
            }
            if (is_file($base)) {
                unlink($base);
            }
        }
    }

    private static function empaquetar(array $partes): string
    {
        // ZIP clásico, método STORE, nombres ASCII del paquete OOXML controlado.
        // No requiere ZipArchive ni modifica los XML que contiene el informe.
        $cuerpo = '';
        $directorio = '';
        $cantidad = count($partes);
        if ($cantidad > 65535) {
            throw new RuntimeException('El informe supera los límites del formato ZIP.');
        }
        foreach ($partes as $nombre => $contenido) {
            $largoNombre = strlen($nombre);
            $largo = strlen($contenido);
            $posicion = strlen($cuerpo);
            if ($largoNombre > 65535 || $largo >= 0xffffffff || $posicion >= 0xffffffff) {
                throw new RuntimeException('El informe supera los límites del formato ZIP.');
            }
            $crc = crc32($contenido);
            // Fecha DOS válida y estable: 01/01/1980; versión mínima ZIP 2.0.
            $cuerpo .= pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 0, 0, 33,
                $crc, $largo, $largo, $largoNombre, 0).$nombre.$contenido;
            $directorio .= pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, 0, 0, 0, 33,
                $crc, $largo, $largo, $largoNombre, 0, 0, 0, 0, 0, $posicion).$nombre;
        }
        if (strlen($cuerpo) + strlen($directorio) >= 0xffffffff) {
            throw new RuntimeException('El informe supera los límites del formato ZIP.');
        }
        return $cuerpo.$directorio.pack('VvvvvVVv', 0x06054b50, 0, 0, $cantidad, $cantidad,
            strlen($directorio), strlen($cuerpo), 0);
    }

    private static function fechaExcel(string $fecha): string
    {
        $utc = new \DateTimeZone('UTC');
        return (new \DateTimeImmutable('1899-12-30', $utc))->diff(new \DateTimeImmutable($fecha, $utc))->format('%r%a');
    }

    private static function xml(string $contenido): DOMDocument
    {
        $doc = new DOMDocument();
        if (!$doc->loadXML($contenido, LIBXML_NONET)) {
            throw new RuntimeException('No se pudo leer la estructura de la plantilla Excel.');
        }
        return $doc;
    }

    private static function xpath(DOMDocument $doc): DOMXPath
    {
        $x = new DOMXPath($doc);
        $x->registerNamespace('s', self::NS);
        return $x;
    }

    private static function actualizar(DOMXPath $x, string $referencia, string $valor, bool $numero = false, ?string $formula = null): void
    {
        $celda = $x->query('//s:c[@r="'.$referencia.'"]')->item(0);
        if (!$celda instanceof DOMElement) {
            throw new RuntimeException('Falta la celda '.$referencia.' en la plantilla Excel.');
        }
        self::valor($celda, $valor, $numero, $formula);
    }

    private static function valor(DOMElement $celda, string $valor, bool $numero = false, ?string $formula = null): void
    {
        $doc = $celda->ownerDocument;
        while ($celda->firstChild) {
            $celda->removeChild($celda->firstChild);
        }
        $celda->setAttribute('t', $numero ? 'n' : 'inlineStr');
        if ($formula !== null) {
            $f = $doc->createElementNS(self::NS, 'f');
            $f->appendChild($doc->createTextNode($formula));
            $celda->appendChild($f);
        }
        if ($numero) {
            $v = $doc->createElementNS(self::NS, 'v');
            $v->appendChild($doc->createTextNode($valor));
            $celda->appendChild($v);
        } else {
            // Texto literal: referencias, NIT y documentos conservan ceros y nunca se ejecutan como fórmulas.
            $is = $doc->createElementNS(self::NS, 'is');
            $t = $doc->createElementNS(self::NS, 't');
            $t->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
            $t->appendChild($doc->createTextNode(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $valor)));
            $is->appendChild($t);
            $celda->appendChild($is);
        }
    }
}
