<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetsAPIRequest;
use App\Http\Requests\API\UpdateAssetsAPIRequest;
use App\Models\Assets;
use App\Repositories\AssetsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetsController
 * @package App\Http\Controllers\API
 */

class AssetsAPIController extends AppBaseController
{
    /** @var  AssetsRepository */
    private $assetsRepository;

    public function __construct(AssetsRepository $assetsRepo)
    {
        $this->assetsRepository = $assetsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assets",
     *      summary="Get a listing of the Assets.",
     *      tags={"Assets"},
     *      description="Get all Assets",
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
     *                  @SWG\Items(ref="#/definitions/Assets")
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
        $this->assetsRepository->pushCriteria(new RequestCriteria($request));
        $this->assetsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assets = $this->assetsRepository->all();

        return $this->sendResponse($assets->toArray(), 'Veículos retornados com sucesso');
    }

    /**
     * @param CreateAssetsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assets",
     *      summary="Store a newly created Assets in storage",
     *      tags={"Assets"},
     *      description="Store Assets",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Assets that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Assets")
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
     *                  ref="#/definitions/Assets"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetsAPIRequest $request)
    {
        $input = $request->all();

        $assets = $this->assetsRepository->create($input);

        return $this->sendResponse($assets->toArray(), 'Veículo criado com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assets/{id}",
     *      summary="Display the specified Assets",
     *      tags={"Assets"},
     *      description="Get Assets",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Assets",
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
     *                  ref="#/definitions/Assets"
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
        /** @var Assets $assets */
        $assets = $this->assetsRepository->findWithoutFail($id);

        if (empty($assets)) {
            return $this->sendError('Veículo não encontrado');
        }

        return $this->sendResponse($assets->toArray(), 'Veículo retornado com sucesso');
    }

    /**
     * @param int $id
     * @param UpdateAssetsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assets/{id}",
     *      summary="Update the specified Assets in storage",
     *      tags={"Assets"},
     *      description="Update Assets",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Assets",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Assets that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Assets")
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
     *                  ref="#/definitions/Assets"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetsAPIRequest $request)
    {
        $input = $request->all();

        /** @var Assets $assets */
        $assets = $this->assetsRepository->findWithoutFail($id);

        if (empty($assets)) {
            return $this->sendError('Veículo não encontrado');
        }

        $assets = $this->assetsRepository->update($input, $id);

        return $this->sendResponse($assets->toArray(), 'Veículo atualizado com sucesso');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assets/{id}",
     *      summary="Remove the specified Assets from storage",
     *      tags={"Assets"},
     *      description="Delete Assets",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Assets",
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
        /** @var Assets $assets */
        $assets = $this->assetsRepository->findWithoutFail($id);

        if (empty($assets)) {
            return $this->sendError('Veículo não encontrado');
        }

        $assets->delete();

        return $this->sendResponse($id, 'Veículo removido com sucesso');
    }
}
