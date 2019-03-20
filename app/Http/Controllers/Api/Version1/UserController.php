<?php

namespace App\Http\Controllers\Api\Version1;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\User;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers\Api\Version1
 *
 * @group User management
 *
 * APIs for managing users
 */
final class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @responseFile responses/users.index.json
     */
    public function index()
    {
        return UserResource::collection(User::with('companies')->paginate(1));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     *
     * @bodyParam name string required
     * @bodyParam companies array
     * @responseFile responses/users.show.json
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
     * Display the specified user.
     *
     * @param  int  $id
     * @return UserResource
     * @responseFile responses/users.show.json
     */
    public function show($id)
    {
        $user = User::with('companies')->findOrFail($id);
        return new UserResource($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return UserResource
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     *
     * @bodyParam name string required
     * @bodyParam companies array
     * @responseFile responses/users.show.json
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
     * @responseFile responses/users.show.json
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
