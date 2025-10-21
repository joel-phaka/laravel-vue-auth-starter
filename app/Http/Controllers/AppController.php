<?php

namespace App\Http\Controllers;

use App\Models\OAuthProvider;
use App\Support\Feature;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    private function getAppConfigData(): array
    {
        $features = Feature::getVisible();

        $settings = [
            'app_name' => config('app.name') . '',
            'oauth_providers' => !empty($features['social_login']) ? OAuthProvider::getActiveProviders() : [],
        ];

        $features['social_login'] = !empty($settings['oauth_providers']);

        return compact('settings', 'features');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index(): Renderable
    {
        return view('app.index')
            ->with(['appConfig' => $this->getAppConfigData()]);
    }

    public function getAppConfig(): JsonResponse
    {
        return response()->json($this->getAppConfigData());
    }
}
