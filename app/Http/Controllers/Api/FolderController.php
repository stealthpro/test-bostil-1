<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FolderRequest;
use App\Http\Resources\FolderResource;
use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/folders",
     *     tags={"Folders"},
     *     description="Список папок",
     *     @OA\Parameter(name="page", required=false, in="query", @OA\Schema(type="integer"), description="Страница"),
     *     @OA\Parameter(name="per_page", required=false, in="query", @OA\Schema(type="integer"), description="Кол-во элементов на страницу."),
     *     @OA\Parameter(name="sort", required=false, in="query", @OA\Schema(type="string"), description="Сортировать по возрастанию/убыванию. Варианты: desc, asc."),
     *     @OA\Response(response="200",
     *         description="Список",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/FolderResource")),
     *             @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
     *         ),
     *     ),
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Folder::query();

        if ($request->get('sort') === 'desc') {
            $query->orderByDesc('title');
        } else {
            $query->orderBy('title');
        }

        $perPage = $request->get('per_page') ?? 10;

        return FolderResource::collection($query->paginate($perPage));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/folders",
     *     tags={"Folders"},
     *     description="Создание записи",
     *     @OA\RequestBody(
     *         description="Входные параметры",
     *         @OA\JsonContent(ref="#/components/schemas/FolderRequest"),
     *     ),
     *     @OA\Response(response="201",
     *         description="Запись успешно создана",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/FolderResource"),
     *         ),
     *     ),
     * )
     *
     * @param  \App\Http\Requests\FolderRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function store(FolderRequest $request)
    {
        $folder = Folder::query()->create($request->validated());

        return (new FolderResource($folder))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/folders/{folder_id}",
     *     tags={"Folders"},
     *     description="Просмотр записи",
     *     @OA\Parameter(name="folder_id", in="path", @OA\Schema(type="integer"), description="ID записи"),
     *     @OA\Response(response="200",
     *         description="Запись",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/FolderResource"),
     *         ),
     *     ),
     * )
     *
     * @param  \App\Models\Folder  $folder
     *
     * @return \App\Http\Resources\FolderResource
     */
    public function show(Folder $folder)
    {
        return new FolderResource($folder);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/folders/{folder_id}",
     *     tags={"Folders"},
     *     description="Изменение записи",
     *     @OA\Parameter(name="folder_id", in="path", @OA\Schema(type="integer"), description="ID записи"),
     *     @OA\RequestBody(
     *         description="Входные параметры",
     *         @OA\JsonContent(ref="#/components/schemas/FolderRequest"),
     *     ),
     *     @OA\Response(response="200", description="Запись успешно обновлена",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/FolderResource"),
     *         ),
     *     ),
     * )
     *
     * @param  \App\Http\Requests\FolderRequest  $request
     * @param  \App\Models\Folder  $folder
     *
     * @return \App\Http\Resources\FolderResource
     */
    public function update(FolderRequest $request, Folder $folder)
    {
        $folder->update($request->validated());

        return new FolderResource($folder);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/folders/{folder_id}",
     *     tags={"Folders"},
     *     description="Удаление записи",
     *     @OA\Parameter(name="folder_id", in="path", @OA\Schema(type="integer"), description="ID записи"),
     *     @OA\Response(response="204", description="Запись успешно удалена"),
     * )
     *
     * @param  \App\Models\Folder  $folder
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Folder $folder)
    {
        $folder->delete();

        return response()->json(null, 204);
    }
}
