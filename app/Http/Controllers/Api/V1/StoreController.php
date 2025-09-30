<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Http\Resources\StoreResource;
use Illuminate\Support\Str;
use App\Http\Requests\Store\StoreCreateRequest;
use App\Http\Requests\Store\StoreUpdateRequest;

/**
 * @see \\App\\OpenApi\\Documentation\\StoreEndpoints
 */
class StoreController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $stores = Store::query();
        if ($request->has('include') && in_array('config', explode(',', $request->input('include')))) {
            $stores->with('config');
        }
        return $this->successResponse(StoreResource::collection($stores->get()));
    }

    public function store(StoreCreateRequest $request)
    {
        $data = $request->validated();
        $store = Store::create([
            'id' => Str::uuid(),
            'name' => $data['name'],
        ]);
        if (isset($data['config'])) {
            $configData = $data['config'];
            $store->config()->create([
                'products' => $configData['products'] ?? true,
                'categories' => $configData['categories'] ?? true,
                'cupons' => $configData['cupons'] ?? false,
                'gifcards' => $configData['gifcards'] ?? false,
                'wishlist' => $configData['wishlist'] ?? false,
                'reviews' => $configData['reviews'] ?? false,
                'notifications_emails' => $configData['notifications']['emails'] ?? true,
                'notifications_telegram' => $configData['notifications']['telegram'] ?? false,
            ]);
        }
        $store->load('config');
        return $this->successResponse(new StoreResource($store), 201);
    }

    public function show(Request $request, $id)
    {
        $store = Store::query();
        if ($request->has('include') && in_array('config', explode(',', $request->input('include')))) {
            $store->with('config');
        }
        $store = $store->findOrFail($id);
        return $this->successResponse(new StoreResource($store));
    }

    public function update(StoreUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $store = Store::findOrFail($id);
        if (isset($data['name'])) {
            $store->name = $data['name'];
            $store->save();
        }
        if (isset($data['config'])) {
            $configData = $data['config'];
            $config = $store->config;
            if ($config) {
                $config->update([
                    'products' => $configData['products'] ?? $config->products,
                    'categories' => $configData['categories'] ?? $config->categories,
                    'cupons' => $configData['cupons'] ?? $config->cupons,
                    'gifcards' => $configData['gifcards'] ?? $config->gifcards,
                    'wishlist' => $configData['wishlist'] ?? $config->wishlist,
                    'reviews' => $configData['reviews'] ?? $config->reviews,
                    'notifications_emails' => $configData['notifications']['emails'] ?? $config->notifications_emails,
                    'notifications_telegram' => $configData['notifications']['telegram'] ?? $config->notifications_telegram,
                ]);
            } else {
                $store->config()->create([
                    'products' => $configData['products'] ?? true,
                    'categories' => $configData['categories'] ?? true,
                    'cupons' => $configData['cupons'] ?? false,
                    'gifcards' => $configData['gifcards'] ?? false,
                    'wishlist' => $configData['wishlist'] ?? false,
                    'reviews' => $configData['reviews'] ?? false,
                    'notifications_emails' => $configData['notifications']['emails'] ?? true,
                    'notifications_telegram' => $configData['notifications']['telegram'] ?? false,
                ]);
            }
        }
        $store->load('config');
        return $this->successResponse(new StoreResource($store));
    }

    public function destroy($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();

        return $this->successResponse(null, 200, 'Store deleted successfully');
    }
}
