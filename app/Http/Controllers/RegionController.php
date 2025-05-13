<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{
    public function getRegion($config)
    {
        $where = (array_key_exists('where', $config)) ? $config['where'] : null;
        $orWhere = (array_key_exists('orWhere', $config)) ? $config['orWhere'] : null;
        $pagination_itemQuantity = (array_key_exists('pagination_itemQuantity', $config)) ? $config['pagination_itemQuantity'] : 0;
        $pagination_step = (array_key_exists('pagination_step', $config)) ? $config['pagination_step'] : 0;

        $search = DB::table('region');

        if ($where) {
            $search = $search->where($where);
        }

        if ($orWhere) {
            $search = $search->orWhere($orWhere);
        }
        $search->select(
            'region.id as id',
            'region.name as name',
            'region.id_creator as id_creator',
            'region.created_at as created_at',
            'region.updated_at as updated_at',
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
        $data = $this->getRegion($arrayConfig);

        return response()->json([
            'status' => true,
            'data' => $data,
            'arrayConfig' => $arrayConfig,
        ]);
    }

    public function add(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
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

        $name = $request->name;
        $id_creator = $request->id_creator;

        $arrayConfig = [
            'where' => [['region.name', '=', $name]],
        ];
        $userFromData = $this->getRegion($arrayConfig, true);
        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este nombre de region ya fue registrado.',
            ]);
        }

        $dataToSubmit = [
            'name' => $name,
            'id_creator' => $id_creator,
        ];

        try {
            $post = Region::create($dataToSubmit);
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
            'name' => 'required',
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

        $arrayConfig = [
            'where' => [['region.name', '=', $name], ['region.id', '!=', $id]],
        ];
        $userFromData = $this->getRegion($arrayConfig, true);

        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este nombre de region ya fue registrado.',
            ]);
        }

        $dataToUpdate = [
            'name' => $name,
        ];

        try {
            $post = Region::where('id', $id)->update($dataToUpdate);

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

        $deletedItem = Region::where('id', $id)->delete();

        return response()->json([
            'status' => true,
            'response' => $deletedItem,
        ]);
    }
}
