<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/healthcheck",
     *     tags={"Health"},
     *     summary="Rota de Health Check",
     *     @OA\Response(
     *         response=200,
     *         description="Health Check",
     *     )
     * )
     */
    public function __invoke()
    {
        $defaultConnection = config('database.default');
        $dbConfig = config('database.connections.' . $defaultConnection);

        // Testa a conexão
        try {
            DB::connection()->getPdo();
            $dbStatus = 'connected';
            $dbError = null;
        } catch (\Exception $e) {
            $dbStatus = 'disconnected';
            $dbError = $e->getMessage();
        }

        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toDateTimeString(),
            'environment' => app()->environment(),
            'database' => [
                'status' => $dbStatus,
                'error' => $dbError,
                'connection' => $defaultConnection,
                'host' => data_get($dbConfig, 'host'),
                'port' => data_get($dbConfig, 'port'),
                'database' => data_get($dbConfig, 'database'),
                'username' => data_get($dbConfig, 'username'),
                'driver' => data_get($dbConfig, 'driver')
            ]
        ]);
    }
}