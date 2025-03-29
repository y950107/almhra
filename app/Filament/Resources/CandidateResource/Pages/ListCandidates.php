<?php

namespace App\Filament\Resources\CandidateResource\Pages;

use App\Filament\Resources\CandidateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

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
            'all' => Tab::make('كل المترشحين '),
            'pending' => Tab::make('المترشحين الجدد')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')->where('evaluated', false)),
            'accepted' => Tab::make(' المترشحين المقبولين')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'accepted')
                        ->where('evaluated', true);
                }),

            'waitinglist' => Tab::make('قائمة الاحتياط')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'pending')
                    ->where('evaluated', true);
                }),
        ];
    }
}
