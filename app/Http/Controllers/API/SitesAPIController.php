<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSitesAPIRequest;
use App\Http\Requests\API\UpdateSitesAPIRequest;
use App\Models\Sites;
use App\Repositories\SitesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SitesController
 * @package App\Http\Controllers\API
 */

class SitesAPIController extends AppBaseController
{
    /** @var  SitesRepository */
    private $sitesRepository;

    public function __construct(SitesRepository $sitesRepo)
    {
        $this->sitesRepository = $sitesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sites",
     *      summary="Get a listing of the Sites.",
     *      tags={"Sites"},
     *      description="Get all Sites",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Sites")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->sitesRepository->pushCriteria(new RequestCriteria($request));
        $this->sitesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sites = $this->sitesRepository->all();

        return $this->sendResponse($sites->toArray(), 'Garagens retornadas com sucesso');
    }

    /**
     * @param CreateSitesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sites",
     *      summary="Store a newly created Sites in storage",
     *      tags={"Sites"},
     *      description="Store Sites",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Sites that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Sites")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Sites"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSitesAPIRequest $request)
    {
        $input = $request->all();

        $sites = $this->sitesRepository->create($input);

        return $this->sendResponse($sites->toArray(), 'Garagem criada com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sites/{id}",
     *      summary="Display the specified Sites",
     *      tags={"Sites"},
     *      description="Get Sites",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Sites",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Sites"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var Sites $sites */
        $sites = $this->sitesRepository->findWithoutFail($id);

        if (empty($sites)) {
            return $this->sendError('Garagem não encontrada');
        }

        return $this->sendResponse($sites->toArray(), 'Garagem retornada com sucesso');
    }

    /**
     * @param int $id
     * @param UpdateSitesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sites/{id}",
     *      summary="Update the specified Sites in storage",
     *      tags={"Sites"},
     *      description="Update Sites",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Sites",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Sites that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Sites")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Sites"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSitesAPIRequest $request)
    {
        $input = $request->all();

        /** @var Sites $sites */
        $sites = $this->sitesRepository->findWithoutFail($id);

        if (empty($sites)) {
            return $this->sendError('Garagem não encontrada');
        }

        $sites = $this->sitesRepository->update($input, $id);

        return $this->sendResponse($sites->toArray(), 'Garagem atualizada com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sites/{id}",
     *      summary="Remove the specified Sites from storage",
     *      tags={"Sites"},
     *      description="Delete Sites",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Sites",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var Sites $sites */
        $sites = $this->sitesRepository->findWithoutFail($id);

        if (empty($sites)) {
            return $this->sendError('Garagem não encontrada');
        }

        $sites->delete();

        return $this->sendResponse($id, 'Garagem removida com sucesso');
    }
}
