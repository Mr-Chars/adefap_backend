<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UbigeosController extends Controller
{
    //
    public function getUbigeo($config)
    {
        $where = (array_key_exists('where', $config)) ? $config['where'] : null;
        $orWhere = (array_key_exists('orWhere', $config)) ? $config['orWhere'] : null;
        $pagination_itemQuantity = (array_key_exists('pagination_itemQuantity', $config)) ? $config['pagination_itemQuantity'] : 0;
        $pagination_step = (array_key_exists('pagination_step', $config)) ? $config['pagination_step'] : 0;

        $search = DB::table('tb_ubigeos');

        if ($where) {
            $search = $search->where($where);
        }

        if ($orWhere) {
            $search = $search->orWhere($orWhere);
        }
        $search->select(
            'tb_ubigeos.id_ubigeo as id_ubigeo',
            'tb_ubigeos.ubigeo_reniec as ubigeo_reniec',
            'tb_ubigeos.departamento as departamento',
            'tb_ubigeos.provincia as provincia',
            'tb_ubigeos.distrito as distrito',
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
        $orWhere = ($request->orWhere) ? json_decode(base64_decode($request->orWhere), true) : null;
        $pagination_itemQuantity = ($request->pagination_itemQuantity) ? $request->pagination_itemQuantity : 0;
        $pagination_step = ($request->pagination_step) ? $request->pagination_step : 0;
        $arrayConfig = [
            'where' => $where,
            'orWhere' => $orWhere,
            'pagination_itemQuantity' => $pagination_itemQuantity,
            'pagination_step' => $pagination_step,
        ];
        $data = $this->getUbigeo($arrayConfig);

        return response()->json([
            'status' => true,
            'data' => $data,
            'arrayConfig' => $arrayConfig,
        ]);
    }
}
