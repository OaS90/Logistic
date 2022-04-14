<?php

namespace App\Http\Controllers\Api;

use App\Infrastructure\Repositories\ApplicationRepository;
use Illuminate\Http\Request;

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
               $errors[] = [
                   'id' => $item['id'],
                   'success' => false,
                   'message' => 'Не найден заказ с номером ' . $item['id']
               ];

               $app = $this->repo->getByOrderNumber($item['id']);

               if ($app)
                   $app->update([
                       'dov_ver' => $app->doc_ver + 1
                   ]);
           }
       }

       return response()->json(array_merge($statuses, $errors));
    }
}
