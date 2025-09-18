<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Enum\ZoneType;
use App\Http\Controllers\Api\Exceptions\DeletedZoneImportException;
use App\Http\Controllers\Api\Exceptions\NewZoneImportException;
use App\Http\Controllers\Controller;
use App\Http\Exceptions\Admin\PolygonWithoutDescriptionException;
use App\Infrastructure\Repositories\Admin\ZoneRepository;
use App\Infrastructure\Repositories\Hru\FilialRepository;
use App\Infrastructure\Repositories\RegionRepository;
use App\Infrastructure\Services\YandexZones\YandexZonesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Response as FResponse;

class YandexZonesController extends Controller
{
    public function index(RegionRepository $regionRepo,
                          FilialRepository $filialRepo,
                          ZoneRepository $zoneRepo
    ): View
    {
        $regions = $regionRepo->getAll();
        $polygonTypes = ZoneType::ALL;
        $zones = $zoneRepo->getAll()->pluck('code');
        $filials = $filialRepo->getAll();
        $exportsHistory = array_map('basename', Storage::files('history'));

        return view(backpack_view('yandex.zones'), [
            'regions' => $regions,
            'polygon_types' => $polygonTypes,
            'filials' => $filials,
            'zones' => $zones,
            'exports_history_files' => array_reverse($exportsHistory)
        ]);
    }

    public function prepareImport(YandexZonesService $service, Request $request): Response
    {
        try {
            $fileContent = $request->file('document')->getContent();
            $exportData = json_decode($fileContent, true);
            $result = $service->handleBeforeImportToService($exportData);
        } catch (\Throwable $e) {
            dd($e->getTraceAsString());
            Log::error('Preparing zones for sending to service error ' . $e->getMessage());
            $message = '';

            if ($e instanceof PolygonWithoutDescriptionException) {
                $message = $e->getMessage();
            }

            return response()->json(['success' => false, 'message' => $message], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'success' => true,
            'changes' => $result['changes'],
            'new_polygons' => $result['newPolygons'],
            'deleted_polygons' => $result['deletedPolygons'],
        ]);
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
            $allZones = $request->get('zones');
            $result = $service->importToService($zonesToImport, $allZones);
        } catch (NewZoneImportException $e) {
            return $e->render();
        } catch (DeletedZoneImportException $e) {
            return $e->render();
        } catch (\Throwable $e) {
            dd($e->getMessage());
            Log::error('Importing zones to service error ' . $e->getMessage());

            return response()->json(['success' => false], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(['success' => true, 'result' => $result]);
    }

    public function downloadExportFile(string $fileName): Response|BinaryFileResponse
    {
        $isFileExists = Storage::exists('history/' . $fileName . '.json');

        if ($isFileExists) {
            return FResponse::download(storage_path('app/public/history/' . $fileName . '.json'));
        }
        return Storage::download('history/' . $fileName);
    }
}
