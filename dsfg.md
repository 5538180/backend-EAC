
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

## TOKEN
"1|lwulsVkHJrNe8fpsgOqYWlB4ypWrT8yMk7VxOxQI5d1d4e73"
