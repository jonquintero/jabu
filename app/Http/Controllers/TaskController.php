<?php

namespace App\Http\Controllers;

use App\Action\UpsertTaskAction;
use App\DataTransferObjects\TaskData;
use App\Helpers\SearchTaskHelper;
use App\Http\Requests\UpsertTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Frequency;
use App\Models\Task;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response;


class TaskController extends Controller
{
    use SearchTaskHelper;
    public function __construct(private readonly UpsertTaskAction $upsertTaskAction)
    {
    }


    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $response = $this->search(Task::with('frequencies', 'user')->filter(Request::only('search'))->get());
        return TaskResource::collection($response);
    }


    /**
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
           return response()->json(
               ['frequencies' => $this->getFrequencies(),
            ]);

    }

    /**
     * @param Task $task
     * @return TaskResource
     */
    public function show (Task $task): TaskResource
    {
        return TaskResource::make($task->load(['frequencies', 'user']));
    }

    /**
     * @param UpsertTaskRequest $request
     * @return JsonResponse
     */
    public function store(UpsertTaskRequest $request):JsonResponse
    {
       return TaskResource::make($this->upsert($request, new Task()))
           ->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param Task $task
     * @return Response
     */
    public function edit(Task $task): Response
    {
        return response()->json([
            'task' => TaskResource::make($task->load(['frequencies', 'user'])),
            'frequencies' => $this->getFrequencies()
            ], Response::HTTP_OK);

    }

    /**
     * @param UpsertTaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpsertTaskRequest $request, Task $task)
    {
        $this->upsert($request, $task);

        return response()->noContent();
    }

    /**
     * @param Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->noContent();
    }



    /**
     * @param UpsertTaskRequest $request
     * @param Task $task
     * @return Task
     */
    public function upsert(UpsertTaskRequest $request, Task $task)
    {
        $taskData = TaskData::fromRequest($request);

        return  $this->upsertTaskAction->execute($taskData, $task);
    }

    /**
     * @return mixed
     */
    public function getFrequencies()
    {
        return  Frequency::orderBy('id')
            ->get()
            ->map
            ->only('id', 'name');
    }

}
