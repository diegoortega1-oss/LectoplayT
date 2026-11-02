<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use Tests\TestCase;

/**
 * Panel del estudiante: primer y último resultado, contador de lecturas y separación
 * entre nivel lector y gamificación. Solo SQLite en memoria; nunca toca MariaDB.
 */
class StudentDashboardTest extends TestCase
{
    public function test_isolation_preflight(): void
    {
        $this->assertIsolated();
    }

    public function test_activity_without_finished_attempts_shows_no_results(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('nuevo');
        $sinIntentos = $this->activity('Lectura nueva', 1);
        $enCurso = $this->activity('Lectura empezada', 1);
        $this->attempt($estudiante, $enCurso, null, null, '2026-11-01 09:00:00');

        $tarjetas = $this->cards($this->actingAs($usuario)->get(route('panel.estudiante'))->assertOk()->getContent());

        $this->assertStringContainsString('Aún no has terminado esta lectura', $tarjetas[$sinIntentos]);
        $this->assertStringContainsString('¡Puedes continuarla!', $tarjetas[$enCurso]);
        $this->assertStringNotContainsString('Tu primer resultado', $tarjetas[$sinIntentos].$tarjetas[$enCurso]);
    }

    public function test_single_finished_attempt_shows_only_first_result(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('uno');
        $actividad = $this->activity('Una vez', 1);
        $intento = $this->attempt($estudiante, $actividad, 70, '2026-11-01 09:00:00');

        $tarjeta = $this->cards($this->actingAs($usuario)->get(route('panel.estudiante'))->assertOk()->getContent())[$actividad];

        $this->assertStringContainsString('Tu primer resultado: 70,00 %', $tarjeta);
        $this->assertStringNotContainsString('Tu último resultado', $tarjeta);
        $this->assertStringContainsString("/estudiante/intentos/$intento/resultado", $tarjeta);
        $this->assertStringContainsString('Lectura de nivel Básico', $tarjeta);
    }

    public function test_first_result_follows_engine_order_and_last_ignores_attempts_in_progress(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('varios');
        $actividad = $this->activity('Repetida', 1);
        // El de id menor termina después; el primero lo decide fecha_fin.
        $this->attempt($estudiante, $actividad, 50, '2026-11-01 10:00:00', '2026-11-01 09:50:00');
        $this->attempt($estudiante, $actividad, 40, '2026-11-01 09:00:00', '2026-11-01 08:50:00');
        $ultimo = $this->attempt($estudiante, $actividad, 90, '2026-11-01 11:00:00');
        $abierto = $this->attempt($estudiante, $actividad, null, null, '2026-11-01 12:00:00');

        $tarjeta = $this->cards($this->actingAs($usuario)->get(route('panel.estudiante'))->assertOk()->getContent())[$actividad];

        $this->assertStringContainsString('Tu primer resultado: 40,00 %', $tarjeta);
        $this->assertStringContainsString('Tu último resultado: 90,00 %', $tarjeta);
        $this->assertStringContainsString("/estudiante/intentos/$ultimo/resultado", $tarjeta);
        $this->assertStringNotContainsString("/estudiante/intentos/$abierto/resultado", $tarjeta);
    }

    public function test_counter_counts_distinct_activities_across_levels_and_separates_points_from_level(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('niveles', 2);
        $basica = $this->activity('Básica', 1);
        $intermedia = $this->activity('Intermedia', 2);
        $this->attempt($estudiante, $basica, 30, '2026-11-01 09:00:00');
        $this->attempt($estudiante, $basica, 80, '2026-11-01 10:00:00');
        $this->attempt($estudiante, $intermedia, 60, '2026-11-01 11:00:00');
        DB::table('gamificacion')->insert(['estudiante_id' => $estudiante, 'puntos' => 45]);
        $logro = DB::table('logro')->insertGetId(['nombre' => 'Primera Evaluación', 'descripcion' => 'Logro de prueba']);
        DB::table('estudiante_logro')->insert(['id_estudiante' => $estudiante, 'id_logro' => $logro, 'fecha_obtencion' => '2026-11-01 08:00:00']);

        $html = $this->actingAs($usuario)->get(route('panel.estudiante'))->assertOk()->getContent();
        $texto = $this->text($html);
        $tarjetas = $this->cards($html);

        $this->assertStringContainsString('Lecturas completadas en total 2 ', $texto);
        $this->assertStringContainsString('Tu nivel lector: Intermedio', $texto);
        $this->assertStringContainsString('Mis puntos acumulados 45 Son tus puntos de juego. No cambian tu nivel lector.', $texto);
        $this->assertStringContainsString('Logros obtenidos 1', $texto);
        $this->assertSame([$intermedia], array_keys($tarjetas), 'Solo se listan las actividades del nivel actual.');
    }

    public function test_student_only_sees_own_results_and_dashboard_does_not_write(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('propio');
        [, $otro] = $this->student('ajeno');
        $actividad = $this->activity('Compartida', 1);
        $ajeno = $this->attempt($otro, $actividad, 100, '2026-11-01 09:00:00');
        $this->attempt($otro, $actividad, 100, '2026-11-01 10:00:00');

        $escrituras = [];
        DB::listen(function ($query) use (&$escrituras) {
            if (! preg_match('/^\s*select\b/i', $query->sql)) {
                $escrituras[] = $query->sql;
            }
        });
        $html = $this->actingAs($usuario)->get(route('panel.estudiante'))->assertOk()->getContent();

        $this->assertStringContainsString('Aún no has terminado esta lectura', $this->cards($html)[$actividad]);
        $this->assertStringNotContainsString("/estudiante/intentos/$ajeno/resultado", $html);
        $this->assertStringContainsString('Lecturas completadas en total 0 ', $this->text($html));
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
            $t->string('estado')->nullable();
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
        Schema::create('gamificacion', function (Blueprint $t) {
            $t->id('id_gamificacion');
            $t->unsignedBigInteger('estudiante_id')->unique();
            $t->integer('puntos')->default(0);
            $t->timestamps();
        });
        Schema::create('logro', function (Blueprint $t) {
            $t->id('id_logro');
            $t->string('nombre');
            $t->text('descripcion');
            $t->timestamps();
        });
        Schema::create('estudiante_logro', function (Blueprint $t) {
            $t->unsignedBigInteger('id_estudiante');
            $t->unsignedBigInteger('id_logro');
            $t->dateTime('fecha_obtencion');
            $t->timestamps();
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

    private function attempt(int $estudiante, int $actividad, ?float $puntaje, ?string $fin, ?string $inicio = null): int
    {
        return DB::table('actividad_intento')->insertGetId([
            'estudiante_id' => $estudiante, 'actividad_id' => $actividad, 'puntaje' => $puntaje, 'puntos_obtenidos' => 0,
            'fecha_inicio' => $inicio ?? date('Y-m-d H:i:s', strtotime($fin) - 60), 'fecha_fin' => $fin,
            'estado' => $fin === null ? 'EN_PROCESO' : 'FINALIZADO',
        ]);
    }

    /** @return array<int, string> Texto de cada tarjeta, indexado por actividad. */
    private function cards(string $html): array
    {
        preg_match_all('#<article class="student-activity-card">(.*?)</article>#s', $html, $coincidencias);
        $tarjetas = [];
        foreach ($coincidencias[1] as $tarjeta) {
            preg_match('#/estudiante/actividades/(\d+)"#', $tarjeta, $m);
            $tarjetas[(int) $m[1]] = $tarjeta.' '.$this->text($tarjeta);
        }

        return $tarjetas;
    }

    private function text(string $html): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html))));
    }
}
