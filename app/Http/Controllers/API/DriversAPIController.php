<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDriversAPIRequest;
use App\Http\Requests\API\UpdateDriversAPIRequest;
use App\Models\Drivers;
use App\Repositories\DriversRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DriversController
 * @package App\Http\Controllers\API
 */

class DriversAPIController extends AppBaseController
{
    /** @var  DriversRepository */
    private $driversRepository;

    public function __construct(DriversRepository $driversRepo)
    {
        $this->driversRepository = $driversRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/drivers",
     *      summary="Get a listing of the Drivers.",
     *      tags={"Drivers"},
     *      description="Get all Drivers",
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
     *                  @SWG\Items(ref="#/definitions/Drivers")
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
        $this->driversRepository->pushCriteria(new RequestCriteria($request));
        $this->driversRepository->pushCriteria(new LimitOffsetCriteria($request));
        $drivers = $this->driversRepository->all();

        return $this->sendResponse($drivers->toArray(), 'Motoristas retornados com sucesso');
    }

    /**
     * @param CreateDriversAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/drivers",
     *      summary="Store a newly created Drivers in storage",
     *      tags={"Drivers"},
     *      description="Store Drivers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Drivers that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Drivers")
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
     *                  ref="#/definitions/Drivers"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDriversAPIRequest $request)
    {
        $input = $request->all();

        $drivers = $this->driversRepository->create($input);

        return $this->sendResponse($drivers->toArray(), 'Motorista criado com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/drivers/{id}",
     *      summary="Display the specified Drivers",
     *      tags={"Drivers"},
     *      description="Get Drivers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Drivers",
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
     *                  ref="#/definitions/Drivers"
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
        /** @var Drivers $drivers */
        $drivers = $this->driversRepository->findWithoutFail($id);

        if (empty($drivers)) {
            return $this->sendError('Motorista não encontrado');
        }

        return $this->sendResponse($drivers->toArray(), 'Motorista retornado com sucesso');
    }

    /**
     * @param int $id
     * @param UpdateDriversAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/drivers/{id}",
     *      summary="Update the specified Drivers in storage",
     *      tags={"Drivers"},
     *      description="Update Drivers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Drivers",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Drivers that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Drivers")
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
     *                  ref="#/definitions/Drivers"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDriversAPIRequest $request)
    {
        $input = $request->all();

        /** @var Drivers $drivers */
        $drivers = $this->driversRepository->findWithoutFail($id);

        if (empty($drivers)) {
            return $this->sendError('Motorista não encontrado');
        }

        $drivers = $this->driversRepository->update($input, $id);

        return $this->sendResponse($drivers->toArray(), 'Motorista atualizado com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/drivers/{id}",
     *      summary="Remove the specified Drivers from storage",
     *      tags={"Drivers"},
     *      description="Delete Drivers",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Drivers",
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
        /** @var Drivers $drivers */
        $drivers = $this->driversRepository->findWithoutFail($id);

        if (empty($drivers)) {
            return $this->sendError('Motorista não encontrado');
        }

        $drivers->delete();

        return $this->sendResponse($id, 'Motorista removido com sucesso');
    }
}
