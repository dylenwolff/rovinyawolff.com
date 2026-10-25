<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\ManageLeads;
use App\Models\Lead;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static string|UnitEnum|null $navigationGroup = 'Client outreach';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->columnSpanFull(), Select::make('organization_id')->relationship('organization', 'name')->searchable()->preload(),
            Select::make('contact_id')->relationship('contact', 'first_name')->searchable()->preload(),
            Select::make('source')->options(['website' => 'Website', 'referral' => 'Referral', 'leo_lions' => 'Leo / Lions network', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'behance' => 'Behance', 'direct_outreach' => 'Direct outreach', 'manual' => 'Manual'])->required(),
            Select::make('status')->options(['new' => 'New', 'researching' => 'Researching', 'contacted' => 'Contacted', 'replied' => 'Replied', 'qualified' => 'Qualified', 'unqualified' => 'Unqualified', 'converted' => 'Converted'])->required(),
            TextInput::make('estimated_value')->numeric()->prefix('USD'), DateTimePicker::make('next_follow_up_at'),
            Textarea::make('description')->rows(6)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable(), TextColumn::make('organization.name')->searchable(), TextColumn::make('source')->badge(),
            TextColumn::make('status')->badge()->sortable(), TextColumn::make('estimated_value')->money('USD'), TextColumn::make('next_follow_up_at')->dateTime()->sortable(),
        ])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageLeads::route('/')];
    }
}
