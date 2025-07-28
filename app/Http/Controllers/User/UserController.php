<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Card\CardController;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      operationId="getUsersList",
     *      tags={"Users"},
     *      summary="Obtener lista de usuarios",
     *      description="Retorna una lista paginada de usuarios con filtros opcionales",
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Filtrar por nombre (búsqueda parcial)",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          in="query",
     *          description="Filtrar por email (búsqueda parcial)",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Número de usuarios por página",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Número de página",
     *          required=false,
     *          @OA\Schema(type="integer", default=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Lista de usuarios obtenida exitosamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(ref="#/components/schemas/User")
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string"),
     *                  @OA\Property(property="from", type="integer"),
     *                  @OA\Property(property="last_page", type="integer"),
     *                  @OA\Property(property="last_page_url", type="string"),
     *                  @OA\Property(property="next_page_url", type="string"),
     *                  @OA\Property(property="path", type="string"),
     *                  @OA\Property(property="per_page", type="integer"),
     *                  @OA\Property(property="prev_page_url", type="string"),
     *                  @OA\Property(property="to", type="integer"),
     *                  @OA\Property(property="total", type="integer")
     *              )
     *          )
     *      )
     * )
     *
     * Display a listing of the users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filtro por nombre si se proporciona
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->get('name') . '%');
        }

        // Filtro por email si se proporciona
        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->get('email') . '%');
        }

        // Paginación
        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      operationId="storeUser",
     *      tags={"Users"},
     *      summary="Crear nuevo usuario",
     *      description="Crea un nuevo usuario en el sistema",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","last_name","gender_id","document_type_id","document_number","email","telephone","password","password_confirmation"},
     *              @OA\Property(property="name", type="string", example="Juan"),
     *              @OA\Property(property="last_name", type="string", example="Pérez"),
     *              @OA\Property(property="gender_id", type="integer", example=1),
     *              @OA\Property(property="document_type_id", type="integer", example=2),
     *              @OA\Property(property="document_number", type="integer", example=12345678),
     *              @OA\Property(property="email", type="string", format="email", example="juan@ejemplo.com"),
     *              @OA\Property(property="telephone", type="integer", example=3001234567),
     *              @OA\Property(property="address", type="string", example="Calle 123 #45-67"),
     *              @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
     *              @OA\Property(property="crd_intsnr", type="string", example="A0:39:16:20"),
     *              @OA\Property(property="password", type="string", format="password", minLength=8, nullable=true, example="password123"),
     *              @OA\Property(
     *                  property="password_confirmation",
     *                  type="string",
     *                  format="password",
     *                  minLength=8,
     *                  description="Required with password"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Usuario creado exitosamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="User created successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Errores de validación",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Error creating user"),
     *              @OA\Property(property="error", type="string")
     *          )
     *      )
     * )
     *
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $serialHex = $data['crd_intsnr'];
            $serialHex = str_replace(':', '', $serialHex); // Remove colons
            // Split hex string into pairs
            $hexPairs = str_split($serialHex, 2);
            // Reverse array of pairs
            $reversedPairs = array_reverse($hexPairs);
            // Join back to string
            $reversedHex = implode('', $reversedPairs);
            // Convert to integer
            $crd_intsnr = hexdec($reversedHex);

            $user = User::create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'gender_id' => $data['gender_id'],
                'document_type_id' => $data['document_type_id'],
                'document_number' => $data['document_number'],
                'birth_date' => $data['birth_date'] ?? null,
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'address' => $data['address'] ?? null,
                'password' => isset($data['password']) ? Hash::make($data['password']) : null,
            ]);


            $reg_card = DB::connection('oracle_mercury')->select("
                select c.ISS_ID,
                       c.CD_ID,
                       c.CRD_SNR,
                       c.CTY_ID,
                        c.DC_CODE,
                       c.CRD_CHKDG,
                       c.CRD_INTSNR,
                       c.CRD_CERTIFICATE,
                       c.CRD_STATUS,
                       c.CRD_REGDATE,
                       c.CRD_REGUSER,
                       c.CRD_SECONDCOPYTAX,
                       u.USR_NAME as user_name,
                       ud.USRDOC_NUMBER as user_document
                  from MERCURY.CARDS c
                  left join MERCURY.CARDSXUSERS cu 
                    on c.ISS_ID = cu.ISS_ID 
                   and c.CD_ID = cu.CD_ID 
                   and c.CRD_SNR = cu.CRD_SNR
                  left join MERCURY.USERS u 
                    on cu.USR_ID = u.USR_ID 
                   and u.USR_STATUS='A'
                  left join MERCURY.USERDOCUMENTS ud 
                    on u.USR_ID = ud.USR_ID 
                   and ud.USRDOC_STATUS='A'
                 where c.CRD_INTSNR = :CRD_INTSNR
                   and (ud.DT_ID in (87, 88, 83) or ud.DT_ID is null)
            ", [
                'CRD_INTSNR' => $crd_intsnr
            ]);
            $is_personalized = 0; // Card is personalized


            if ($reg_card[0]->user_document == $data['document_number']) {
                $is_personalized = 1; // Card is personalized
            }




            $reg_card[0]->user_id = $user->user_id;
            $reg_card[0]->is_personalized = $is_personalized;


            $card = new CardController();
            $reg_card = $card->store($reg_card);

            if (!$reg_card['success']) {
                User::deleted($user->user_id);
                return response()->json([
                    'success' => false,
                    'message' => $reg_card['message'],
                    'error' => 'Card creation failed'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => 'hola'
            ], 201);
        } catch (\Exception $e) {
            dd($e);
            User::deleted($user->user_id);
            return response()->json([
                'success' => false,
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      operationId="getUserById",
     *      tags={"Users"},
     *      summary="Obtener usuario por ID",
     *      description="Retorna un usuario específico por su ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID del usuario",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuario obtenido exitosamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="User retrieved successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuario no encontrado",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *      )
     * )
     *
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/users/{id}",
     *      operationId="updateUser",
     *      tags={"Users"},
     *      summary="Actualizar usuario",
     *      description="Actualiza un usuario existente",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID del usuario",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuario actualizado exitosamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="User updated successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuario no encontrado",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Errores de validación",
     *          @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *      )
     * )
     *
     * @OA\Patch(
     *      path="/api/v1/users/{id}",
     *      operationId="patchUser",
     *      tags={"Users"},
     *      summary="Actualizar usuario parcialmente",
     *      description="Actualiza parcialmente un usuario existente",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID del usuario",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserUpdateRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuario actualizado exitosamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="User updated successfully"),
     *              @OA\Property(property="data", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuario no encontrado"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Errores de validación"
     *      )
     * )
     *
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $data = $request->validated();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      operationId="deleteUser",
     *      tags={"Users"},
     *      summary="Eliminar usuario",
     *      description="Elimina un usuario del sistema",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID del usuario",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Usuario eliminado exitosamente",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="User deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Usuario no encontrado",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error del servidor",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Error deleting user"),
     *              @OA\Property(property="error", type="string")
     *          )
     *      )
     * )
     *
     * Remove the specified user.
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
