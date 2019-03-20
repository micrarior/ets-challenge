<?php

namespace App\Http\Controllers\Api\Version1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company as CompanyResource;
use App\Company;
use Illuminate\Http\Request;

/**
 * Class CompanyController
 * @package App\Http\Controllers\Api\Version1
 *
 * @group Company management
 *
 * APIs for managing companies
 */
final class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CompanyResource::collection(Company::with('users')->paginate());
    }

    /**
     * Store a newly created company.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     *
     * @bodyParam name string required
     * @bodyParam users array
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);
        $company = new Company($request->all());
        $company->saveOrFail();
        if ($request->exists('users')) {
            $company->users()->sync($request->input('users'));
        }
        return response()->json(new CompanyResource($company), 201);
    }

    /**
     * Display the specified company.
     *
     * @param  int  $id
     * @return CompanyResource
     */
    public function show($id)
    {
        $company = Company::with('users')->findOrFail($id);
        return new CompanyResource($company);
    }

    /**
     * Update the specified company in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return CompanyResource
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     *
     * @bodyParam name string required
     * @bodyParam users array
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($request);
        $company = Company::with('users')->findOrFail($id);
        $company->fill($request->all())->saveOrFail();
        if ($request->exists('users')) {
            $company->users()->sync($request->input('users'));
        }
        return new CompanyResource($company);
    }

    /**
     * Remove the specified company from storage.
     *
     * @param  int $id
     * @return CompanyResource
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Company $company */
        $company = Company::query()->findOrFail($id);
        $company->delete();
        return new CompanyResource($company);
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRequest(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required|min:1',
                'users' => 'array|exists:users,id',
            ]
        );
    }
}
