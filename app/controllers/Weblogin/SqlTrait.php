<?php

namespace App\Controllers\Weblogin;

use App\Models\BaseModel;

trait SqlTrait
{
    public static function viewFetch($referenciaId, $dni)
    {
        /** Determina que vamos a llamar desde el front */
        $data = self::sqlViewFetch($referenciaId, $dni);

        $data['legajo'] = $data['legajo'] != null && FETCH_LEGAJO ? true : false;
        $data['libreta'] = $data['libreta'] != null && FETCH_LIBRETA ? true : false;
        $data['licencia'] = ($data['licencia'] == null || $data['licencia'] == -1) && FETCH_LICENCIA ? false : true;
        $data['acarreo'] = $data['acarreo'] != null && FETCH_ACARREO ? true : false;

        return $data;
    }

    private static function sqlViewFetch($referenciaId, $doc)
    {
        $sql =
            "SELECT 
            (SELECT AppID FROM  wapUsuariosPerfiles WHERE ReferenciaID = $referenciaId AND AppID = 19) as legajo,
            (
                SELECT
                TOP 1
                    sol.id as id
                FROM wapUsuarios wu
                    LEFT JOIN wapPersonas per ON per.ReferenciaID = wu.PersonaID
                    LEFT JOIN libretas_usuarios usu ON usu.id_wappersonas = per.ReferenciaID
                    LEFT JOIN libretas_solicitudes sol ON sol.id_usuario_solicitante = usu.id
                WHERE wu.ReferenciaID = $referenciaId ORDER BY id DESC
            ) AS libreta,
            (SELECT insumo FROM licLicencias WHERE Licencia = $doc) as licencia,
            (
            SELECT 
                a.PATENTE as patente
            FROM dbo.wapUsuarios wu
                LEFT JOIN AC_ACARREO a ON a.ID_PERSONA = wu.PersonaID
            WHERE wu.ReferenciaID = $referenciaId and a.BORRADO_LOGICO = 'NO'
            ) as acarreo";

        $model = new BaseModel();
        $result = $model->executeSqlQuery($sql);
        return $result;
    }

    public static function datosLegajo($gender, $doc)
    {
        $sql =
            "SELECT 
                lega as numero, 
                cate as categoria 
            FROM PERSONAL.su.dbo.mae 
            WHERE doc = '0$doc' AND sexo = '$gender'";

        return $sql;
    }

    public static function datosAccareo($id)
    {
        $sql =
            "SELECT
                a.ID_ACARREO as id_acarreo,
                a.PATENTE AS patente,
                m.ID_MOTIVO as id_motivo,
                m.NOMBRE AS motivo,
                p.NOMBRE AS playa,
                p.DESCRIPCION AS direccion,
                a.FECHA_HORA as fecha
            FROM dbo.wapUsuarios wu
                LEFT JOIN AC_ACARREO a ON a.ID_PERSONA = wu.PersonaID 
                LEFT JOIN AC_MOTIVO m ON m.ID_MOTIVO = a.ID_MOTIVO
                LEFT JOIN AC_PLAYA p ON p.ID_PLAYA= a.ID_PLAYA
            WHERE wu.ReferenciaID = $id and a.BORRADO_LOGICO = 'NO'";

        return $sql;
    }

    public static function datosLicConducir($id)
    {
        $sql =
            "SELECT 
                SubClaseID as subclase,
                Categoria as categoria,
                FechaVigencia as venc,
                FechaEmision as emision,
                Domicilio as direccion,
                GrupoSangre as grupo_sangre,
                Donante as donante,
                Insumo as insumo
            FROM dbo.licLicencias 
                WHERE Licencia = $id";

        return $sql;
    }

    public static function datosLibretaSanitaria($id)
    {
        $sql =
            "SELECT TOP 1
                sol.id as id,
                sol.estado as estado,
                sol.nro_recibo as recibo,
                sol.fecha_vencimiento as venc,
                sol.fecha_alta as fecha_alta
            FROM wapUsuarios wu
                LEFT JOIN wapPersonas per ON per.ReferenciaID = wu.PersonaID
                LEFT JOIN libretas_usuarios usu ON usu.id_wappersonas = per.ReferenciaID
                LEFT JOIN libretas_solicitudes sol ON sol.id_usuario_solicitante = usu.id
            WHERE wu.ReferenciaID = $id ORDER BY id DESC";

        return $sql;
    }
}
