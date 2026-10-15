<?php

namespace Tests\Feature;

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

        return $user;
    }

    private function startAttempt(): int
    {
        $this->post(route('student.activities.start', $this->activityId))->assertRedirect();

        return (int) DB::table('actividad_intento')->latest('id_intento')->value('id_intento');
    }

    public function test_start_and_draft_continuation(): void
    {
        $this->prepare();
        $id = $this->startAttempt();
        $this->assertDatabaseHas('actividad_intento', ['id_intento' => $id, 'estudiante_id' => $this->studentId, 'estado' => 'EN_PROCESO']);

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
            $this->get(route('student.attempts.result', $id))->assertOk();
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
}
