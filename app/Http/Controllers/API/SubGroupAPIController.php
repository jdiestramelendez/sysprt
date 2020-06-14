<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSubGroupAPIRequest;
use App\Http\Requests\API\UpdateSubGroupAPIRequest;
use App\Models\SubGroup;
use App\Repositories\SubGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SubGroupController
 * @package App\Http\Controllers\API
 */

class SubGroupAPIController extends AppBaseController
{
    /** @var  SubGroupRepository */
    private $subGroupRepository;

    public function __construct(SubGroupRepository $subGroupRepo)
    {
        $this->subGroupRepository = $subGroupRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/subgroups",
     *      summary="Get a listing of the subgroups.",
     *      tags={"SubGroup"},
     *      description="Get all subgroups",
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
     *                  @SWG\Items(ref="#/definitions/SubGroup")
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
        $this->subGroupRepository->pushCriteria(new RequestCriteria($request));
        $this->subGroupRepository->pushCriteria(new LimitOffsetCriteria($request));
        $subgroups = $this->subGroupRepository->all();

        return $this->sendResponse($subgroups->toArray(), 'Sub Groups retornado com sucesso');
    }

    /**
     * @param CreateSubGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/subgroups",
     *      summary="Store a newly created SubGroup in storage",
     *      tags={"SubGroup"},
     *      description="Store SubGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SubGroup that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SubGroup")
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
     *                  ref="#/definitions/SubGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSubGroupAPIRequest $request)
    {
        $input = $request->all();

        $subgroups = $this->subGroupRepository->create($input);

        return $this->sendResponse($subgroups->toArray(), 'Sub Group criado com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/subgroups/{id}",
     *      summary="Display the specified SubGroup",
     *      tags={"SubGroup"},
     *      description="Get SubGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SubGroup",
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
     *                  ref="#/definitions/SubGroup"
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
        /** @var SubGroup $subGroup */
        $subGroup = $this->subGroupRepository->findWithoutFail($id);

        if (empty($subGroup)) {
            return $this->sendError('Sub Group não encontrado');
        }

        return $this->sendResponse($subGroup->toArray(), 'Sub Group retornado com sucesso');
    }

    /**
     * @param int $id
     * @param UpdateSubGroupAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/subgroups/{id}",
     *      summary="Update the specified SubGroup in storage",
     *      tags={"SubGroup"},
     *      description="Update SubGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SubGroup",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SubGroup that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SubGroup")
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
     *                  ref="#/definitions/SubGroup"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSubGroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var SubGroup $subGroup */
        $subGroup = $this->subGroupRepository->findWithoutFail($id);

        if (empty($subGroup)) {
            return $this->sendError('Sub Group não encontrado');
        }

        $subGroup = $this->subGroupRepository->update($input, $id);

        return $this->sendResponse($subGroup->toArray(), 'SubGroup atualizado com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/subgroups/{id}",
     *      summary="Remove the specified SubGroup from storage",
     *      tags={"SubGroup"},
     *      description="Delete SubGroup",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SubGroup",
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
        /** @var SubGroup $subGroup */
        $subGroup = $this->subGroupRepository->findWithoutFail($id);

        if (empty($subGroup)) {
            return $this->sendError('Sub Group não encontrado');
        }

        $subGroup->delete();

        return $this->sendResponse($id, 'Sub Group removido com sucesso');
    }
}
