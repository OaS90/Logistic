<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Exceptions\JsonParseException;
use App\Infrastructure\Repositories\ApplicationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Exceptions\StatusUpdateException;

class ApplicationController
{
    protected $repo;

    public function __construct(ApplicationRepository $repository)
    {
        $this->repo = $repository;
    }

    public function setStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $errors = [];
        $statuses = [];

       $data = json_decode($request->getContent(), true);

       if (!$data || !is_array($data)) {
           $exception = new JsonParseException('Ошибка формата json');

           return response()->json(['message' => $exception->getMessage(), 'success' => false], 500);
       }

       foreach ($data as $item) {
           $newStatus = last($item['statuses'])['status'];

           try {
               $this->repo->updateStatus($item['id'], $newStatus);
               $statuses[] = [
                   'id' => $item['id'],
                   'success' => true,
                   'message' => ""
               ];
           } catch (\Throwable $e) {
               $exception = new StatusUpdateException();
               $errors[] = $exception->getError($e, $item['id']);
               $app = $this->repo->getByOrderNumber($item['id']);

               if ($app)
                   $app->update(['doc_ver' => $app->doc_ver + 1]);
           }
       }

       return response()->json(array_merge($statuses, $errors));
    }
}
