
## * TODO DENTRO DE TINKER

php artisan tinker

## TOKEN API ESTUDIANTE
 
- $user = App\Models\User::where('email', 'estudiante@backend-eac.test')->first();

= App\Models\User {#8170
    id: 2,
    name: "Estudiante Ejemplo",
    email: "estudiante@backend-eac.test",
    email_verified_at: "2026-04-11 16:33:22",
    #password: "\$2y\$12\$i34z9ld27V9kvNY2rFaQmOKS0indofwE7O.cHmRcOu9i6u3fxz8nC",
    #remember_token: "9R8Qi8Hyur86NoNA7RVlmbKwW4sZgquHE9umf68ACIxkhwdAkBmlz5mmcveu",
    created_at: "2026-04-11 16:33:22",
    updated_at: "2026-04-11 16:33:22",
  }



> $token = $user->createToken('test')->plainTextToken;

# TOKEN
"1|lwulsVkHJrNe8fpsgOqYWlB4ypWrT8yMk7VxOxQI5d1d4e73"




## COMPROBACIONES ESTUDIANTE

# Perfil del estudiante en el ecosistema 1
curl -s http://backend-eac.test/api/v1/estudiante/perfil/1 \
  -H "Authorization: Bearer 1|lwulsVkHJrNe8fpsgOqYWlB4ypWrT8yMk7VxOxQI5d1d4e73" | jq .

# Matricularse en el módulo 1
curl -s -X POST http://backend-eac.test/api/v1/estudiante/matriculas \
  -H "Authorization: Bearer 1|lwulsVkHJrNe8fpsgOqYWlB4ypWrT8yMk7VxOxQI5d1d4e73" \
  -H "Content-Type: application/json" \
  -d '{"modulo_id": 1}' | jq .

  ## TOKEN API DOCENTE  

  # Generar variable del usuario

  $user = App\Models\User::where('email', 'docente@backend-eac.test')->first();

# Generar token respecto a ese usuario

$token = $user->createToken('test')->plainTextToken;


# Datos

= App\Models\User {#8180
    id: 1,
    name: "Profesora Ejemplo",
    email: "docente@backend-eac.test",
    email_verified_at: "2026-04-11 16:33:22",
    #password: "\$2y\$12\$i34z9ld27V9kvNY2rFaQmOKS0indofwE7O.cHmRcOu9i6u3fxz8nC",
    #remember_token: "S2xg72eeYdEVlw7O5AIEdD3H7F0R98WOl3ujqvO8SJDGCDF4TLsi9QVJHJQO",
    created_at: "2026-04-11 16:33:22",
    updated_at: "2026-04-11 16:33:22",
  }

> $token = $user->createToken('test')->plainTextToken;

# TOKEN
= "2|atz8jurgVehI1CLYBAadSrCvrLfNfHnnccZK74nzd4bb141e"

## COMPROBACIONES DOCENTE

# Progreso del grupo en el ecosistema 1
curl -s http://backend-eac.test/api/v1/docente/ecosistemas/1/progreso \
  -H "Authorization: Bearer 2|atz8jurgVehI1CLYBAadSrCvrLfNfHnnccZK74nzd4bb141e" | jq .

# Registrar conquista de SC-01 al estudiante 2
curl -s -X POST http://backend-eac.test/api/v1/docente/ecosistemas/1/conquistas \
  -H "Authorization: Bearer 2|atz8jurgVehI1CLYBAadSrCvrLfNfHnnccZK74nzd4bb141e" \
  -H "Content-Type: application/json" \
  -d '{
        "estudiante_id": 2,
        "sc_codigo": "SC-01",
        "gradiente_autonomia": "supervisado",
        "puntuacion_conquista": 84.5
      }' | jq .