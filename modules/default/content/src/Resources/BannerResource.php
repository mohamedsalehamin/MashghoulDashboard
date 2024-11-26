<?php

namespace App\ContentModule\Resources;

use App\ContentModule\Resources\BannerResource\Pages\ListBanners;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\ContentModule\Models\Banner;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ContentModule\Resources\BannerResource\Pages\CreateBanner;
use App\ContentModule\Resources\BannerResource\Pages\EditBanner;

class BannerResource extends Resource {
    use HasTranslationLabel;
    use Translatable;

    protected static ?string $model = Banner::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function form(Form $form): Form {

        return $form->schema([
            TextInput::make('name')
                ->required()
                ->translateLabel(),

            SpatieMediaLibraryFileUpload::make('image_ar')
                ->label(__('forms.fields.image_en'))
                ->disk('public')
                ->collection('en')
                ->required(),

            SpatieMediaLibraryFileUpload::make('image_en')
                ->disk('public')
                ->label(__('forms.fields.image_ar'))
                ->collection('ar')
                ->required(),
            Toggle::make('status')->default(1)

                ->onColor('success')
                ->offColor('danger')
                ->translateLabel()

        ])->columns(1);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                IconColumn::make('status')
                    ->boolean()
                    ->action(
                        \Filament\Tables\Actions\Action::make('Active')
                            ->label(fn(Banner $record): string => $record->status ? __('panel.messages.deactivate') : __('panel.messages.activate'))
                            ->disabled(fn(Model $record): bool => !filament()->auth()->user()->can('update', $record))
                            ->requiresConfirmation()
                            ->action(fn(Banner $record) => $record->toggleStatus())


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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.content');
    }
}
