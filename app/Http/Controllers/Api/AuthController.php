<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

use App\Http\Services\SmsServices;
use Illuminate\Support\Facades\Hash;
use App\Models\Cart;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Company;
use App\Models\CodigoCiiu;
use App\Models\CodigoPostal;
use App\Models\Subscriber;
use App\Notifications\EmailVerificationNotification;
use Str;
use DB;
use Log;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        DB::beginTransaction();

        try {

            $input = json_decode($request->form);

            if (!$input) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de datos inválido.',
                    'data' => null
                ], 400);
            }

            if (empty($input->email) || empty($input->phone) || empty($input->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Correo, celular y contraseña son requeridos.',
                    'data' => null
                ], 400);
            }

            $user = User::where('email', $input->email)->first();

            if ($user) {
                return response()->json([
                    'success' => false,
                    'message' => translate('El usuario ya existe.'),
                    'data' => null
                ], 409);
            }

            $path_docs = public_path('/docs/');
            $path_camara = public_path('/camara/');
            $path_ruts = public_path('/ruts/');

            $docfile = '';
            $camarafile = '';
            $rutfile = '';

            // SUBIDA ARCHIVOS
            if ($request->hasFile('filecamara')) {

                $fileCamara = $request->file('filecamara');

                if (!$fileCamara->isValid()) {
                    throw new \Exception('Archivo cámara inválido.');
                }

                $fileNameToStore = time() . '_' . $fileCamara->getClientOriginalName();

                $fileCamara->move($path_camara, $fileNameToStore);

                $camarafile = $fileNameToStore;
            }

            // USER
            $user = new User([
                'email' => $input->email,
                'password' => Hash::make($input->password),
                'phone' => $input->phone,
                'verification_code' => rand(100000, 999999),
                'document_number' => $input->documentNumber,
                'document_type' => $input->documentType,
                'user_type' => 'customer',
            ]);

            $user->save();

            // COMPANY
            if (($input->personType ?? '') == 'Juridical') {

                $company = new Company([
                    'user_id' => $user->id,
                    'company_razon' => $input->companyRazon ?? null,
                ]);

                $company->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado correctamente.',
                'user' => $user
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Error signup: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error durante el registro.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:phone',
            'phone' => 'required_without:email',
            'password' => 'required|string',
        ]);

        $phone = Str::replace(' ', '', $request->phone);
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->phone) {
            $user = User::where('phone', $phone)->first();
        } else {
            $user = null;
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => translate('Credenciales Invalidas')
            ], 200);
        }

        // banned user
        if ($user->banned) {
            auth()->logout();
            return response()->json(
                [
                    'success' => false,
                    'message' => translate('Estas baneado!'),
                ],
                200,
            );
        }

        if ($user->user_type == 'customer') {
            if ($request->has('temp_user_id') && $request->temp_user_id != null) {
                Cart::where('temp_user_id', $request->temp_user_id)->update(
                    [
                        'user_id' => $user->id,
                        'temp_user_id' => null
                    ]
                );
            }

            if (get_setting('customer_otp_with') != 'disabled') {
                if (get_setting('customer_login_with') == 'email' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'email') && $user->email_verified_at == null) {

                    $user->notify(new EmailVerificationNotification());
                    return response()->json([
                        'success' => true,
                        'verified' => false,
                        'email_verified' => false,
                        'message' => translate('Por favor verifica tu cuenta')
                    ], 200);
                } elseif ((get_setting('customer_login_with') == 'phone' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'phone')) && $user->phone_verified_at == null) {

                    (new SmsServices)->phoneVerificationSms($user->phone, $user->verification_code);
                    return response()->json([
                        'success' => true,
                        'verified' => false,
                        'phone_verified' => false,
                        'message' => translate('Por favor verifica tu cuenta')
                    ], 200);
                }
            }

            $tokenResult = $user->createToken('Personal Access Token');
            return $this->loginSuccess($tokenResult, $user);
        } else {
            return response()->json([
                'success' => false,
                'message' => translate('Only customers can login here')
            ], 200);
        }
    }

    public function verify(Request $request)
    {
        $phone = Str::replace(' ', '', $request->phone);
        if (get_setting('customer_login_with') == 'email' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'email')) {
            $user = User::where('email', $request->email)->first();
        } elseif (get_setting('customer_login_with') == 'phone' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'phone')) {
            $user = User::where('phone', $phone)->first();
        } else {
            $user = null;
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => translate('Usuario no encontrado con este correo.')
            ], 200);
        }
        if ($user->verification_code != $request->code) {
            return response()->json([
                'success' => false,
                'message' => translate('El codigo no coincide con nuestros registros.')
            ], 200);
        } else {

            if (get_setting('customer_login_with') == 'email' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'email')) {
                $user->email_verified_at = date('Y-m-d H:m:s');
            } else {
                $user->phone_verified_at = date('Y-m-d H:m:s');
            }

            $user->save();
            $tokenResult = $user->createToken('Personal Access Token');
            return $this->loginSuccess($tokenResult, $user);
        }
    }

    public function resend_code(Request $request)
    {
        $phone = Str::replace(' ', '', $request->phone);
        if (get_setting('customer_login_with') == 'email' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'email')) {
            $user = User::where('email', $request->email)->first();
        } elseif (get_setting('customer_login_with') == 'phone' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'phone')) {
            $user = User::where('phone', $phone)->first();
        } else {
            $user = null;
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => translate('Usuario no encontrado con este correo.')
            ], 200);
        }

        $user->verification_code = rand(100000, 999999);
        $user->save();

        if (get_setting('customer_login_with') == 'email' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'email')) {
            $user->notify(new EmailVerificationNotification());
            return response()->json([
                'success' => true,
                'verified' => false,
                'message' => translate('Codigo de Verificación enviado al correo.')
            ], 200);
        } else {
            (new SmsServices)->phoneVerificationSms($user->phone, $user->verification_code);
            return response()->json([
                'success' => true,
                'verified' => false,
                'message' => translate('Codigo de verificación enviado al celular.')
            ], 200);
        }
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $request->user()->token()->delete();

        return response()->json([
            'message' => translate('Sesión cerrada')
        ]);
    }

    protected function loginSuccess($tokenResult, $user)
    {
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(100);
        $token->save();

        return response()->json([
            'success' => true,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'verified' => true,
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => [
                'id' => $user->id,
                'balance' => $user->balance,
                'name' => $user->first_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => api_asset($user->avatar),
            ],
            'message' => translate('Inicio de sesión correcto'),
            'followed_shops' => $user->followed_shops->pluck('id')->toArray()
        ]);
    }

    public function tempIdCartUpdate(Request $request)
    {
        if ($request->temp_user_id != null) {
            Cart::where('temp_user_id', $request->temp_user_id)->update([
                'user_id' => auth()->guard('api')->user()->id,
                'temp_user_id' => null,
            ]);
        }

        return response()->json([
            'result' => true,
            'message' => translate('Carrito actualizado'),
        ]);
    }

    public function verifyData(Request $request)
    {
        $user = User::where('first_name', $request->first_name)->where('first_lastname', $request->first_lastname)->where('email', $request->email)->get();

        if ($user->count() == 0) {
            return response()->json([
                'result' => false,
                'message' => 'Error!'
            ]);
        } else {
            return response()->json([
                'result' => true,
                'message' => 'Exitoso!'
            ]);
        }
    }

    public function get_all_ciiu()
    {
        $array = array();
        $codigo = CodigoCiiu::all();

        foreach ($codigo as $cod) {
            $arr = ["text" => $cod->codigo, "value" => $cod->codigo];
            array_push($array, $arr);
        }

        return response()->json([
            'success' => true,
            'data' => $array
        ]);
    }

    public function get_all_codigo_postal()
    {
        $array = array();
        $codigo = CodigoPostal::all();

        foreach ($codigo as $cod) {
            $arr = ["text" => $cod->codigo, "value" => $cod->codigo];
            array_push($array, $arr);
        }

        return response()->json([
            'success' => true,
            'data' => $array
        ]);
    }

    public function get_all_subscriber()
    {
        $subscriber = Subscriber::all();

        return response()->json([
            'success' => true,
            'data' => $subscriber
        ]);
    }
}
