<?php

namespace App\Filament\Resources\MaleResidentResource\Pages;

use App\Filament\Resources\MaleResidentResource;
use Filament\Resources\Pages\Page;

class ResidentRelativesReport extends Page
{
    protected static string $resource = MaleResidentResource::class;

    protected static string $view = 'filament.resources.male-resident-resource.pages.resident-relatives-report';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }
}
