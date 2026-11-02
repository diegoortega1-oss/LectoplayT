<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Primer lote de actividades de comprensión lectora de nivel Avanzado (paso 21AE).
 *
 * - Amplía las actividades 7 y 8: nueva lectura, distractores mejorados en sus dos
 *   preguntas originales y ocho preguntas nuevas en cada una.
 * - Crea la actividad «Videojuegos: ¿aliados o enemigos del aprendizaje?».
 * - Deja las tres actividades en BORRADOR para revisarlas antes de publicarlas.
 *
 * Ejecución manual y explícita:
 *   php artisan db:seed --class=ActividadesAvanzadoLote1Seeder
 *
 * Todo ocurre en una sola transacción. Cualquier inconsistencia lanza una excepción
 * y revierte la carga completa. Una segunda ejecución detecta el lote ya aplicado y
 * no escribe nada. No borra registros ni toca otras actividades, niveles, estudiantes,
 * intentos, puntos ni adaptaciones.
 */
class ActividadesAvanzadoLote1Seeder extends Seeder
{
    private const NIVEL_AVANZADO_ID = 3;

    private const NIVEL_AVANZADO_NOMBRE = 'Avanzado';

    private const DIMENSIONES = ['LITERAL', 'INFERENCIAL', 'CRITICA'];

    private const DISTRIBUCION = ['LITERAL' => 3, 'INFERENCIAL' => 4, 'CRITICA' => 3];

    private const CLAVES = ['A', 'B', 'C', 'D'];

    public function run(): void
    {
        $actividades = $this->contenido();
        $this->validarContenido($actividades);

        DB::transaction(function () use ($actividades): void {
            $nivel = DB::table('nivel')->where('id_nivel', self::NIVEL_AVANZADO_ID)->first(['id_nivel', 'nombre']);
            if (! $nivel || $nivel->nombre !== self::NIVEL_AVANZADO_NOMBRE) {
                throw new RuntimeException('El nivel 3 no existe o no se llama Avanzado.');
            }

            $ahora = now();
            foreach ($actividades as $actividad) {
                $actividad['id_actividad'] === null
                    ? $this->procesarActividadNueva($actividad, $ahora)
                    : $this->procesarActividadExistente($actividad, $ahora);
            }
        });
    }

    // ---------------------------------------------------------------------
    // Actividades existentes (7 y 8)
    // ---------------------------------------------------------------------

    private function procesarActividadExistente(array $datos, $ahora): void
    {
        $id = $datos['id_actividad'];

        // El bloqueo impide que se inicien intentos sobre la actividad durante la carga:
        // insertar un intento exige un bloqueo compartido sobre la fila de la actividad.
        $actividad = DB::table('actividad')->where('id_actividad', $id)->lockForUpdate()
            ->first(['id_actividad', 'nivel_id', 'titulo', 'contenido', 'estado', 'deleted_at']);

        if (! $actividad) {
            throw new RuntimeException("La actividad $id no existe.");
        }
        if ((int) $actividad->nivel_id !== self::NIVEL_AVANZADO_ID || $actividad->titulo !== $datos['titulo']
            || $actividad->deleted_at !== null) {
            throw new RuntimeException("La actividad $id no coincide con el nivel, título o estado de borrado esperados.");
        }

        $preguntas = DB::table('pregunta')->where('actividad_id', $id)->orderBy('id_pregunta')->lockForUpdate()->get();

        if ($this->yaAplicada($actividad, $preguntas, $datos)) {
            $this->info("Actividad $id: el lote ya estaba aplicado. Sin cambios.");

            return;
        }

        $this->verificarEstadoOriginal($actividad, $preguntas, $datos);
        $this->verificarSinUso($id, $preguntas->pluck('id_pregunta')->all());

        $actualizadas = DB::table('actividad')->where('id_actividad', $id)
            ->where('contenido', $actividad->contenido)
            ->update(['contenido' => $datos['contenido'], 'estado' => 'BORRADOR', 'updated_at' => $ahora]);
        if ($actualizadas !== 1) {
            throw new RuntimeException("No se pudo actualizar la lectura de la actividad $id.");
        }

        foreach ($datos['preguntas'] as $orden => $pregunta) {
            $campos = [
                'dimension' => $pregunta['dimension'],
                'orden' => $orden + 1,
                'alternativa_a' => $pregunta['a'],
                'alternativa_b' => $pregunta['b'],
                'alternativa_c' => $pregunta['c'],
                'alternativa_d' => $pregunta['d'],
                'updated_at' => $ahora,
            ];

            if ($pregunta['id'] !== null) {
                // Pregunta original: se conservan enunciado y respuesta correcta.
                $filas = DB::table('pregunta')
                    ->where('id_pregunta', $pregunta['id'])
                    ->where('actividad_id', $id)
                    ->where('enunciado', $pregunta['enunciado'])
                    ->where('respuesta_correcta', $pregunta['correcta'])
                    ->update($campos);
                if ($filas !== 1) {
                    throw new RuntimeException("No se pudo actualizar la pregunta {$pregunta['id']}.");
                }

                continue;
            }

            $this->insertarPregunta($id, $pregunta, $orden + 1, $ahora);
        }

        $this->comprobarResultado($id, $datos);
        $this->info("Actividad $id: lectura ampliada, 2 preguntas actualizadas y 8 insertadas. Estado BORRADOR.");
    }

