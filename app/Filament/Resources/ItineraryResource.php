<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItineraryResource\Pages;
use App\Filament\Resources\ItineraryResource\RelationManagers;
use App\Models\Activity;
use App\Models\Itinerary;
use App\Models\Meal;
use DateTime;
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
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use AnourValar\Office\DocumentService;
use PhpOffice\PhpWord\Element\Table as OTable;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\SimpleType\Jc;

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
        // helper function to get stay dates as text
        function stay($checkin, $checkout) {
            $checkinDate = new DateTime($checkin);
            $checkoutDate = new DateTime($checkout);
        
            if ($checkinDate->format('F') === $checkoutDate->format('F')) {
                return $checkinDate->format('j') . ' to ' . $checkoutDate->format('j F Y');
            } else {
                return $checkinDate->format('j F') . ' to ' . $checkoutDate->format('j F Y');
            }
        }

        // helper function to get meal name from meal id
        function mealName ($id) {
            $meal = Meal::find($id);
            return $meal?->name;
        }

        // helper function to generate activity time
        function timeRange($timeStart, $timeEnd) {
            $startTime = DateTime::createFromFormat('H:i', $timeStart);
            $endTime = DateTime::createFromFormat('H:i', $timeEnd);
        
            return $startTime->format('g:i a') . ' to ' . $endTime->format('g:i a');
        }

        return $table
            ->columns([
                TextColumn::make('guest_name'),
                TextColumn::make('checkin')
                ->date(),
                TextColumn::make('checkout')
                ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('generate')->action(function(Itinerary $record){
                    // main template data
                    $data = [
                        'guest_name'=> $record->guest_name,
                        'dates' => stay($record->checkin,$record->checkout),
                        'meal_plan' =>mealName($record->meal_plam),
                    ];

                    // generate base docx with main data
                    $fileName = 'generated_document.docx';
                    $filePath = public_path($fileName);
                    (new DocumentService())->generate('Itinerary.docx', $data)->saveAs($fileName);

                    // table variables
                    $textColor = '#595959';
                    $tableWidth = 11906;
                    $timeWidth = 2508;
                    $mainWidth = 6490;
                    $table = new OTable(['alignment' => Jc::START,]);
                    $templateProcessor = new TemplateProcessor($filePath);

                    $table_data = $record->activities;
                    foreach($table_data as $data){
                        // Add a row for the date (full width)
                        $table->addRow();
                        $cell = $table->addCell($tableWidth, ['gridSpan' => 2]); // Span across both columns
                        $cell->addText($data['date'], ['bold' => false, 'color' => $textColor], ['alignment' => Jc::START]);

                        // list of activities for the day
                        foreach($data['activities'] as $activity){
                            // time slot
                            $timeSlot = timeRange($activity['time_start'],$activity['time_end']);
                            $table->addRow();
                            $table->addCell($timeWidth)->addText($timeSlot, ['color' => $textColor], ['alignment' => Jc::START]);

                            // main text and sub text
                            $act = Activity::find($activity['activity']);
                            $cell = $table->addCell($mainWidth);
                            $textRun = $cell->addTextRun(['alignment' => Jc::START]);
                            $textRun->addText($act->name, ['color' => $textColor]);
                            $textRun->addTextBreak();
                            $textRun->addText("         " . $act->details, ['italic' => true, 'color' => $textColor]);
                        }
                    }

                    $templateProcessor->setComplexBlock('table_placeholder', $table);

                    $qfileName = 'final_document.docx';
                    $qfilePath = public_path($qfileName);
                    $templateProcessor->saveAs($qfilePath);
                    file_exists($filePath) && unlink($filePath);

                    return response()->download($qfilePath, $qfileName)->deleteFileAfterSend(true);
                })->requiresConfirmation(),
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
