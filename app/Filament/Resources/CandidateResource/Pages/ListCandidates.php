<?php

namespace App\Filament\Resources\CandidateResource\Pages;

use Filament\Actions;
use App\Models\Candidate;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CandidateResource;

class ListCandidates extends ListRecords
{
    protected static string $resource = CandidateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'all' => Tab::make('كل المترشحين ')->badge(Candidate::count()),
            'pending' => Tab::make('المترشحين الجدد')
                ->badge(Candidate::where('status', 'pending')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),
            'accepted' => Tab::make(' المترشحين المقبولين')
                ->badge(Candidate::where('status', 'accepted')->count())
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'accepted');
                }),

            'waitinglist' => Tab::make('قائمة الاحتياط')
                ->badge(Candidate::where('status', 'pending')->where('evaluated', true)->count())
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'pending')
                    ->where('evaluated', true);
                }),
        ];
    }
}
