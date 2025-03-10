<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Meal;
use App\Models\Season;
use App\Models\Reservation;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
use AnourValar\Office\SheetsService;
use AnourValar\Office\DocumentService;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Element\Table as OTable;
use PhpOffice\PhpWord\SimpleType\Jc;
use AnourValar\Office\Format;
use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Rate Calculation';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    private static function generate(Get $get){
        // get room
        $room = Room::find($get('room_id'));
        $meal = Meal::find($get('meal_id'));
        $nights = (int) $get('nights') ?? 0;
        $total=0;

        // get occupants
        $adults = (int) $get('adults') ?? 0;
        $children = (int) $get('children') ?? 0;
        $extra = max(0, $adults - $room->base_rate_occupancy);
        $baseOccupants = min($room->base_rate_occupancy, $adults);
        $occupants =$adults . ' Adults' . ($children > 0 ? ' + ' . $children . ' Children' : '');
        
        // get season
        $checkInDate = $get('check_in');
        $season = Season::whereHas('dates', function ($query) use ($checkInDate) {
            $query->whereDate('start_date', '<=', $checkInDate)->whereDate('end_date', '>=', $checkInDate);
        })->first();

        // base room price
        $baseRate = match ($season->name) {
            'High Season' => $room->rate_high_season,
            'Peak Season' => $room->rate_peak_season,
            'Low Season'  => $room->rate_low_season,
            'Shoulder Season'  => $room->rate_shoulder_season,
        };

        // get discounts
        // advance discount: more than 30 days ahead AND not in peak season
        $advanceDiscount = (floor(now()->diffInDays($checkInDate ?? now())) >= 30 && $season?->name !== 'Peak Season') ? 0.3 : 0;
        // room discount
        $roomDiscount = $get('room_discount') !== null ? (float) $get('room_discount') / 100 : 0;
        // total applicable discount
        $totalDiscountPercentage = min(1,$advanceDiscount+$roomDiscount); 
        $RoomDiscountAmount = $baseRate * $nights * (1-$totalDiscountPercentage);

        // prepare data
        // calculate the room charges
        $checkInDateTxt = Carbon::parse($get('check_in'));
        $checkOutDate = $checkInDateTxt->copy()->addDays($nights);
        $data=[
            'meal_name' => $meal?->name ?? '',
            'occupants' => $occupants,
            'checkin' => $checkInDateTxt->format('j M y'),
            'checkout' => $checkOutDate->format('j M y'),
            'nights' => $nights,

            'room' =>[
                'row' => [
                    'number' => 1,
                    'details' => $room->name,
                    'room_rate' => $baseRate,
                    'room_total' =>$baseRate*$nights,
                ],
            ],
        ];

        if($totalDiscountPercentage>0){
            $data['discount'] = [
                "percentage" => $totalDiscountPercentage*100,
                "total" => -$RoomDiscountAmount
            ];
        }

        // Get meal details
        $mealPlanRate = $advanceDiscount > 0 ? $meal?->promo_rate : $meal?->base_rate;
        $mealDiscountRate = (float) ($get('meal_discount') ?? 0) / 100;
        // Calculate meal charges for base occupants
        $mealRate = $mealPlanRate * (1 - $mealDiscountRate);
        $baseAmount = $mealRate * $nights * $baseOccupants;
        $mealDetails = $advanceDiscount > 0 ?'Complimentary '. $meal->name: $meal->name ;
        $data['meal'][] = [
            'pax' => $baseOccupants,
            'details' => $mealDetails,
            'rate' => $mealRate,
            'amount' => $baseAmount,
        ];
        // Calculate meal charges for extra occupants, if applicable
        $extraAmount = 0;
        if ($extra > 0) {
            $mealDiscountType = $get('meal_discount_type');
            $extraMealRate = $mealDiscountType ? $meal?->base_rate * (1 - $mealDiscountRate) : $meal?->base_rate;
            $extraAmount = $extraMealRate * $nights * $extra;
            $data['meal'][] = [
                'pax' => $extra,
                'details' => $meal->name . ' ' . $extra . ' Extra Adult Meals',
                'rate' => $extraMealRate,
                'amount' => $extraAmount,
            ];
        }

        // extra occupant charges
        if($extra>0){
            $data['extra'] = [
                'pax' => $extra,
                'details' => 'Extra Person Supplement - '.$extra.' Adults',
                'rate' => 300,
                'amount' => 300*$nights,
            ];
        }

        // calculate the seaplane charges
        $adultSeaplane = 300;
        $childSeaplane = 150;
        $seaplaneDiscount = (float) $get('seaplane_discount') ?? 0;
        $seaplaneDiscountType = $get('seaplane_discount_type');
        $adultSeaplanePrice = $adults*$adultSeaplane*(1-$seaplaneDiscount/100);
        $extraSeaplanePrice = $extra * $seaplaneDiscountType? $adultSeaplane*(1-$seaplaneDiscount/100):$extra * $adultSeaplane;
        $childSeaplanePrice = $children * $seaplaneDiscountType? $childSeaplane*(1-$seaplaneDiscount/100):$children * $childSeaplane;
        // adult seaplane
        $data['seaplane'][]=[
            'pax' => $baseOccupants,
            'details' => 'Return shared seaplane transfer - '. $baseOccupants .' Adult '.($seaplaneDiscount > 0 ? '( ' . $seaplaneDiscount . '% Discount )' : ''),
            'rate' => $adultSeaplane*(1-$seaplaneDiscount/100),
            'amount' => $adultSeaplanePrice,
        ];
        //extra seaplane
        if($extra>0){
            $data['seaplane'][]=[
                'pax' => $extra,
                'details' => 'Return shared seaplane transfer - '. $extra .' Extra Adult '.($seaplaneDiscount > 0 && $seaplaneDiscountType==true? '( ' . $seaplaneDiscount . '% Discount )' : ''),
                'rate' => $seaplaneDiscountType? $adultSeaplane*(1-$seaplaneDiscount/100):$adultSeaplane,
                'amount' => $extraSeaplanePrice,
            ];
        }
        //child seaplane
        if($children>0) {
            $data['seaplane'][]=[
                'pax' => $adults,
                'details' => 'Return shared seaplane transfer - '. $children .' Children ' .($seaplaneDiscount > 0 && $seaplaneDiscountType==true? '( ' . $seaplaneDiscount . '% Discount )' : ''),
                'rate' => $children * $seaplaneDiscountType? $childSeaplane*(1-$seaplaneDiscount/100):$children * $childSeaplane,
                'amount' => $childSeaplanePrice,
            ];
        }

        //calculate the tax charges
        $finalTotal = $baseAmount + $extraAmount + $adultSeaplanePrice + $extraSeaplanePrice + $childSeaplanePrice;
        //service charge
        $data['tax'][]=[
            'pax' => 1,
            'details' => '10% Service Charge ',
            'rate' => '10%',
            'amount' => 0.1*$finalTotal,
        ];
        // gst
        $data['tax'][]=[
            'pax' => 1,
            'details' => '16% GST Tax ',
            'rate' => '16%',
            'amount' =>0.16*(0.1*$finalTotal),
        ];
        //green tax for children only
        $greenTax = 6 * $children * $nights;
        if($children > 0){
            $data['tax'][]=[
                'pax' => $children,
                'details' => 'Green Tax per person per night - '. $children .' Children ',
                'rate' => 6,
                'amount' => $greenTax,
            ];
        }
        return $data;
    }

    private static function calculateTotal(Get $get, Set $set)
    {
        $roomId = $get('room_id');
        $room = Room::find($roomId);
        $checkInDate = $get('check_in');
        $numDays = (int) $get('nights') ?? 0;
        $numAdults = (int) $get('adults') ?? 0;
        $numChildren = (int) $get('children') ?? 0;
        $daysInAdvance = floor(now()->diffInDays($checkInDate ?? now()));
        $roomDiscount = (float) $get('room_discount') ?? 0;
        $mealDiscount = (float) $get('meal_discount') ?? 0;
        $mealDiscountType = $get('meal_discount_type');
        $seaplaneDiscount = (float) $get('seaplane_discount') ?? 0;
        $seaplaneDiscountType = $get('seaplane_discount_type');
        $extraChargePerNight = 300;
        $seaplaneChargeAdult = 700;
        $seaplaneChargeChild = 350;

        // VALIDATE ROOM DROPDOWN
        if (
            $room && 
            ($room->max_adult_occupancy < $numAdults 
            || $room->max_child_occupancy < $numChildren 
            || $room->max_total_occupancy < ($numChildren +$numAdults))
            ) 
        {
            $set('room_id', null);
        }

        //Step 1: Determine the Season based on check-in date
        $season = Season::whereHas('dates', function ($query) use ($checkInDate) {
            $query->whereDate('start_date', '<=', $checkInDate)
                  ->whereDate('end_date', '>=', $checkInDate);
        })->first();

        $set('testing',$season->name.'
        ');

        // gets season name
        $seasonName = $season?->name ?? 'Normal';
        if($daysInAdvance >= 30 && $seasonName !== 'Peak Season'){
            $set('adavance_discount',true);
        }
        else {
            $set('adavance_discount',false);
        }

        // SETS Days in Advance and Checkout Date for reference
        $set('DIA',$daysInAdvance);
        $carbon_date =Carbon::parse($checkInDate);
        $checkOutDate = $carbon_date->addDays($numDays);
        $set('checkout', $checkOutDate->format('d M Y'));

        // Step 2: Retrieve the correct room rate based on season
        $room = Room::find($roomId);
        if (!$room) {
            $set('total', 0);
            return;
        }

        $baseRate = match ($seasonName) {
            'High Season' => $room->rate_high_season,
            'Peak Season' => $room->rate_peak_season,
            'Low Season'  => $room->rate_low_season,
            'Shoulder Season'  => $room->rate_shoulder_season,
        };

        $set('testing',$get('testing') . 'Base Rate: ' . $baseRate .'
        ');

        // Step 3: Calculate discounts
        // Days in advance
        if($daysInAdvance >= 30 && $seasonName !== 'Peak Season'){
            $advanceDiscount = $baseRate * 0.30 * $numDays;
        }
        else {
            $advanceDiscount = 0;
        }
        
        $roomRateDiscount = ($baseRate * ($roomDiscount / 100) * $numDays);
        $totalRoomDiscount = $roomRateDiscount + $advanceDiscount;

        $set('testing',$get('testing') . '30 Day Advance Discount: ' . $advanceDiscount .'
        ');
        $set('testing',$get('testing') . 'Base Room Rate Discount: ' . $roomRateDiscount .'
        ');
        $set('testing',$get('testing') . 'Total Room Discount: ' . $totalRoomDiscount .'
        ');

        $adr = $baseRate - (($baseRate * ($roomDiscount / 100)) + ($baseRate * 0.30)) ;
        $set('ADR',$adr);
        $set('testing',$get('testing') . 'ADR: ' . $adr .'
        ');
        
        // Step 4: Calculate base room rate and extra charges
        $totalBaseRate = max(0, $adr) * $numDays;
        $extraOccupants = max(0, $numAdults - $room->base_rate_occupancy);
        $extraCharge = $extraOccupants * $extraChargePerNight * $numDays;
        $totalRate = $totalBaseRate + $extraCharge;

        $finalRoomPrice = $totalBaseRate - $totalRoomDiscount;

        $set('testing',$get('testing') . 'Base Room x nights: ' . $totalBaseRate .'
        ');
        $set('testing',$get('testing') . 'Extra x nights: ' . $extraCharge .'
        ');
        $set('testing',$get('testing') . 'Total Room: ' . $totalRate .'
        
        ');

        // Step 5: Calculate meal plan cost
        $meal = Meal::find($get('meal_id'));
        $mealPlanBaseRate = $meal?->base_rate ?? 0;
        $mealPlanPromoRate = $meal?->promo_rate ?? 0;
        $mealPlanRate = ($daysInAdvance >= 30) ? $mealPlanPromoRate : $mealPlanBaseRate;
        $numBaseOccupants = min($room->base_rate_occupancy, $numAdults);
        $mealDiscountRate = $mealDiscount / 100;

        $set('testing',$get('testing') . 'Base Occupants: ' . $numBaseOccupants .'
        ');
        $set('testing',$get('testing') . 'Extra Occupants: ' . $extraOccupants .'
        
        ');

        if ($mealDiscountType == false) {
            $baseMeals = ($numBaseOccupants * $mealPlanRate * (1 - $mealDiscountRate)) * $numDays;
            $extraMeals = ($extraOccupants * $mealPlanBaseRate) * $numDays;
        } else {
            $extraMealRate = $mealPlanBaseRate * (1 - $mealDiscountRate);
            $baseMeals = ($numBaseOccupants * $mealPlanRate * (1 - $mealDiscountRate)) * $numDays;
            $extraMeals = ($extraOccupants * $extraMealRate) * $numDays;
        }
        $totalMealCost = $baseMeals + $extraMeals;
        $totalRate += $totalMealCost;

        $set('testing',$get('testing') . 'Meal Plan Rate: ' . $mealPlanRate .'
        ');
        $set('testing',$get('testing') . 'Meal Plan Type: ' . $mealDiscountType .'
        ');
        $set('testing',$get('testing') . 'base pax meal: ' . $baseMeals .'
        ');
        $set('testing',$get('testing') . 'extra pax meal: ' . $extraMeals .'
        ');
        $set('testing',$get('testing') . 'total meal: ' . $totalMealCost .'
        
        ');

        // Step 6: Calculate seaplane charges
        $seaplaneDiscountRate = $seaplaneDiscount / 100;
        $seaplaneChargeAdultExtra = $seaplaneChargeAdult * (1 - $seaplaneDiscountRate);
        $seaplaneBaseRateDiscounted = $seaplaneChargeAdult * (1 - $seaplaneDiscountRate);

        if ($seaplaneDiscountType == false) {
            $seaplaneBaseCost = $numBaseOccupants * $seaplaneBaseRateDiscounted;
            $seaplaneExtraCost = $extraOccupants * $seaplaneChargeAdult;
        } else {
            $seaplaneBaseCost = $numBaseOccupants * $seaplaneBaseRateDiscounted;
            $seaplaneExtraCost = $extraOccupants * $seaplaneChargeAdultExtra;
        }
        $seaplaneChildCost = $numChildren * $seaplaneChargeChild;
        $seaplaneTotalCost = $seaplaneBaseCost + $seaplaneExtraCost + $seaplaneChildCost;
        $totalRate += $seaplaneTotalCost;

        $set('testing',$get('testing') . 'seaplane cost base pax: ' . $seaplaneBaseCost .'
        ');
        $set('testing',$get('testing') . 'seaplane cost extra pax: ' . $seaplaneExtraCost .'
        ');
        $set('testing',$get('testing') . 'seaplane cost children: ' . $seaplaneChildCost .'
        ');
        $set('testing',$get('testing') . 'seaplane total: ' . $seaplaneTotalCost .'
        
        ');

        // Step 7: Calculate taxes
        $serviceCharge = ($totalMealCost + $seaplaneTotalCost) * 0.1;
        $gstCharge = ($serviceCharge + $totalMealCost + $seaplaneTotalCost) * 0.16;
        $greenTax = 6 * $numChildren * $numDays;

        $set('testing',$get('testing') . 'service charge: ' . $serviceCharge .'
        ');
        $set('testing',$get('testing') . 'gst charge: ' . $gstCharge .'
        ');
        $set('testing',$get('testing') . 'green tax charge: ' . $greenTax .'
        ');

        $totalRate += $serviceCharge + $gstCharge + $greenTax;
        $set('total', round($totalRate, 2));
    }

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
                            ->live()
                            ->required(),
                        Select::make('meal_id')
                            ->label('Meal')
                            ->options(Meal::all()->pluck('name', 'id'))
                            ->searchable()
                            ->columnSpan(12)
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set)),
                        DatePicker::make('check_in')
                            ->label('Check In')
                            ->columnSpan(4)
                            ->minDate(Carbon::tomorrow())
                            ->required()
                            ->displayFormat('d M Y')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->default(Carbon::tomorrow()->toDateString())
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set)),
                        TextInput::make('nights')
                            ->label('Nights')
                            ->minValue(1)
                            ->default(1)
                            ->columnSpan(4)
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set)),
                        TextInput::make('DIA')
                            ->label('Days in advance')
                            ->columnSpan(4)
                            ->disabled()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set)),
                        TextInput::make('checkout')
                            ->label('Check Out Date')
                            ->columnSpan(4)
                            ->disabled()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set)),
                        TextInput::make('adults')
                            ->minValue(1)
                            ->label('Adults')
                            ->default(1)
                            ->columnSpan(4)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->numeric(),
                        TextInput::make('children')
                            ->minValue(0)
                            ->label('Children')
                            ->default(0)
                            ->columnSpan(4)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->numeric(),
                        Select::make('room_id')
                            ->label('Room')
                            ->live()
                            ->required()
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
                                $room = Room::find($state);
                                $adults = $get('adults')?? 0;
                                $children = $get('children')?? 0;
                                $total = $adults + $children;

                                if ($room 
                                    && ($room->max_adult_occupancy < $adults 
                                    || $room->max_child_occupancy < $children 
                                    || $room->max_total_occupancy < $total)) 
                                { $set('room_id', null);}

                                self::calculateTotal($get, $set);
                            })
                            ->searchable()
                            ->columnSpan(12),
                        TextInput::make('total_without_discount')
                            ->label('Total Without Discount')
                            ->columnSpan(6)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->numeric()
                            ->default(0),
                        TextInput::make('discounted_amount')
                            ->label('Discounted Amount')
                            ->columnSpan(6)
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->numeric()
                            ->default(0),
                        TextInput::make('total')
                            ->label('Total')
                            ->columnSpan(6)
                            ->required()
                            ->numeric()
                            ->live()
                            ->readOnly()
                            ->default(0)
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->suffixActions([
                                Action::make('generate_total')
                                    ->label('Generate Excel')
                                    ->icon('heroicon-o-document-arrow-down')
                                    ->requiresConfirmation()
                                    ->action(function (Get $get) {
                                        $data = self::generate($get);
                                        $fileName = 'generated_document.xlsx';
                                        //$filePath = base_path('public_html/' . $fileName); 
                                        $filePath = public_path($fileName);
                                        
                                        (new SheetsService())
                                            ->generate('template.xlsx', $data)
                                            ->saveAs($fileName);
                                        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);

                                    }),
                                Action::make('generate_itinerary')
                                ->label('Generate Itinerary')
                                ->icon('heroicon-o-document-arrow-up')
                                ->requiresConfirmation()
                                ->action(function(Get $get){
                                    // main template data
                                    $data = [
                                        'guest_name'=>' Mr. Mohamed Wishan',
                                        'dates' => '10 to 15 July 2025',
                                        'meal_plan' =>'All Inclusive',
                                    ];

                                    // generate base docx with main data
                                    $fileName = 'generated_document.docx';
                                    //$filePath = base_path('public_html/' . $fileName); 
                                    $filePath = public_path($fileName);
                                    (new DocumentService())->generate('Itinerary.docx', $data)->saveAs($fileName);

                                    // table variables
                                    $textColor = '#595959';
                                    $tableWidth = 11906;
                                    $timeWidth = 2508;
                                    $mainWidth = 6490;
                                    $table = new OTable(['alignment' => Jc::START,]);
                                    $templateProcessor = new TemplateProcessor($filePath);

                                    $table_data = [
                                        [
                                            'date'=>'TUESDAY, 28 JANUARY 2025',
                                            'activities'=>[
                                                [
                                                    'time'=>'10:00 am to 11:00 am',
                                                    'main'=>'Breakfast at the Pool',
                                                    'sub' =>'something about the pool. Cool stuff auto generated right here'
                                                ],
                                                [
                                                    'time'=>'2:00 pm to 3:00 pm',
                                                    'main'=>'Afternoon Lunch',
                                                    'sub' =>'Have lunch at the Lighthouse, beat the trials of Osiris something something'
                                                ],
                                            ]
                                        ],
                                        [
                                            'date'=>'WEDNESDAY, 29 JANUARY 2025',
                                            'activities'=>[
                                                [
                                                    'time'=>'10:00 am to 11:00 am',
                                                    'main'=>'MORE Breakfast at the Pool',
                                                    'sub' =>'something about the pool. Cool stuff auto generated right here'
                                                ],
                                                [
                                                    'time'=>'2:00 pm to 3:00 pm',
                                                    'main'=>'Sunset Yatch Cruise',
                                                    'sub' =>'heres the thing that we all wanna know. How does this thing cost'
                                                ],
                                            ]
                                        ],
                                    ];

                                    foreach($table_data as $data){
                                        // Add a row for the date (full width)
                                        $table->addRow();
                                        $cell = $table->addCell($tableWidth, ['gridSpan' => 2]); // Span across both columns
                                        $cell->addText($data['date'], ['bold' => false, 'color' => $textColor], ['alignment' => Jc::START]);

                                        // list of activities for the day
                                        foreach($data['activities'] as $activity){
                                            // time slot
                                            $table->addRow();
                                            $table->addCell($timeWidth)->addText($activity['time'], ['color' => $textColor], ['alignment' => Jc::START]);

                                            // main text and sub text
                                            $cell = $table->addCell($mainWidth);
                                            $textRun = $cell->addTextRun(['alignment' => Jc::START]);
                                            $textRun->addText($activity['main'], ['color' => $textColor]);
                                            $textRun->addTextBreak(); // New line
                                            $textRun->addText("         " . $activity['sub'], ['italic' => true, 'color' => $textColor]);
                                        }
                                    }

                                    // Insert the dynamically created table into the document
                                    $templateProcessor->setComplexBlock('table_placeholder', $table);

                                    $qfileName = 'final_document.docx';
                                    //$qfilePath = base_path('public_html/' . $qfileName); 
                                    $qfilePath = public_path($qfileName);
                                    $templateProcessor->saveAs($qfilePath);
                                    file_exists($filePath) && unlink($filePath);

                                    return response()->download($qfilePath, $qfileName)->deleteFileAfterSend(true);
                                }),
                            ]),
                        TextInput::make('ADR')
                            ->label('ADR')
                            ->columnSpan(6)
                            ->readOnly()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->numeric()
                            ->default(0),
                        Textarea::make('testing')
                            ->readonly()
                            ->columnSpan(12)
                            ->rows(25)
                    ]),
                Section::make('Discounts')
                    ->columns(12)
                    ->columnSpan(4)
                    ->schema([
                        TextInput::make('seaplane_discount')
                            ->label('Seaplane Discount')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->numeric()
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->columnSpan(8)
                            ->default(0),
                        Radio::make('seaplane_discount_type')
                            ->label('')
                            ->required()
                            ->inline()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->options([false => 'base',true => 'all' ,])
                            ->columnSpan(4)
                            ->default(false),
                        TextInput::make('meal_discount')
                            ->label('Meals Discount')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->numeric()
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->columnSpan(8)
                            ->default(0),
                        Radio::make('meal_discount_type')
                            ->label('')
                            ->required()
                            ->inline()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->options([false => 'base',true => 'all' ,])
                            ->columnSpan(4)
                            ->default(false),
                        TextInput::make('room_discount')
                            ->label('Room Discount')
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->numeric()
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->columnSpan(8)
                            ->default(0),
                        Radio::make('room_discount_type')
                            ->label('')
                            ->required()
                            ->inline()
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->options([false => 'base',true => 'all' ,])
                            ->columnSpan(4)
                            ->default(false),
                        Toggle::make('adavance_discount')
                            ->label('30 day Advance Discount')
                            ->onColor('primary')
                            ->offColor('danger')
                            ->columnSpan(12)
                            ->live()
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::calculateTotal($get, $set))
                            ->required(),
                        
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
            ])
            ->actions([
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
