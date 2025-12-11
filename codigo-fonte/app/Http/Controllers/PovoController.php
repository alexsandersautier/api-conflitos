<?php
namespace App\Http\Controllers;

use App\Models\Povo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 *
 *  @OA\Schema(
 *     schema="Povo",
 *     type="object",
 *     @OA\Property(property="idPovo", type="integer", example="1"),
 *     @OA\Property(property="nome", type="string", example="Tupi-guarani"),
 *     @OA\Property(property="codEtnia", type="string", example="267.00"),
 *     @OA\Property(property="lingua", type="string", example="Tupi-guarani"),
 *     @OA\Property(property="familia_linguistica", type="string", example="Tupi"),
 *     @OA\Property(property="ufs_povos", type="string", example="PA,MA"),
 *     @OA\Property(property="qtd_ti_povo", type="integer", example="3"),
 * )
 *
 * @OA\PathItem(
 *     path="/api/povo"
 * )
 *
 * @OA\Tag(
 *     name="Povos",
 *     description="Endpoints para Povos"
 * )
 */
class PovoController extends Controller
{

    /**
     *
     * @OA\Get(
     *     path="/api/povo",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Listar todos os povos",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de povos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Povo")
     *         )
     *     )
     * )
     */
    public function index()
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $povos = Povo::all();
        return response()->json($povos);
    }

    /**
     *
     * @OA\Post(
     *     path="/api/povo",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Criar um novo povo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Departamento de TI")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Povo criado"
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'codEtnia' => 'required|string',
            'lingua' => 'string|max:255',
            'ufs_povo' => 'string|max:255',
            'qtd_ti_povo' => 'integer|min:0'
        ]);

        $povo = Povo::create($validatedData);
        return response()->json($povo, Response::HTTP_CREATED);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/povo/{id}",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Obter um povo específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do povo",
     *         @OA\JsonContent(ref="#/components/schemas/Povo")
     *     )
     * )
     */
    public function show($id)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $povo = Povo::findOrFail($id);
        return response()->json($povo);
    }

    /**
     *
     * @OA\Put(
     *     path="/api/povo/{id}",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Atualizar um povo específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome"},
     *             @OA\Property(property="nome", type="string", example="Yanomami")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Povo atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $povo = Povo::findOrFail($id);

        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'codEtnia' => 'required|string',
            'lingua' => 'string|max:255',
            'ufs_povo' => 'string|max:255',
            'qtd_ti_povo' => 'integer|min:0'
        ]);

        $povo->update($validatedData);
        return response()->json($povo);
    }

    /**
     *
     * @OA\Delete(
     *     path="/api/povo/{id}",
     *     tags={"Povos"},
     *     security={ {"sanctum": {} } },
     *     summary="Excluir um povo específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Povo excluído"
     *     )
     * )
     */
    public function destroy($id)
    {
        if (! Auth::guard('sanctum')->check()) {
            return response()->json([
                'message' => 'Não autorizado',
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        $povo = Povo::findOrFail($id);
        $povo->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
