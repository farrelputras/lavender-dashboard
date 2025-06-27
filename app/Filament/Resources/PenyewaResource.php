<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenyewaResource\Pages;
use App\Filament\Resources\PenyewaResource\RelationManagers;
use App\Models\Penyewa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenyewaResource extends Resource
{
    protected static ?string $model = Penyewa::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    public static function getModel(): string
    {
        return \App\Models\Kendaraan::class;
    }

    public static function getModelLabel(): string
    {
        return 'Penyewa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penyewa';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Informasi & Data';
    }
    
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
            'index' => Pages\ListPenyewas::route('/'),
            'create' => Pages\CreatePenyewa::route('/create'),
            'edit' => Pages\EditPenyewa::route('/{record}/edit'),
        ];
    }
}
