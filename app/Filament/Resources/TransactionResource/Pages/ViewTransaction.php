<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('General Information')
                    ->schema([
                        TextEntry::make('invoice')
                            ->label('Invoice'),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'success' => 'success',
                                'expired' => 'gray',
                                'failed' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('created_at')
                            ->label('Created At'),
                    ])
                    ->columns(3),

                Section::make('Customer Information')
                    ->schema([
                        TextEntry::make('customer.name')
                            ->label('Customer Name'),

                        TextEntry::make('customer.email')
                            ->label('Email Address'),

                        TextEntry::make('address')
                            ->label('Address'),
                    ])
                    ->columns(3),

                Section::make('Ongkos Kirim')
                    ->schema([
                        TextEntry::make('shipping.shipping_courier')
                            ->label('Jasa Kirim'),

                        TextEntry::make('shipping.shipping_service')
                            ->label('Layanan Kirim'),

                        TextEntry::make('shipping.shipping_cost')
                            ->label('Ongkos Kirim')
                            ->numeric(decimalPlaces: 0)
                            ->money('IDR', locale: 'id'),
                    ])
                    ->columns(3),

                Section::make('Transaction Details')
                    ->schema([
                        RepeatableEntry::make('transactionDetails')
                            ->label('Items Details')
                            ->schema([
                                ImageEntry::make('product.image')
                                    ->label('Product Image')
                                    ->circular()
                                    ->width(100)
                                    ->height(100),

                                TextEntry::make('product.title')
                                    ->label('Product Name'),

                                TextEntry::make('qty')
                                    ->label('Quantity'),

                                TextEntry::make('price')
                                    ->label('Price')
                                    ->numeric(decimalPlaces: 0)
                                    ->money('IDR', locale: 'id'),
                            ])
                            ->columns(4),
                    ]),

                Section::make('Grand Total')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('total')
                                    ->label('Grand Total')
                                    ->numeric(decimalPlaces: 0)
                                    ->money('IDR', locale: 'id')
                                    ->color('primary')
                                    ->alignEnd(),
                            ]),
                    ]),
            ]);
    }
}