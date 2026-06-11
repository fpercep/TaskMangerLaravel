<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Elimina las columnas 'status' y 'visibility' de la tabla projects.
     * Estas columnas no se utilizan actualmente en ningún punto de la aplicación.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'visibility']);
        });
    }

    /**
     * Restaura las columnas eliminadas con sus valores por defecto originales.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('status')->default('active')->after('description');
            $table->string('visibility')->default('private')->after('status');
        });
    }
};
