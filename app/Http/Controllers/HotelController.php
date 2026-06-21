<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;
use Exception;

class HotelController extends Controller
{
    // --- SETTINGS HELPERS ---
    private function getSetting($key, $default = null)
    {
        $row = DB::table('settings')->where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    private function setSetting($key, $value)
    {
        DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value]);
    }

    private function getAllSettings()
    {
        return DB::table('settings')->pluck('value', 'key')->toArray();
    }

    // --- GUEST LANDING PAGE ---
    public function index(Request $request)
    {
        $settings = $this->getAllSettings();
        
        $checkIn = $request->input('check_in', '');
        $checkOut = $request->input('check_out', '');
        $guests = (int)$request->input('guests', 1);

        $searched = !empty($checkIn) && !empty($checkOut);
        $catalog = [];

        if ($searched) {
            $catalog = $this->getAvailableRoomsForCatalog($checkIn, $checkOut, $guests);
        } else {
            $catalog = DB::table('room_types')->orderBy('base_price', 'asc')->get()->map(function($type) {
                return [
                    'room_type' => (array)$type,
                    'available_count' => 0,
                    'available_rooms' => [],
                    'price_info' => [
                        'total_price' => $type->base_price,
                        'nights' => 1
                    ]
                ];
            })->toArray();
        }

        return view('index', [
            'settings' => $settings,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'guests' => $guests,
            'searched' => $searched,
            'catalog' => $catalog
        ]);
    }

    // --- ADMIN DASHBOARD ---
    public function admin(Request $request)
    {
        $settings = $this->getAllSettings();
        $roomTypes = DB::table('room_types')->orderBy('base_price', 'asc')->get();
        
        $rooms = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('rooms.*', 'room_types.name as room_type_name')
            ->orderBy('rooms.room_number', 'asc')
            ->get();

        $pricingRules = DB::table('dynamic_pricings')
            ->join('room_types', 'dynamic_pricings.room_type_id', '=', 'room_types.id')
            ->select('dynamic_pricings.*', 'room_types.name as room_type_name')
            ->orderBy('dynamic_pricings.id', 'desc')
            ->get();

        // Separate Reservations and Blockings
        $reservations = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('bookings.booking_type', 'reservation')
            ->select('bookings.*', 'rooms.room_number', 'room_types.name as room_type_name')
            ->orderBy('bookings.check_in', 'desc')
            ->get();

        $blockings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('bookings.booking_type', 'blocking')
            ->select('bookings.*', 'rooms.room_number', 'room_types.name as room_type_name')
            ->orderBy('bookings.check_in', 'desc')
            ->get();

        $currentTab = $request->input('tab', 'dashboard');

        return view('admin', [
            'settings' => $settings,
            'roomTypes' => $roomTypes,
            'rooms' => $rooms,
            'pricingRules' => $pricingRules,
            'reservations' => $reservations,
            'blockings' => $blockings,
            'currentTab' => $currentTab
        ]);
    }

    // --- PRICING CALCULATION LOGIC ---
    public function calculatePrice($room_type_id, $check_in, $check_out)
    {
        $roomType = DB::table('room_types')->where('id', $room_type_id)->first();
        if (!$roomType) return ['total_price' => 0, 'breakdown' => [], 'nights' => 0];

        $basePrice = $roomType->base_price;
        $rules = DB::table('dynamic_pricings')->where('room_type_id', $room_type_id)->get();

        $weekendRule = $rules->where('type', 'weekend')->first();
        $customRules = $rules->where('type', 'custom_date')->all();

        $startDate = new DateTime($check_in);
        $endDate = new DateTime($check_out);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($startDate, $interval, $endDate);

        $totalPrice = 0;
        $breakdown = [];

        foreach ($period as $date) {
            $currentDateStr = $date->format('Y-m-d');
            $dayOfWeek = (int)$date->format('N');
            $isWeekend = ($dayOfWeek === 5 || $dayOfWeek === 6 || $dayOfWeek === 7);

            $nightPrice = $basePrice;
            $appliedRule = 'Base Price';

            $customApplied = false;
            foreach ($customRules as $rule) {
                if ($currentDateStr >= $rule->start_date && $currentDateStr <= $rule->end_date) {
                    if ($rule->fixed_price !== null) {
                        $nightPrice = $rule->fixed_price;
                    } elseif ($rule->multiplier !== null) {
                        $nightPrice = $basePrice * $rule->multiplier;
                    }
                    $appliedRule = 'Custom Holiday Rate (' . $rule->start_date . ' to ' . $rule->end_date . ')';
                    $customApplied = true;
                    break;
                }
            }

            if (!$customApplied && $isWeekend && $weekendRule) {
                if ($weekendRule->fixed_price !== null) {
                    $nightPrice = $weekendRule->fixed_price;
                } elseif ($weekendRule->multiplier !== null) {
                    $nightPrice = $basePrice * $weekendRule->multiplier;
                }
                $appliedRule = 'Weekend Rate';
            }

            $totalPrice += $nightPrice;
            $breakdown[] = [
                'date' => $currentDateStr,
                'price' => $nightPrice,
                'rule' => $appliedRule
            ];
        }

        return [
            'total_price' => $totalPrice,
            'breakdown' => $breakdown,
            'nights' => count($breakdown)
        ];
    }

    // --- AVAILABILITY LOGIC ---
    public function getAvailableRoomsForCatalog($check_in, $check_out, $guests)
    {
        $roomTypes = DB::table('room_types')->get();
        $availableCatalog = [];

        foreach ($roomTypes as $type) {
            if ($guests > $type->capacity) {
                continue;
            }

            $availableRooms = $this->getAvailablePhysicalRooms($type->id, $check_in, $check_out);
            $count = count($availableRooms);

            $priceInfo = $this->calculatePrice($type->id, $check_in, $check_out);

            $availableCatalog[] = [
                'room_type' => (array)$type,
                'available_count' => $count,
                'available_rooms' => $availableRooms,
                'price_info' => $priceInfo
            ];
        }

        return $availableCatalog;
    }

    public function getAvailablePhysicalRooms($room_type_id, $check_in, $check_out, $excludeBookingId = null)
    {
        $query = DB::table('rooms')
            ->where('room_type_id', $room_type_id)
            ->whereNotIn('id', function($q) use ($check_in, $check_out, $excludeBookingId) {
                $q->select('room_id')
                  ->from('bookings')
                  ->where(function($inner) use ($check_in, $check_out) {
                      $inner->where('check_in', '<', $check_out)
                            ->where(DB::raw("CASE WHEN status = 'checked_out' THEN actual_checkout ELSE check_out END"), '>', $check_in);
                  });
                if ($excludeBookingId !== null) {
                    $q->where('id', '!=', $excludeBookingId);
                }
            });

        return $query->get()->toArray();
    }

    public function getAvailableRoomsForEdit(Request $request)
    {
        $bookingId = $request->input('booking_id');
        
        $booking = DB::table('bookings')->where('id', $bookingId)->first();
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // Get all physical rooms that are available on this booking's dates, excluding this booking itself
        $availableRooms = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->whereNotIn('rooms.id', function($q) use ($booking) {
                $q->select('room_id')
                  ->from('bookings')
                  ->where('id', '!=', $booking->id)
                  ->where('check_in', '<', $booking->check_out)
                  ->where(DB::raw("CASE WHEN status = 'checked_out' THEN actual_checkout ELSE check_out END"), '>', $booking->check_in);
            })
            ->select('rooms.*', 'room_types.name as room_type_name')
            ->orderBy('room_types.name', 'asc')
            ->orderBy('rooms.room_number', 'asc')
            ->get();

        return response()->json([
            'current_room_id' => $booking->room_id,
            'available_rooms' => $availableRooms
        ]);
    }

    // --- ACTIONS STORE / UPDATE ---
    public function updateSettings(Request $request)
    {
        $this->setSetting('hotel_name', $request->input('hotel_name'));
        $this->setSetting('x_days_limit', $request->input('x_days_limit'));
        $this->setSetting('admin_phone', $request->input('admin_phone'));
        $this->setSetting('hotel_about', $request->input('hotel_about'));
        $this->setSetting('hotel_vision', $request->input('hotel_vision'));
        $this->setSetting('hotel_mission', $request->input('hotel_mission'));
        $this->setSetting('hotel_address', $request->input('hotel_address'));

        return redirect()->route('admin', ['tab' => 'settings'])->with('message', 'Settings updated successfully.');
    }

    public function storeRoomType(Request $request)
    {
        // Parse currency input
        $basePrice = (float)str_replace(['Rp', '.', ' '], '', $request->input('base_price'));

        DB::table('room_types')->insert([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'capacity' => (int)$request->input('capacity'),
            'base_price' => $basePrice,
            'amenities' => $request->input('amenities'),
            'images' => $request->input('images'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin', ['tab' => 'rooms'])->with('message', 'Room type created successfully.');
    }

    public function deleteRoomType($id)
    {
        DB::table('room_types')->where('id', $id)->delete();
        return redirect()->route('admin', ['tab' => 'rooms'])->with('message', 'Room type deleted successfully.');
    }

    public function storeRoom(Request $request)
    {
        DB::table('rooms')->insert([
            'room_number' => $request->input('room_number'),
            'room_type_id' => (int)$request->input('room_type_id'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin', ['tab' => 'rooms'])->with('message', 'Physical room created successfully.');
    }

    public function deleteRoom($id)
    {
        DB::table('rooms')->where('id', $id)->delete();
        return redirect()->route('admin', ['tab' => 'rooms'])->with('message', 'Physical room deleted successfully.');
    }

    public function storePricingRule(Request $request)
    {
        $roomTypeId = (int)$request->input('room_type_id');
        $type = $request->input('type');
        
        $multiplier = $request->filled('multiplier') ? (float)$request->input('multiplier') : null;
        
        $fixedPriceRaw = $request->input('fixed_price');
        $fixedPrice = $request->filled('fixed_price') ? (float)str_replace(['Rp', '.', ' '], '', $fixedPriceRaw) : null;
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($type === 'weekend') {
            DB::table('dynamic_pricings')->updateOrInsert(
                ['room_type_id' => $roomTypeId, 'type' => 'weekend'],
                [
                    'multiplier' => $multiplier ?? 1.00,
                    'fixed_price' => $fixedPrice,
                    'start_date' => null,
                    'end_date' => null,
                    'updated_at' => now()
                ]
            );
        } else {
            DB::table('dynamic_pricings')->insert([
                'room_type_id' => $roomTypeId,
                'type' => 'custom_date',
                'multiplier' => $multiplier ?? 1.00,
                'fixed_price' => $fixedPrice,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->route('admin', ['tab' => 'pricing'])->with('message', 'Pricing rule applied successfully.');
    }

    public function deletePricingRule($id)
    {
        DB::table('dynamic_pricings')->where('id', $id)->delete();
        return redirect()->route('admin', ['tab' => 'pricing'])->with('message', 'Pricing rule removed.');
    }

    public function storeBooking(Request $request)
    {
        $roomId = (int)$request->input('room_id');
        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $type = $request->input('booking_type', 'reservation');

        // Double check availability
        $overlapping = DB::table('bookings')
            ->where('room_id', $roomId)
            ->where('check_in', '<', $checkOut)
            ->where(DB::raw("CASE WHEN status = 'checked_out' THEN actual_checkout ELSE check_out END"), '>', $checkIn)
            ->first();

        if ($overlapping) {
            return redirect()->back()->with('error', 'The room is already booked or blocked on those dates.');
        }

        $totalPrice = 0;
        if ($type === 'reservation') {
            // Find room type
            $room = DB::table('rooms')->where('id', $roomId)->first();
            $priceInfo = $this->calculatePrice($room->room_type_id, $checkIn, $checkOut);
            $totalPrice = $priceInfo['total_price'];
        }

        DB::table('bookings')->insert([
            'room_id' => $roomId,
            'guest_name' => $type === 'reservation' ? $request->input('guest_name') : 'BLOCKING',
            'guest_phone' => $type === 'reservation' ? $request->input('guest_phone') : '-',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'total_price' => $totalPrice,
            'notes' => $request->input('notes'),
            'booking_type' => $type,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $tab = $type === 'reservation' ? 'dashboard' : 'blockings';
        return redirect()->route('admin', ['tab' => $tab])->with('message', 'Manual entry registered successfully.');
    }

    public function deleteBooking($id, Request $request)
    {
        DB::table('bookings')->where('id', $id)->delete();
        return redirect()->back()->with('message', 'Entry removed successfully.');
    }

    public function changeBookingRoom(Request $request)
    {
        $bookingId = (int)$request->input('booking_id');
        $newRoomId = (int)$request->input('new_room_id');

        $booking = DB::table('bookings')->where('id', $bookingId)->first();
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking record not found.');
        }

        // Verify availability excluding this booking itself
        $overlapping = DB::table('bookings')
            ->where('room_id', $newRoomId)
            ->where('id', '!=', $bookingId)
            ->where('check_in', '<', $booking->check_out)
            ->where(DB::raw("CASE WHEN status = 'checked_out' THEN actual_checkout ELSE check_out END"), '>', $booking->check_in)
            ->first();

        if ($overlapping) {
            return redirect()->back()->with('error', 'The target room is already booked/blocked on those dates.');
        }

        $totalPrice = $booking->total_price;
        if ($booking->booking_type === 'reservation') {
            $room = DB::table('rooms')->where('id', $newRoomId)->first();
            $priceInfo = $this->calculatePrice($room->room_type_id, $booking->check_in, $booking->check_out);
            $totalPrice = $priceInfo['total_price'];
        }

        DB::table('bookings')->where('id', $bookingId)->update([
            'room_id' => $newRoomId,
            'total_price' => $totalPrice,
            'updated_at' => now()
        ]);

        $tab = $booking->booking_type === 'reservation' ? 'dashboard' : 'blockings';
        return redirect()->route('admin', ['tab' => $tab])->with('message', 'Room reassigned successfully.');
    }

    public function checkoutBooking($id)
    {
        $booking = DB::table('bookings')->where('id', $id)->first();
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking record not found.');
        }

        $today = date('Y-m-d');

        DB::table('bookings')->where('id', $id)->update([
            'status' => 'checked_out',
            'actual_checkout' => $today,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('message', 'Guest checked out successfully. Room is now available.');
    }

    public function getRoomAvailabilityGrid(Request $request)
    {
        $check_in = $request->input('check_in');
        $check_out = $request->input('check_out');

        if (!$check_in || !$check_out) {
            return response()->json(['error' => 'Please select check-in and check-out dates.'], 400);
        }

        $rooms = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('rooms.*', 'room_types.name as room_type_name')
            ->orderBy('rooms.room_number', 'asc')
            ->get();

        $result = [];

        foreach ($rooms as $room) {
            // Find any overlapping bookings/blockings for this room
            $booking = DB::table('bookings')
                ->where('room_id', $room->id)
                ->where('check_in', '<', $check_out)
                ->where(DB::raw("CASE WHEN status = 'checked_out' THEN actual_checkout ELSE check_out END"), '>', $check_in)
                ->first();

            if ($booking) {
                if ($booking->booking_type === 'blocking') {
                    $result[] = [
                        'room' => $room,
                        'status' => 'blocked',
                        'notes' => $booking->notes,
                        'check_in' => $booking->check_in,
                        'check_out' => $booking->check_out
                    ];
                } else {
                    $result[] = [
                        'room' => $room,
                        'status' => 'booked',
                        'guest_name' => $booking->guest_name,
                        'guest_phone' => $booking->guest_phone,
                        'booking_status' => $booking->status,
                        'check_in' => $booking->check_in,
                        'check_out' => $booking->check_out
                    ];
                }
            } else {
                $result[] = [
                    'room' => $room,
                    'status' => 'available'
                ];
            }
        }

        return response()->json($result);
    }
}
