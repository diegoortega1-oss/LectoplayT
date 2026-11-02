<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use Tests\TestCase;

/**
 * Historial de lecturas: primer intento, reintentos, mejor resultado y uso en adaptaciones.
 *
 * Usa exclusivamente SQLite en memoria con un esquema propio; nunca toca MariaDB.
 */
class StudentHistoryTest extends TestCase
{
    private int $nivelBasico = 1;

    private int $nivelIntermedio = 2;

    public function test_isolation_preflight(): void
    {
        $this->assertIsolated();
    }

    public function test_single_attempt_is_marked_as_first_without_best_or_practice(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('uno');
        $actividad = $this->activity('Lectura única', $this->nivelBasico);
        $intento = $this->attempt($estudiante, $actividad, 60, '2026-11-01 10:00:00');

        $tarjetas = $this->cards($this->actingAs($usuario)->get(route('student.activities.history'))->assertOk()->getContent());

        $this->assertStringContainsString('Primer intento', $tarjetas[$intento]);
        $this->assertStringContainsString('Lectura de nivel Básico', $tarjetas[$intento]);
        $this->assertStringContainsString('Obtuviste 60,00 %', $tarjetas[$intento]);
        $this->assertStringNotContainsString('Volviste a practicar', $tarjetas[$intento]);
        $this->assertStringNotContainsString('Tu mejor resultado', $tarjetas[$intento]);
    }

    public function test_first_attempt_follows_engine_order_and_best_result_uses_finished_attempts(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('varios');
        $actividad = $this->activity('Lectura repetida', $this->nivelBasico);
        // El id más bajo termina después: el primer intento lo decide fecha_fin, como en el motor.
        $tardio = $this->attempt($estudiante, $actividad, 50, '2026-11-01 10:30:00', '2026-11-01 10:00:00');
        $temprano = $this->attempt($estudiante, $actividad, 40, '2026-11-01 09:30:00', '2026-11-01 09:00:00');
        $mejor = $this->attempt($estudiante, $actividad, 90, '2026-11-01 11:00:00');
        $empate = $this->attempt($estudiante, $actividad, 90, '2026-11-01 12:00:00');
        $abierto = $this->attempt($estudiante, $actividad, null, null, '2026-11-01 13:00:00');

        $tarjetas = $this->cards($this->actingAs($usuario)->get(route('student.activities.history'))->assertOk()->getContent());

        $this->assertStringContainsString('Primer intento', $tarjetas[$temprano]);
        foreach ([$tardio, $mejor, $empate] as $id) {
            $this->assertStringContainsString('Volviste a practicar', $tarjetas[$id]);
            $this->assertStringContainsString('no reemplaza al de tu primer intento', $tarjetas[$id]);
            $this->assertStringContainsString('Tu mejor resultado en esta lectura: 90,00 %', $tarjetas[$id]);
        }
        $this->assertStringContainsString('⭐ Tu mejor resultado', $tarjetas[$mejor]);
        $this->assertStringNotContainsString('⭐ Tu mejor resultado', $tarjetas[$empate]);
        $this->assertStringNotContainsString('⭐ Tu mejor resultado', $tarjetas[$temprano]);
        $this->assertArrayNotHasKey($abierto, $tarjetas, 'Un intento en proceso no tiene enlace de resultado.');
    }

    public function test_attempts_used_in_adaptations_and_next_review_are_distinguished(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('adaptado', $this->nivelIntermedio);
        $this->diagnostic($estudiante, $this->nivelBasico, '2026-11-01 08:00:00');
        $a1 = $this->activity('Básica 1', $this->nivelBasico);
        $a2 = $this->activity('Básica 2', $this->nivelBasico);
        $a3 = $this->activity('Básica 3', $this->nivelBasico);
        $i1 = $this->attempt($estudiante, $a1, 100, '2026-11-01 09:00:00');
        $i2 = $this->attempt($estudiante, $a2, 90, '2026-11-01 09:10:00');
        $i3 = $this->attempt($estudiante, $a3, 80, '2026-11-01 09:20:00');
        $this->adaptation($estudiante, 'SUBIR', $this->nivelBasico, $this->nivelIntermedio, '2026-11-01 09:20:00', [[$a1, $i1, 100], [$a2, $i2, 90], [$a3, $i3, 80]]);
        $intermedia = $this->activity('Intermedia 1', $this->nivelIntermedio);
        $pendiente = $this->attempt($estudiante, $intermedia, 70, '2026-11-01 10:00:00', '2026-11-01 09:50:00');

        $tarjetas = $this->cards($this->actingAs($usuario)->get(route('student.activities.history'))->assertOk()->getContent());

        foreach ([$i1, $i2, $i3] as $id) {
            $this->assertStringContainsString('Esta lectura contó para un cambio de nivel', $tarjetas[$id]);
        }
        $this->assertStringContainsString('Esta lectura cuenta para tu próxima revisión de nivel', $tarjetas[$pendiente]);
        $this->assertStringNotContainsString('contó para', $tarjetas[$pendiente]);
    }

