<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use Tests\TestCase;

/**
 * Sección «Evolución de mis lecturas» de la página «Mi progreso».
 * Solo SQLite en memoria con esquema propio; nunca toca MariaDB.
 */
class StudentProgressEvolutionTest extends TestCase
{
    public function test_isolation_preflight(): void
    {
        $this->assertIsolated();
    }

    public function test_without_finished_activities_shows_empty_message(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('vacio');
        $actividad = $this->activity('Empezada', 1);
        $this->attempt($estudiante, $actividad, null, null, '2026-11-01 09:00:00');

        $html = $this->actingAs($usuario)->get(route('student.progress'))->assertOk()->getContent();

        $this->assertStringContainsString('Cuando termines tus primeras lecturas, podrás ver aquí tus resultados.', $html);
        $this->assertSame([], $this->bars($html));
    }

    public function test_single_activity_shows_result_without_claiming_a_trend(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('una');
        $actividad = $this->activity('Única', 1);
        $this->attempt($estudiante, $actividad, 60, '2026-11-01 09:00:00');

        $html = $this->actingAs($usuario)->get(route('student.progress'))->assertOk()->getContent();

        $this->assertSame(['60 % 1. Única 01/11/2026 Básico'], $this->bars($html));
        $this->assertStringContainsString('Cuando termines más lecturas, aquí podrás compararlos.', $html);
        $this->assertStringNotContainsString('de la más antigua a la más reciente.</p>', $html);
    }

    public function test_only_first_finished_attempt_per_activity_is_shown_using_engine_order(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('reintentos');
        $actividad = $this->activity('Repetida', 1);
        // El de id menor termina después: el primer intento lo decide fecha_fin.
        $this->attempt($estudiante, $actividad, 50, '2026-11-01 10:00:00', '2026-11-01 09:50:00');
        $this->attempt($estudiante, $actividad, 40, '2026-11-01 09:00:00', '2026-11-01 08:50:00');
        $this->attempt($estudiante, $actividad, 100, '2026-11-01 11:00:00');
        $this->attempt($estudiante, $actividad, null, null, '2026-11-01 12:00:00');

        $html = $this->actingAs($usuario)->get(route('student.progress'))->assertOk()->getContent();

        $this->assertSame(['40 % 1. Repetida 01/11/2026 Básico'], $this->bars($html));
        $this->assertStringContainsString('height: 40.00%', $html);
    }

    public function test_activities_are_chronological_keep_their_level_and_legacy_attempts_go_first(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('niveles', 2);
        $basica = $this->activity('Básica', 1);
        $intermedia = $this->activity('Intermedia', 2);
        $retirada = $this->activity('Retirada', 1);
        $heredada = $this->activity('Heredada', 1);
        $this->attempt($estudiante, $intermedia, 70, '2026-11-03 09:00:00');
        $this->attempt($estudiante, $basica, 66.67, '2026-11-01 09:00:00');
        $this->attempt($estudiante, $retirada, 0, '2026-11-02 09:00:00');
        $this->attempt($estudiante, $heredada, 100, null, '2026-10-01 09:00:00', 'FINALIZADO');
        DB::table('actividad')->where('id_actividad', $retirada)->update(['deleted_at' => '2026-11-04 00:00:00']);

        $html = $this->actingAs($usuario)->get(route('student.progress'))->assertOk()->getContent();

        $this->assertSame([
            '100 % 1. Heredada Fecha no registrada Básico',
            '66,67 % 2. Básica 01/11/2026 Básico',
            '0 % 3. Retirada 02/11/2026 Básico',
            '70 % 4. Intermedia 03/11/2026 Intermedio',
        ], $this->bars($html));
        $this->assertStringContainsString('evolucion-nivel-intermedio', $html);
    }

    public function test_student_only_sees_own_results_and_page_does_not_write(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('propio');
        [, $otro] = $this->student('ajeno');
        $propia = $this->activity('Propia', 1);
        $ajena = $this->activity('Ajena', 1);
        $this->attempt($estudiante, $propia, 80, '2026-11-01 09:00:00');
        $this->attempt($otro, $ajena, 90, '2026-11-01 09:30:00');

        $escrituras = [];
        DB::listen(function ($query) use (&$escrituras) {
            if (! preg_match('/^\s*select\b/i', $query->sql)) {
                $escrituras[] = $query->sql;
            }
        });
        $html = $this->actingAs($usuario)->get(route('student.progress'))->assertOk()->getContent();

        $this->assertSame(['80 % 1. Propia 01/11/2026 Básico'], $this->bars($html));
        $this->assertStringNotContainsString('Ajena', $html);
        $this->assertSame([], $escrituras);
    }

    // ------------------------------------------------------------------

