<?php

namespace App\Http\Controllers\Api\Version1;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\User;
use Illuminate\Http\Request;

final class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return UserResource::collection(User::with('companies')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);
        $user = new User($request->all());
        $user->saveOrFail();
        if ($request->exists('companies')) {
            $user->companies()->sync($request->input('companies'));
        }
        return response()->json(new UserResource($user), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return UserResource
     */
    public function show($id)
    {
        $user = User::with('companies')->findOrFail($id);
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return UserResource
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($request);
        $user = User::with('companies')->findOrFail($id);
        $user->fill($request->all())->saveOrFail();
        if ($request->exists('companies')) {
            $user->companies()->sync($request->input('companies'));
        }
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return UserResource
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->delete();
        return new UserResource($user);
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
                'companies' => 'array|exists:companies,id',
            ]
        );
    }
}
