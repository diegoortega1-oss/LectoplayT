<?php

namespace Tests\Feature;

use App\Models\ActividadIntento;
use App\Models\Usuario;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use Tests\TestCase;

class StudentActivityFlowTest extends TestCase
{
    private int $studentId;
    private int $activityId;
    private array $questionIds;

    public function test_isolation_preflight(): void
    {
        $this->assertIsolated();
    }

    private function assertIsolated(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame('sqlite', DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME));
    }

    private function prepare(): Usuario
    {
        $this->assertIsolated();

        Schema::create('usuario', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('usuario');
            $table->string('password_hash');
            $table->string('rol');
            $table->boolean('estado');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('estudiante', function (Blueprint $table) {
            $table->id('id_estudiante');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('nivel_actual_id');
            $table->string('nombre');
            $table->string('apellido');
            $table->timestamps();
        });
        Schema::create('nivel', function (Blueprint $table) {
            $table->id('id_nivel');
            $table->string('nombre');
            $table->timestamps();
        });
        Schema::create('logro', function (Blueprint $table) {
            $table->id('id_logro');
            $table->string('nombre');
            $table->text('descripcion');
            $table->timestamps();
        });
        Schema::create('estudiante_logro', function (Blueprint $table) {
            $table->unsignedBigInteger('id_estudiante');
            $table->unsignedBigInteger('id_logro');
            $table->dateTime('fecha_obtencion');
            $table->timestamps();
        });
        Schema::create('evaluacion', function (Blueprint $table) {
            $table->id('id_evaluacion');
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('nivel_id');
            $table->dateTime('fecha');
            $table->timestamps();
        });
        Schema::create('actividad', function (Blueprint $table) {
            $table->id('id_actividad');
            $table->unsignedBigInteger('nivel_id');
            $table->string('titulo');
            $table->text('contenido');
            $table->string('estado');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('pregunta', function (Blueprint $table) {
            $table->id('id_pregunta');
            $table->unsignedBigInteger('actividad_id');
            $table->unsignedBigInteger('evaluacion_id')->nullable();
            $table->string('enunciado');
            foreach (['a', 'b', 'c', 'd'] as $letter) {
                $table->string('alternativa_'.$letter);
            }
            $table->string('respuesta_correcta');
            $table->timestamps();
        });
        Schema::create('actividad_intento', function (Blueprint $table) {
            $table->id('id_intento');
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('actividad_id');
            $table->decimal('puntaje', 5, 2)->nullable();
            $table->integer('puntos_obtenidos')->nullable();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->string('estado');
            $table->timestamps();
        });
        Schema::create('respuesta_actividad', function (Blueprint $table) {
            $table->id('id_respuesta_actividad');
            $table->unsignedBigInteger('intento_id');
            $table->unsignedBigInteger('pregunta_id');
            $table->string('alternativa');
            $table->boolean('es_correcta');
            $table->timestamps();
            $table->unique(['intento_id', 'pregunta_id']);
        });
        Schema::create('gamificacion', function (Blueprint $table) {
            $table->id('id_gamificacion');
            $table->unsignedBigInteger('estudiante_id')->unique();
            $table->integer('puntos')->default(0);
            $table->timestamps();
        });
        Schema::create('progreso', function (Blueprint $table) {
            $table->id('id_progreso');
            $table->unsignedBigInteger('estudiante_id')->unique();
            $table->integer('actividades_completadas')->default(0);
            $table->decimal('puntaje_acumulado', 8, 2)->default(0);
            $table->timestamps();
        });

        DB::table('nivel')->insert(['id_nivel' => 1, 'nombre' => 'Nivel de prueba']);
        $userId = DB::table('usuario')->insertGetId(['usuario' => 'test_student', 'password_hash' => 'unused', 'rol' => 'ESTUDIANTE', 'estado' => 1]);
        $this->studentId = DB::table('estudiante')->insertGetId(['id_usuario' => $userId, 'nivel_actual_id' => 1, 'nombre' => 'Test', 'apellido' => 'Student']);
        $this->activityId = DB::table('actividad')->insertGetId(['nivel_id' => 1, 'titulo' => 'Lectura', 'contenido' => 'Texto de prueba', 'estado' => 'ACTIVA']);
        foreach (['A', 'B'] as $correct) {
            $this->questionIds[] = DB::table('pregunta')->insertGetId([
                'actividad_id' => $this->activityId, 'enunciado' => 'Pregunta',
                'alternativa_a' => 'A', 'alternativa_b' => 'B', 'alternativa_c' => 'C', 'alternativa_d' => 'D',
                'respuesta_correcta' => $correct,
            ]);
        }

        $user = Usuario::findOrFail($userId);
        $this->actingAs($user);
        config()->set('gamification.attempt_cutoff_id', 0);

        return $user;
    }

    private function startAttempt(): int
    {
        $this->post(route('student.activities.start', $this->activityId))->assertRedirect();

        return (int) DB::table('actividad_intento')->latest('id_intento')->value('id_intento');
    }

    public function test_dashboard_counts_distinct_finished_activities_for_current_student(): void
    {
        $this->prepare();
        DB::table('progreso')->insert([
            'estudiante_id' => $this->studentId, 'actividades_completadas' => 99, 'puntaje_acumulado' => 0,
        ]);

        $assertCount = function (int $expected): void {
            $this->get(route('panel.estudiante'))->assertOk()
                ->assertSeeInOrder(['Actividades completadas', '<strong>'.$expected.'</strong>'], false);
        };

        $assertCount(0);
        $first = $this->startAttempt();
        $assertCount(0);
        $this->post(route('student.attempts.finish', $first), ['respuestas' => []])->assertRedirect();
        $assertCount(1);

        $retry = $this->startAttempt();
        $assertCount(1);
        $this->post(route('student.attempts.finish', $retry), ['respuestas' => []])->assertRedirect();
        $assertCount(1);

        $otherActivityId = DB::table('actividad')->insertGetId([
            'nivel_id' => 1, 'titulo' => 'Segunda lectura', 'contenido' => 'Texto', 'estado' => 'ACTIVA',
        ]);
        DB::table('actividad_intento')->insert([
            'estudiante_id' => $this->studentId, 'actividad_id' => $otherActivityId,
            'fecha_inicio' => now(), 'estado' => 'FINALIZADO', 'puntaje' => 0,
        ]);
        $assertCount(2);

        $otherUserId = DB::table('usuario')->insertGetId([
            'usuario' => 'count_other_student', 'password_hash' => 'unused', 'rol' => 'ESTUDIANTE', 'estado' => 1,
        ]);
        $otherStudentId = DB::table('estudiante')->insertGetId([
            'id_usuario' => $otherUserId, 'nivel_actual_id' => 1, 'nombre' => 'Other', 'apellido' => 'Student',
        ]);
        DB::table('actividad_intento')->insert([
            'estudiante_id' => $otherStudentId, 'actividad_id' => $this->activityId,
            'fecha_inicio' => now(), 'estado' => 'FINALIZADO', 'puntaje' => 100,
        ]);
        $assertCount(2);

        $this->actingAs(Usuario::findOrFail($otherUserId));
        $assertCount(1);
        $this->assertDatabaseHas('progreso', ['estudiante_id' => $this->studentId, 'actividades_completadas' => 99]);
    }

    public function test_dashboard_tracks_activity_state_and_latest_percentage_through_retries(): void
    {
        $this->prepare();
        $this->get(route('panel.estudiante'))->assertOk()->assertSee('Nueva')
            ->assertDontSee('Último porcentaje:');
        $first = $this->startAttempt();
        $this->get(route('panel.estudiante'))->assertOk()->assertSee('En progreso')
            ->assertSee(route('student.attempts.edit', $first))->assertSee('Continuar intento');
        $this->post(route('student.attempts.finish', $first), ['respuestas' => [
            $this->questionIds[0] => 'A', $this->questionIds[1] => 'B',
        ]])->assertRedirect();
        $this->get(route('panel.estudiante'))->assertOk()->assertSee('Resuelta')
            ->assertSee('Último porcentaje: 100,00%')->assertSee('Repetir actividad');

        $second = $this->startAttempt();
        $this->get(route('panel.estudiante'))->assertOk()->assertSee('Resuelta')
            ->assertDontSee('En progreso')->assertSee('Último porcentaje: 100,00%');
        $this->post(route('student.attempts.finish', $second), ['respuestas' => []])->assertRedirect();
        $this->get(route('panel.estudiante'))->assertOk()->assertSee('Resuelta')
            ->assertSee('Último porcentaje: 0,00%')->assertDontSee('Último porcentaje: 100,00%');
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 10]);
    }

    public function test_dashboard_does_not_use_another_students_attempts(): void
    {
        $this->prepare();
        $otherUserId = DB::table('usuario')->insertGetId([
            'usuario' => 'other_dashboard_student', 'password_hash' => 'unused', 'rol' => 'ESTUDIANTE', 'estado' => 1,
        ]);
        $otherStudentId = DB::table('estudiante')->insertGetId([
            'id_usuario' => $otherUserId, 'nivel_actual_id' => 1, 'nombre' => 'Other', 'apellido' => 'Student',
        ]);
        foreach (['FINALIZADO', 'EN_PROCESO'] as $state) {
            DB::table('actividad_intento')->insert([
                'estudiante_id' => $otherStudentId, 'actividad_id' => $this->activityId,
                'estado' => $state, 'fecha_inicio' => now(), 'puntaje' => $state === 'FINALIZADO' ? 75 : null,
                'fecha_fin' => $state === 'FINALIZADO' ? now() : null,
            ]);
        }
        $this->get(route('panel.estudiante'))->assertOk()->assertSee('Nueva')
            ->assertDontSee('Resuelta')->assertDontSee('En progreso')->assertDontSee('Último porcentaje:');
    }

    public function test_start_and_draft_continuation(): void
    {
        $this->prepare();
        $id = $this->startAttempt();
        $this->assertDatabaseHas('actividad_intento', ['id_intento' => $id, 'estudiante_id' => $this->studentId, 'estado' => 'EN_PROCESO']);
        $this->assertNull(ActividadIntento::findOrFail($id)->puntos_obtenidos);

        $this->post(route('student.attempts.save', $id), ['respuestas' => [$this->questionIds[0] => 'B']])->assertRedirect();
        $this->get(route('student.attempts.edit', $id))->assertOk();
        $this->post(route('student.attempts.save', $id), ['respuestas' => [$this->questionIds[0] => 'A', $this->questionIds[1] => 'D']])->assertRedirect();

        $this->assertSame(1, DB::table('actividad_intento')->count());
        $this->assertDatabaseHas('respuesta_actividad', ['intento_id' => $id, 'pregunta_id' => $this->questionIds[0], 'alternativa' => 'A', 'es_correcta' => 1]);
        $this->assertDatabaseHas('respuesta_actividad', ['intento_id' => $id, 'pregunta_id' => $this->questionIds[1], 'alternativa' => 'D', 'es_correcta' => 0]);
    }

    public function test_omissions_and_percentage_bounds(): void
    {
        $this->prepare();
        foreach ([0 => [], 50 => [$this->questionIds[0] => 'A'], 100 => [$this->questionIds[0] => 'A', $this->questionIds[1] => 'B']] as $score => $answers) {
            $id = $this->startAttempt();
            $this->post(route('student.attempts.finish', $id), ['respuestas' => $answers])->assertRedirect(route('student.attempts.result', $id));
            $this->assertDatabaseHas('actividad_intento', ['id_intento' => $id, 'estado' => 'FINALIZADO', 'puntaje' => $score]);
            $this->assertSame(count($answers), DB::table('respuesta_actividad')->where('intento_id', $id)->count());
            $this->get(route('student.attempts.result', $id))
                ->assertOk()
                ->assertSee('Porcentaje de este intento')
                ->assertSee('Respuestas correctas')
                ->assertSee('Respuestas incorrectas')
                ->assertSee('Incluye las preguntas sin responder')
                ->assertSeeInOrder([
                    'Respuestas correctas', '<strong>'.($score / 50).'</strong>',
                    'Respuestas incorrectas', '<strong>'.(2 - $score / 50).'</strong>',
                ], false);
        }
    }

    public function test_finalized_attempt_cannot_change_or_finish_again_and_history_remains(): void
    {
        $this->prepare();
        $first = $this->startAttempt();
        $this->post(route('student.attempts.finish', $first), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $snapshot = DB::table('actividad_intento')->where('id_intento', $first)->first();
        $answers = DB::table('respuesta_actividad')->where('intento_id', $first)->get()->toArray();

        $this->post(route('student.attempts.save', $first), ['respuestas' => [$this->questionIds[0] => 'B']])->assertStatus(409);
        $this->post(route('student.attempts.finish', $first), ['respuestas' => [$this->questionIds[0] => 'B']])->assertStatus(409);
        $second = $this->startAttempt();

        $this->assertNotSame($first, $second);
        $this->assertEquals($snapshot, DB::table('actividad_intento')->where('id_intento', $first)->first());
        $this->assertEquals($answers, DB::table('respuesta_actividad')->where('intento_id', $first)->get()->toArray());
        $this->get(route('student.activities.history'))->assertOk();
    }

    public function test_other_student_cannot_access_or_change_attempt(): void
    {
        $this->prepare();
        $id = $this->startAttempt();
        $otherUserId = DB::table('usuario')->insertGetId(['usuario' => 'other_student', 'password_hash' => 'unused', 'rol' => 'ESTUDIANTE', 'estado' => 1]);
        DB::table('estudiante')->insert(['id_usuario' => $otherUserId, 'nivel_actual_id' => 1, 'nombre' => 'Other', 'apellido' => 'Student']);
        $this->actingAs(Usuario::findOrFail($otherUserId));

        $this->get(route('student.attempts.edit', $id))->assertNotFound();
        $this->get(route('student.attempts.result', $id))->assertNotFound();
        $this->post(route('student.attempts.save', $id), ['respuestas' => [$this->questionIds[0] => 'A']])->assertNotFound();
        $this->post(route('student.attempts.finish', $id), ['respuestas' => [$this->questionIds[0] => 'A']])->assertNotFound();
        $this->assertSame(0, DB::table('respuesta_actividad')->count());
        $this->assertDatabaseHas('actividad_intento', ['id_intento' => $id, 'estado' => 'EN_PROCESO']);
    }

    public function test_first_eligible_attempt_adds_five_points_per_correct_answer_only_once(): void
    {
        $this->prepare();
        DB::table('progreso')->insert(['estudiante_id' => $this->studentId, 'actividades_completadas' => 3, 'puntaje_acumulado' => 75]);
        $first = $this->startAttempt();
        $this->post(route('student.attempts.finish', $first), ['respuestas' => [$this->questionIds[0] => 'A', $this->questionIds[1] => 'B']])->assertRedirect();
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 10]);
        $this->assertSame(10, ActividadIntento::findOrFail($first)->puntos_obtenidos);
        $this->get(route('student.attempts.result', $first))->assertOk()->assertSee('10 puntos obtenidos.');
        $this->assertDatabaseHas('progreso', ['estudiante_id' => $this->studentId, 'actividades_completadas' => 3, 'puntaje_acumulado' => 75]);

        $second = $this->startAttempt();
        $this->post(route('student.attempts.finish', $second), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $this->assertDatabaseHas('actividad_intento', ['id_intento' => $second, 'estado' => 'FINALIZADO', 'puntaje' => 50]);
        $this->assertSame(0, ActividadIntento::findOrFail($second)->puntos_obtenidos);
        $this->get(route('student.attempts.result', $second))->assertOk()->assertSee('0 puntos obtenidos.');
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 10]);
        $this->post(route('student.attempts.finish', $first), ['respuestas' => [$this->questionIds[0] => 'A']])->assertStatus(409);
        $this->assertSame(10, ActividadIntento::findOrFail($first)->puntos_obtenidos);
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 10]);
    }

    public function test_zero_correct_consumes_opportunity_even_without_existing_gamification_row(): void
    {
        $this->prepare();
        $first = $this->startAttempt();
        $this->post(route('student.attempts.finish', $first), ['respuestas' => []])->assertRedirect();
        $this->assertSame(0, ActividadIntento::findOrFail($first)->puntos_obtenidos);
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 0]);

        $second = $this->startAttempt();
        $this->post(route('student.attempts.finish', $second), ['respuestas' => [$this->questionIds[0] => 'A', $this->questionIds[1] => 'B']])->assertRedirect();
        $this->assertDatabaseHas('actividad_intento', ['id_intento' => $second, 'puntaje' => 100]);
        $this->assertSame(0, ActividadIntento::findOrFail($second)->puntos_obtenidos);
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 0]);
    }

    public function test_historical_attempts_never_receive_points_and_do_not_consume_new_opportunity(): void
    {
        $this->prepare();
        $historical = $this->startAttempt();
        config()->set('gamification.attempt_cutoff_id', $historical);
        $this->post(route('student.attempts.finish', $historical), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $this->assertSame(0, DB::table('gamificacion')->count());

        $eligible = $this->startAttempt();
        $this->post(route('student.attempts.finish', $eligible), ['respuestas' => [$this->questionIds[1] => 'B']])->assertRedirect();
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 5]);
    }

    public function test_missing_cutoff_keeps_rewards_disabled(): void
    {
        $this->prepare();
        config()->set('gamification.attempt_cutoff_id', null);
        $id = $this->startAttempt();
        $this->post(route('student.attempts.finish', $id), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $this->assertDatabaseHas('actividad_intento', ['id_intento' => $id, 'puntaje' => 50]);
        $this->assertSame(0, ActividadIntento::findOrFail($id)->puntos_obtenidos);
        $this->assertSame(0, DB::table('gamificacion')->count());
        $this->get(route('student.attempts.result', $id))
            ->assertOk()
            ->assertSee('Porcentaje de este intento')
            ->assertSee('50,00%')
            ->assertSee('Aún no hay puntos registrados')
            ->assertSee('Las recompensas por actividades aún no están activas');
    }

    public function test_existing_gamification_points_are_incremented_without_touching_progress(): void
    {
        $this->prepare();
        DB::table('gamificacion')->insert(['estudiante_id' => $this->studentId, 'puntos' => 7]);
        $id = $this->startAttempt();
        $this->post(route('student.attempts.finish', $id), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 12]);
        $this->assertSame(0, DB::table('progreso')->count());
    }

    public function test_existing_accumulated_points_are_shown_separately_from_attempt_percentage(): void
    {
        $this->prepare();
        config()->set('gamification.attempt_cutoff_id', null);
        DB::table('gamificacion')->insert(['estudiante_id' => $this->studentId, 'puntos' => 35]);
        $id = $this->startAttempt();
        $this->post(route('student.attempts.finish', $id), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();

        $this->get(route('student.attempts.result', $id))
            ->assertOk()
            ->assertSee('Porcentaje de este intento')
            ->assertSee('50,00%')
            ->assertSee('Mis puntos acumulados')
            ->assertSee('35 puntos en total')
            ->assertSee('Las recompensas por actividades aún no están activas');
        $this->get(route('panel.estudiante'))
            ->assertOk()
            ->assertSee('Mis puntos acumulados')
            ->assertSee('35');
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 35]);
    }

    public function test_reward_failure_rolls_back_answers_and_completion(): void
    {
        $this->prepare();
        $id = $this->startAttempt();
        Schema::drop('gamificacion'); // Solo la base SQLite :memory: de esta prueba.
        $this->withoutExceptionHandling();

        try {
            $this->post(route('student.attempts.finish', $id), ['respuestas' => [$this->questionIds[0] => 'A']]);
            $this->fail('Se esperaba un error al persistir la recompensa.');
        } catch (\Illuminate\Database\QueryException $exception) {
            $this->assertDatabaseHas('actividad_intento', ['id_intento' => $id, 'estado' => 'EN_PROCESO', 'puntaje' => null]);
            $this->assertNull(ActividadIntento::findOrFail($id)->puntos_obtenidos);
            $this->assertSame(0, DB::table('respuesta_actividad')->where('intento_id', $id)->count());
        }
    }

    public function test_cutoff_14_preserves_unknown_historical_points_and_records_new_rewards(): void
    {
        $this->prepare();
        config()->set('gamification.attempt_cutoff_id', 14);
        foreach ([13 => 'FINALIZADO', 14 => 'EN_PROCESO'] as $id => $state) {
            DB::table('actividad_intento')->insert([
                'id_intento' => $id, 'estudiante_id' => $this->studentId,
                'actividad_id' => $this->activityId, 'estado' => $state,
                'fecha_inicio' => now(),
            ]);
        }
        $historical = DB::table('actividad_intento')->where('id_intento', 13)->first();
        $this->get(route('student.attempts.result', 13))->assertOk()->assertSee('Dato no disponible para este intento.');
        $this->post(route('student.attempts.finish', 13))->assertStatus(409);
        $this->post(route('student.attempts.finish', 14), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $this->assertNull(ActividadIntento::findOrFail(14)->puntos_obtenidos);
        $this->assertSame(0, DB::table('gamificacion')->count());

        $new = $this->startAttempt();
        $this->assertSame(15, $new);
        $this->post(route('student.attempts.finish', $new), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $this->assertSame(5, ActividadIntento::findOrFail($new)->puntos_obtenidos);
        $this->assertEquals($historical, DB::table('actividad_intento')->where('id_intento', 13)->first());
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 5]);
    }

    public function test_legacy_finalized_eligible_attempt_with_null_points_still_blocks_repeat_rewards(): void
    {
        $this->prepare();
        config()->set('gamification.attempt_cutoff_id', 14);
        DB::table('actividad_intento')->insert([
            'id_intento' => 15, 'estudiante_id' => $this->studentId,
            'actividad_id' => $this->activityId, 'estado' => 'FINALIZADO', 'fecha_inicio' => now(),
        ]);
        $id = $this->startAttempt();
        $this->post(route('student.attempts.finish', $id), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $this->assertSame(0, ActividadIntento::findOrFail($id)->puntos_obtenidos);
        $this->assertNull(ActividadIntento::findOrFail(15)->puntos_obtenidos);
        $this->assertSame(0, DB::table('gamificacion')->count());
    }

    public function test_interruption_after_completion_rolls_back_reward_and_recorded_points(): void
    {
        $this->prepare();
        DB::table('gamificacion')->insert(['estudiante_id' => $this->studentId, 'puntos' => 7]);
        $id = $this->startAttempt();
        $before = DB::table('actividad_intento')->where('id_intento', $id)->first();
        $this->withoutExceptionHandling();
        $dispatcher = ActividadIntento::getEventDispatcher();
        ActividadIntento::setEventDispatcher(clone $dispatcher);
        ActividadIntento::updated(function (ActividadIntento $intento): void {
            if ($intento->estado === 'FINALIZADO') {
                throw new \RuntimeException('Interrupción de prueba antes del commit.');
            }
        });

        try {
            $this->post(route('student.attempts.finish', $id), ['respuestas' => [$this->questionIds[0] => 'A']]);
            $this->fail('La entrega debía interrumpirse antes del commit.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Interrupción de prueba antes del commit.', $exception->getMessage());
        } finally {
            ActividadIntento::setEventDispatcher($dispatcher);
        }

        $this->assertEquals($before, DB::table('actividad_intento')->where('id_intento', $id)->first());
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 7]);
        $this->assertSame(0, DB::table('respuesta_actividad')->where('intento_id', $id)->count());
        $this->post(route('student.attempts.finish', $id), ['respuestas' => [$this->questionIds[0] => 'A']])->assertRedirect();
        $this->assertSame(5, ActividadIntento::findOrFail($id)->puntos_obtenidos);
        $this->assertDatabaseHas('gamificacion', ['estudiante_id' => $this->studentId, 'puntos' => 12]);
    }
}
