<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDashboardsAPIRequest;
use App\Http\Requests\API\UpdateDashboardsAPIRequest;
use App\Models\Dashboards;
use App\Repositories\DashboardsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DashboardsController
 * @package App\Http\Controllers\API
 */

class DashboardsAPIController extends AppBaseController
{
    /** @var  DashboardsRepository */
    private $dashboardsRepository;

    public function __construct(DashboardsRepository $dashboardsRepo)
    {
        $this->dashboardsRepository = $dashboardsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/dashboards",
     *      summary="Get a listing of the Dashboards.",
     *      tags={"Dashboards"},
     *      description="Get all Dashboards",
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
     *                  @SWG\Items(ref="#/definitions/Dashboards")
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
        $this->dashboardsRepository->pushCriteria(new RequestCriteria($request));
        $this->dashboardsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $dashboards = $this->dashboardsRepository->all();

        return $this->sendResponse($dashboards->toArray(), 'Dashboards retornado com sucesso');
    }

    /**
     * @param CreateDashboardsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/dashboards",
     *      summary="Store a newly created Dashboards in storage",
     *      tags={"Dashboards"},
     *      description="Store Dashboards",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Dashboards that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Dashboards")
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
     *                  ref="#/definitions/Dashboards"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDashboardsAPIRequest $request)
    {
        $input = $request->all();

        $dashboards = $this->dashboardsRepository->create($input);

        return $this->sendResponse($dashboards->toArray(), 'Dashboards criado com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/dashboards/{id}",
     *      summary="Display the specified Dashboards",
     *      tags={"Dashboards"},
     *      description="Get Dashboards",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Dashboards",
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
     *                  ref="#/definitions/Dashboards"
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
        /** @var Dashboards $dashboards */
        $dashboards = $this->dashboardsRepository->findWithoutFail($id);

        if (empty($dashboards)) {
            return $this->sendError('Dashboards não encontrado');
        }

        return $this->sendResponse($dashboards->toArray(), 'Dashboards retornado com sucesso');
    }

    /**
     * @param int $id
     * @param UpdateDashboardsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/dashboards/{id}",
     *      summary="Update the specified Dashboards in storage",
     *      tags={"Dashboards"},
     *      description="Update Dashboards",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Dashboards",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Dashboards that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Dashboards")
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
     *                  ref="#/definitions/Dashboards"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDashboardsAPIRequest $request)
    {
        $input = $request->all();

        /** @var Dashboards $dashboards */
        $dashboards = $this->dashboardsRepository->findWithoutFail($id);

        if (empty($dashboards)) {
            return $this->sendError('Dashboards não encontrado');
        }

        $dashboards = $this->dashboardsRepository->update($input, $id);

        return $this->sendResponse($dashboards->toArray(), 'Dashboards atualizado com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/dashboards/{id}",
     *      summary="Remove the specified Dashboards from storage",
     *      tags={"Dashboards"},
     *      description="Delete Dashboards",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Dashboards",
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
        /** @var Dashboards $dashboards */
        $dashboards = $this->dashboardsRepository->findWithoutFail($id);

        if (empty($dashboards)) {
            return $this->sendError('Dashboards não encontrado');
        }

        $dashboards->delete();

        return $this->sendResponse($id, 'Dashboards removido com sucesso');
    }
}
