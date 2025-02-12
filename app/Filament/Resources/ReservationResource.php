<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Meal;
use App\Models\Reservation;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use AnourValar\Office\SheetsService;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Reservation Information')
                    ->columns(12)
                    ->columnSpan(8)
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->columnSpan(12)
                            ->required(),
                        Select::make('meal_id')
                            ->label('Meal')
                            ->options(Meal::all()->pluck('name', 'id'))
                            ->searchable()
                            ->columnSpan(12),
                        DatePicker::make('check_in')
                            ->label('Check In')
                            ->columnSpan(6)
                            ->required(),
                        TextInput::make('nights')
                            ->label('Nights')
                            ->columnSpan(6)
                            ->required()
                            ->numeric(),
                        TextInput::make('adults')
                            ->minValue(1)
                            ->label('Adults')
                            ->default(0)
                            ->columnSpan(6)
                            ->required()
                            ->live(onBlur: true)
                            ->numeric(),
                        TextInput::make('children')
                            ->minValue(0)
                            ->label('Children')
                            ->default(0)
                            ->columnSpan(6)
                            ->required()
                            ->live(onBlur: true)
                            ->numeric(),
                        Select::make('room_id')
                            ->label('Room')
                            ->options(function (Get $get){
                                // Get Current Values
                                $adults = $get('adults')?? 0;
                                $children = $get('children')?? 0;
                                $total = $adults + $children;

                                //Return filtered rooms
                                return Room::query()
                                ->where('max_adult_occupancy', '>=', $adults)
                                ->where('max_child_occupancy', '>=', $children)
                                ->where('max_total_occupancy', '>=', $total)
                                ->pluck('name', 'id');
                            })
                            ->afterStateUpdated(function (Get $get, Set $set, $state){
                                // Get Current Values
                                $room = Room::find($state);
                                $nights = $get('nights')?? 0;
                                $adults = $get('adults')?? 0;
                                $children = $get('children')?? 0;
                                $total = $adults + $children;

                                if ($room && ($room->max_adult_occupancy < $adults || $room->max_child_occupancy < $children || $room->max_total_occupancy < $total)) {
                                    $set('room_id', null);
                                }
                            })
                            
                            ->searchable()
                            ->columnSpan(12),
                        Toggle::make('adavance_discount')
                            ->label('Advance Discount')
                            ->columnSpan(4)
                            ->required(),
                        TextInput::make('total_without_discount')
                            ->label('Total Without Discount')
                            ->columnSpan(4)
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('discounted_amount')
                            ->label('Discounted Amount')
                            ->columnSpan(4)
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('total')
                            ->label('Total')
                            ->columnSpan(6)
                            ->required()
                            ->numeric()
                            ->suffixActions([
                                Action::make('generate_total')
                                    ->label('Generate Excel')
                                    ->icon('heroicon-o-document-plus')
                                    ->requiresConfirmation()
                                    ->action(function (Set $set, $state) {
                                        $data = [
                                            'best_manager' => 'Sveta',
                                        
                                            // two-dimensional table
                                            'managers' => [
                                                'titles' => [[ 'William', 'James', 'Sveta' ]],
                                        
                                                'values' => [
                                                    [ // additional row
                                                        'month' => 'January',
                                                        'amount' => [700, 800, 900], // additional columns
                                                    ],
                                                    [
                                                        'month' => 'February',
                                                        'amount' => [7000, 8000, 9000],
                                                    ],
                                                    [
                                                        'month' => 'March',
                                                        'amount' => [70000, 80000, 90000],
                                                    ],
                                                ],
                                            ],
                                        ];
                                        
                                        (new SheetsService())
                                            ->generate('template2.xlsx', $data)
                                            ->saveAs('generated_document.xlsx');
                                    })
                            ])
                            ->default(0),
                        TextInput::make('APR')
                            ->label('APR')
                            ->columnSpan(6)
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
                Section::make('Discounts')
                    ->columns(12)
                    ->columnSpan(4)
                    ->schema([
                        TextInput::make('seaplane_discount')
                            ->label('Seaplane Discount')
                            ->minValue(0)
                            ->required()
                            ->numeric()
                            ->columnSpan(8)
                            ->default(0),
                        Radio::make('seaplane_discount_type')
                            ->label('')
                            ->required()
                            ->inline()
                            ->options([
                                false => 'base',
                                true => 'all' ,
                            ])
                            ->columnSpan(4)
                            ->default(false),
                        TextInput::make('meal_discount')
                            ->label('Meals Discount')
                            ->minValue(0)
                            ->required()
                            ->numeric()
                            ->columnSpan(8)
                            ->default(0),
                        Radio::make('meal_discount_type')
                            ->label('')
                            ->required()
                            ->inline()
                            ->options([
                                false => 'base',
                                true => 'all' ,
                            ])
                            ->columnSpan(4)
                            ->default(false),
                        TextInput::make('room_discount')
                            ->label('Meals Discount')
                            ->minValue(0)
                            ->required()
                            ->numeric()
                            ->columnSpan(8)
                            ->default(0),
                        Radio::make('room_discount_type')
                            ->label('')
                            ->required()
                            ->inline()
                            ->options([
                                false => 'base',
                                true => 'all' ,
                            ])
                            ->columnSpan(4)
                            ->default(false),
                        
                    ]),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Room')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('meal_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('check_in')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nights')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('adults')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('children')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('adavance_discount')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('seaplane_discount_type')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('meal_discount_type')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('room_discount_type')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('seaplane_discount')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('meal_discount')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('room_discount')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_without_discount')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('discounted_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
