<?php

namespace App\CatalogModule\Resources;

use App\CatalogModule\Models\ReportReason;
use App\CatalogModule\Resources\CancellationReasonResource\Pages\CreateCancellationReason;
use App\CatalogModule\Resources\CancellationReasonResource\Pages\EditCancellationReason;
use App\CatalogModule\Resources\CancellationReasonResource\Pages\ListCancellationReasons;
use App\CatalogModule\Resources\CancellationReasonResource\Pages\ViewCancellationReason;
use App\CatalogModule\Resources\ReportReasonResource\Pages\CreateReportReason;
use App\CatalogModule\Resources\ReportReasonResource\Pages\EditReportReason;
use App\CatalogModule\Resources\ReportReasonResource\Pages\ListReportReasons;
use App\CatalogModule\Resources\ReportReasonResource\Pages\ViewReportReason;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;

class ReportReasonResource extends Resource {
    use Translatable, HasTranslationLabel;

    protected static ?string $model = ReportReason::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->columnSpan(['xl' => 2])
                    ->translateLabel(),
                Toggle::make('status')->default(1)
                    ->onColor('success')
                    ->offColor('danger')
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        Action::make('Active')
                            ->label(fn(Model $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Model $record) => $record->toggleStatus())

                    )
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ModelStatus::class)
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

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListReportReasons::route('/'),
            'create' => CreateReportReason::route('/create'),
            'view' => ViewReportReason::route('/{record}'),
            'edit' => EditReportReason::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.content');
    }
}
