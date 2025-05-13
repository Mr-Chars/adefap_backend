<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //

    public function search(Request $request)
    {
        $where = ($request->where) ? (json_decode(base64_decode($request->where), true)) : null;
        $pagination_itemQuantity = ($request->pagination_itemQuantity) ? $request->pagination_itemQuantity : 0;
        $pagination_step = ($request->pagination_step) ? $request->pagination_step : 0;
        $arrayConfig = [
            'where' => $where,
            'pagination_itemQuantity' => $pagination_itemQuantity,
            'pagination_step' => $pagination_step,
        ];
        $data = $this->getUsers($arrayConfig);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function delete(Request $request, string $id)
    {
        $token = $request->header('token');
        if (!$token) {
            return response()->json([
                'status' => false,
                'error' => 'no token receive',
            ]);
        }

        $deletedUser = User::where('id', $id)->delete();

        return response()->json([
            'status' => true,
            'response' => $deletedUser,
        ]);
    }

    public function add(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        $token = $request->header('token');

        if (!$token) {
            return response()->json([
                'status' => false,
                'error' => 'no token receive',
            ]);
        }

        if ($validated->fails()) {
            // validation failed
            $error = $validated->errors()->first();

            return response()->json([
                'status' => false,
                'error' => $error,
            ]);
        }

        $name = $request->name;
        $username = $request->username;
        $password = $request->password;
        $role = $request->role;
        $id_region = $request->id_region;

        $arrayConfig = [
            'where' => [['user.username', '=', $username]],
        ];
        $userFromData = $this->getUsers($arrayConfig, true);
        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este nombre de usuario ya fue registrado.',
            ]);
        }

        $dataToUpdate = [
            'name' => $name,
            'username' => $username,
            'password' => $password,
            'role' => $role,
            'id_region' => $id_region,
        ];

        try {
            $post = User::create($dataToUpdate);

            return response()->json([
                'status' => true,
                'post' => $post,
                'dataToadd' => $dataToUpdate,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status' => true,
                'error' => $e->getMessage(),
                'dataToadd' => $dataToUpdate,
            ]);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'role' => 'required',
        ]);

        $token = $request->header('token');

        if (!$token) {
            return response()->json([
                'status' => false,
                'error' => 'no token receive',
            ]);
        }

        if ($validated->fails()) {
            // validation failed
            $error = $validated->errors()->first();

            return response()->json([
                'status' => false,
                'error' => $error,
            ]);
        }

        $name = $request->name;
        $username = $request->username;
        $password = $request->password;
        $role = $request->role;

        $arrayConfig = [
            'where' => [['user.username', '=', $username], ['user.id', '!=', $id]],
        ];
        $userFromData = $this->getUsers($arrayConfig, true);
        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este nombre de usuario ya fue registrado.',
            ]);
        }

        $dataToUpdate = [
            'name' => $name,
            'username' => $username,
            'role' => $role,
        ];

        try {
            if ($password) {
                $dataToUpdate['password'] = Hash::make($password);
            }
            $post = User::where('id', $id)->update($dataToUpdate);

            return response()->json([
                'status' => true,
                'post' => $post,
                'dataToadd' => $dataToUpdate,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status' => true,
                'error' => $e->getMessage(),
                'dataToadd' => $dataToUpdate,
            ]);
        }
    }

    public function getUsers($config, $withPassword = false)
    {
        $where = (array_key_exists('where', $config)) ? $config['where'] : null;
        $orWhere = (array_key_exists('orWhere', $config)) ? $config['orWhere'] : null;
        $pagination_itemQuantity = (array_key_exists('pagination_itemQuantity', $config)) ? $config['pagination_itemQuantity'] : 0;
        $pagination_step = (array_key_exists('pagination_step', $config)) ? $config['pagination_step'] : 0;

        $search = DB::table('user')
            ->leftJoin('region', 'user.id_region', '=', 'region.id');

        if ($where) {
            $search = $search->where($where);
        }

        if ($orWhere) {
            $search = $search->orWhere($orWhere);
        }
        $search->select(
            'user.id as id',
            'user.name as name',
            'user.username as username',
            'user.password as password',
            'user.role as role',
            'user.id_region as id_region',
            'user.created_at as created_at',
            'user.updated_at as updated_at',

            'region.name as region_name',
        );
        if ($pagination_itemQuantity) {
            $search = $search->paginate($pagination_itemQuantity, null, 'page', $pagination_step);
            $search = $search;
        } else {
            $search = $search->get();
        }

        if (!$withPassword) {
            foreach ($search as $key => $value) {
                $search[$key]->password = ':)';
            }
        }

        return $search;
    }
}