    private function verificarEstadoOriginal(object $actividad, $preguntas, array $datos): void
    {
        $id = $actividad->id_actividad;

        if ($this->normalizar($actividad->contenido) !== $datos['contenido_original']) {
            throw new RuntimeException("La lectura de la actividad $id no coincide con la original esperada.");
        }

        $esperadas = $datos['preguntas_originales'];
        if ($preguntas->count() !== count($esperadas)) {
            throw new RuntimeException("La actividad $id no tiene exactamente las preguntas originales esperadas.");
        }

        foreach ($esperadas as $original) {
            $actual = $preguntas->firstWhere('id_pregunta', $original['id']);
            if (! $actual
                || $actual->enunciado !== $original['enunciado']
                || $actual->respuesta_correcta !== $original['correcta']
                || $actual->alternativa_a !== $original['a'] || $actual->alternativa_b !== $original['b']
                || $actual->alternativa_c !== $original['c'] || $actual->alternativa_d !== $original['d']
                || $actual->evaluacion_id !== null || $actual->cuestionario_id !== null || $actual->lectura_diagnostica_id !== null) {
                throw new RuntimeException("La pregunta {$original['id']} no coincide con la original esperada.");
            }
        }

        // La alternativa correcta de cada pregunta original debe conservarse literalmente.
        foreach ($datos['preguntas'] as $pregunta) {
            if ($pregunta['id'] === null) {
                continue;
            }
            $original = collect($esperadas)->firstWhere('id', $pregunta['id']);
            $campo = strtolower($pregunta['correcta']);
            if ($pregunta['enunciado'] !== $original['enunciado'] || $pregunta['correcta'] !== $original['correcta']
                || $pregunta[$campo] !== $original[$campo]) {
                throw new RuntimeException("La pregunta {$pregunta['id']} cambiaría su enunciado o su respuesta correcta.");
            }
        }
    }

    private function verificarSinUso(int $actividadId, array $preguntaIds): void
    {
        if (DB::table('actividad_intento')->where('actividad_id', $actividadId)->exists()) {
            throw new RuntimeException("La actividad $actividadId ya tiene intentos.");
        }
        if (DB::table('respuesta_actividad')->whereIn('pregunta_id', $preguntaIds)->exists()) {
            throw new RuntimeException("Las preguntas de la actividad $actividadId ya tienen respuestas.");
        }
        if (DB::table('adaptacion_nivel_actividad')->where('actividad_id', $actividadId)->exists()) {
            throw new RuntimeException("La actividad $actividadId ya fue usada en una adaptación.");
        }
    }

    // ---------------------------------------------------------------------
    // Actividad nueva
    // ---------------------------------------------------------------------

