<?php

namespace Database\Seeders;

use App\Models\MotorcycleModel;
use Illuminate\Database\Seeder;

class MotorcycleModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            // Yamaha
            ['make' => 'Yamaha', 'model' => 'Sniper 155',    'year_start' => 2019, 'year_end' => 2024, 'engine_cc' => 155, 'slug' => 'yamaha-sniper-155'],
            ['make' => 'Yamaha', 'model' => 'Mio Aerox 155', 'year_start' => 2018, 'year_end' => 2024, 'engine_cc' => 155, 'slug' => 'yamaha-mio-aerox-155'],
            ['make' => 'Yamaha', 'model' => 'NMAX 155',      'year_start' => 2020, 'year_end' => 2024, 'engine_cc' => 155, 'slug' => 'yamaha-nmax-155'],
            ['make' => 'Yamaha', 'model' => 'R15 V3',        'year_start' => 2017, 'year_end' => 2023, 'engine_cc' => 155, 'slug' => 'yamaha-r15-v3'],
            ['make' => 'Yamaha', 'model' => 'MT-15',         'year_start' => 2019, 'year_end' => 2024, 'engine_cc' => 155, 'slug' => 'yamaha-mt-15'],
            ['make' => 'Yamaha', 'model' => 'Mio Gravis',    'year_start' => 2022, 'year_end' => 2024, 'engine_cc' => 125, 'slug' => 'yamaha-mio-gravis'],
            // Honda
            ['make' => 'Honda',  'model' => 'Click 160',     'year_start' => 2021, 'year_end' => 2024, 'engine_cc' => 160, 'slug' => 'honda-click-160'],
            ['make' => 'Honda',  'model' => 'PCX 160',       'year_start' => 2021, 'year_end' => 2024, 'engine_cc' => 160, 'slug' => 'honda-pcx-160'],
            ['make' => 'Honda',  'model' => 'ADV 160',       'year_start' => 2021, 'year_end' => 2024, 'engine_cc' => 160, 'slug' => 'honda-adv-160'],
            ['make' => 'Honda',  'model' => 'CBR150R',       'year_start' => 2019, 'year_end' => 2024, 'engine_cc' => 150, 'slug' => 'honda-cbr150r'],
            ['make' => 'Honda',  'model' => 'RS150R',        'year_start' => 2017, 'year_end' => 2023, 'engine_cc' => 150, 'slug' => 'honda-rs150r'],
            // Suzuki
            ['make' => 'Suzuki', 'model' => 'Raider R150',   'year_start' => 2016, 'year_end' => 2024, 'engine_cc' => 150, 'slug' => 'suzuki-raider-r150'],
            ['make' => 'Suzuki', 'model' => 'GSX-R150',      'year_start' => 2017, 'year_end' => 2023, 'engine_cc' => 150, 'slug' => 'suzuki-gsx-r150'],
            ['make' => 'Suzuki', 'model' => 'Skydrive 125',  'year_start' => 2018, 'year_end' => 2024, 'engine_cc' => 125, 'slug' => 'suzuki-skydrive-125'],
            // Kawasaki
            ['make' => 'Kawasaki', 'model' => 'Rouser NS200',  'year_start' => 2016, 'year_end' => 2023, 'engine_cc' => 200, 'slug' => 'kawasaki-rouser-ns200'],
            ['make' => 'Kawasaki', 'model' => 'Z125 Pro',      'year_start' => 2016, 'year_end' => 2023, 'engine_cc' => 125, 'slug' => 'kawasaki-z125-pro'],
            ['make' => 'Kawasaki', 'model' => 'Ninja 400',     'year_start' => 2018, 'year_end' => 2024, 'engine_cc' => 399, 'slug' => 'kawasaki-ninja-400'],
            // KYMCO
            ['make' => 'KYMCO',    'model' => 'Xciting S 400', 'year_start' => 2018, 'year_end' => 2024, 'engine_cc' => 400, 'slug' => 'kymco-xciting-s400'],
            // CFMoto
            ['make' => 'CFMoto',   'model' => '300NK',         'year_start' => 2020, 'year_end' => 2024, 'engine_cc' => 300, 'slug' => 'cfmoto-300nk'],
            // Royal Enfield
            ['make' => 'Royal Enfield', 'model' => 'Meteor 350', 'year_start' => 2021, 'year_end' => 2024, 'engine_cc' => 349, 'slug' => 'royal-enfield-meteor-350'],
        ];

        foreach ($models as $m) {
            MotorcycleModel::create(array_merge($m, ['is_active' => true]));
        }
    }
}
