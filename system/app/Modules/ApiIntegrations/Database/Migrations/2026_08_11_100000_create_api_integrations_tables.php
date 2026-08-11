<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_integration_providers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->default('ph ph-plug');
            $table->string('logo_class')->nullable();
            $table->string('category')->default('crm');
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_configuration')->default(true);
            $table->json('config_schema')->nullable();
            $table->string('docs_url')->nullable();
            $table->unsignedInteger('sort_order')->default(100);
            $table->timestamps();
        });

        Schema::create('user_integration_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('api_integration_provider_id')->constrained('api_integration_providers')->cascadeOnDelete();
            $table->string('account_name')->nullable();
            $table->string('status')->default('configured');
            $table->json('settings')->nullable();
            $table->unsignedInteger('synced_leads_count')->default(0);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('configured_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'api_integration_provider_id'], 'user_provider_unique');
            $table->index(['user_id', 'status']);
            $table->index(['api_integration_provider_id', 'status'], 'provider_status_index');
        });

        DB::table('api_integration_providers')->insert([
            [
                'slug' => 'hubspot',
                'name' => 'HubSpot',
                'description' => 'New leads arrive as contacts, with the score and its reasoning on the record.',
                'icon' => 'ph-fill ph-circles-three',
                'logo_class' => 'integ__logo--hubspot',
                'category' => 'crm',
                'sort_order' => 10,
                'config_schema' => json_encode(['account_name' => 'HubSpot portal or account name']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'google-sheets',
                'name' => 'Google Sheets',
                'description' => 'Every new lead appends a row to a sheet you choose.',
                'icon' => 'ph-fill ph-table',
                'logo_class' => 'integ__logo--sheets',
                'category' => 'spreadsheet',
                'sort_order' => 20,
                'config_schema' => json_encode(['account_name' => 'Sheet name']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'pipedrive',
                'name' => 'Pipedrive',
                'description' => 'Push qualified leads straight into a Pipedrive stage as deals.',
                'icon' => 'ph-fill ph-kanban',
                'logo_class' => 'integ__logo--pipedrive',
                'category' => 'crm',
                'sort_order' => 30,
                'config_schema' => json_encode(['account_name' => 'Pipedrive company domain']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'salesforce',
                'name' => 'Salesforce',
                'description' => 'Create leads in Salesforce with the score mapped to a custom field.',
                'icon' => 'ph-fill ph-cloud',
                'logo_class' => 'integ__logo--salesforce',
                'category' => 'crm',
                'sort_order' => 40,
                'config_schema' => json_encode(['account_name' => 'Salesforce org name']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'slack',
                'name' => 'Slack',
                'description' => 'Post a message to a channel when a search finishes or a lead scores above your threshold.',
                'icon' => 'ph-fill ph-chat-circle-dots',
                'logo_class' => 'integ__logo--slack',
                'category' => 'notification',
                'sort_order' => 50,
                'config_schema' => json_encode(['account_name' => 'Workspace and channel']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'zapier',
                'name' => 'Zapier',
                'description' => 'Reach thousands of apps without a direct connection.',
                'icon' => 'ph-fill ph-lightning',
                'logo_class' => 'integ__logo--zapier',
                'category' => 'automation',
                'sort_order' => 60,
                'config_schema' => json_encode(['account_name' => 'Zap name or webhook label']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_integration_connections');
        Schema::dropIfExists('api_integration_providers');
    }
};
