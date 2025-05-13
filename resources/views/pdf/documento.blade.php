<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #333;
        }
    </style>
</head>

<body>

    <table width="100%" border="0">
        <tr>
            <td style="width: 75%;">
                <table width="100%" border="0">
                    <tr>
                        <td style="width: 30%;">
                            <img src="{{ public_path('img/logo.png') }}" style="width:100%;">
                        </td>
                        <td style="width: 70%; text-align: center;font-size: 22px;">
                            <img src="{{ public_path('img/logo_portada.png') }}" style="width:80%;margin: auto;">

                            <span style="display: block;">COPA DE ORO 2025</span>
                            <span style="display: block;color: red;">LIMA</span>
                            FECHA DE INSCRIPCIÓN
                        </td>
                    </tr>
                </table>
                <label style="display: block;border: 1px solid #000;">
                    CLUB: {{ $clubSelected }}
                </label>
            </td>
            <td style="width: 25%; text-align: right;">
                <div style="width: 95%; margin: auto;border: 1px solid #000; height: 150px;position: relative;">
                    <span style="position: absolute;left: 50%;top: 50%;-webkit-transform: translate(-50%, -50%);
                    transform: translate(-50%, -50%);">
                        <img src="{{ $participantPhoto }}" style="width:100%;">

                    </span>
                </div>
            </td>
        </tr>
    </table>

    <h2 style="text-align: center;font-weight: bold;">DATOS DEL JUGADOR</h2>

    <table width="100%" border="1px" style="border-collapse: collapse;margin-bottom: 70px;">
        <tr>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">APELLIDO PATERNO:</span> {{ $apellido_paterno }}
            </td>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">APELLIDO MATERNO:</span> {{ $apellido_materno }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 100%; text-align: left;">
                <span style="font-weight: bold;">NOMBRES:</span> {{ $nombres }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">FECHA DE NACIMIENTO:</span> {{ $fecha_nacimiento }}
            </td>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">LUGAR DE NACIMIENTO:</span> {{ $lugar_nacimiento }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">CENTRO DE ESTUDIOS:</span> {{ $centro_estudios }}
            </td>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">DISTRITO:</span> {{ $distrito_centro_estudios }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">CATEGORÍA:</span> {{ $category_name }}
            </td>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">DNI N°:</span> {{ $dni }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">DOMICILIO:</span> {{ $domicilio }}
            </td>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">TELÉFONOS O CEL:</span> {{ $n_celular }}
                <span style="display: block;">______________________________________</span>
                <span style="font-weight: bold;">TALLA:</span> {{ $talla }}
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">DISTRITO:</span> {{ $distrito_domicilio }}
            </td>
            <td style="width: 50%; text-align: left;">
                <span style="font-weight: bold;">PESO:</span> {{ $peso }}
            </td>
        </tr>
    </table>

    <!-- <table width="100%" border="0" style="margin-bottom: 30px;">
        <tr>
            <td style="width: 50%; text-align: left;">
                ________________________ <br>
                FIRMA DEL JUGADOR
            </td>
            <td style="width: 50%; text-align: right;">
                ________________________ <br>
                FIRMA DEL DELEGADO <br>
                <span style="font-size: 12px;">(RESPONSABLE VERACIDAD DE DATOS)</span>
            </td>
        </tr>
    </table>

    <span style="font-weight: bold;display:block">AUTORIZACIÓN DEL PADRE, MADRE O APODERADO</span>
    <span style="text-align: left;">
        Autorizo a mi menor hijo(a) {{ $apellido_paterno }} {{ $apellido_materno }} {{ $nombres }}<br>
        quien goza de perfectas condiciones físicas y de salud para practicar el futbol, a participar en <br>
        representación del CLUB {{ $clubSelected }}<br>
        Nombre y Apellidos del Padre, Madre o Apoderado Daniela Roca <br>
        DNI N° 7518489
    </span>

    <div style="margin-top: 70px;width: 100%;text-align: center;">
        FIRMA DEL APODERADO <br>
        ________________________
    </div> -->
</body>

</html>
