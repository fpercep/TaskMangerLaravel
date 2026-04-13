<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Migración destructiva: elimina la tabla teams (entidad obsoleta).
     */
    public function up(): void
    {
        Schema::dropIfExists('team_user');
        Schema::dropIfExists('teams');
    }

    /**
     * No se recrea la tabla. El concepto de "equipo" ha sido eliminado.
     */
    public function down(): void
    {
        // Intencional: no se restaura la tabla teams.
    }
};
