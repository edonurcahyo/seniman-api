// database/migrations/xxxx_xx_xx_update_slot_waktu_status_enum.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE slot_waktu MODIFY COLUMN status ENUM('tersedia', 'dibooking', 'lewat') DEFAULT 'tersedia'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE slot_waktu MODIFY COLUMN status ENUM('tersedia', 'dibooking') DEFAULT 'tersedia'");
    }
};