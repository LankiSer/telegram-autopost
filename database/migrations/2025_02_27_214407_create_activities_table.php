<?php



use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;



return new class extends Migration

{

    public function up()

    {

        if (!Schema::hasTable('activities')) {

            Schema::create('activities', function (Blueprint $table) {

                $table->id();

                $table->foreignId('user_id')->constrained()->onDelete('cascade');

                $table->string('type');

                $table->string('description');

                $table->json('data')->nullable();

                $table->timestamps();

            });

        }

    }



    public function down()

    {

        Schema::dropIfExists('activities');

    }

}; 
