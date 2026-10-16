<?php

return [
    // Antes de activar, detener la creación y entrega de intentos y esperar las solicitudes en curso.
    // Consultar SELECT COALESCE(MAX(id_intento), 0) FROM actividad_intento;
    // Sustituir null por ese entero exacto, desplegar la misma configuración en todas las instancias
    // (y renovar la caché de configuración, si se usa) antes de reanudar las solicitudes.
    // No cambiar este valor después de la activación: IDs <= corte nunca reciben puntos.
    'attempt_cutoff_id' => 14,
];
