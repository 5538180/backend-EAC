<?php

namespace Database\Seeders;

use App\Models\CicloFormativo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class ModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        // ? Donde se optienen los datos
        $path = database_path('seeders/csv/modulos.csv');

        // ? Si no encuntra el archivo devuelve el ERROR
        if (!file_exists($path)) {
            $this->command->error("CSV no encontrado: $path");
            return;
        }

        // ?  Leer todas las líneas y parsear con str_getcsv
        $rows = array_map('str_getcsv', file($path));

        // ? El primer registro es la cabecera
        $header = array_map('trim', array_shift($rows));


        // ? Declaracion del array de datos
        $data = [];

        // ? Recorrer cada fila que es un objeto
        foreach ($rows as $row) {


            // ? Si hay menos datos en las filas que datos en  columnas salta a la sigioente fila y no rellena
            // Ignorar filas vacías o mal formadas
            if (count($row) < count($header)) {
                continue;
            }



            // ! tiene value erro para contener si no coincide las keys, mejor usar try catch ValueError?
            $rec = array_combine($header, $row);

            $codFamilia =  trim($rec['cod_modulo']);


            $idCicloFormativo = $rec['ciclo_formativo_id'];
            /* $idFamilia =  CicloFormativo::where('codigo', $codFamilia)->value('id');
            if(is_null($idFamilia)){
                continue;
            }  */


/* CicloFormativo::where('ciclo_formativo_id',$idCicloFormativo)->first()->get(); */

            // ! mejor con un regex de nuemros ?
            // ? casteo a int si esta vacio o nulo le pongo un nuemro al azar
            // ! Deberia llevarme la logica de esto a otro sitio, donde?
            $horasTotales = (int)$rec['horas_totales'];

            if (is_null($horasTotales) || isEmpty($horasTotales)) {


                $horasTotales = random_int(100, 200);
            };

            $data[] = [

            // ! No se que poner aqui porque no se la asociacion porque deberia 
           // ! tener este modulo comprobar que ciclos formativos pueden tener este modulo 
            
                'ciclo_formativo_id' =>  999999,


                'nombre' => $rec['nombre_modulo'] ?? '',
                'codigo' => trim($rec['cod_modulo'] ?? ''),

                'horas_totales' => $horasTotales,

                'descripcion' => $rec['descripcion'] ?? '',

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // ? Recorre la tabla indicada para ver que no existe duplicados en lo que queramos,
        // ? indicamos que ha de ser unico y si hay coincidencia, actualiza con los datos nuevos los demas atributos
        // Insertar/actualizar usando upsert para evitar duplicados por 'codigo'
        DB::transaction(function () use ($data) {
            foreach (array_chunk($data, 200) as $chunk) {
                DB::table('modulos')->upsert(
                    $chunk,
                    ['codigo'], // llave única para evitar duplicados
                    ['horas_totales', 'descripcion', 'updated_at']
                );
            }
        });
    }
}

/* MODULOS {
    bigint id PK
    bigint ciclo_formativo_id FK
    varchar nombre
    varchar codigo
    smallint horas_totales
    text descripcion
    timestamp created_at
    timestamp updated_at
} */