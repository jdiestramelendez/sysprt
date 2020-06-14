<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDealerAPIRequest;
use App\Http\Requests\API\UpdateDealerAPIRequest;
use App\Models\Dealer;
use App\Repositories\DealerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DealerController
 * @package App\Http\Controllers\API
 */

class DealerAPIController extends AppBaseController
{
    /** @var  DealerRepository */
    private $dealerRepository;

    public function __construct(DealerRepository $dealerRepo)
    {
        $this->dealerRepository = $dealerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/dealers",
     *      summary="Get a listing of the Dealers.",
     *      tags={"Dealer"},
     *      description="Get all Dealers",
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
     *                  @SWG\Items(ref="#/definitions/Dealer")
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
        $this->dealerRepository->pushCriteria(new RequestCriteria($request));
        $this->dealerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $dealers = $this->dealerRepository->all();

        return $this->sendResponse($dealers->toArray(), 'Dealers retrieved successfully');
    }

    /**
     * @param CreateDealerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/dealers",
     *      summary="Store a newly created Dealer in storage",
     *      tags={"Dealer"},
     *      description="Store Dealer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Dealer that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Dealer")
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
     *                  ref="#/definitions/Dealer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDealerAPIRequest $request)
    {
        $input = $request->all();

        $dealers = $this->dealerRepository->create($input);

        return $this->sendResponse($dealers->toArray(), 'Dealer saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/dealers/{id}",
     *      summary="Display the specified Dealer",
     *      tags={"Dealer"},
     *      description="Get Dealer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Dealer",
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
     *                  ref="#/definitions/Dealer"
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
        /** @var Dealer $dealer */
        $dealer = $this->dealerRepository->findWithoutFail($id);

        if (empty($dealer)) {
            return $this->sendError('Dealer not found');
        }

        return $this->sendResponse($dealer->toArray(), 'Dealer retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDealerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/dealers/{id}",
     *      summary="Update the specified Dealer in storage",
     *      tags={"Dealer"},
     *      description="Update Dealer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Dealer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Dealer that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Dealer")
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
     *                  ref="#/definitions/Dealer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDealerAPIRequest $request)
    {
        $input = $request->all();

        /** @var Dealer $dealer */
        $dealer = $this->dealerRepository->findWithoutFail($id);

        if (empty($dealer)) {
            return $this->sendError('Dealer not found');
        }

        $dealer = $this->dealerRepository->update($input, $id);

        return $this->sendResponse($dealer->toArray(), 'Dealer updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/dealers/{id}",
     *      summary="Remove the specified Dealer from storage",
     *      tags={"Dealer"},
     *      description="Delete Dealer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Dealer",
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
        /** @var Dealer $dealer */
        $dealer = $this->dealerRepository->findWithoutFail($id);

        if (empty($dealer)) {
            return $this->sendError('Dealer not found');
        }

        $dealer->delete();

        return $this->sendResponse($id, 'Dealer deleted successfully');
    }
}
