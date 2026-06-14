<?php

namespace App\Http\Controllers;

use App\Modules\Compliance\Services\LegalDocumentRegistry;
use App\Modules\Compliance\Services\LegalDocumentResolver;
use App\Modules\Localization\Services\LocaleResolver;
use App\Modules\Navigation\Services\NavigationResolver;
use App\Modules\Theme\Services\ThemeResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function __construct(
        private readonly LegalDocumentRegistry $registry,
        private readonly LegalDocumentResolver $documents,
        private readonly LocaleResolver $locales,
        private readonly NavigationResolver $navigation,
        private readonly ThemeResolver $theme,
    ) {}

    public function show(Request $request): View
    {
        $definition = $this->registry->forRoute((string) $request->route()?->getName());
        $locale = $this->locales->resolveFromRequest($request);
        $document = $this->documents->published($definition->type, $locale->code);

        abort_if($document === null, 404);

        return view('legal.show', [
            'document' => $document,
            'locale' => $locale,
            'theme' => $this->theme->resolve(),
            'navigationItems' => $this->navigation->resolve('public', $locale->code, $request->route()?->getName()),
            'meta' => [
                'title' => $document->title,
                'description' => "{$document->title} version {$document->version}",
            ],
        ]);
    }
}
