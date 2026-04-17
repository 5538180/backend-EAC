<?php

namespace Database\Seeders;

use App\Models\Modulo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResultadosAprendizajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ? Donde se optienen los datos
        $path = database_path('seeders/csv/resultados_aprendizaje.csv');

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
            // ! Cambiado a si es menor Si la fila no tiene el campo tipo
            if (count($row) < count($header)) {
                continue;
            }



            // ! tiene value erro para contener si no coincide las keys, mejor usar try catch ValueError?
            $rec = array_combine($header, $row);

            $codModulo =  trim($rec['cod_modulo']);

            // ? si no existe la familia profesional salta a la siguiente iteracion
            $idModulo =  Modulo::where('codigo', $codModulo)->value('id');

            // - Comprovacion si existe
            /* if (is_null($idModulo)) {
                continue;
            } */

            // ! TIPO en el csv ?????

            $data[] = [
             
                'modulo_id' => (int) $idModulo, // - Casteo a int  
                'codigo' => 'RA' . trim($rec['id_ra'] ?? ''), // - RAx    
                'descripcion' => $rec['definicion'] ?? '',

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
// - 5 RA3
        // ? Recorre la tabla indicada para ver que no existe duplicados en lo que queramos,
        // ? indicamos que ha de ser unico y si hay coincidencia, actualiza con los datos nuevos los demas atributos
        // Insertar/actualizar usando upsert para evitar duplicados por 'codigo'
        DB::transaction(function () use ($data) {
            foreach (array_chunk($data, 200) as $chunk) {
                DB::table('resultados_aprendizaje')->upsert(
                    $chunk,
                    ['codigo','modulo_id'], 
                    ['descripcion', 'updated_at']
                );
            }
        });
    }
}
/* RESULTADOS_APRENDIZAJE {
    bigint id PK
    bigint modulo_id FK
    varchar codigo
    text descripcion
    timestamp created_at
    timestamp updated_at
} */