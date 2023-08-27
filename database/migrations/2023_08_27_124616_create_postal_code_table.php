<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('postal_code_tmp', function (Blueprint $table) {
            //$table->id();
            //$table->timestamps();
            // @see https://www.post.japanpost.jp/zipcode/dl/readme.html
            $table->string('code', 10)->comment('全国地方公共団体コード');
            $table->char('old_postal_code', 5)->comment('旧郵便番号');
            $table->char('postal_code char' ,7)->comment('郵便番号');
            $table->string('prefecture_kana', 60)->comment('都道府県カナ');
            $table->string('city_kana', 100)->comment('市区町村カナ');
            $table->string('town_kana', 100)->comment('町域名カナ');
            $table->string('prefecture', 60)->comment('都道府県');
            $table->string('city', 100)->comment('市区町村');
            $table->string('town', 100)->comment('町域名');
            $table->unsignedInteger('flag1')->comment('1町域複数郵便番号フラグ');
            $table->unsignedInteger('flag2')->comment('小字毎番号町域フラグ');
            $table->unsignedInteger('flag3')->comment('丁目あり町域フラグ');
            $table->unsignedInteger('flag4')->comment ('1郵便番号複数町域フラグ');
            $table->unsignedInteger('flag5')->comment('更新状態');
            $table->unsignedInteger('flag6')->comment('変更理由');
        });
        $sql = 'ALTER TABLE postal_code '
            . 'ADD FULLTEXT idx_postal_code'
        ;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_code_tmp');
    }
};
