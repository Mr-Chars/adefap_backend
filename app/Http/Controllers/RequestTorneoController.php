<?php

namespace App\Http\Controllers;

use App\Models\RequestTorneo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestTorneoController extends Controller
{
    protected $clubController;

    public function __construct(
        ClubController $clubController,
    ) {
        $this->clubController = $clubController;
    }

    public function getRequestTorneo($config)
    {
        $where = (array_key_exists('where', $config)) ? $config['where'] : null;
        $pagination_itemQuantity = ($config && array_key_exists('pagination_itemQuantity', $config)) ? $config['pagination_itemQuantity'] : 0;
        $pagination_step = ($config && array_key_exists('pagination_step', $config)) ? $config['pagination_step'] : 0;

        $search = DB::table('request_torneo')
            ->join('tb_ubigeos as ubigeo_centro_estudios', 'request_torneo.ubigeo_centro_estudios', '=', 'ubigeo_centro_estudios.ubigeo_reniec')
            ->join('participant', 'request_torneo.id_participant', '=', 'participant.id');

        if ($where) {
            $search = $search->where($where);
        }
        $search->select(
            'request_torneo.id as id',
            'request_torneo.centro_estudios as centro_estudios',
            'request_torneo.year_estudios as year_estudios',

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

            'ubigeo_centro_estudios.distrito as ubigeo_centro_estudios_distrito',
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
            'id_club' => 'required',
            'centro_estudios' => 'required',
            'ubigeo_centro_estudios' => 'required',
            'year_estudios' => 'required',
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
        $id_club = $request->id_club;
        $centro_estudios = $request->centro_estudios;
        $ubigeo_centro_estudios = $request->ubigeo_centro_estudios;
        $year_estudios = $request->year_estudios;

        $arrayConfig = [
            'where' => [['request_torneo.id_participant', '=', $id_participant], ['request_torneo.id_club', '=', $id_club]],
        ];
        $userFromData = $this->getRequestTorneo($arrayConfig, true);
        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este participante ya fue registrado en el club seleccionado.',
            ]);
        }

        $dataToUpdate = [
            'id_participant' => $id_participant,
            'id_club' => $id_club,
            'centro_estudios' => $centro_estudios,
            'ubigeo_centro_estudios' => $ubigeo_centro_estudios,
            'year_estudios' => $year_estudios,
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
        $userFromData = $this->getRequestTorneo($arrayConfig, true);
        if (count($userFromData) <= 0) {
            return response()->json([
                'status' => false,
                'error' => 'requerimiento no encontrado',
            ]);
        }

        // $arrayConfig = [
        //     'where' => [['clubs.id', '=', $userFromData[0]->id_club]],
        // ];

        // $clubSelected = $this->clubController->getClubs($arrayConfig);

        // $data = [
        //     'clubSelected' => $clubSelected[0]->name,
        //     'nombres' => $userFromData[0]->nombres,
        //     'apellido_paterno' => $userFromData[0]->apellido_paterno,
        //     'apellido_materno' => $userFromData[0]->apellido_materno,
        //     'fecha_nacimiento' => $userFromData[0]->fecha_nacimiento,
        //     'lugar_nacimiento' => $userFromData[0]->lugar_nacimiento,
        //     'centro_estudios' => $userFromData[0]->centro_estudios,
        //     'distrito_centro_estudios' => $userFromData[0]->ubigeo_centro_estudios_distrito,
        //     'year_estudios' => $userFromData[0]->year_estudios,
        //     'dni' => $userFromData[0]->dni,
        //     'domicilio' => $userFromData[0]->domicilio,
        //     'distrito_domicilio' => $userFromData[0]->ubigeo_domicilio_distrito,
        //     'n_celular' => $userFromData[0]->n_celular,
        //     'talla' => $userFromData[0]->talla,
        //     'peso' => $userFromData[0]->peso,
        //     'participantPhoto' => $userFromData[0]->participantPhoto,
        // ];

        // $pdf = Pdf::loadView('pdf.documento', $data);
        // // return $pdf->download('documento.pdf'); // Para descargarlo
        // return $pdf->stream(); // Para verlo en el navegador
    }
}
