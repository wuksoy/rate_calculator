<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItineraryResource\Pages;
use App\Filament\Resources\ItineraryResource\RelationManagers;
use App\Models\Activity;
use App\Models\Itinerary;
use App\Models\Meal;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\Alignment;

class ItineraryResource extends Resource
{
    protected static ?string $model = Itinerary::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Itinerary';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('guest_name')
                    ->columnSpan(6),
                Select::make('meal_plan')
                    ->label('Meal')
                    ->options(Meal::all()->pluck('name', 'id'))
                    ->searchable()
                    ->columnSpan(6),
                DatePicker::make('checkin')
                    ->columnSpan(6),
                DatePicker::make('checkout')
                    ->columnSpan(6),
                Repeater::make('activities')->schema([
                    DatePicker::make('date')->columnSpan(4),
                    Repeater::make('activities')->schema([
                        TimePicker::make('time_start')
                        ->label('Start time')
                        ->seconds(false)
                        ->minutesStep(60)
                        ->columnSpan(2),
                        TimePicker::make('time_end')
                        ->label('End time')
                        ->seconds(false)
                        ->minutesStep(60)
                        ->columnSpan(2),
                        Select::make('activity')
                        ->options(Activity::all()->pluck('name','id'))
                        ->searchable()
                        ->columnSpan(8),
                    ])
                    ->addActionAlignment(Alignment::Start)
                    ->addActionLabel('Add Activity')
                    ->columns(12)
                    ->columnSpan(8)
                    ])
                ->label('Itinerary')
                ->addActionAlignment(Alignment::Start)
                ->addActionLabel('Add Date')
                ->columns(12)
                ->columnSpan(12)
                  
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItineraries::route('/'),
            'create' => Pages\CreateItinerary::route('/create'),
            'edit' => Pages\EditItinerary::route('/{record}/edit'),
        ];
    }
}
