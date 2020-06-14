<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEscalasAPIRequest;
use App\Http\Requests\API\UpdateEscalasAPIRequest;
use App\Models\Escalas;
use App\Repositories\EscalasRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EscalasController
 * @package App\Http\Controllers\API
 */

class EscalasAPIController extends AppBaseController
{
    /** @var  EscalasRepository */
    private $escalasRepository;

    public function __construct(EscalasRepository $escalasRepo)
    {
        $this->escalasRepository = $escalasRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/escalas",
     *      summary="Get a listing of the Escalas.",
     *      tags={"Escalas"},
     *      description="Get all Escalas",
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
     *                  @SWG\Items(ref="#/definitions/Escalas")
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
        $this->escalasRepository->pushCriteria(new RequestCriteria($request));
        $this->escalasRepository->pushCriteria(new LimitOffsetCriteria($request));
        $escalas = $this->escalasRepository->all();

        return $this->sendResponse($escalas->toArray(), 'Escalas retrieved successfully');
    }

    /**
     * @param CreateEscalasAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/escalas",
     *      summary="Store a newly created Escalas in storage",
     *      tags={"Escalas"},
     *      description="Store Escalas",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Escalas that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Escalas")
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
     *                  ref="#/definitions/Escalas"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEscalasAPIRequest $request)
    {
        $input = $request->all();

        $escalas = $this->escalasRepository->create($input);

        return $this->sendResponse($escalas->toArray(), 'Escalas saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/escalas/{id}",
     *      summary="Display the specified Escalas",
     *      tags={"Escalas"},
     *      description="Get Escalas",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Escalas",
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
     *                  ref="#/definitions/Escalas"
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
        /** @var Escalas $escalas */
        $escalas = $this->escalasRepository->findWithoutFail($id);

        if (empty($escalas)) {
            return $this->sendError('Escalas not found');
        }

        return $this->sendResponse($escalas->toArray(), 'Escalas retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateEscalasAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/escalas/{id}",
     *      summary="Update the specified Escalas in storage",
     *      tags={"Escalas"},
     *      description="Update Escalas",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Escalas",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Escalas that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Escalas")
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
     *                  ref="#/definitions/Escalas"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEscalasAPIRequest $request)
    {
        $input = $request->all();

        /** @var Escalas $escalas */
        $escalas = $this->escalasRepository->findWithoutFail($id);

        if (empty($escalas)) {
            return $this->sendError('Escalas not found');
        }

        $escalas = $this->escalasRepository->update($input, $id);

        return $this->sendResponse($escalas->toArray(), 'Escalas updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/escalas/{id}",
     *      summary="Remove the specified Escalas from storage",
     *      tags={"Escalas"},
     *      description="Delete Escalas",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Escalas",
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
        /** @var Escalas $escalas */
        $escalas = $this->escalasRepository->findWithoutFail($id);

        if (empty($escalas)) {
            return $this->sendError('Escalas not found');
        }

        $escalas->delete();

        return $this->sendResponse($id, 'Escalas deleted successfully');
    }
}
