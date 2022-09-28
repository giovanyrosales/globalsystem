<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(RolesSeeder::class);
        $this->call(UsuariosSeeder::class);
        $this->call(NaturalezaSeeder::class);
        $this->call(EstadoProyectoSeeder::class);
        $this->call(UnidadMedidaSeeder::class);
        $this->call(FFinanciamientoSeeder::class);
        $this->call(LineaTrabajoSeeder::class);
        $this->call(ClasificacionSeeder::class);
        $this->call(RubroSeeder::class);
        $this->call(CuentaSeeder::class);
        $this->call(GestionSeeder::class);
        $this->call(ObjetoSeeder::class);
        $this->call(FuenteRSeeder::class);
        $this->call(TipoPartidaSeeder::class);
        $this->call(PunidadMedidaSeeder::class);
    }
}