    public function test_maintain_decision_is_described_as_level_review_not_level_change(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('mantiene');
        $a1 = $this->activity('Mantener 1', $this->nivelBasico);
        $i1 = $this->attempt($estudiante, $a1, 30, '2026-11-01 09:00:00');
        $this->adaptation($estudiante, 'MANTENER', $this->nivelBasico, $this->nivelBasico, '2026-11-01 09:30:00', [[$a1, $i1, 30]]);

        $tarjetas = $this->cards($this->actingAs($usuario)->get(route('student.activities.history'))->assertOk()->getContent());

        $this->assertStringContainsString('Esta lectura contó para revisar tu nivel', $tarjetas[$i1]);
        $this->assertStringNotContainsString('cambio de nivel', $tarjetas[$i1]);
    }

    public function test_student_without_attempts_sees_empty_state(): void
    {
        $this->prepare();
        [$usuario] = $this->student('vacio');

        $this->actingAs($usuario)->get(route('student.activities.history'))
            ->assertOk()
            ->assertSee('Aún no hay intentos')
            ->assertDontSee('Cómo leer tu historial', false);
    }

    public function test_soft_deleted_activity_keeps_history_but_cannot_be_continued(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('borrada');
        $actividad = $this->activity('Lectura retirada', $this->nivelBasico);
        $terminado = $this->attempt($estudiante, $actividad, 70, '2026-11-01 09:00:00');
        $this->attempt($estudiante, $actividad, null, null, '2026-11-01 10:00:00');
        DB::table('actividad')->where('id_actividad', $actividad)->update(['deleted_at' => '2026-11-01 11:00:00']);

        $html = $this->actingAs($usuario)->get(route('student.activities.history'))->assertOk()->getContent();
        $tarjetas = $this->cards($html);

        $this->assertStringContainsString('Lectura retirada', $tarjetas[$terminado]);
        $this->assertStringContainsString('Lectura de nivel Básico', $tarjetas[$terminado]);
        $this->assertStringContainsString('No se puede continuar esta actividad', $html);
        $this->assertStringNotContainsString('Continuar →', $html);
    }

    public function test_student_only_sees_own_attempts_and_pagination_is_kept(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('propio');
        [, $otro] = $this->student('ajeno');
        $actividad = $this->activity('Lectura compartida', $this->nivelBasico);
        $ajeno = $this->attempt($otro, $actividad, 100, '2026-11-01 08:00:00');
        $propios = [];
        for ($i = 0; $i < 12; $i++) {
            $propios[] = $this->attempt($estudiante, $actividad, 10 * ($i % 10), sprintf('2026-11-01 %02d:00:00', 9 + $i));
        }

        $pagina1 = $this->cards($this->actingAs($usuario)->get(route('student.activities.history'))->assertOk()->assertSee('page=2', false)->getContent());
        $pagina2 = $this->cards($this->actingAs($usuario)->get(route('student.activities.history', ['page' => 2]))->assertOk()->getContent());

        $this->assertCount(10, $pagina1);
        $this->assertCount(2, $pagina2);
        $this->assertSame([], array_diff(array_merge(array_keys($pagina1), array_keys($pagina2)), $propios));
        $this->assertArrayNotHasKey($ajeno, $pagina1 + $pagina2);
    }

    public function test_history_does_not_write_to_the_database(): void
    {
        $this->prepare();
        [$usuario, $estudiante] = $this->student('lectura');
        $actividad = $this->activity('Solo lectura', $this->nivelBasico);
        $this->attempt($estudiante, $actividad, 80, '2026-11-01 09:00:00');

        $escrituras = [];
        DB::listen(function ($query) use (&$escrituras) {
            if (! preg_match('/^\s*select\b/i', $query->sql)) {
                $escrituras[] = $query->sql;
            }
        });
        $this->actingAs($usuario)->get(route('student.activities.history'))->assertOk();

        $this->assertSame([], $escrituras);
    }

