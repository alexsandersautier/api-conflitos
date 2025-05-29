<?php
// database/migrations/xxxx_create_usuarios_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('idUsuario')->primary();
            $table->foreignId('idOrgao')->constrained('orgao');
            $table->foreignId('idPerfil')->constrained('perfil');
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('orgao', function (Blueprint $table) {
            $table->id('idOrgao')->primary();
            $table->string('nome');
        });
        
        Schema::create('perfil', function (Blueprint $table) {
            $table->id('idPerfil')->primary();
            $table->string('nome');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('orgao');
        Schema::dropIfExists('perfil');
    }
};
