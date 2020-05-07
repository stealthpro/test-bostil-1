<?php

namespace App\Http\Controllers\Api;

use App\Filters\Filters;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageCreateRequest;
use App\Http\Requests\PageUpdateRequest;
use App\Http\Resources\PageListResource;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/pages",
     *     tags={"Pages"},
     *     description="Список страниц",
     *     @OA\Parameter(name="page", required=false, in="query", @OA\Schema(type="integer"), description="Страница"),
     *     @OA\Parameter(name="per_page", required=false, in="query", @OA\Schema(type="integer"), description="Кол-во элементов на страницу."),
     *     @OA\Parameter(name="sort", required=false, in="query", @OA\Schema(type="string"), description="Сортировать по возрастанию/убыванию. Варианты: desc, asc."),
     *     @OA\Response(response="200",
     *         description="Список",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PageListResource")),
     *             @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
     *             @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta"),
     *         ),
     *     ),
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @param  \App\Filters\Filters  $filters
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, Filters $filters)
    {
        $query = Page::query();

        $filters->setQuery($query)
            ->orderBy('title', $request->get('sort'));

        $perPage = $request->get('per_page') ?? 10;

        return PageListResource::collection($query->paginate($perPage));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/pages",
     *     tags={"Pages"},
     *     description="Создание записи",
     *     @OA\RequestBody(
     *         description="Входные параметры",
     *         @OA\JsonContent(ref="#/components/schemas/PageCreateRequest"),
     *     ),
     *     @OA\Response(response="201",
     *         description="Запись успешно создана",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/PageResource"),
     *         ),
     *     ),
     * )
     *
     * @param  \App\Http\Requests\PageCreateRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function store(PageCreateRequest $request)
    {
        $page = Page::query()->create($request->validated());

        return (new PageResource($page))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/pages/{page_id}",
     *     tags={"Pages"},
     *     description="Просмотр записи",
     *     @OA\Parameter(name="page_id", in="path", @OA\Schema(type="integer"), description="ID записи"),
     *     @OA\Response(response="200",
     *         description="Запись",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/PageResource"),
     *         ),
     *     ),
     * )
     *
     * @param  \App\Models\Page  $page
     *
     * @return \App\Http\Resources\PageResource
     */
    public function show(Page $page)
    {
        return new PageResource($page);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/pages/{page_id}",
     *     tags={"Pages"},
     *     description="Изменение записи",
     *     @OA\Parameter(name="page_id", in="path", @OA\Schema(type="integer"), description="ID записи"),
     *     @OA\RequestBody(
     *         description="Входные параметры",
     *         @OA\JsonContent(ref="#/components/schemas/PageUpdateRequest"),
     *     ),
     *     @OA\Response(response="200", description="Запись успешно обновлена",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/PageResource"),
     *         ),
     *     ),
     * )
     *
     * @param  \App\Http\Requests\PageUpdateRequest  $request
     * @param  \App\Models\Page  $page
     * @param  \App\Services\PageService  $service
     *
     * @return \App\Http\Resources\PageResource
     */
    public function update(
        PageUpdateRequest $request,
        Page $page,
        PageService $service
    ) {
        $service->update($page, $request);

        return new PageResource($page);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/pages/{page_id}",
     *     tags={"Pages"},
     *     description="Удаление записи",
     *     @OA\Parameter(name="page_id", in="path", @OA\Schema(type="integer"), description="ID записи"),
     *     @OA\Response(response="204", description="Запись успешно удалена"),
     * )
     *
     * @param  \App\Models\Page  $page
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/pages/{page_id}/publish",
     *     tags={"Pages"},
     *     description="Публикация записи",
     *     @OA\Parameter(name="page_id", in="path", @OA\Schema(type="integer"), description="ID записи"),
     *     @OA\Response(response="200", description="Запись успешно опубликована",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/PageResource"),
     *         ),
     *     ),
     * )
     *
     * @param $id
     * @param  \App\Services\PageService  $service
     *
     * @return \App\Http\Resources\PageResource
     * @throws \Throwable
     */
    public function publish($id, PageService $service)
    {
        $page = Page::query()->notPublished()->findOrFail($id);

        $service->publish($page);

        return new PageResource($page);
    }
}