    private function assertIsolated(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame('sqlite', DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME));
    }

    private function prepare(): void
    {
        $this->assertIsolated();

        Schema::create('usuario', function (Blueprint $t) {
            $t->id('id_usuario');
            $t->string('usuario');
            $t->string('password_hash');
            $t->string('rol');
            $t->boolean('estado');
            $t->softDeletes();
            $t->timestamps();
        });
        Schema::create('nivel', function (Blueprint $t) {
            $t->id('id_nivel');
            $t->string('nombre');
            $t->decimal('rango_min', 5, 2);
            $t->decimal('rango_max', 5, 2);
            $t->timestamps();
        });
        Schema::create('estudiante', function (Blueprint $t) {
            $t->id('id_estudiante');
            $t->unsignedBigInteger('id_usuario');
            $t->unsignedBigInteger('nivel_actual_id')->nullable();
            $t->string('nombre');
            $t->string('apellido');
            $t->timestamps();
        });
        Schema::create('cuestionario_diagnostico', function (Blueprint $t) {
            $t->id('id_cuestionario');
            $t->string('estado');
            $t->timestamps();
        });
        Schema::create('evaluacion', function (Blueprint $t) {
            $t->id('id_evaluacion');
            $t->unsignedBigInteger('estudiante_id');
            $t->unsignedBigInteger('cuestionario_id')->nullable();
            $t->string('tipo')->nullable();
            $t->string('estado')->nullable();
            $t->dateTime('fecha')->nullable();
            $t->dateTime('fecha_fin')->nullable();
            $t->decimal('puntaje', 5, 2)->nullable();
            $t->unsignedBigInteger('nivel_id')->nullable();
            $t->timestamps();
        });
        Schema::create('actividad', function (Blueprint $t) {
            $t->id('id_actividad');
            $t->unsignedBigInteger('nivel_id');
            $t->string('titulo');
            $t->text('contenido');
            $t->string('estado');
            $t->softDeletes();
            $t->timestamps();
        });
        Schema::create('pregunta', function (Blueprint $t) {
            $t->id('id_pregunta');
            $t->unsignedBigInteger('actividad_id')->nullable();
            $t->timestamps();
        });
        Schema::create('actividad_intento', function (Blueprint $t) {
            $t->id('id_intento');
            $t->unsignedBigInteger('estudiante_id');
            $t->unsignedBigInteger('actividad_id');
            $t->decimal('puntaje', 5, 2)->nullable();
            $t->integer('puntos_obtenidos')->nullable();
            $t->dateTime('fecha_inicio');
            $t->dateTime('fecha_fin')->nullable();
            $t->string('estado');
            $t->timestamps();
        });
        Schema::create('adaptacion_nivel', function (Blueprint $t) {
            $t->id('id_adaptacion');
            $t->unsignedBigInteger('estudiante_id');
            $t->unsignedBigInteger('nivel_anterior_id');
            $t->unsignedBigInteger('nivel_resultante_id');
            $t->string('decision');
            $t->decimal('promedio', 5, 2);
            $t->dateTime('fecha');
            $t->timestamps();
        });
        Schema::create('adaptacion_nivel_actividad', function (Blueprint $t) {
            $t->unsignedBigInteger('adaptacion_id');
            $t->unsignedBigInteger('estudiante_id');
            $t->unsignedBigInteger('actividad_id');
            $t->unsignedBigInteger('intento_id');
            $t->decimal('puntaje', 5, 2);
        });

        DB::table('nivel')->insert([
            ['id_nivel' => 1, 'nombre' => 'Básico', 'rango_min' => 0, 'rango_max' => 59.99],
            ['id_nivel' => 2, 'nombre' => 'Intermedio', 'rango_min' => 60, 'rango_max' => 79.99],
            ['id_nivel' => 3, 'nombre' => 'Avanzado', 'rango_min' => 80, 'rango_max' => 100],
        ]);
    }

    /** @return array{0: Usuario, 1: int} */
    private function student(string $nombre, int $nivel = 1): array
    {
        $usuarioId = DB::table('usuario')->insertGetId([
            'usuario' => "test_$nombre", 'password_hash' => 'sin-uso', 'rol' => Usuario::ROL_ESTUDIANTE, 'estado' => 1,
        ]);
        $estudianteId = DB::table('estudiante')->insertGetId([
            'id_usuario' => $usuarioId, 'nivel_actual_id' => $nivel, 'nombre' => ucfirst($nombre), 'apellido' => 'Prueba',
        ]);

        return [Usuario::findOrFail($usuarioId), $estudianteId];
    }

    private function activity(string $titulo, int $nivel): int
    {
        return DB::table('actividad')->insertGetId([
            'nivel_id' => $nivel, 'titulo' => $titulo, 'contenido' => 'Texto de prueba', 'estado' => 'ACTIVA',
        ]);
    }

    private function attempt(int $estudiante, int $actividad, ?float $puntaje, ?string $fin, ?string $inicio = null, ?string $estado = null): int
    {
        return DB::table('actividad_intento')->insertGetId([
            'estudiante_id' => $estudiante, 'actividad_id' => $actividad, 'puntaje' => $puntaje, 'puntos_obtenidos' => 0,
            'fecha_inicio' => $inicio ?? date('Y-m-d H:i:s', strtotime($fin) - 60), 'fecha_fin' => $fin,
            'estado' => $estado ?? ($fin === null ? 'EN_PROCESO' : 'FINALIZADO'),
        ]);
    }

    /** @return list<string> Texto visible de cada barra, en orden. */
    private function bars(string $html): array
    {
        preg_match_all('#<li class="evolucion-item">(.*?)</li>#s', $html, $coincidencias);

        return array_map(
            fn (string $item): string => trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($item)))),
            $coincidencias[1]
        );
    }
}
