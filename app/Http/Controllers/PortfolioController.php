<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $portfolio = config('portfolio');

        $portfolio['profile']['has_photo'] = $this->publicFileExists($portfolio['profile']['photo']);
        $portfolio['profile']['has_cv'] = $this->publicFileExists($portfolio['profile']['cv']);
        $portfolio['seo']['has_share_image'] = $this->publicFileExists($portfolio['seo']['share_image']);
        $portfolio['scene']['has_custom_model'] = $this->publicFileExists($portfolio['scene']['custom_model']);

        $portfolio['projects'] = collect($portfolio['projects'])
            ->map(function (array $project): array {
                $project['has_image'] = $this->publicFileExists($project['image']);

                return $project;
            })
            ->all();

        return view('portfolio', compact('portfolio'));
    }

    private function publicFileExists(?string $path): bool
    {
        return filled($path) && is_file(public_path(ltrim($path, '/')));
    }
}
