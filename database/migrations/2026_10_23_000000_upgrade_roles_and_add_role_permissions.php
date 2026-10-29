<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('roles', 'nombre') && ! Schema::hasColumn('roles', 'name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique(['nombre']);
                $table->renameColumn('nombre', 'name');
            });
        }

        if (! Schema::hasColumn('roles', 'company_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedSmallInteger('company_id')->nullable()->after('id');
                $table->index('company_id');
            });
        }

        $roleIndexes = Schema::getIndexes('roles');
        $hasCompanyNameUnique = collect($roleIndexes)->contains(
            fn (array $index) => $index['unique']
                && $index['columns'] === ['company_id', 'name']
        );

        if (! $hasCompanyNameUnique) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unique(['company_id', 'name']);
            });
        }

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('module');
            $table->boolean('view')->default(false);
            $table->boolean('create')->default(false);
            $table->boolean('edit')->default(false);
            $table->boolean('delete')->default(false);
            $table->timestamps();
            $table->unique(['role_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');

        if (Schema::hasColumn('roles', 'name') && Schema::hasColumn('roles', 'company_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique(['company_id', 'name']);
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
                $table->renameColumn('name', 'nombre');
                $table->unique('nombre');
            });
        }
    }
};
