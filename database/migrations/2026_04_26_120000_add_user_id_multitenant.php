<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar user_id a tabla marca
        Schema::table('marca', function (Blueprint $table) {
            if (!Schema::hasColumn('marca', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });

        // Agregar user_id a tabla producto
        Schema::table('producto', function (Blueprint $table) {
            if (!Schema::hasColumn('producto', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });

        // Agregar user_id a tabla cliente
        Schema::table('cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('cliente', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });

        // Agregar user_id a tabla clasificacion
        Schema::table('clasificacion', function (Blueprint $table) {
            if (!Schema::hasColumn('clasificacion', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index('user_id');
            }
        });

        // Agregar user_id a tabla recibo
        if (Schema::hasTable('recibos')) {
            Schema::table('recibos', function (Blueprint $table) {
                if (!Schema::hasColumn('recibos', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->index('user_id');
                }
            });
        }

        // Agregar user_id a tabla credito
        if (Schema::hasTable('credito')) {
            Schema::table('credito', function (Blueprint $table) {
                if (!Schema::hasColumn('credito', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->index('user_id');
                }
            });
        }

        // Agregar user_id a tabla referencias_personales
        if (Schema::hasTable('referencias_personales')) {
            Schema::table('referencias_personales', function (Blueprint $table) {
                if (!Schema::hasColumn('referencias_personales', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->index('user_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marca', function (Blueprint $table) {
            if (Schema::hasColumn('marca', 'user_id')) {
                $table->dropForeignIdFor('users', 'user_id');
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('producto', function (Blueprint $table) {
            if (Schema::hasColumn('producto', 'user_id')) {
                $table->dropForeignIdFor('users', 'user_id');
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('cliente', function (Blueprint $table) {
            if (Schema::hasColumn('cliente', 'user_id')) {
                $table->dropForeignIdFor('users', 'user_id');
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        Schema::table('clasificacion', function (Blueprint $table) {
            if (Schema::hasColumn('clasificacion', 'user_id')) {
                $table->dropForeignIdFor('users', 'user_id');
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        if (Schema::hasTable('recibos')) {
            Schema::table('recibos', function (Blueprint $table) {
                if (Schema::hasColumn('recibos', 'user_id')) {
                    $table->dropForeignIdFor('users', 'user_id');
                    $table->dropIndex(['user_id']);
                    $table->dropColumn('user_id');
                }
            });
        }

        if (Schema::hasTable('credito')) {
            Schema::table('credito', function (Blueprint $table) {
                if (Schema::hasColumn('credito', 'user_id')) {
                    $table->dropForeignIdFor('users', 'user_id');
                    $table->dropIndex(['user_id']);
                    $table->dropColumn('user_id');
                }
            });
        }

        if (Schema::hasTable('referencias_personales')) {
            Schema::table('referencias_personales', function (Blueprint $table) {
                if (Schema::hasColumn('referencias_personales', 'user_id')) {
                    $table->dropForeignIdFor('users', 'user_id');
                    $table->dropIndex(['user_id']);
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
