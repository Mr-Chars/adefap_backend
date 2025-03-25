<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT; // include this at top
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    protected $userController;

    // protected $tokenController;
    // , TokenController $tokenController
    public function __construct(UserController $userController)
    {
        $this->userController = $userController;
        // $this->tokenController = $tokenController;
    }

    public function decriptTokenData($token)
    {
        $secret_key = '5b5d4c173899b9e900317db00f0d967e6c20cb9120c0b2d08dfe5343361413f6';

        try {
            // $decoded = JWT::decode($token, $secret_key, array('HS256'));
            JWT::$leeway = 600; // $leeway in seconds
            $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
            // Access is granted. Add code of the operation here
            return $decoded;
        } catch (Exception $e) {
            return false;
        }
    }

    public function decriptToken(Request $request)
    {
        $validated = Validator::make($request->all(), [
            // 'token'   =>  'required|max:255',
            'token' => 'required',
        ]);

        if ($validated->fails()) {
            // validation failed
            $error = $validated->errors()->first();

            return response()->json([
                'status' => false,
                'error' => $error,
            ]);
        }
        // validation passed
        $token = $request->token;
        $tokenDecript = $this->decriptTokenData($token);
        //
        return response()->json([
            'status' => $tokenDecript ? true : false,
            'token' => $token,
            'tokenDecript' => $tokenDecript,
        ]);
    }

    public function generateToken($data)
    {
        $secret_key = '5b5d4c173899b9e900317db00f0d967e6c20cb9120c0b2d08dfe5343361413f6';

        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 100; //not before in seconds
        $expire_claim = $issuedat_claim + 72000; // expire time in seconds
        $token = [
            'iat' => $issuedat_claim,
            'nbf' => $notbefore_claim,
            'exp' => $expire_claim,
            'data' => $data,
        ];

        $jwt = JWT::encode($token, $secret_key, 'HS256');

        $dato[0] = $jwt;
        $dato[1] = $expire_claim;

        return $dato;
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'username' => 'required|max:255',
            'password' => 'required|max:255',
        ]);

        if ($validated->fails()) {
            // validation failed
            $error = $validated->errors()->first();

            return response()->json([
                'status' => false,
                'error' => $error,
            ]);
        }
        // validation passed
        $username = $request->username;
        $password = $request->password;

        // check if exist user in db
        $arrayConfig = [
            'where' => [['user.username', '=', $username]],
        ];
        $userFromData = $this->userController->getUsers($arrayConfig, true);

        if (count($userFromData) <= 0) {
            return response()->json([
                'status' => false,
                'error' => 'usuario no encontrado en la bd',
            ]);
        }

        if (!Hash::check($password, $userFromData[0]->password)) {
            return response()->json([
                'status' => false,
                'password_db' => $userFromData[0]->password,
                'error' => 'Error en la autenticación',
            ]);
        }

        $userFromData[0]->password = ':)';
        $tokenData = $this->generateToken($userFromData);

        return response()->json([
            'status' => true,
            'msn' => 'Bienvenido',
            'user' => $userFromData,
            'token' => $tokenData[0],
            'tokenExpiration' => $tokenData[1],
        ]);
    }
}
