<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServisResource\Pages;
use App\Filament\Resources\ServisResource\RelationManagers;
use App\Models\Servis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServisResource extends Resource
{
    protected static ?string $model = Servis::class;

    protected static ?string $navigationIcon = 'fas-gear';
    protected static ?string $navigationGroup = 'Bisnis & Keuangan';
    protected static ?string $modelLabel = 'Servis';
    protected static ?string $pluralModelLabel = 'Servis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
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
            'index' => Pages\ListServis::route('/'),
            'create' => Pages\CreateServis::route('/create'),
            'edit' => Pages\EditServis::route('/{record}/edit'),
        ];
    }
}
