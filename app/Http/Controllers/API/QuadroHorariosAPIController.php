<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuadroHorariosAPIRequest;
use App\Http\Requests\API\UpdateQuadroHorariosAPIRequest;
use App\Models\QuadroHorarios;
use App\Repositories\QuadroHorariosRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class QuadroHorariosController
 * @package App\Http\Controllers\API
 */

class QuadroHorariosAPIController extends AppBaseController
{
    /** @var  QuadroHorariosRepository */
    private $quadroHorariosRepository;

    public function __construct(QuadroHorariosRepository $quadroHorariosRepo)
    {
        $this->quadroHorariosRepository = $quadroHorariosRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quadroHorarios",
     *      summary="Get a listing of the QuadroHorarios.",
     *      tags={"QuadroHorarios"},
     *      description="Get all QuadroHorarios",
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
     *                  @SWG\Items(ref="#/definitions/QuadroHorarios")
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
        $this->quadroHorariosRepository->pushCriteria(new RequestCriteria($request));
        $this->quadroHorariosRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quadroHorarios = $this->quadroHorariosRepository->all();

        return $this->sendResponse($quadroHorarios->toArray(), 'Quadro Horarios retrieved successfully');
    }

    /**
     * @param CreateQuadroHorariosAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quadroHorarios",
     *      summary="Store a newly created QuadroHorarios in storage",
     *      tags={"QuadroHorarios"},
     *      description="Store QuadroHorarios",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuadroHorarios that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuadroHorarios")
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
     *                  ref="#/definitions/QuadroHorarios"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuadroHorariosAPIRequest $request)
    {
        $input = $request->all();

        $quadroHorarios = $this->quadroHorariosRepository->create($input);

        return $this->sendResponse($quadroHorarios->toArray(), 'Quadro Horarios saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quadroHorarios/{id}",
     *      summary="Display the specified QuadroHorarios",
     *      tags={"QuadroHorarios"},
     *      description="Get QuadroHorarios",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuadroHorarios",
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
     *                  ref="#/definitions/QuadroHorarios"
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
        /** @var QuadroHorarios $quadroHorarios */
        $quadroHorarios = $this->quadroHorariosRepository->findWithoutFail($id);

        if (empty($quadroHorarios)) {
            return $this->sendError('Quadro Horarios not found');
        }

        return $this->sendResponse($quadroHorarios->toArray(), 'Quadro Horarios retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuadroHorariosAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quadroHorarios/{id}",
     *      summary="Update the specified QuadroHorarios in storage",
     *      tags={"QuadroHorarios"},
     *      description="Update QuadroHorarios",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuadroHorarios",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuadroHorarios that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuadroHorarios")
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
     *                  ref="#/definitions/QuadroHorarios"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuadroHorariosAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuadroHorarios $quadroHorarios */
        $quadroHorarios = $this->quadroHorariosRepository->findWithoutFail($id);

        if (empty($quadroHorarios)) {
            return $this->sendError('Quadro Horarios not found');
        }

        $quadroHorarios = $this->quadroHorariosRepository->update($input, $id);

        return $this->sendResponse($quadroHorarios->toArray(), 'QuadroHorarios updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quadroHorarios/{id}",
     *      summary="Remove the specified QuadroHorarios from storage",
     *      tags={"QuadroHorarios"},
     *      description="Delete QuadroHorarios",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuadroHorarios",
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
        /** @var QuadroHorarios $quadroHorarios */
        $quadroHorarios = $this->quadroHorariosRepository->findWithoutFail($id);

        if (empty($quadroHorarios)) {
            return $this->sendError('Quadro Horarios not found');
        }

        $quadroHorarios->delete();

        return $this->sendResponse($id, 'Quadro Horarios deleted successfully');
    }
}
