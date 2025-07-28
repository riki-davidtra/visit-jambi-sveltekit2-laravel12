<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestinationResource\Pages;
use App\Filament\Resources\DestinationResource\RelationManagers;
use App\Models\Destination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ViewColumn;

use App\Models\User;

class DestinationResource extends Resource
{
    protected static ?string $model = Destination::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    // ->placeholder('')
                    ->required()
                    ->options(
                        User::all()->pluck('name', 'id')->toArray()
                    )
                    ->preload()
                    ->searchable(),
                Select::make('category_id')
                    ->label('Category')
                    // ->placeholder('')
                    ->nullable()
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable(),
                TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Enter Name')
                    ->required()
                    ->string()
                    ->maxLength(50),
                TextInput::make('location')
                    ->label('Location')
                    ->placeholder('Enter Location')
                    ->required()
                    ->string()
                    ->maxLength(50),
                FileUpload::make('image')
                    ->label('Image')
                    ->placeholder('')
                    ->nullable()
                    ->image()
                    ->directory('destinations')
                    ->disk('public')
                    ->enableOpen()
                    ->enableDownload()
                    ->deleteUploadedFileUsing(function ($file, $record) {
                        Storage::disk('public')->delete($file);

                        $record->update([
                            'image' => null,
                        ]);
                    }),
                RichEditor::make('description')
                    ->label('Description')
                    ->placeholder('Enter Description')
                    ->nullable()
                    ->string()
                    ->maxLength(5000)
                    ->columnSpan('full')
                    // ->toolbarButtons([
                    //     'bold',
                    //     'italic',
                    //     'underline',
                    //     'redo',
                    //     'undo',
                    // ])
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('/destinations/description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->width(50)
                    ->height(50)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListDestinations::route('/'),
            'create' => Pages\CreateDestination::route('/create'),
            'edit' => Pages\EditDestination::route('/{record}/edit'),
        ];
    }
}
