<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    Section::make('Information')
                    ->description('Basic information about the room.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpan(8),
                        TextInput::make('bedding')
                            ->columnSpan(4),
                        TextInput::make('size')
                            ->label('Size (sqm)')
                            ->columnSpan(3),
                        TextInput::make('code')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Unique identifier From Oracle')
                            ->columnSpan(3),
                        TextInput::make('unit')
                            ->label('Units')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Number of rooms available for booking.')
                            ->required()
                            ->numeric()
                            ->columnSpan(3),
                        TextInput::make('extra_bed')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Maximum extra beds that can be added to the room.')
                            ->required()
                            ->numeric()
                            ->columnSpan(3),
                    ])->columnSpan(12)->columns(12),
                    Section::make('Rates')
                    ->description('Room rates for different seasons.')
                    ->schema([
                        TextInput::make('rate_low_season')
                            ->label('Low Season Rate')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        TextInput::make('rate_shoulder_season')
                            ->label('Shoulder Season Rate')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        TextInput::make('rate_high_season')
                            ->label('High Season Rate')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        TextInput::make('rate_peak_season')
                            ->label('Peak Season Rate')
                            ->required()
                            ->numeric()
                            ->columnSpan(6),
                        
                    ])->columnSpan(12)->columns(12),
                ])->columnSpan(8)->columns(12),
                Grid::make()->schema([
                    Section::make('Occupancy')
                    ->description('Number of guests that can stay in the room.')
                    ->schema([
                        TextInput::make('base_rate_occupancy')
                            ->label('Base Rate')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Number of guests that can stay in the room without incurring additional charges.')
                            ->required()
                            ->numeric()
                            ->columnSpan(12),
                        TextInput::make('max_adult_occupancy')
                            ->label('Maximum Adult')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Maximum number of adult guests that can stay in the room.')
                            ->required()
                            ->numeric()
                            ->columnSpan(12),
                        TextInput::make('max_child_occupancy')
                            ->label('Maximum Children')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Maximum number of child guests that can stay in the room.')
                            ->required()
                            ->numeric()
                            ->columnSpan(12),
                        TextInput::make('max_total_occupancy')
                            ->label('Maximum Total')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Maximum number of guests that can stay in the room.')
                            ->required()
                            ->numeric()
                            ->columnSpan(12),
                    ])->columnSpan(12)->columns(12),
                ])->columnSpan(4)->columns(12),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit')
                    ->numeric()
                    ->sortable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bedding')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('extra_bed')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_total_occupancy')
                    ->label('Max Pax')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_adult_occupancy')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_child_occupancy')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('base_rate_occupancy')
                    ->label('Base Pax')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rate_high_season')
                    ->label('High Season')
                    ->money('USD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate_low_season')
                    ->label('Low Season')
                    ->money('USD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate_peak_season')
                    ->label('Peak Season')
                    ->money('USD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate_shoulder_season')
                    ->label('Shoulder Season')
                    ->money('USD')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'view' => Pages\ViewRoom::route('/{record}'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
