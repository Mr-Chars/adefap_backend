<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ParticipantController extends Controller
{
    //

    public function getParticipant($config)
    {
        $where = (array_key_exists('where', $config)) ? $config['where'] : null;
        $orWhere = (array_key_exists('orWhere', $config)) ? $config['orWhere'] : null;
        $pagination_itemQuantity = (array_key_exists('pagination_itemQuantity', $config)) ? $config['pagination_itemQuantity'] : 0;
        $pagination_step = (array_key_exists('pagination_step', $config)) ? $config['pagination_step'] : 0;

        $search = DB::table('participant');

        if ($where) {
            $search = $search->where($where);
        }

        if ($orWhere) {
            $search = $search->orWhere($orWhere);
        }
        $search->select(
            'participant.id as id',
            'participant.nombres as nombres',
            'participant.apellido_paterno as apellido_paterno',
            'participant.apellido_materno as apellido_materno',
            'participant.dni as dni',
            'participant.fecha_nacimiento as fecha_nacimiento',
            'participant.ubigeo_nacimiento as ubigeo_nacimiento',
            'participant.domicilio as domicilio',
            'participant.ubigeo_domicilio as ubigeo_domicilio',
            'participant.n_celular as n_celular',
            'participant.talla as talla',
            'participant.peso as peso',
            'participant.participantPhoto as participantPhoto',
            'participant.id_creator as id_creator',

            'participant.created_at as created_at',
            'participant.updated_at as updated_at',
        );
        if ($pagination_itemQuantity) {
            $search = $search->paginate($pagination_itemQuantity, null, 'page', $pagination_step);
            $search = $search;
        } else {
            $search = $search->get();
        }

        return $search;
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

        $deletedItem = Participant::where('id', $id)->delete();

        return response()->json([
            'status' => true,
            'response' => $deletedItem,
        ]);
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
        $data = $this->getParticipant($arrayConfig);

        return response()->json([
            'status' => true,
            'data' => $data,
            'arrayConfig' => $arrayConfig,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = Validator::make($request->all(), [
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'fecha_nacimiento' => 'required',
            'ubigeo_nacimiento' => 'required',
            'dni' => 'required',
            'domicilio' => 'required',
            'ubigeo_domicilio' => 'required',
            'n_celular' => 'required',
            'talla' => 'required',
            'peso' => 'required',
            'participantPhoto' => 'required',
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

        $nombres = $request->nombres;
        $apellido_paterno = $request->apellido_paterno;
        $apellido_materno = $request->apellido_materno;
        $dni = $request->dni;
        $fecha_nacimiento = $request->fecha_nacimiento;
        $ubigeo_nacimiento = $request->ubigeo_nacimiento;
        $domicilio = $request->domicilio;
        $ubigeo_domicilio = $request->ubigeo_domicilio;
        $n_celular = $request->n_celular;
        $talla = $request->talla;
        $peso = $request->peso;
        $participantPhoto = $request->participantPhoto;

        $arrayConfig = [
            'where' => [['participant.dni', '=', $dni], ['participant.id', '!=', $id]],
        ];
        $userFromData = $this->getParticipant($arrayConfig, true);

        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'El dni de este participante ya fue registrado.',
            ]);
        }

        $dataToUpdate = [
            'nombres' => $nombres,
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'dni' => $dni,
            'fecha_nacimiento' => $fecha_nacimiento,
            'ubigeo_nacimiento' => $ubigeo_nacimiento,
            'domicilio' => $domicilio,
            'ubigeo_domicilio' => $ubigeo_domicilio,
            'n_celular' => $n_celular,
            'talla' => $talla,
            'peso' => $peso,
            'participantPhoto' => $participantPhoto,
        ];

        try {
            $post = Participant::where('id', $id)->update($dataToUpdate);

            return response()->json([
                'status' => true,
                'post' => $post,
                'dataToUpdate' => $dataToUpdate,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status' => true,
                'error' => $e->getMessage(),
                'dataToUpdate' => $dataToUpdate,
            ]);
        }
    }

    public function add(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'fecha_nacimiento' => 'required',
            'ubigeo_nacimiento' => 'required',
            'dni' => 'required',
            'domicilio' => 'required',
            'ubigeo_domicilio' => 'required',
            'n_celular' => 'required',
            'talla' => 'required',
            'peso' => 'required',
            'participantPhoto' => 'required',
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

        $nombres = $request->nombres;
        $apellido_paterno = $request->apellido_paterno;
        $apellido_materno = $request->apellido_materno;
        $dni = $request->dni;
        $fecha_nacimiento = $request->fecha_nacimiento;
        $ubigeo_nacimiento = $request->ubigeo_nacimiento;
        $domicilio = $request->domicilio;
        $ubigeo_domicilio = $request->ubigeo_domicilio;
        $n_celular = $request->n_celular;
        $talla = $request->talla;
        $peso = $request->peso;
        $participantPhoto = $request->participantPhoto;
        $id_creator = $request->id_creator;

        $arrayConfig = [
            'where' => [['participant.dni', '=', $dni]],
        ];
        $userFromData = $this->getParticipant($arrayConfig, true);
        if (count($userFromData)) {
            return response()->json([
                'status' => false,
                'error' => 'Este dni ya fue registrado.',
            ]);
        }

        $dataToUpdate = [
            'nombres' => $nombres,
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'dni' => $dni,
            'fecha_nacimiento' => $fecha_nacimiento,
            'ubigeo_nacimiento' => $ubigeo_nacimiento,
            'domicilio' => $domicilio,
            'ubigeo_domicilio' => $ubigeo_domicilio,
            'n_celular' => $n_celular,
            'talla' => $talla,
            'peso' => $peso,
            'participantPhoto' => $participantPhoto,
            'id_creator' => $id_creator,
        ];

        try {
            $post = Participant::create($dataToUpdate);
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
}
