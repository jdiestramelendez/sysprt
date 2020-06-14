<?php

namespace App\Repositories;

use App\Models\Linhas;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LinhasRepository
 * @package App\Repositories
 * @version November 13, 2018, 11:23 am UTC
 *
 * @method Linhas findWithoutFail($id, $columns = ['*'])
 * @method Linhas find($id, $columns = ['*'])
 * @method Linhas first($columns = ['*'])
*/
class LinhasRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
      'codigo',
      'nome_fantasia',
      'nome'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Linhas::class;
    }

    public function findByGrupo($grupoId)
    {
        return $this->model->where('idgrupolinha', $grupoId);
    }
}
