<?php

namespace App\Models\Adopciones;

use App\Models\BaseModel;

class Adop_Animal extends BaseModel
{
    protected $table = 'adop_animales';
    protected $logPath = 'v1/adopciones';
    protected $identity = 'id';

    protected $fillable = [
        'id_solicitud',
        'id_tipo_documento',
        'documento',
        'verificado',
    ];

    // public $filesUrl = FILE_PATH . 'licencia_comercial/solicitud/';

    public function saveInitDocuments($idSolicitud, $solicitud)
    {
        /* Impuesto Municipal */
        $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 1, 'verificado' => 0];
        $this->set($params);
        $this->save();

        if ($solicitud['pertenece'] == 'tercero') {
            /* Poder */
            $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 2, 'verificado' => 0];
            $this->set($params);
            $this->save();

            /* DNI por el PODER */
            $params = ['id_solicitud' => $idSolicitud, 'id_tipo_documento' => 3, 'verificado' => 0];
            $this->set($params);
            $this->save();
        }

        if ($solicitud['tipo_persona'] == 'fisica') {
            /* AFIP */
            $params['id_tipo_documento'] = 4;
            $this->set($params);
            $this->save();

            /* AFIP */
            $params['id_tipo_documento'] = 5;
            $this->set($params);
            $this->save();

            /* AFIP */
            $params['id_tipo_documento'] = 6;
            $this->set($params);
            $this->save();
        }

        if ($solicitud['tipo_persona'] == 'juridica') {
            /* AFIP */
            $params['id_tipo_documento'] = 7;
            $this->set($params);
            $this->save();

            /* AFIP */
            $params['id_tipo_documento'] = 8;
            $this->set($params);
            $this->save();

            /* AFIP */
            $params['id_tipo_documento'] = 9;
            $this->set($params);
            $this->save();

            /* AFIP */
            $params['id_tipo_documento'] = 10;
            $this->set($params);
            $this->save();
        }
    }
}
