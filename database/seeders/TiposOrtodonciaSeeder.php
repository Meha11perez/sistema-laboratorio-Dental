<?php

namespace Database\Seeders;

use App\Models\TipoProtesis;
use Illuminate\Database\Seeder;

class TiposOrtodonciaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Hawley',
                'descripcion' => 'Aparato de ortodoncia tipo Hawley.',
            ],
            [
                'nombre' => 'Botón de Nance',
                'descripcion' => 'Aparato de ortodoncia tipo Botón de Nance.',
            ],
            [
                'nombre' => 'Mantenedor de espacio',
                'descripcion' => 'Aparato utilizado como mantenedor de espacio.',
            ],
            [
                'nombre' => 'Arco palatino',
                'descripcion' => 'Aparato de ortodoncia tipo arco palatino.',
            ],
            [
                'nombre' => 'Arco vestibular',
                'descripcion' => 'Aparato de ortodoncia tipo arco vestibular.',
            ],
            [
                'nombre' => 'Expansor',
                'descripcion' => 'Aparato de ortodoncia utilizado para expansión.',
            ],
            [
                'nombre' => 'Hyrax',
                'descripcion' => 'Aparato expansor tipo Hyrax.',
            ],
        ];

        foreach ($tipos as $tipo) {

            TipoProtesis::updateOrCreate(
                [
                    'nombre' => $tipo['nombre'],
                    'categoria' => 'ortodoncia',
                ],
                [
                    'descripcion' => $tipo['descripcion'],
                    'estado' => true,
                ]
            );

        }
    }
}