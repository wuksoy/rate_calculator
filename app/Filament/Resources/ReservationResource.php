<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                Forms\Components\TextInput::make('customer_name')
                    ->required(),
                Forms\Components\TextInput::make('room_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('meal_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('check_in')
                    ->required(),
                Forms\Components\TextInput::make('nights')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('adults')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('children')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('adavance_discount')
                    ->required(),
                Forms\Components\Toggle::make('seaplane_discount_type')
                    ->required(),
                Forms\Components\Toggle::make('meal_discount_type')
                    ->required(),
                Forms\Components\Toggle::make('room_discount_type')
                    ->required(),
                Forms\Components\TextInput::make('seaplane_discount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('meal_discount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('room_discount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_without_discount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('discounted_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('meal_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nights')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('adults')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('children')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('adavance_discount')
                    ->boolean(),
                Tables\Columns\IconColumn::make('seaplane_discount_type')
                    ->boolean(),
                Tables\Columns\IconColumn::make('meal_discount_type')
                    ->boolean(),
                Tables\Columns\IconColumn::make('room_discount_type')
                    ->boolean(),
                Tables\Columns\TextColumn::make('seaplane_discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('meal_discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room_discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_without_discount')
                    ->numeric()
                    ->sortable(),
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