    private function procesarActividadNueva(array $datos, $ahora): void
    {
        $existentes = DB::table('actividad')
            ->where('nivel_id', self::NIVEL_AVANZADO_ID)
            ->where('titulo', $datos['titulo'])
            ->lockForUpdate()
            ->get(['id_actividad', 'nivel_id', 'titulo', 'contenido', 'estado', 'deleted_at']);

        if ($existentes->count() > 1) {
            throw new RuntimeException("Hay más de una actividad titulada «{$datos['titulo']}».");
        }

        if ($existentes->count() === 1) {
            $actividad = $existentes->first();
            $preguntas = DB::table('pregunta')->where('actividad_id', $actividad->id_actividad)->orderBy('id_pregunta')->get();
            if (! $this->yaAplicada($actividad, $preguntas, $datos)) {
                throw new RuntimeException("Ya existe una actividad «{$datos['titulo']}» con contenido distinto al del lote.");
            }
            $this->info("Actividad nueva: ya existía con id {$actividad->id_actividad}. Sin cambios.");

            return;
        }

        $id = DB::table('actividad')->insertGetId([
            'nivel_id' => self::NIVEL_AVANZADO_ID,
            'titulo' => $datos['titulo'],
            'contenido' => $datos['contenido'],
            'estado' => 'BORRADOR',
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        foreach ($datos['preguntas'] as $orden => $pregunta) {
            $this->insertarPregunta((int) $id, $pregunta, $orden + 1, $ahora);
        }

        $this->comprobarResultado((int) $id, $datos);
        $this->info("Actividad nueva creada con id $id: 10 preguntas insertadas. Estado BORRADOR.");
    }

    // ---------------------------------------------------------------------
    // Utilidades comunes
    // ---------------------------------------------------------------------

    private function insertarPregunta(int $actividadId, array $pregunta, int $orden, $ahora): void
    {
        $insertada = DB::table('pregunta')->insert([
            'evaluacion_id' => null,
            'actividad_id' => $actividadId,
            'cuestionario_id' => null,
            'lectura_diagnostica_id' => null,
            'dimension' => $pregunta['dimension'],
            'orden' => $orden,
            'enunciado' => $pregunta['enunciado'],
            'alternativa_a' => $pregunta['a'],
            'alternativa_b' => $pregunta['b'],
            'alternativa_c' => $pregunta['c'],
            'alternativa_d' => $pregunta['d'],
            'respuesta_correcta' => $pregunta['correcta'],
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        if (! $insertada) {
            throw new RuntimeException("No se pudo insertar una pregunta de la actividad $actividadId.");
        }
    }

    /**
     * El lote está aplicado si la lectura y las diez preguntas coinciden exactamente,
     * en el orden en que la aplicación las muestra (por id_pregunta).
     */
    private function yaAplicada(object $actividad, $preguntas, array $datos): bool
    {
        if ((int) $actividad->nivel_id !== self::NIVEL_AVANZADO_ID || $this->normalizar($actividad->contenido) !== $datos['contenido']) {
            return false;
        }
        if ($preguntas->count() !== count($datos['preguntas'])) {
            return false;
        }

        foreach (array_values($preguntas->all()) as $indice => $actual) {
            $esperada = $datos['preguntas'][$indice];
            if (($esperada['id'] !== null && (int) $actual->id_pregunta !== $esperada['id'])
                || $actual->enunciado !== $esperada['enunciado']
                || $actual->alternativa_a !== $esperada['a'] || $actual->alternativa_b !== $esperada['b']
                || $actual->alternativa_c !== $esperada['c'] || $actual->alternativa_d !== $esperada['d']
                || $actual->respuesta_correcta !== $esperada['correcta']
                || $actual->dimension !== $esperada['dimension']
                || (int) $actual->orden !== $indice + 1) {
                return false;
            }
        }

        return true;
    }

    private function comprobarResultado(int $actividadId, array $datos): void
    {
        $actividad = DB::table('actividad')->where('id_actividad', $actividadId)
            ->first(['id_actividad', 'nivel_id', 'contenido', 'estado']);
        $preguntas = DB::table('pregunta')->where('actividad_id', $actividadId)->orderBy('id_pregunta')->get();

        if (! $actividad || $actividad->estado !== 'BORRADOR' || ! $this->yaAplicada($actividad, $preguntas, $datos)) {
            throw new RuntimeException("La comprobación final de la actividad $actividadId falló.");
        }
    }

    private function validarContenido(array $actividades): void
    {
        $enunciadosGlobales = [];

        foreach ($actividades as $actividad) {
            $nombre = $actividad['titulo'];

            if (trim($actividad['titulo']) === '' || mb_strlen($actividad['titulo']) > 150 || trim($actividad['contenido']) === '') {
                throw new RuntimeException("Título o lectura no válidos en «{$nombre}».");
            }
            if (count($actividad['preguntas']) !== 10) {
                throw new RuntimeException("«{$nombre}» debe tener exactamente diez preguntas.");
            }

            $conteo = array_fill_keys(self::DIMENSIONES, 0);
            $enunciados = [];
            foreach ($actividad['preguntas'] as $i => $p) {
                $n = $i + 1;
                foreach (['enunciado', 'a', 'b', 'c', 'd'] as $campo) {
                    if (! isset($p[$campo]) || trim($p[$campo]) === '') {
                        throw new RuntimeException("Pregunta $n de «{$nombre}»: el campo $campo está vacío.");
                    }
                }
                $alternativas = array_map('trim', [$p['a'], $p['b'], $p['c'], $p['d']]);
                if (count(array_unique($alternativas)) !== 4) {
                    throw new RuntimeException("Pregunta $n de «{$nombre}»: hay alternativas repetidas.");
                }
                if (! in_array($p['correcta'], self::CLAVES, true)) {
                    throw new RuntimeException("Pregunta $n de «{$nombre}»: clave no válida.");
                }
                if (! in_array($p['dimension'], self::DIMENSIONES, true)) {
                    throw new RuntimeException("Pregunta $n de «{$nombre}»: dimensión no válida.");
                }
                $conteo[$p['dimension']]++;
                $enunciados[] = $p['enunciado'];
            }

            if ($conteo !== self::DISTRIBUCION) {
                throw new RuntimeException("«{$nombre}» no tiene la distribución 3 literales, 4 inferenciales y 3 críticas.");
            }
            if (count(array_unique($enunciados)) !== 10) {
                throw new RuntimeException("«{$nombre}» tiene enunciados repetidos.");
            }

            $idsOriginales = array_column($actividad['preguntas_originales'] ?? [], 'id');
            $idsConservados = array_values(array_filter(array_column($actividad['preguntas'], 'id')));
            if ($idsOriginales !== $idsConservados
                || array_slice(array_column($actividad['preguntas'], 'id'), 0, count($idsOriginales)) !== $idsOriginales) {
                throw new RuntimeException("Las preguntas originales de «{$nombre}» deben ir primero y coincidir con las esperadas.");
            }

            $enunciadosGlobales = array_merge($enunciadosGlobales, $enunciados);
        }

        if (count($enunciadosGlobales) !== 30) {
            throw new RuntimeException('El lote debe contener exactamente treinta preguntas.');
        }
    }

    private function info(string $mensaje): void
    {
        $this->command?->info($mensaje);
    }

    // ---------------------------------------------------------------------
    // Contenido aprobado provisionalmente (paso 21AE)
    // ---------------------------------------------------------------------

    private function contenido(): array
    {
        return array_map(function (array $actividad): array {
            $actividad['contenido'] = $this->normalizar($actividad['contenido']);
            if (isset($actividad['contenido_original'])) {
                $actividad['contenido_original'] = $this->normalizar($actividad['contenido_original']);
            }

            return $actividad;
        }, [
            $this->actividad7(),
            $this->actividad8(),
            $this->actividadVideojuegos(),
        ]);
    }

    /**
     * Saltos de línea LF y sin espacios en los extremos, para que la detección de
     * «ya aplicado» no dependa del formato de fin de línea del archivo.
     */
    private function normalizar(string $texto): string
    {
        return trim(str_replace(["\r\n", "\r"], "\n", $texto));
    }

    private function actividad7(): array
    {
        return [
            'id_actividad' => 7,
            'titulo' => 'La importancia de la lectura crítica',
            'contenido_original' => 'La lectura crítica consiste en analizar un texto, identificar sus argumentos y evaluar las evidencias que presenta. No basta con comprender las palabras: también es necesario reconocer la intención del autor, distinguir hechos de opiniones y contrastar la información con otras fuentes.',
            'contenido' => <<<'TXT'
La lectura crítica consiste en analizar un texto, identificar sus argumentos y evaluar las evidencias que presenta. No basta con comprender las palabras: también es necesario reconocer la intención del autor, distinguir hechos de opiniones y contrastar la información con otras fuentes.

Imaginemos un ejemplo. Sofía encontró en internet un anuncio que decía: «El jugo Supernaranja te hará crecer cinco centímetros en un mes. ¡Es el mejor jugo del mundo!». Antes de creerlo, se hizo algunas preguntas. ¿Quién escribió el anuncio? Descubrió que lo había publicado la misma empresa que vende el jugo, cuya intención era convencer a las personas de comprarlo.

Luego, Sofía separó los hechos de las opiniones. Que el jugo se fabrique con naranjas es un dato que se puede comprobar en la etiqueta. En cambio, decir que es «el mejor jugo del mundo» es una opinión. Además, el anuncio no mostraba ningún estudio que demostrara que el jugo hace crecer.

Finalmente, Sofía buscó información en un libro de ciencias y conversó con la enfermera de su escuela. Ambas fuentes coincidieron en que el crecimiento depende de una alimentación variada, del descanso y del ejercicio, y no de un solo producto. Así, Sofía no se dejó llevar por el anuncio y tomó una decisión mejor informada.
TXT,
            'preguntas_originales' => [
                [
                    'id' => 11,
                    'enunciado' => '¿Qué caracteriza a la lectura crítica?',
                    'a' => 'Memorizar todas las palabras.',
                    'b' => 'Leer únicamente el título.',
                    'c' => 'Aceptar cualquier afirmación sin analizarla.',
                    'd' => 'Evaluar argumentos y evidencias.',
                    'correcta' => 'D',
                ],
                [
                    'id' => 12,
                    'enunciado' => '¿Qué conviene hacer para comprobar una afirmación?',
                    'a' => 'Contrastar la información con fuentes confiables.',
                    'b' => 'Compartirla inmediatamente.',
                    'c' => 'Ignorar las evidencias.',
                    'd' => 'Elegir la fuente más breve.',
                    'correcta' => 'A',
                ],
            ],
            'preguntas' => [
                [
                    'id' => 11,
                    'dimension' => 'LITERAL',
                    'enunciado' => '¿Qué caracteriza a la lectura crítica?',
                    'a' => 'Leer con atención todas las palabras del texto.',
                    'b' => 'Leer el texto varias veces en voz alta.',
                    'c' => 'Aceptar las ideas del autor que parecen convincentes.',
                    'd' => 'Evaluar argumentos y evidencias.',
                    'correcta' => 'D',
                ],
                [
                    'id' => 12,
                    'dimension' => 'LITERAL',
                    'enunciado' => '¿Qué conviene hacer para comprobar una afirmación?',
                    'a' => 'Contrastar la información con fuentes confiables.',
                    'b' => 'Preguntar si otras personas ya la compartieron.',
                    'c' => 'Confiar en ella si está escrita con seguridad.',
                    'd' => 'Elegir la fuente que dé la respuesta más corta.',
                    'correcta' => 'A',
                ],
                [
                    'id' => null,
                    'dimension' => 'LITERAL',
                    'enunciado' => 'Según el texto, ¿quién publicó el anuncio del jugo Supernaranja?',
                    'a' => 'La enfermera de la escuela.',
                    'b' => 'Una compañera de Sofía.',
                    'c' => 'La misma empresa que vende el jugo.',
                    'd' => 'El autor de un libro de ciencias.',
                    'correcta' => 'C',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Por qué fue importante para Sofía saber quién había publicado el anuncio?',
                    'a' => 'Porque quien lo publicó ganaría dinero si las personas compraban el jugo.',
                    'b' => 'Porque así podía saber cuánto costaba el jugo.',
                    'c' => 'Porque quería conocer a las personas de la empresa.',
                    'd' => 'Porque así sabría en qué tienda comprarlo.',
                    'correcta' => 'A',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => 'Según lo que aprendió Sofía, ¿cuál de estas afirmaciones sobre el jugo sería un hecho?',
                    'a' => '«Es el jugo más delicioso de todos».',
                    'b' => '«Cada botella contiene un litro de jugo».',
                    'c' => '«Su sabor encanta a toda la familia».',
                    'd' => '«Es la mejor forma de empezar el día».',
                    'correcta' => 'B',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Qué significa que Sofía «tomó una decisión mejor informada»?',
                    'a' => 'Que decidió rápidamente, sin detenerse a pensar.',
                    'b' => 'Que decidió seguir lo que decía el anuncio.',
                    'c' => 'Que decidió según lo que preferían sus amigos.',
                    'd' => 'Que decidió después de reunir y comparar información.',
                    'correcta' => 'D',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Qué tienen en común el libro de ciencias y la enfermera en la historia?',
                    'a' => 'Ambos recomendaron tomar el jugo Supernaranja.',
                    'b' => 'Ambos fueron fuentes distintas que confirmaron la misma información.',
                    'c' => 'Ambos publicaron el anuncio en internet.',
                    'd' => 'Ambos dijeron que el anuncio era verdadero.',
                    'correcta' => 'B',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => '¿Por qué no está bien sustentada la afirmación de que el jugo hace crecer cinco centímetros?',
                    'a' => 'Porque el anuncio no presenta ninguna prueba que la demuestre.',
                    'b' => 'Porque cinco centímetros es muy poco para notarlo.',
                    'c' => 'Porque toda la información de internet es falsa.',
                    'd' => 'Porque los jugos de naranja no son saludables.',
                    'correcta' => 'A',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => 'Si otro anuncio dijera «Estas zapatillas te harán correr más rápido», ¿qué haría un lector crítico, según el texto?',
                    'a' => 'Comprarlas para probarlas.',
                    'b' => 'Creerlo si el anuncio tiene buenas imágenes.',
                    'c' => 'Averiguar quién lo publicó y buscar pruebas en otras fuentes.',
                    'd' => 'Pensar que es falso solo porque es un anuncio.',
                    'correcta' => 'C',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => '¿Cuál es la conclusión mejor sustentada por el texto?',
                    'a' => 'Hay que desconfiar de todo lo que se lee.',
                    'b' => 'Antes de aceptar una información, conviene analizar quién la dice, qué pruebas ofrece y qué dicen otras fuentes.',
                    'c' => 'Los anuncios siempre dicen mentiras.',
                    'd' => 'Los libros de ciencias son las únicas fuentes confiables.',
                    'correcta' => 'B',
                ],
            ],
        ];
    }

    private function actividad8(): array
    {
        return [
            'id_actividad' => 8,
            'titulo' => 'La tecnología en la educación',
            'contenido_original' => 'Las herramientas digitales permiten acceder a materiales educativos y colaborar a distancia. Sin embargo, su utilidad depende de cómo se empleen. Para aprovecharlas, es importante verificar las fuentes, proteger los datos personales y mantener un equilibrio entre las actividades digitales y las presenciales.',
            'contenido' => <<<'TXT'
Las herramientas digitales permiten acceder a materiales educativos y colaborar a distancia. Sin embargo, su utilidad depende de cómo se empleen. Para aprovecharlas, es importante verificar las fuentes, proteger los datos personales y mantener un equilibrio entre las actividades digitales y las presenciales.

Por ejemplo, el grupo de Mateo debía preparar una exposición sobre los animales de la Amazonía. Como sus integrantes vivían lejos unos de otros, usaron una plataforma escolar para escribir juntos un documento y compartir fotografías. Gracias a ello, avanzaron sin necesidad de reunirse todos los días.

Sin embargo, también tuvieron dificultades. Una compañera copió un dato de una página sin autor, según la cual los delfines rosados vivían en el mar. Al revisar la enciclopedia de la biblioteca, descubrieron que esos delfines habitan en ríos y corrigieron el error. Otro día, un mensaje desconocido les pidió su contraseña a cambio de un premio. Mateo recordó que nunca debe compartirla y avisó a su profesora.

Al final, la exposición fue un éxito. El grupo concluyó que la tecnología había sido muy útil, pero que también había sido necesario revisar la información con cuidado y conversar en persona para ponerse de acuerdo en los detalles.
TXT,
            'preguntas_originales' => [
                [
                    'id' => 13,
                    'enunciado' => '¿Qué permite hacer una herramienta digital educativa?',
                    'a' => 'Evitar todo tipo de evaluación.',
                    'b' => 'Garantizar que cualquier información sea verdadera.',
                    'c' => 'Acceder a materiales y colaborar a distancia.',
                    'd' => 'Eliminar la necesidad de aprender.',
                    'correcta' => 'C',
                ],
                [
                    'id' => 14,
                    'enunciado' => '¿Qué práctica es recomendable al utilizar recursos digitales?',
                    'a' => 'Compartir contraseñas con desconocidos.',
                    'b' => 'Verificar las fuentes y proteger los datos personales.',
                    'c' => 'Abrir todos los enlaces recibidos.',
                    'd' => 'Publicar información privada.',
                    'correcta' => 'B',
                ],
            ],
            'preguntas' => [
                [
                    'id' => 13,
                    'dimension' => 'LITERAL',
                    'enunciado' => '¿Qué permite hacer una herramienta digital educativa?',
                    'a' => 'Reemplazar por completo las clases presenciales.',
                    'b' => 'Garantizar que cualquier información sea verdadera.',
                    'c' => 'Acceder a materiales y colaborar a distancia.',
                    'd' => 'Aprender sin necesidad de esfuerzo.',
                    'correcta' => 'C',
                ],
                [
                    'id' => 14,
                    'dimension' => 'LITERAL',
                    'enunciado' => '¿Qué práctica es recomendable al utilizar recursos digitales?',
                    'a' => 'Usar la misma contraseña en todas las aplicaciones.',
                    'b' => 'Verificar las fuentes y proteger los datos personales.',
                    'c' => 'Abrir los enlaces que envían los compañeros sin revisarlos.',
                    'd' => 'Pasar todo el tiempo libre usando recursos digitales.',
                    'correcta' => 'B',
                ],
                [
                    'id' => null,
                    'dimension' => 'LITERAL',
                    'enunciado' => '¿Sobre qué tema debía exponer el grupo de Mateo?',
                    'a' => 'Los ríos de la costa.',
                    'b' => 'El uso seguro de las contraseñas.',
                    'c' => 'Las enciclopedias de la biblioteca.',
                    'd' => 'Los animales de la Amazonía.',
                    'correcta' => 'D',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Qué ventaja de las herramientas digitales, mencionada en el primer párrafo, se observa cuando el grupo usa la plataforma escolar?',
                    'a' => 'Colaborar a distancia.',
                    'b' => 'Proteger los datos personales.',
                    'c' => 'Verificar las fuentes.',
                    'd' => 'Equilibrar las actividades digitales y presenciales.',
                    'correcta' => 'A',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Qué enseña lo ocurrido con el dato de los delfines rosados?',
                    'a' => 'Que las enciclopedias suelen tener errores.',
                    'b' => 'Que una página sin autor puede contener información equivocada.',
                    'c' => 'Que los delfines rosados viven en el mar.',
                    'd' => 'Que el grupo trabajó con demasiada prisa.',
                    'correcta' => 'B',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Por qué el mensaje que pedía la contraseña era sospechoso?',
                    'a' => 'Porque llegó el mismo día de la exposición.',
                    'b' => 'Porque lo había enviado la profesora.',
                    'c' => 'Porque ofrecía información sobre animales.',
                    'd' => 'Porque alguien desconocido pedía un dato privado a cambio de un premio.',
                    'correcta' => 'D',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Qué momento de la historia muestra la importancia de las actividades presenciales?',
                    'a' => 'Cuando escribieron juntos el documento en la plataforma.',
                    'b' => 'Cuando compartieron fotografías.',
                    'c' => 'Cuando conversaron en persona para ponerse de acuerdo en los detalles.',
                    'd' => 'Cuando recibieron el mensaje desconocido.',
                    'correcta' => 'C',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => 'Mateo no compartió su contraseña y avisó a su profesora. ¿Por qué fue una buena decisión?',
                    'a' => 'Porque protegió sus datos personales y pidió ayuda a un adulto de confianza.',
                    'b' => 'Porque el premio que ofrecían era muy pequeño.',
                    'c' => 'Porque solo la profesora podía responder mensajes.',
                    'd' => 'Porque así nadie del grupo tendría que usar internet.',
                    'correcta' => 'A',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => 'Un compañero opina: «Si la información está en internet, no hace falta revisarla». ¿Qué hecho del texto contradice mejor esa opinión?',
                    'a' => 'Que el grupo usó una plataforma para escribir juntos.',
                    'b' => 'Que una página sin autor tenía un dato equivocado sobre los delfines.',
                    'c' => 'Que la exposición fue un éxito.',
                    'd' => 'Que el grupo compartió fotografías.',
                    'correcta' => 'B',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => '¿Cuál es la conclusión mejor sustentada por el texto?',
                    'a' => 'La tecnología siempre resuelve los problemas escolares.',
                    'b' => 'Es mejor dejar de usar herramientas digitales porque tienen riesgos.',
                    'c' => 'Las herramientas digitales ayudan a aprender si se usan con cuidado y se combinan con el trabajo presencial.',
                    'd' => 'Las enciclopedias son más útiles que cualquier herramienta digital.',
                    'correcta' => 'C',
                ],
            ],
        ];
    }

    private function actividadVideojuegos(): array
    {
        return [
            'id_actividad' => null,
            'titulo' => 'Videojuegos: ¿aliados o enemigos del aprendizaje?',
            'contenido' => <<<'TXT'
En la escuela de Lucía y Andrés se organizó un debate sobre una pregunta: ¿los videojuegos ayudan o perjudican el aprendizaje?

Lucía defendió que pueden ser aliados. Explicó que algunos juegos obligan a planificar, resolver problemas y tomar decisiones rápidas. Además, contó que su profesora usa un juego de matemáticas en clase y que, según los registros del aula, los estudiantes mejoraron en cálculo mental después de dos meses. «Aprender jugando puede ser más motivador», afirmó.

Andrés sostuvo una postura diferente. Señaló que muchos niños juegan hasta muy tarde y luego duermen poco, lo que les impide concentrarse al día siguiente. También advirtió que algunos juegos están hechos solo para entretener y que pasar demasiadas horas frente a la pantalla reduce el tiempo para leer, hacer tareas o jugar al aire libre. «Mi primo juega todo el día y sus notas bajaron», agregó.

Al terminar, el profesor no anunció un ganador. En cambio, pidió a la clase comparar los argumentos de cada uno y pensar qué razones estaban mejor sustentadas.
TXT,
            'preguntas_originales' => [],
            'preguntas' => [
                [
                    'id' => null,
                    'dimension' => 'LITERAL',
                    'enunciado' => '¿Qué pregunta se discutió en el debate?',
                    'a' => 'Cuál es el mejor videojuego de matemáticas.',
                    'b' => 'Cuántas horas deben dormir los niños.',
                    'c' => 'Si los niños deben jugar más al aire libre.',
                    'd' => 'Si los videojuegos ayudan o perjudican el aprendizaje.',
                    'correcta' => 'D',
                ],
                [
                    'id' => null,
                    'dimension' => 'LITERAL',
                    'enunciado' => 'Según Lucía, ¿qué ocurrió con los estudiantes que usaron el juego de matemáticas?',
                    'a' => 'Durmieron menos horas.',
                    'b' => 'Mejoraron en cálculo mental después de dos meses.',
                    'c' => 'Dejaron de hacer sus tareas.',
                    'd' => 'Aprendieron a crear sus propios juegos.',
                    'correcta' => 'B',
                ],
                [
                    'id' => null,
                    'dimension' => 'LITERAL',
                    'enunciado' => 'Según Andrés, ¿por qué jugar hasta muy tarde afecta a los niños?',
                    'a' => 'Porque olvidan lo que aprendieron en el juego.',
                    'b' => 'Porque pierden tiempo para hacer deporte.',
                    'c' => 'Porque duermen poco y luego les cuesta concentrarse.',
                    'd' => 'Porque se pelean con sus hermanos.',
                    'correcta' => 'C',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Para qué menciona Lucía el juego de matemáticas de su profesora?',
                    'a' => 'Para dar un ejemplo que apoye su postura.',
                    'b' => 'Para demostrar que Andrés está equivocado en todo.',
                    'c' => 'Para recomendar ese juego a sus compañeros.',
                    'd' => 'Para quejarse de las clases de matemáticas.',
                    'correcta' => 'A',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => '¿Qué quiere decir Lucía con «aprender jugando puede ser más motivador»?',
                    'a' => 'Que los juegos pueden reemplazar a los profesores.',
                    'b' => 'Que solo se aprende cuando se juega.',
                    'c' => 'Que los juegos de matemáticas son los más fáciles.',
                    'd' => 'Que jugar puede despertar más interés por aprender.',
                    'correcta' => 'D',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => 'De acuerdo con los argumentos de Andrés, ¿qué consejo le daría a un niño que juega hasta muy tarde?',
                    'a' => 'Que juegue solo juegos de matemáticas.',
                    'b' => 'Que deje de jugar para siempre.',
                    'c' => 'Que juegue menos tiempo para descansar y concentrarse mejor.',
                    'd' => 'Que juegue junto con su primo.',
                    'correcta' => 'C',
                ],
                [
                    'id' => null,
                    'dimension' => 'INFERENCIAL',
                    'enunciado' => 'En la frase «qué razones estaban mejor sustentadas», ¿qué significa «sustentadas»?',
                    'a' => 'Apoyadas en pruebas o ejemplos confiables.',
                    'b' => 'Dichas en voz más alta.',
                    'c' => 'Repetidas muchas veces.',
                    'd' => 'Escritas con palabras difíciles.',
                    'correcta' => 'A',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => '¿Cuál de estas razones está mejor sustentada con evidencias en el texto?',
                    'a' => '«Mi primo juega todo el día y sus notas bajaron».',
                    'b' => 'Los estudiantes mejoraron en cálculo mental, según los registros del aula.',
                    'c' => '«Aprender jugando puede ser más motivador».',
                    'd' => 'Todos los videojuegos sirven solo para entretener.',
                    'correcta' => 'B',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => '¿Por qué el ejemplo del primo de Andrés no basta para demostrar que los videojuegos perjudican a todos los niños?',
                    'a' => 'Porque es un solo caso y sus notas pudieron bajar por otras razones.',
                    'b' => 'Porque el primo es mayor que Andrés.',
                    'c' => 'Porque Andrés no sabe jugar videojuegos.',
                    'd' => 'Porque las notas no son importantes.',
                    'correcta' => 'A',
                ],
                [
                    'id' => null,
                    'dimension' => 'CRITICA',
                    'enunciado' => '¿Cuál conclusión está mejor sustentada por los argumentos del debate?',
                    'a' => 'Los videojuegos siempre mejoran el aprendizaje.',
                    'b' => 'Los videojuegos siempre perjudican el aprendizaje.',
                    'c' => 'Lucía tenía razón porque usó un ejemplo de la escuela.',
                    'd' => 'El efecto de los videojuegos depende del tipo de juego, del tiempo que se juega y del propósito.',
                    'correcta' => 'D',
                ],
            ],
        ];
    }
}
