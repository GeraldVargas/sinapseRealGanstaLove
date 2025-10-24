<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SinapseSeeder extends Seeder
{
    public function run()
    {
        // Insertar Roles - CON NOMBRES CORRECTOS
        DB::table('roles')->insert([
            [
                'Id_rol' => 1, 
                'Nombre' => 'Estudiante', 
                'Descripcion' => 'Usuario que toma cursos'
            ],
            [
                'Id_rol' => 2, 
                'Nombre' => 'Docente', 
                'Descripcion' => 'Usuario que imparte cursos'
            ],
            [
                'Id_rol' => 3, 
                'Nombre' => 'Admin', 
                'Descripcion' => 'Administrador del sistema'
            ],
        ]);

        // Insertar Usuarios - CON NOMBRES CORRECTOS
        DB::table('usuarios')->insert([
            [
                'Id_usuario' => 1,
                'Nombre' => 'Juan',
                'Apellido' => 'Pérez',
                'Email' => 'juan@sinapse.com',
                'Contraseña' => Hash::make('password123'),
                'Fecha_registro' => now()->format('Y-m-d'),
                'Estado' => 1
            ],
            [
                'Id_usuario' => 2,
                'Nombre' => 'María',
                'Apellido' => 'García',
                'Email' => 'maria@sinapse.com',
                'Contraseña' => Hash::make('password123'),
                'Fecha_registro' => now()->format('Y-m-d'),
                'Estado' => 1
            ],
            [
                'Id_usuario' => 3,
                'Nombre' => 'Carlos',
                'Apellido' => 'López',
                'Email' => 'carlos@sinapse.com',
                'Contraseña' => Hash::make('password123'),
                'Fecha_registro' => now()->format('Y-m-d'),
                'Estado' => 1
            ],
        ]);

        // Asignar Roles a Usuarios
        DB::table('rol_usuario')->insert([
            ['id_usuario' => 1, 'id_rol' => 1], // Juan es Estudiante
            ['id_usuario' => 2, 'id_rol' => 1], // María es Estudiante
            ['id_usuario' => 3, 'id_rol' => 2], // Carlos es Docente
            ['id_usuario' => 3, 'id_rol' => 3], // Carlos también es Admin
        ]);

        // Insertar Cursos - CON NOMBRES CORRECTOS
        DB::table('cursos')->insert([
            [
                'Id_curso' => 1,
                'Titulo' => 'Programación Web con Laravel',
                'Descripcion' => 'Aprende a crear aplicaciones web modernas con Laravel',
                'Duracion' => 40,
                'Costo' => 0.00,
                'Estado' => 1
            ],
            [
                'Id_curso' => 2,
                'Titulo' => 'JavaScript Avanzado',
                'Descripcion' => 'Domina JavaScript moderno y frameworks',
                'Duracion' => 30,
                'Costo' => 0.00,
                'Estado' => 1
            ],
            [
                'Id_curso' => 3,
                'Titulo' => 'Base de Datos MySQL',
                'Descripcion' => 'Aprende diseño y administración de bases de datos',
                'Duracion' => 25,
                'Costo' => 0.00,
                'Estado' => 1
            ],
        ]);

        // Insertar Inscripciones
        DB::table('inscripciones')->insert([
            [
                'id_inscripc' => 1,
                'id_usuario' => 1,
                'id_curso' => 1,
                'Fecha_inscripcion' => now()->format('Y-m-d'),
                'Estado' => 1
            ],
            [
                'id_inscripc' => 2,
                'id_usuario' => 1,
                'id_curso' => 2,
                'Fecha_inscripcion' => now()->format('Y-m-d'),
                'Estado' => 1
            ],
            [
                'id_inscripc' => 3,
                'id_usuario' => 2,
                'id_curso' => 1,
                'Fecha_inscripcion' => now()->format('Y-m-d'),
                'Estado' => 1
            ],
        ]);

        // Insertar Insignias (si la tabla existe)
        if (DB::getSchemaBuilder()->hasTable('insignia')) {
            DB::table('insignia')->insert([
                [
                    'Id_insignia' => 1,
                    'Nombre' => 'Primer Curso',
                    'Descripcion' => 'Completaste tu primer curso',
                    'Valor_Puntos' => 100,
                    'Dificultad' => 'Fácil',
                    'Categoria' => 'Progreso',
                    'Imagen' => 'badge1.png'
                ],
                [
                    'Id_insignia' => 2,
                    'Nombre' => 'Programador Novato',
                    'Descripcion' => 'Completaste un curso de programación',
                    'Valor_Puntos' => 200,
                    'Dificultad' => 'Medio',
                    'Categoria' => 'Habilidad',
                    'Imagen' => 'badge2.png'
                ],
            ]);

            // Asignar Insignias a Usuarios
            DB::table('usuario_insignia')->insert([
                [
                    'id_usuario' => 1,
                    'id_insig' => 1,
                    'Fecha_obtencion' => now()->format('Y-m-d'),
                    'Puntos_Obten' => 100
                ],
                [
                    'id_usuario' => 1,
                    'id_insig' => 2,
                    'Fecha_obtencion' => now()->format('Y-m-d'),
                    'Puntos_Obten' => 200
                ],
            ]);
        }

        echo "Datos de prueba insertados correctamente!\n";
        echo "Usuarios creados:\n";
        echo "- juan@sinapse.com / password123 (Estudiante)\n";
        echo "- maria@sinapse.com / password123 (Estudiante)\n";
        echo "- carlos@sinapse.com / password123 (Docente + Admin)\n";
    }
}