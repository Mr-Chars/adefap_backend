<?php

namespace App\Http\Controllers;

use App\Models\RequestTorneo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;

class RequestTorneoController extends Controller
{
    protected $userController;

    public function __construct(
        UserController $userController,
    ) {
        $this->userController = $userController;
    }

    public function getRequestTorneo($config)
    {
        $where = (array_key_exists('where', $config)) ? $config['where'] : null;
        $pagination_itemQuantity = ($config && array_key_exists('pagination_itemQuantity', $config)) ? $config['pagination_itemQuantity'] : 0;
        $pagination_step = ($config && array_key_exists('pagination_step', $config)) ? $config['pagination_step'] : 0;

        $search = DB::table('request_torneo')
            ->leftJoin('region', 'request_torneo.id_region', '=', 'region.id')
            ->leftJoin('centro_estudios', 'request_torneo.id_centro_estudios', '=', 'centro_estudios.id')
            ->leftJoin('tb_ubigeos as ubigeo_centro_estudios', 'centro_estudios.ubigeo', '=', 'ubigeo_centro_estudios.ubigeo_reniec')
            ->join('participant', 'request_torneo.id_participant', '=', 'participant.id')
            ->join('tb_ubigeos as ubigeo_nacimiento', 'participant.ubigeo_nacimiento', '=', 'ubigeo_nacimiento.ubigeo_reniec')
            ->join('tb_ubigeos as ubigeo_domicilio', 'participant.ubigeo_domicilio', '=', 'ubigeo_domicilio.ubigeo_reniec')
            ->join('category', 'request_torneo.id_category', '=', 'category.id');

        if ($where) {
            $search = $search->where($where);
        }
        $search->select(
            'request_torneo.id as id',
            // 'request_torneo.centro_estudios as centro_estudios',
            'request_torneo.id_category as id_category',
            'request_torneo.id_creator as id_creator',
            'request_torneo.id_region as id_region',

            'participant.id as participant_id',
            'participant.nombres as participant_nombres',
            'participant.apellido_paterno as participant_apellido_paterno',
            'participant.apellido_materno as participant_apellido_materno',
            'participant.dni as participant_dni',
            'participant.fecha_nacimiento as participant_fecha_nacimiento',
            'participant.ubigeo_nacimiento as participant_ubigeo_nacimiento',
            'participant.domicilio as participant_domicilio',
            'participant.ubigeo_domicilio as participant_ubigeo_domicilio',
            'participant.n_celular as participant_n_celular',
            'participant.talla as participant_talla',
            'participant.peso as participant_peso',
            'participant.participantPhoto as participant_participantPhoto',

            'centro_estudios.nombre as centro_estudios_nombre',

            'ubigeo_nacimiento.distrito as ubigeo_nacimiento_distrito',
            'ubigeo_centro_estudios.distrito as ubigeo_centro_estudios_distrito',
            'ubigeo_domicilio.distrito as ubigeo_domicilio_distrito',

            'category.name as category_name',

            'region.name as region_name',
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

        $data = $this->getRequestTorneo($arrayConfig);

        return response()->json([
            'status' => true,
            'data' => $data,
            'where' => $where,
        ]);
    }

    public function add(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id_participant' => 'required',
            'id_creator' => 'required',
            'id_category' => 'required',
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

        $id_participant = $request->id_participant;
        $id_creator = $request->id_creator;
        $id_centro_estudios = $request->id_centro_estudios;
        $id_category = $request->id_category;
        $id_region = $request->id_region;

        $arrayConfig = [
            'where' => [['request_torneo.id_participant', '=', $id_participant]],
        ];
        $userFromData = $this->getRequestTorneo($arrayConfig, true);
        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este participante ya fue registrado.',
            ]);
        }

        $dataToUpdate = [
            'id_participant' => $id_participant,
            'id_creator' => $id_creator,
            'id_centro_estudios' => $id_centro_estudios,
            'id_category' => $id_category,
            'id_region' => $id_region,
        ];

        try {
            $post = RequestTorneo::create($dataToUpdate);
        } catch (QueryException $e) {
            return response()->json([
                'status' => true,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'status' => true,
            'post' => $post,
            'dataToadd' => $dataToUpdate,
        ]);
    }

    public function generarExcel()
    {

        $data = DB::table('request_torneo')
            ->join('participant', 'request_torneo.id_participant', '=', 'participant.id')
            ->join('category', 'request_torneo.id_category', '=', 'category.id')
            ->select(
                'request_torneo.id as id',
                'participant.nombres as participant_nombres',
                'participant.apellido_paterno as participant_apellido_paterno',
                'participant.apellido_materno as participant_apellido_materno',
                'participant.dni as participant_dni',
                'participant.fecha_nacimiento as participant_fecha_nacimiento',

                'category.name as category_name',

                'request_torneo.created_at as created_at',
            ) // Selecciona las columnas necesarias
            ->get(); // Esto ya devuelve una Collection

        return (new FastExcel($data))->download('archivo.xlsx', function ($item) {
            return [
                'Nombre' => $item->participant_nombres,
                'Apellidos' => $item->participant_apellido_paterno . ' ' . $item->participant_apellido_materno,
                'FN' => $item->participant_fecha_nacimiento,
                'DNI' => $item->participant_dni,
                'Categoría' => $item->category_name,
                // Agrega más campos según necesites
            ];
        });

        // $response = (new FastExcel($data))->download('archivo.xlsx');

        // return $response;
    }

    public function generarPdf(Request $request)
    {
        if (!$request->idRequest) {
            return response()->json([
                'status' => false,
                'error' => 'idRequest no encontrado',
            ]);
        }

        $arrayConfig = [
            'where' => [['request_torneo.id', '=', $request->idRequest]],
        ];
        $requestTorneoFromData = $this->getRequestTorneo($arrayConfig, true);
        if (count($requestTorneoFromData) <= 0) {
            return response()->json([
                'status' => false,
                'error' => 'requerimiento no encontrado',
            ]);
        }
        $arrayConfig = [
            'where' => [['user.id', '=', $requestTorneoFromData[0]->id_creator]],
        ];
        $userFromData = $this->userController->getUsers($arrayConfig, true);
        $data = [
            'clubSelected' => $userFromData[0]->name,
            'nombres' => $requestTorneoFromData[0]->participant_nombres,
            'apellido_paterno' => $requestTorneoFromData[0]->participant_apellido_paterno,
            'apellido_materno' => $requestTorneoFromData[0]->participant_apellido_materno,
            'fecha_nacimiento' => $requestTorneoFromData[0]->participant_fecha_nacimiento,
            'lugar_nacimiento' => $requestTorneoFromData[0]->ubigeo_nacimiento_distrito,
            'centro_estudios' => $requestTorneoFromData[0]->centro_estudios_nombre,
            'distrito_centro_estudios' => $requestTorneoFromData[0]->ubigeo_centro_estudios_distrito,
            'category_name' => $requestTorneoFromData[0]->category_name,
            'dni' => $requestTorneoFromData[0]->participant_dni,
            'domicilio' => $requestTorneoFromData[0]->participant_domicilio,
            'distrito_domicilio' => $requestTorneoFromData[0]->ubigeo_domicilio_distrito,
            'n_celular' => $requestTorneoFromData[0]->participant_n_celular,
            'talla' => $requestTorneoFromData[0]->participant_talla,
            'peso' => $requestTorneoFromData[0]->participant_peso,
            'participantPhoto' => $requestTorneoFromData[0]->participant_participantPhoto,
        ];
        // print_r($data);
        $pdf = Pdf::loadView('pdf.documento', $data);
        // return $pdf->download('documento.pdf'); // Para descargarlo
        return $pdf->stream(); // Para verlo en el navegador
    }
}
