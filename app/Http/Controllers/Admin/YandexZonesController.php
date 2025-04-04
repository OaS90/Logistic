<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Infrastructure\Services\YandexZones\YandexZonesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class YandexZonesController extends Controller
{
    public function index(): View
    {
        return view(backpack_view('yandex.zones'));
    }

    public function prepareImport(YandexZonesService $service, Request $request): Response
    {
        try {
            $fileContent = $request->file('document')->getContent();
            $exportData = json_decode($fileContent, true);
            $result = $service->importFromDelivery($exportData);
        } catch (\Throwable $e) {
            dd($e->getMessage());
            Log::error('Preparing zones for sending to service error ' . $e->getMessage());

            return response()->json(['success' => false], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['success' => true, 'changes' => $result['changes']]);
    }

    public function export(YandexZonesService $service): Response
    {
        try {
            $result = $service->exportFromDelivery();
            $jsonData = json_encode($result);
        } catch (\Throwable $e) {
            Log::error('Exporting zones from service error ' . $e->getMessage());

            return response()->json(['success' => false], Response::HTTP_BAD_REQUEST);
        }

        return response()->stream(function () use ($jsonData) {
            echo $jsonData;
        }, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="data.geojson"',
        ]);
    }

    public function importToService(YandexZonesService $service, Request $request): Response
    {
        try {
            $zonesToImport = $request->get('zonesToImport');
            $changedZones = $request->get('zones');
            $service->importToService($zonesToImport, $changedZones);
        } catch (\Throwable $e) {
            Log::error('Importing zones to service error ' . $e->getMessage());

            return response()->json(['success' => false], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['success' => true]);
    }
}
