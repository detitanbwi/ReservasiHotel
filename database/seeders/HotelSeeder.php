<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Settings
        $settings = [
            ['key' => 'x_days_limit', 'value' => '30'],
            ['key' => 'admin_phone', 'value' => '6281234567890'],
            ['key' => 'hotel_name', 'value' => 'The Bronze Oasis Resort'],
            ['key' => 'hotel_about', 'value' => 'Welcome to The Bronze Oasis Resort, a sanctuary of peace, elegance, and luxurious minimalism. Tucked away from the bustling city, our property offers bespoke services and high-end room options designed for discerning travelers seeking rest and rejuvenation.'],
            ['key' => 'hotel_vision', 'value' => 'To define the peak of independent boutique hospitality through exquisite design and personal touch.'],
            ['key' => 'hotel_mission', 'value' => 'Delivering comfort, peace of mind, and modern visual design while preserving human-to-human hospitality.'],
            ['key' => 'hotel_address', 'value' => 'Jl. Luxury Heights No. 88, Kuta, Bali'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(['key' => $setting['key']], ['value' => $setting['value']]);
        }

        // Room Types
        $roomTypes = [
            [
                'id' => 1,
                'name' => 'Deluxe Suite Room',
                'description' => 'A spacious suite featuring high-end furnishings, a king-size bed, private balcony, and state-of-the-art bathroom.',
                'capacity' => 2,
                'base_price' => 750000,
                'amenities' => 'King Bed, Balcony, Mini Bar, Free Wi-Fi, Coffee Maker, Bathtub'
            ],
            [
                'id' => 2,
                'name' => 'Executive Suite',
                'description' => 'Designed for maximum comfort and business stays, includes a separated living area, desk, and gorgeous sunset view.',
                'capacity' => 2,
                'base_price' => 1200000,
                'amenities' => 'King Bed, Separate Living Area, High-speed Wi-Fi, Bathtub, Smart TV, Premium Coffee'
            ],
            [
                'id' => 3,
                'name' => 'Presidential Penthouse',
                'description' => 'The ultimate luxury experience. Full kitchen, private plunge pool, 360 panoramic views, and premium personalized service.',
                'capacity' => 4,
                'base_price' => 3500000,
                'amenities' => '2 King Beds, Private Pool, Full Kitchen, Panoramic View, 24/7 Butler, Wine Cellar'
            ]
        ];

        foreach ($roomTypes as $type) {
            DB::table('room_types')->updateOrInsert(['id' => $type['id']], $type);
        }

        // Rooms
        $rooms = [
            ['id' => 1, 'room_number' => 'Room 101', 'room_type_id' => 1],
            ['id' => 2, 'room_number' => 'Room 102', 'room_type_id' => 1],
            ['id' => 3, 'room_number' => 'Room 103', 'room_type_id' => 1],
            ['id' => 4, 'room_number' => 'Room 201', 'room_type_id' => 2],
            ['id' => 5, 'room_number' => 'Room 202', 'room_type_id' => 2],
            ['id' => 6, 'room_number' => 'Penthouse 501', 'room_type_id' => 3],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->updateOrInsert(['id' => $room['id']], $room);
        }

        // Weekend Pricing rules
        $pricings = [
            ['id' => 1, 'room_type_id' => 1, 'type' => 'weekend', 'multiplier' => 1.15, 'fixed_price' => null, 'start_date' => null, 'end_date' => null],
            ['id' => 2, 'room_type_id' => 2, 'type' => 'weekend', 'multiplier' => 1.15, 'fixed_price' => null, 'start_date' => null, 'end_date' => null],
            ['id' => 3, 'room_type_id' => 3, 'type' => 'weekend', 'multiplier' => 1.20, 'fixed_price' => null, 'start_date' => null, 'end_date' => null],
        ];

        foreach ($pricings as $pricing) {
            DB::table('dynamic_pricings')->updateOrInsert(['id' => $pricing['id']], $pricing);
        }
    }
}
