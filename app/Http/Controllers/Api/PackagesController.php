<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Package as PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PackagesController extends Controller
{
    public function __invoke(Request $request)
    {
        $githubUsername = $request->input('github_username');
        $authorName = $request->input('author_name');

        Log::info('API: /packages');

        return PackageResource::collection(Package::orderBy('created_at', 'desc')
            ->when($githubUsername, function ($query) use ($githubUsername) {
                $query->whereHas('author', function ($query) use ($githubUsername) {
                    $query->where('github_username', $githubUsername);
                });
            })
            ->when($authorName, function ($query) use ($authorName) {
                $query->whereHas('author', function ($query) use ($authorName) {
                    $query->where('name', $authorName);
                });
            })
            ->with(['author', 'tags'])->paginate(10));
    }
}
