<?php

namespace App\Filament\Resources\EvaluationResource\Pages;
use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;


    public function getTabs(): array
    {
        return [
            'all' => Tab::make('كل التقييمات '),
            'pending' => Tab::make('المترشحين الجدد')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),

            'accepted' => Tab::make(' المترشحين المقبولين')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'passed');
                }),

            'waitinglist' => Tab::make('قائمة الاحتياط')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'failed');
                }),
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
