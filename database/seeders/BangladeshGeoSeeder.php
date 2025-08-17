<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BangladeshGeoSeeder extends Seeder
{
    public function run(): void
    {
        $districtsPath = database_path('seeders/data/bd-districts.json');
        $upazilasPath = database_path('seeders/data/bd-upazilas.json');

        if (!File::exists($districtsPath) || !File::exists($upazilasPath)) {
            $this->command?->warn('Bangladesh geo JSON files not found. Skipping BangladeshGeoSeeder.');
            return;
        }

        $districtsJson = json_decode(File::get($districtsPath), true);
        $upazilasJson = json_decode(File::get($upazilasPath), true);

        // Normalize json roots to plain lists
        $districtsList = $this->extractList($districtsJson, ['districts','data','records','items','features']);
        $upazilasList = $this->extractList($upazilasJson, ['upazilas','data','records','items','features']);

        DB::transaction(function () use ($districtsList, $upazilasList) {
            $nameMap = [];
            $districtCount = 0;
            $thanaCount = 0;

            // Seed districts
            foreach ($districtsList as $item) {
                if (!is_array($item)) continue;
                // If geojson-like, unwrap properties
                if (isset($item['properties']) && is_array($item['properties'])) {
                    $item = $item['properties'];
                }
                $en = $this->pickString($item, ['name_en', 'en', 'english_name', 'english', 'district_en', 'district', 'district_name']);
                if (empty($en)) {
                    $en = $this->pickNested($item['name'] ?? null);
                }
                if (empty($en)) continue;

                $bn = $this->pickString($item, ['name_bn', 'bn', 'bangla_name', 'bangla']);
                $divisionEn = $this->pickString($item, ['division_en', 'division', 'division_name_en', 'division_name']);
                $divisionBn = $this->pickString($item, ['division_bn', 'division_name_bn']);
                $lat = $this->pickString($item, ['latitude', 'lat']);
                $lng = $this->pickString($item, ['longitude', 'lng', 'lon']);

                $existing = DB::table('districts')->where('name_en', $en)->first();
                if ($existing) {
                    $districtId = $existing->id;
                } else {
                    $districtId = DB::table('districts')->insertGetId([
                        'name_en' => $en,
                        'name_bn' => $bn ?: null,
                        'division_en' => $divisionEn ?: null,
                        'division_bn' => $divisionBn ?: null,
                        'latitude' => is_numeric($lat) ? $lat : null,
                        'longitude' => is_numeric($lng) ? $lng : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $districtCount++;
                }

                $nameMap[$this->normalize($en)] = $districtId;
            }

            // Seed thanas
            foreach ($upazilasList as $item) {
                if (!is_array($item)) continue;
                if (isset($item['properties']) && is_array($item['properties'])) {
                    $item = $item['properties'];
                }
                $dist = $this->pickString($item, ['district_en', 'districtEnglish', 'district_name_en', 'district_name', 'district']);
                if (empty($dist)) {
                    $dist = $this->pickNested($item['district'] ?? null);
                }
                $upazila = $this->pickString($item, ['upazila_en', 'name_en', 'english_name', 'upazila', 'name']);
                if (empty($upazila)) {
                    $upazila = $this->pickNested($item['name'] ?? null);
                }
                if (empty($dist) || empty($upazila)) continue;

                $districtId = $nameMap[$this->normalize($dist)] ?? null;
                if (!$districtId) continue;

                $bn = $this->pickString($item, ['name_bn', 'bn']);
                $lat = $this->pickString($item, ['latitude', 'lat']);
                $lng = $this->pickString($item, ['longitude', 'lng', 'lon']);

                $updated = DB::table('thanas')->updateOrInsert(
                    ['district_id' => $districtId, 'name_en' => $upazila],
                    [
                        'name_bn' => $bn ?: null,
                        'latitude' => is_numeric($lat) ? $lat : null,
                        'longitude' => is_numeric($lng) ? $lng : null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
                // updateOrInsert returns void in query builder; increment optimistically
                $thanaCount++;
            }

            if (app()->runningInConsole()) {
                $this->command?->info("BangladeshGeoSeeder: districts added: {$districtCount}, thanas processed: {$thanaCount}");
            }
        });
    }

    private function extractList($json, array $preferredKeys): array
    {
        if (is_array($json)) {
            // Already a simple list (numeric keys)
            $isList = array_keys($json) === range(0, count($json) - 1);
            if ($isList) return $json;
            // Try preferred keys
            foreach ($preferredKeys as $k) {
                if (isset($json[$k]) && is_array($json[$k])) {
                    $v = $json[$k];
                    $isList2 = array_keys($v) === range(0, count($v) - 1);
                    if ($isList2) return $v;
                }
            }
            // GeoJSON features
            if (isset($json['features']) && is_array($json['features'])) {
                return $json['features'];
            }
        }
        return [];
    }

    private function pickString(array $item, array $keys): string
    {
        foreach ($keys as $k) {
            $raw = $item[$k] ?? null;
            if ($raw === null) continue;
            if (is_string($raw)) {
                $v = trim($raw);
                if ($v !== '') return $v;
            } elseif (is_array($raw)) {
                $nested = $this->pickNested($raw);
                if ($nested !== '') return $nested;
            }
        }
        return '';
    }

    private function pickNested($v): string
    {
        if (is_string($v)) return trim($v);
        if (is_array($v)) {
            foreach (['en','name_en','english_name','english','name','title'] as $k) {
                $raw = $v[$k] ?? null;
                if (is_string($raw) && trim($raw) !== '') return trim($raw);
                if (is_array($raw)) {
                    $d = $this->pickNested($raw);
                    if ($d !== '') return $d;
                }
            }
        }
        return '';
    }

    private function normalize(string $name): string
    {
        $name = strtolower($name);
        $name = str_replace([' zila',' district','-','_'], ['','',' ',' '], $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);
        $aliases = [
            'chittagong' => 'chattogram',
            'comilla' => 'cumilla',
            'barisal' => 'barishal',
            'jessore' => 'jashore',
            'bogra' => 'bogura',
        ];
        return $aliases[$name] ?? $name;
    }
}


