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

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $input = json_decode($request->form);

        if (get_setting('customer_login_with') == 'email') {
            $user = User::where('email', $input->email)->first();
        } elseif (get_setting('customer_login_with') == 'phone') {
            // $user = User::where('phone', $input->phone)->first();
        } else {
            $user = User::where('email', $input->email)->first();
        }

        if ($user != null) {
            return response()->json([
                'success' => false,
                'message' => translate('El usuario ya existe.'),
                'data' => null
            ]);
        }

        if (!isset($input->phone) || !isset($input->email)) {
            return response()->json([
                'success' => false,
                'message' => translate('Correo y celular requeridos.'),
                'data' => null
            ], 200);
        }

        $path_docs = public_path() . '/docs/';
        $path_camara = public_path() . '/camara/';
        $path_ruts = public_path() . '/ruts/';

        $docfile = '';
        $camarafile = '';
        $rutfile = '';

        if ($request->hasFile('filecamara')) {
            $fileCamara = $request->file('filecamara');
            $filenameWithExt = $fileCamara->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $fileCamara->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $fileCamara->move($path_camara, $fileNameToStore);
            $camarafile = $fileNameToStore;
        }

        if ($request->hasFile('filedocumento')) {
            $fileDocument = $request->file('filedocumento');
            $filenameWithExt = $fileDocument->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $fileDocument->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $fileDocument->move($path_docs, $fileNameToStore);
            $docfile = $fileNameToStore;
        }

        if ($request->hasFile('filerut')) {
            $fileRut = $request->file('filerut');
            $filenameWithExt = $fileRut->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $fileRut->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $fileRut->move($path_ruts, $fileNameToStore);
            $rutfile = $fileNameToStore;
        }

        $user = new User([
            'email' => $input->email,
            'password' => Hash::make($input->password),
            'person_type' => $input->personType,
            'first_name' => $input->firstName,
            'second_name' => $input->secondName,
            'first_lastname' => $input->firstLastname,
            'second_lastname' => $input->secondLastname,
            'document_type' => $input->documentType,
            'document_number' => $input->documentNumber,
            'company_razon' => $input->companyRazon,
            'company_email' => $input->companyEmail,
            'company_phone' => $input->companyPhone,
            'company_actividad' => json_encode($input->companyActividad),
            'company_type' => $input->companyType,
            'company_document_type' => $input->companyDocumentType,
            'company_document_number' => $input->companyDocumentNumber,
            'documento_file' => $docfile,
            'camara_file' => $camarafile,
            'rut_file' => $rutfile,
            'phone' => $input->phone,
            'policies_and_cookies_consent' => $input->policiesAndCookiesConsent,
            'offers_consent' => $input->offersConsent,
            'verification_code' => rand(100000, 999999),
            'emailPurchasingPerson' => $input->emailPurchasingPerson,
            'cellphonePurchasingPerson' => $input->cellphonePurchasingPerson,
            'phonePurchasingPerson' => $input->phonePurchasingPerson,
        ]);

        $user->save();

        if ($input->personType == 'Juridical') {
            $company = new Company([
                'user_id' => $user->id,
                'person_type' => $user->person_type,
                'first_name' => $user->first_name,
                'second_name' => $user->second_name,
                'first_lastname' => $user->first_lastname,
                'second_lastname' => $user->second_lastname,
                'document_type' => $user->document_type,
                'document_number' => $user->document_number,
                'company_razon' => $user->company_razon,
                'company_email' => $user->company_email,
                'company_phone' => $user->company_phone,
                'company_actividad' => json_encode($input->companyActividad),
                'regimen_fiscal' => json_encode($input->regimenFiscal),
                'responsabilidad_tribut' => json_encode($input->responsabilidadTribut),
                'company_type' => $user->company_type,
                'company_document_type' => $user->company_document_type,
                'company_document_number' => $user->company_document_number,
                'documento_file' => $docfile,
                'camara_file' => $camarafile,
                'rut_file' => $rutfile,
            ]);
            $company->save();
        }



        if (isset($input->temp_user_id) && $input->temp_user_id != null) {
            Cart::where('temp_user_id', $input->temp_user_id)->update(
                [
                    'user_id' => $user->id,
                    'temp_user_id' => null
                ]
            );
        }

        if (get_setting('customer_otp_with') != 'disabled') {
            if (get_setting('customer_login_with') == 'email' || (get_setting('customer_login_with') == 'email_phone' && get_setting('customer_otp_with') == 'email')) {
                $user->notify(new EmailVerificationNotification());
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'verified' => false,
                    'message' => translate('Codigo de Verificación enviado al correo.')
                ], 200);
            } else {
                (new SmsServices)->phoneVerificationSms($user->phone, $user->verification_code);
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'verified' => false,
                    'message' => translate('Codigo de Verificación enviado al celular.')
                ], 200);
            }
        }

        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);
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
