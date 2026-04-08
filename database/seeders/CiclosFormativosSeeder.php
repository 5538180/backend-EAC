<?php

namespace Database\Seeders;

use App\Models\FamiliaProfesional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class CiclosFormativosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ? Donde se optienen los datos
        $path = database_path('seeders/csv/ciclos.csv');

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
            // ! Si es mayor ? 
            if (count($row) < count($header)) {
                continue;
            }



            // ! tiene value erro para contener si no coincide las keys, mejor usar try catch ValueError?
            $rec = array_combine($header, $row);

            $codFamilia =  trim($rec['familia']);


            // ? si no existe la familia profesional salta a la siguiente iteracion
            $idFamilia =  FamiliaProfesional::where('codigo', $codFamilia)->value('id');


            // - Controlo de si existe la familia en la BBDD
            /* if(is_null($idFamilia)){
                continue;
            } 
             */

            /*    try {
                   $data[] = [
                'nombre' => trim($rec['nombre'] ?? ''),
                'codigo' => trim($rec['codigo'] ?? ''),
                'descripcion' => $rec['descripcion'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            } catch (\ValueError $ERROR) {
                $ERROR->error_log('sjadoghoahf');
            } */



            $data[] = [

                'familia_profesional_id' =>  $idFamilia,
                'nombre' => trim($rec['nombre'] ?? ''),

                'codigo' => trim($rec['cod_ciclo'] ?? ''), // - <- UK
                'grado' => trim($rec['nivel'] ?? ''),

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // ? Recorre la tabla indicada para ver que no existe duplicados en lo que queramos,
        // ? indicamos que ha de ser unico y si hay coincidencia, actualiza con los datos nuevos los demas atributos
        // Insertar/actualizar usando upsert para evitar duplicados por 'codigo'
        DB::transaction(function () use ($data) {
            foreach (array_chunk($data, 200) as $chunk) {
                DB::table('ciclos_formativos')->upsert(
                    $chunk,
                    ['codigo'], // llave única para evitar duplicados
                    ['grado', 'descripcion', 'updated_at']
                );
            }
        });
    }
}
/* CICLOS_FORMATIVOS {
    bigint id PK
    bigint familia_profesional_id FK
    varchar nombre
    varchar codigo UK
    varchar grado
    text descripcion
    timestamp created_at
    timestamp updated_at
} */