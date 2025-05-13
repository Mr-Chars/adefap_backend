<?php

namespace App\Http\Controllers;

use App\Models\CentroEstudios;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CentroEstudiosController extends Controller
{

    public function getCentroEstudios($config)
    {
        $where = (array_key_exists('where', $config)) ? $config['where'] : null;
        $orWhere = (array_key_exists('orWhere', $config)) ? $config['orWhere'] : null;
        $pagination_itemQuantity = (array_key_exists('pagination_itemQuantity', $config)) ? $config['pagination_itemQuantity'] : 0;
        $pagination_step = (array_key_exists('pagination_step', $config)) ? $config['pagination_step'] : 0;

        $search = DB::table('centro_estudios')
            ->join('tb_ubigeos as ubigeo_centro_estudios', 'centro_estudios.ubigeo', '=', 'ubigeo_centro_estudios.ubigeo_reniec');

        if ($where) {
            $search = $search->where($where);
        }

        if ($orWhere) {
            $search = $search->orWhere($orWhere);
        }
        $search->select(
            'centro_estudios.id as id',
            'centro_estudios.nombre as nombre',
            'centro_estudios.ubigeo as ubigeo',
            'centro_estudios.id_creator as id_creator',

            'ubigeo_centro_estudios.distrito as ubigeo_distrito',

            'centro_estudios.created_at as created_at',
            'centro_estudios.updated_at as updated_at',
        );
        if ($pagination_itemQuantity) {
            $search = $search->paginate($pagination_itemQuantity, null, 'page', $pagination_step);
            $search = $search;
        } else {
            $search = $search->get();
        }

        return $search;
    }

    public function search(Request $request)
    {
        $where = ($request->where) ? json_decode(base64_decode($request->where), true) : null;
        $pagination_itemQuantity = ($request->pagination_itemQuantity) ? $request->pagination_itemQuantity : 0;
        $pagination_step = ($request->pagination_step) ? $request->pagination_step : 0;
        $arrayConfig = [
            'where' => $where,
            'pagination_itemQuantity' => $pagination_itemQuantity,
            'pagination_step' => $pagination_step,
        ];
        $data = $this->getCentroEstudios($arrayConfig);

        return response()->json([
            'status' => true,
            'data' => $data,
            'arrayConfig' => $arrayConfig,
        ]);
    }

    public function add(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nombre' => 'required',
            'ubigeo' => 'required',
            'id_creator' => 'required',
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

        $nombre = $request->nombre;
        $ubigeo = $request->ubigeo;
        $id_creator = $request->id_creator;

        $arrayConfig = [
            'where' => [['centro_estudios.nombre', '=', $nombre]],
        ];
        $userFromData = $this->getCentroEstudios($arrayConfig, true);
        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este nombre ya fue registrado.',
            ]);
        }

        $dataToSubmit = [
            'nombre' => $nombre,
            'ubigeo' => $ubigeo,
            'id_creator' => $id_creator,
        ];

        try {
            $post = CentroEstudios::create($dataToSubmit);
        } catch (QueryException $e) {
            return response()->json([
                'status' => true,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => true,
            'post' => $post,
            'dataToadd' => $dataToSubmit,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = Validator::make($request->all(), [
            'nombre' => 'required',
            'ubigeo' => 'required',
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

        $nombre = $request->nombre;
        $ubigeo = $request->ubigeo;

        $arrayConfig = [
            'where' => [['centro_estudios.nombre', '=', $nombre], ['centro_estudios.id', '!=', $id]],
        ];
        $userFromData = $this->getCentroEstudios($arrayConfig, true);

        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este nombre ya fue registrado.',
            ]);
        }

        $dataToUpdate = [
            'nombre' => $nombre,
            'ubigeo' => $ubigeo,
        ];

        try {
            $post = CentroEstudios::where('id', $id)->update($dataToUpdate);

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

    public function delete(Request $request, string $id)
    {
        $token = $request->header('token');
        if (!$token) {
            return response()->json([
                'status' => false,
                'error' => 'not token received',
            ]);
        }

        $deletedItem = CentroEstudios::where('id', $id)->delete();

        return response()->json([
            'status' => true,
            'response' => $deletedItem,
        ]);
    }
}
