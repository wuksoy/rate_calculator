<?php

namespace App\Filament\Resources\SeasonResource\RelationManagers;

use App\Models\Season;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DatesRelationManager extends RelationManager
{
    protected static string $relationship = 'dates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('season_id')
                    ->options(Season::all()->pluck('name', 'id'))
                    ->columnSpan(2),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('season_id')
            ->columns([
                TextColumn::make('start_date')
                    ->dateTime('d/m/Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->dateTime('d/m/Y')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