    // ------------------------------------------------------------------
    // Preparación
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
            $t->string('nombre');
            $t->string('estado');
            $t->timestamps();
        });
        Schema::create('evaluacion', function (Blueprint $t) {
            $t->id('id_evaluacion');
            $t->unsignedBigInteger('estudiante_id');
            $t->unsignedBigInteger('cuestionario_id')->nullable();
            $t->string('tipo');
            $t->string('estado')->nullable();
            $t->dateTime('fecha');
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
            $t->unsignedBigInteger('intento_id')->unique();
            $t->decimal('puntaje', 5, 2);
            $t->unique(['estudiante_id', 'actividad_id']);
        });

        DB::table('nivel')->insert([
            ['id_nivel' => $this->nivelBasico, 'nombre' => 'Básico', 'rango_min' => 0, 'rango_max' => 59.99],
            ['id_nivel' => $this->nivelIntermedio, 'nombre' => 'Intermedio', 'rango_min' => 60, 'rango_max' => 79.99],
            ['id_nivel' => 3, 'nombre' => 'Avanzado', 'rango_min' => 80, 'rango_max' => 100],
        ]);
    }

    /** @return array{0: Usuario, 1: int} */
    private function student(string $nombre, ?int $nivel = null): array
    {
        $usuarioId = DB::table('usuario')->insertGetId([
            'usuario' => "test_$nombre", 'password_hash' => 'sin-uso', 'rol' => Usuario::ROL_ESTUDIANTE, 'estado' => 1,
        ]);
        $estudianteId = DB::table('estudiante')->insertGetId([
            'id_usuario' => $usuarioId, 'nivel_actual_id' => $nivel ?? $this->nivelBasico, 'nombre' => ucfirst($nombre), 'apellido' => 'Prueba',
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
            'estudiante_id' => $estudiante,
            'actividad_id' => $actividad,
            'puntaje' => $puntaje,
            'puntos_obtenidos' => 0,
            'fecha_inicio' => $inicio ?? date('Y-m-d H:i:s', strtotime($fin) - 60),
            'fecha_fin' => $fin,
            'estado' => $fin === null ? 'EN_PROCESO' : 'FINALIZADO',
        ]);
    }

    private function diagnostic(int $estudiante, int $nivel, string $fin): void
    {
        $cuestionario = DB::table('cuestionario_diagnostico')->insertGetId(['nombre' => 'Diagnóstico', 'estado' => 'CERRADO']);
        DB::table('evaluacion')->insert([
            'estudiante_id' => $estudiante, 'cuestionario_id' => $cuestionario, 'tipo' => 'DIAGNOSTICA', 'estado' => 'FINALIZADA',
            'fecha' => $fin, 'fecha_fin' => $fin, 'puntaje' => 40, 'nivel_id' => $nivel,
        ]);
    }

    /** @param list<array{0: int, 1: int, 2: float}> $detalles */
    private function adaptation(int $estudiante, string $decision, int $anterior, int $resultante, string $fecha, array $detalles): void
    {
        $id = DB::table('adaptacion_nivel')->insertGetId([
            'estudiante_id' => $estudiante, 'nivel_anterior_id' => $anterior, 'nivel_resultante_id' => $resultante,
            'decision' => $decision, 'promedio' => 50, 'fecha' => $fecha,
        ]);
        foreach ($detalles as [$actividad, $intento, $puntaje]) {
            DB::table('adaptacion_nivel_actividad')->insert([
                'adaptacion_id' => $id, 'estudiante_id' => $estudiante, 'actividad_id' => $actividad, 'intento_id' => $intento, 'puntaje' => $puntaje,
            ]);
        }
    }

    /** @return array<int, string> Texto de cada tarjeta finalizada, indexado por intento. */
    private function cards(string $html): array
    {
        preg_match_all('#<article class="reading-history-card">(.*?)</article>#s', $html, $coincidencias);
        $tarjetas = [];
        foreach ($coincidencias[1] as $tarjeta) {
            if (preg_match('#/estudiante/intentos/(\d+)/resultado#', $tarjeta, $m)) {
                $tarjetas[(int) $m[1]] = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($tarjeta))));
            }
        }

        return $tarjetas;
    }
}
