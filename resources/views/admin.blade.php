<?php
use App\Helpers\Icons;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ $settings['hotel_name'] ?? 'Hotel Resort' }}</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}?v={{ time() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('calendar.js') }}?v={{ time() }}"></script>
</head>
<body>

    <!-- Mobile Admin Header -->
    <header class="admin-mobile-header">
        <div class="logo-wrapper">
            {!! Icons::getLogo('#ffffff', 28) !!}
            <span class="logo-text" style="font-size: 1rem; text-transform: uppercase; font-weight:700;">Admin Panel</span>
        </div>
        <button class="hamburger-toggle" onclick="toggleAdminSidebar()">
            &#9776;
        </button>
    </header>

    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-logo">
                {!! Icons::getLogo('#ffffff', 32) !!}
                <span class="logo-text" style="font-size: 1.1rem; color: #ffffff;">Admin Panel</span>
            </div>
            <ul class="admin-menu">
                <li class="admin-menu-item {{ $currentTab === 'dashboard' ? 'active' : '' }}">
                    <a href="{{ route('admin', ['tab' => 'dashboard']) }}">
                        {!! Icons::getUserGear('#ffffff', 18) !!} Guest Reservations
                    </a>
                </li>
                <li class="admin-menu-item {{ $currentTab === 'blockings' ? 'active' : '' }}">
                    <a href="{{ route('admin', ['tab' => 'blockings']) }}">
                        {!! Icons::getShieldCheck('#ffffff', 18) !!} Room Blockings
                    </a>
                </li>
                <li class="admin-menu-item {{ $currentTab === 'rooms' ? 'active' : '' }}">
                    <a href="{{ route('admin', ['tab' => 'rooms']) }}">
                        {!! Icons::getBed('#ffffff', 18) !!} Room Management
                    </a>
                </li>
                <li class="admin-menu-item {{ $currentTab === 'pricing' ? 'active' : '' }}">
                    <a href="{{ route('admin', ['tab' => 'pricing']) }}">
                        {!! Icons::getPricingChart('#ffffff', 18) !!} Dynamic Pricing
                    </a>
                </li>
                <li class="admin-menu-item {{ $currentTab === 'settings' ? 'active' : '' }}">
                    <a href="{{ route('admin', ['tab' => 'settings']) }}">
                        {!! Icons::getClock('#ffffff', 18) !!} Global Settings
                    </a>
                </li>
                <li class="admin-menu-item" style="margin-top: 40px;">
                    <a href="{{ route('index') }}" target="_blank" style="border: 1px solid rgba(255,255,255,0.2);">
                        View Live Website
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-content">
            <!-- Header -->
            <div class="admin-header">
                <div style="display: flex; align-items: center; gap: 15px; width: 100%; flex-wrap: wrap;">
                    <button type="button" class="sidebar-toggle-desktop-btn" onclick="toggleSidebarDesktop()" style="font-size: 1.4rem; padding: 6px 12px; border: 1px solid var(--color-bronze-light); background: var(--color-bg-alt); border-radius: 6px; cursor: pointer; color: var(--color-text-dark); display: flex; align-items: center; justify-content: center; transition: var(--transition-smooth);">
                        &#9776;
                    </button>
                    
                    <div class="admin-header-logo" style="display: flex; align-items: center; gap: 10px; margin-right: 15px; border-right: 1px solid var(--color-bronze-light); padding-right: 15px; height: 36px;">
                        {!! Icons::getLogo('#241d01', 28) !!}
                        <span class="logo-text" style="color: var(--color-text-dark); font-size: 1.1rem; font-weight: 700; text-transform: uppercase;">{{ $settings['hotel_name'] ?? 'Hotel Resort' }}</span>
                    </div>

                    <div style="flex-grow: 1; min-width: 200px;">
                        <h1 style="font-size: 1.6rem; color: var(--color-text-dark); margin: 0; line-height: 1.2;">
                            @switch($currentTab)
                                @case('rooms') Room & Type Management @break
                                @case('pricing') Dynamic Pricing Rules @break
                                @case('blockings') Maintenance Room Blockings @break
                                @case('settings') Global Configurations @break
                                @default Guest Reservations Overview
                            @endswitch
                        </h1>
                        <div class="admin-title-desc" style="margin-top: 2px;">Manage your property, custom pricing rules, and reservation blockings.</div>
                    </div>
                </div>
            </div>

            <!-- Status Alerts -->
            @if(session('message'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: @json(session('message')),
                            confirmButtonColor: '#b19453'
                        });
                    });
                </script>
            @endif
            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: @json(session('error')),
                            confirmButtonColor: '#b19453'
                        });
                    });
                </script>
            @endif

            <!-- Reservations Tab (Dashboard) -->
            @if ($currentTab === 'dashboard')
                <div class="admin-stacked-layout">
                    <!-- Manual Reservation Form -->
                    <div class="admin-card">
                        <div class="admin-card-title">
                            {!! Icons::getCalendar('#b19453', 20) !!} Input Guest Reservation
                        </div>
                        <form action="{{ route('admin.booking.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_type" value="reservation">
                            
                            <input type="hidden" name="check_in" id="res_check_in" required>
                            <input type="hidden" name="check_out" id="res_check_out" required>
                            
                            <div class="admin-form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 15px;">
                                <div class="form-group">
                                    <label>Guest Name</label>
                                    <input type="text" name="guest_name" class="form-control" placeholder="e.g. John Doe" required>
                                </div>
                                <div class="form-group">
                                    <label>Guest WhatsApp</label>
                                    <input type="text" name="guest_phone" class="form-control" placeholder="e.g. 62812345678" required>
                                </div>
                                <div class="form-group">
                                    <label>Stay Dates</label>
                                    <div id="resDateRangePicker"></div>
                                </div>
                                <div class="form-group">
                                    <label>Assigned Room</label>
                                    <input type="hidden" name="room_id" id="selected_room_id" required>
                                    <div style="display: flex; gap: 8px;">
                                        <input type="text" id="selected_room_display" class="form-control" placeholder="Select dates first, then choose a room..." readonly required style="flex-grow: 1;">
                                        <button type="button" class="btn-primary" onclick="openRoomSelectionModal()" style="white-space: nowrap; padding: 10px 16px;">
                                            Select Room
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Any requests..."></textarea>
                            </div>
                            <button type="submit" class="btn-primary" style="width:100%;">Create Guest Reservation</button>
                        </form>
                    </div>

                    <!-- Active Reservations Table -->
                    <div class="admin-card">
                        <div class="admin-card-title">
                            {!! Icons::getShieldCheck('#b19453', 20) !!} Registered Guest Bookings
                        </div>
                        <div class="booking-filter-bar">
                            <span class="booking-filter-title">Filter list by stay dates:</span>
                            <div class="filter-picker-wrapper">
                                <div id="bookingListFilterPicker"></div>
                            </div>
                            <button type="button" class="btn-clear-filter" onclick="clearBookingFilter()">
                                Clear Filter
                            </button>
                        </div>
                        <div class="admin-table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Room Type</th>
                                        <th>Guest Name</th>
                                        <th>WA Phone</th>
                                        <th>Date Range</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($reservations->isEmpty())
                                        <tr>
                                            <td colspan="8" style="text-align: center; color: var(--color-text-muted);">No active reservations registered.</td>
                                        </tr>
                                    @else
                                        @foreach ($reservations as $b)
                                            <tr class="booking-row" data-check-in="{{ $b->check_in }}" data-check-out="{{ $b->status === 'checked_out' ? $b->actual_checkout : $b->check_out }}">
                                                <td><strong>{{ $b->room_number }}</strong></td>
                                                <td><span style="font-size: 0.85rem; color: var(--color-text-muted);">{{ $b->room_type_name }}</span></td>
                                                <td><strong>{{ $b->guest_name }}</strong></td>
                                                <td>{{ $b->guest_phone }}</td>
                                                <td><span style="font-size:0.85rem;">{{ date('d/m/y', strtotime($b->check_in)) }} - {{ date('d/m/y', strtotime($b->check_out)) }}</span></td>
                                                <td><strong>Rp {{ number_format($b->total_price, 0, ',', '.') }}</strong></td>
                                                <td>
                                                    @if ($b->status === 'checked_in')
                                                        <span class="badge" style="background-color: #2e7d32; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Checked In</span>
                                                    @else
                                                        <span class="badge" style="background-color: #757575; color: #ffffff; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Checked Out</span>
                                                        @if ($b->actual_checkout)
                                                            <div style="font-size: 0.7rem; color: var(--color-text-muted); margin-top: 2px;">Out: {{ date('d/m/y', strtotime($b->actual_checkout)) }}</div>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    <div style="display:flex; gap: 8px; flex-wrap: wrap;">
                                                        @if ($b->status === 'checked_in')
                                                            <button class="action-btn action-btn-primary" onclick="openEditRoomModal({{ $b->id }}, '{{ $b->guest_name }}')">
                                                                Move Room
                                                            </button>
                                                            <form id="checkout-form-{{ $b->id }}" action="{{ route('admin.booking.checkout', $b->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="button" class="action-btn action-btn-success" style="background-color: #2e7d32;" onclick="confirmCheckout({{ $b->id }}, '{{ $b->guest_name }}')">
                                                                    Check Out
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form id="delete-form-{{ $b->id }}" action="{{ route('admin.booking.delete', $b->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="button" class="action-btn action-btn-danger" onclick="confirmCancel({{ $b->id }}, '{{ $b->guest_name }}')">
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <!-- Room Blockings Tab -->
            @elseif ($currentTab === 'blockings')
                <div class="admin-stacked-layout">
                    <!-- Room Blocking Form -->
                    <div class="admin-card">
                        <div class="admin-card-title">
                            {!! Icons::getShieldCheck('#b19453', 20) !!} Block Room (Offline/Maintenance)
                        </div>
                        <form action="{{ route('admin.booking.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_type" value="blocking">
                            
                            <input type="hidden" name="check_in" id="block_check_in" required>
                            <input type="hidden" name="check_out" id="block_check_out" required>

                            <div class="admin-form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 15px;">
                                <div class="form-group">
                                    <label>Select Room to Block</label>
                                    <select name="room_id" class="form-control" required>
                                        @foreach ($rooms as $r)
                                            <option value="{{ $r->id }}">{{ $r->room_number }} ({{ $r->room_type_name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Block Dates</label>
                                    <div id="blockDateRangePicker"></div>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>Reason / Notes</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Maintenance, out of service, private use, etc." required></textarea>
                            </div>
                            <button type="submit" class="btn-primary" style="width:100%;">Create Room Block</button>
                        </form>
                    </div>

                    <!-- Active Blockings Table -->
                    <div class="admin-card">
                        <div class="admin-card-title">
                            {!! Icons::getShieldCheck('#b19453', 20) !!} Active Room Blockings
                        </div>
                        <div class="admin-table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Room Number</th>
                                        <th>Room Type</th>
                                        <th>Block Dates</th>
                                        <th>Reason / Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($blockings->isEmpty())
                                        <tr>
                                            <td colspan="5" style="text-align: center; color: var(--color-text-muted);">No active offline/maintenance blocks.</td>
                                        </tr>
                                    @else
                                        @foreach ($blockings as $b)
                                            <tr>
                                                <td><strong>{{ $b->room_number }}</strong></td>
                                                <td>{{ $b->room_type_name }}</td>
                                                <td><strong>{{ date('d M Y', strtotime($b->check_in)) }} to {{ date('d M Y', strtotime($b->check_out)) }}</strong></td>
                                                <td><span style="font-size:0.9rem; color: var(--color-text-muted);">{{ $b->notes }}</span></td>
                                                <td>
                                                    <form id="unblock-form-{{ $b->id }}" action="{{ route('admin.booking.delete', $b->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="button" class="action-btn action-btn-danger" onclick="confirmUnblock({{ $b->id }}, '{{ $b->room_number }}')">
                                                            Unblock
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <!-- Room CRUD Tab -->
            @elseif ($currentTab === 'rooms')
                <div class="admin-grid-layout split-equal">
                    <div>
                        <div class="admin-card">
                            <div class="admin-card-title">Add Room Type</div>
                            <form action="{{ route('admin.room-type.store') }}" method="POST">
                                @csrf
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Type Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Deluxe Room" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Base Price per Night</label>
                                    <div class="currency-input-wrapper">
                                        <span class="currency-prefix">Rp</span>
                                        <input type="text" name="base_price" class="form-control currency-format" placeholder="750.000" required>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Max Pax / Capacity</label>
                                    <input type="number" name="capacity" class="form-control" placeholder="2" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Amenities (Comma separated)</label>
                                    <input type="text" name="amenities" class="form-control" placeholder="Wi-Fi, AC, Balcony" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Room Images (Comma-separated URLs)</label>
                                    <textarea name="images" class="form-control" rows="2" placeholder="https://example.com/img1.jpg, https://example.com/img2.jpg"></textarea>
                                </div>
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Description of the room..." required></textarea>
                                </div>
                                <button type="submit" class="btn-primary" style="width:100%;">Create Room Type</button>
                            </form>
                        </div>

                        <div class="admin-card">
                            <div class="admin-card-title">Add Physical Room</div>
                            <form action="{{ route('admin.room.store') }}" method="POST">
                                @csrf
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Room Number / Label</label>
                                    <input type="text" name="room_number" class="form-control" placeholder="Room 101" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label>Room Type</label>
                                    <select name="room_type_id" class="form-control" required>
                                        @foreach ($roomTypes as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn-primary" style="width:100%;">Add Physical Room</button>
                            </form>
                        </div>
                    </div>

                    <div>
                        <div class="admin-card">
                            <div class="admin-card-title">Room Types</div>
                            <div class="admin-table-wrapper">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Capacity</th>
                                            <th>Base Price</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roomTypes as $t)
                                            <tr>
                                                <td><strong>{{ $t->name }}</strong></td>
                                                <td>{{ $t->capacity }} Pax</td>
                                                <td>Rp {{ number_format($t->base_price, 0, ',', '.') }}</td>
                                                <td>
                                                    <form id="delete-roomtype-form-{{ $t->id }}" action="{{ route('admin.room-type.delete', $t->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="button" style="color: #dc3545; background: none; border:none; cursor:pointer; font-weight:600;" onclick="confirmDeleteRoomType({{ $t->id }}, '{{ $t->name }}')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="admin-card">
                            <div class="admin-card-title">Physical Rooms Map</div>
                            <div class="admin-table-wrapper">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Room Number</th>
                                            <th>Room Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rooms as $r)
                                            <tr>
                                                <td><strong>{{ $r->room_number }}</strong></td>
                                                <td>{{ $r->room_type_name }}</td>
                                                <td>
                                                    <form id="delete-room-form-{{ $r->id }}" action="{{ route('admin.room.delete', $r->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="button" style="color: #dc3545; background: none; border:none; cursor:pointer; font-weight:600;" onclick="confirmDeleteRoom({{ $r->id }}, '{{ $r->room_number }}')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Dynamic Pricing Tab -->
            @elseif ($currentTab === 'pricing')
                <div class="admin-grid-layout split-equal">
                    <div>
                        <div class="admin-card">
                            <div class="admin-card-title">Create / Update Pricing Rule</div>
                            <form action="{{ route('admin.pricing.store') }}" method="POST">
                                @csrf
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Target Room Type</label>
                                    <select name="room_type_id" class="form-control" required>
                                        @foreach ($roomTypes as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Rule Type</label>
                                    <select name="type" id="rule_type_select" class="form-control" onchange="toggleRuleFields()" required>
                                        <option value="weekend">Weekend Rate (Fri, Sat, Sun)</option>
                                        <option value="custom_date">Custom Date Range (High Season)</option>
                                    </select>
                                </div>
                                
                                <div id="custom_date_fields" style="display:none; border: 1px solid var(--color-bronze-light); padding: 15px; border-radius: 6px; margin-bottom: 15px; background: var(--color-bg-alt);">
                                    <div class="form-group" style="margin-bottom: 10px;">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label>Multiplier Adjustment (e.g. 1.15 = 15% Increase)</label>
                                    <input type="number" step="0.01" name="multiplier" class="form-control" placeholder="1.15">
                                </div>
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label>OR Set Fixed Flat Price</label>
                                    <div class="currency-input-wrapper">
                                        <span class="currency-prefix">Rp</span>
                                        <input type="text" name="fixed_price" class="form-control currency-format" placeholder="900.000">
                                    </div>
                                    <small style="color: var(--color-text-muted);">Fixed price overrides multiplier if both are filled.</small>
                                </div>
                                
                                <button type="submit" class="btn-primary" style="width:100%;">Apply Pricing Rule</button>
                            </form>
                        </div>
                    </div>

                    <div>
                        <div class="admin-card">
                            <div class="admin-card-title">Applied Dynamic Pricing Rules</div>
                            <div class="admin-table-wrapper">
                                <table class="admin-table">
                                    <thead>
                                        <tr>
                                            <th>Room Type</th>
                                            <th>Type</th>
                                            <th>Adjustments</th>
                                            <th>Validity Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($pricingRules->isEmpty())
                                            <tr>
                                                <td colspan="5" style="text-align:center; color: var(--color-text-muted);">No custom rules defined yet.</td>
                                            </tr>
                                        @else
                                            @foreach ($pricingRules as $rule)
                                                <tr>
                                                    <td><strong>{{ $rule->room_type_name }}</strong></td>
                                                    <td>
                                                        <span class="badge {{ $rule->type === 'weekend' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ strtoupper($rule->type) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($rule->fixed_price !== null)
                                                            Flat Rp {{ number_format($rule->fixed_price, 0, ',', '.') }}
                                                        @else
                                                            x{{ $rule->multiplier }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($rule->type === 'weekend')
                                                            Fri - Sun nights (Auto)
                                                        @else
                                                            {{ $rule->start_date }} to {{ $rule->end_date }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <form id="delete-pricing-form-{{ $rule->id }}" action="{{ route('admin.pricing.delete', $rule->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="button" style="color: #dc3545; background: none; border:none; cursor:pointer; font-weight:600;" onclick="confirmDeletePricing({{ $rule->id }})">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                    function toggleRuleFields() {
                        const select = document.getElementById('rule_type_select').value;
                        const dateBox = document.getElementById('custom_date_fields');
                        if (select === 'custom_date') {
                            dateBox.style.display = 'block';
                        } else {
                            dateBox.style.display = 'none';
                        }
                    }
                </script>

            <!-- Global Configurations Tab -->
            @elseif ($currentTab === 'settings')
                <div class="admin-card" style="max-width: 700px;">
                    <div class="admin-card-title">General Property Settings</div>
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label>Hotel / Property Name</label>
                            <input type="text" name="hotel_name" class="form-control" value="{{ $settings['hotel_name'] ?? '' }}" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label>Calendar Booking Window limit (X Days)</label>
                            <input type="number" name="x_days_limit" class="form-control" value="{{ $settings['x_days_limit'] ?? '30' }}" required>
                            <small style="color: var(--color-text-muted);">Restricts guests from checking availability past X days from today.</small>
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label>Admin WhatsApp Number (Include country code without +)</label>
                            <input type="text" name="admin_phone" class="form-control" value="{{ $settings['admin_phone'] ?? '' }}" required placeholder="62812345678">
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label>Property Address</label>
                            <input type="text" name="hotel_address" class="form-control" value="{{ $settings['hotel_address'] ?? '' }}" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label>About Us Description</label>
                            <textarea name="hotel_about" class="form-control" rows="4" required>{{ $settings['hotel_about'] ?? '' }}</textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label>Property Vision</label>
                            <input type="text" name="hotel_vision" class="form-control" value="{{ $settings['hotel_vision'] ?? '' }}" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 25px;">
                            <label>Property Mission</label>
                            <input type="text" name="hotel_mission" class="form-control" value="{{ $settings['hotel_mission'] ?? '' }}" required>
                        </div>

                        <button type="submit" class="btn-primary">Save Settings</button>
                    </form>
                </div>
            @endif

        </main>
    </div>

    <!-- Edit Reservation Room Modal -->
    <div class="modal-overlay" id="editRoomModal">
        <div class="modal-content" style="max-width: 480px;">
            <button class="modal-close" onclick="closeEditRoomModal()">&times;</button>
            <h3 class="modal-title">Change Assigned Room</h3>
            <p class="modal-desc" style="margin-bottom: 20px;">Move <strong id="editGuestName">Guest</strong> to another available room.</p>
            
            <form action="{{ route('admin.booking.change-room') }}" method="POST">
                @csrf
                <input type="hidden" name="booking_id" id="editBookingId">
                
                <div class="form-group" style="margin-bottom: 25px;">
                    <label>Select Alternative Available Room</label>
                    <select name="new_room_id" id="editAvailableRoomsSelect" class="form-control" required>
                        <!-- Dynamic Options loaded via JavaScript Fetch -->
                    </select>
                    <small style="color: var(--color-text-muted); margin-top: 5px; display: block;">Only rooms that are fully available for this reservation's dates are displayed.</small>
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%;">
                    Confirm Room Reassignment
                </button>
            </form>
        </div>
    </div>

    <!-- Room Selection Grid Modal -->
    <div class="modal-overlay" id="roomSelectionModal">
        <div class="modal-content" style="max-width: 750px;">
            <button class="modal-close" onclick="closeRoomSelectionModal()">&times;</button>
            <h3 class="modal-title">Select Assigned Room</h3>
            <p class="modal-desc" style="margin-bottom: 15px;">Choose a room from the grid below. Green rooms are available.</p>
            
            <div id="gridContainer" class="room-selection-grid">
                <!-- Loaded dynamically via JS Fetch -->
            </div>
        </div>
    </div>

    <!-- Real-time currency input formatting and edit room Ajax trigger script -->
    <script>
        // 1. Currency Formatting Typing Helper
        const currencyInputs = document.querySelectorAll('.currency-format');
        
        currencyInputs.forEach(input => {
            // Format on load if value exists
            if (input.value) {
                input.value = formatNumber(input.value.replace(/\D/g, ''));
            }

            input.addEventListener('input', (e) => {
                let cleanVal = e.target.value.replace(/\D/g, '');
                e.target.value = formatNumber(cleanVal);
            });
        });

        function formatNumber(num) {
            if (!num) return '';
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // 2. Edit Room AJAX fetcher
        function openEditRoomModal(bookingId, guestName) {
            document.getElementById('editBookingId').value = bookingId;
            document.getElementById('editGuestName').innerText = guestName;
            
            const selectEl = document.getElementById('editAvailableRoomsSelect');
            selectEl.innerHTML = '<option>Loading available rooms...</option>';
            
            // Show modal
            document.getElementById('editRoomModal').classList.add('active');

            // Fetch available rooms
            fetch(`{{ route('admin.booking.available-rooms') }}?booking_id=${bookingId}`)
                .then(res => res.json())
                .then(data => {
                    selectEl.innerHTML = '';
                    if (data.available_rooms.length === 0) {
                        selectEl.innerHTML = '<option disabled>No alternative rooms available for these dates.</option>';
                    } else {
                        data.available_rooms.forEach(room => {
                            let isCurrent = room.id == data.current_room_id;
                            let option = document.createElement('option');
                            option.value = room.id;
                            option.innerText = `${room.room_number} (${room.room_type_name}) ${isCurrent ? '[CURRENT]' : ''}`;
                            if (isCurrent) option.selected = true;
                            selectEl.appendChild(option);
                        });
                    }
                })
                .catch(err => {
                    selectEl.innerHTML = '<option disabled>Error loading rooms.</option>';
                    console.error(err);
                });
        }

        function closeEditRoomModal() {
            document.getElementById('editRoomModal').classList.remove('active');
        }

        // 3. Mobile Sidebar Hamburger Toggle
        function toggleAdminSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
        }

        // 4. SweetAlert Confirmation Helpers
        function confirmCheckout(bookingId, guestName) {
            Swal.fire({
                title: 'Check Out Guest?',
                text: `Are you sure you want to check out ${guestName}? This will make the room immediately available starting from today.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2e7d32',
                cancelButtonColor: '#757575',
                confirmButtonText: 'Yes, Check Out'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`checkout-form-${bookingId}`).submit();
                }
            });
        }

        function confirmCancel(bookingId, guestName) {
            Swal.fire({
                title: 'Cancel Booking?',
                text: `Are you sure you want to cancel the booking for ${guestName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#757575',
                confirmButtonText: 'Yes, Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${bookingId}`).submit();
                }
            });
        }

        function confirmUnblock(bookingId, roomNumber) {
            Swal.fire({
                title: 'Remove Block?',
                text: `Are you sure you want to remove the maintenance/offline block on ${roomNumber}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#757575',
                confirmButtonText: 'Yes, Unblock'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`unblock-form-${bookingId}`).submit();
                }
            });
        }

        function confirmDeleteRoomType(roomTypeId, name) {
            Swal.fire({
                title: 'Delete Room Type?',
                text: `Deleting "${name}" will permanently delete all associated physical rooms and bookings. Are you sure?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#757575',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-roomtype-form-${roomTypeId}`).submit();
                }
            });
        }

        function confirmDeleteRoom(roomId, roomNumber) {
            Swal.fire({
                title: 'Delete Physical Room?',
                text: `Are you sure you want to delete ${roomNumber}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#757575',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-room-form-${roomId}`).submit();
                }
            });
        }

        function confirmDeletePricing(ruleId) {
            Swal.fire({
                title: 'Remove Pricing Rule?',
                text: 'Are you sure you want to remove this pricing rule?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#757575',
                confirmButtonText: 'Yes, Remove'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-pricing-form-${ruleId}`).submit();
                }
            });
        }

        // 5. Date Picker Instantiation
        const xDaysLimit = {{ $settings['x_days_limit'] ?? 30 }};
        
        let resPicker = null;
        let blockPicker = null;

        // Instantiate picker for Reservations
        const resPickerEl = document.getElementById('resDateRangePicker');
        if (resPickerEl) {
            resPicker = new CustomDateRangePicker('resDateRangePicker', {
                xDaysLimit: xDaysLimit,
                onSelect: (start, end) => {
                    document.getElementById('res_check_in').value = start;
                    document.getElementById('res_check_out').value = end;
                    // Reset room selection if dates change
                    document.getElementById('selected_room_id').value = '';
                    document.getElementById('selected_room_display').value = '';
                    document.getElementById('selected_room_display').placeholder = 'Select Room now';
                }
            });
        }

        // Instantiate picker for Blockings
        const blockPickerEl = document.getElementById('blockDateRangePicker');
        if (blockPickerEl) {
            blockPicker = new CustomDateRangePicker('blockDateRangePicker', {
                xDaysLimit: xDaysLimit,
                onSelect: (start, end) => {
                    document.getElementById('block_check_in').value = start;
                    document.getElementById('block_check_out').value = end;
                }
            });
        }

        // 6. Visual Room Selection Grid Modal functions
        function openRoomSelectionModal() {
            const checkIn = document.getElementById('res_check_in').value;
            const checkOut = document.getElementById('res_check_out').value;

            if (!checkIn || !checkOut) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Dates First',
                    text: 'Please choose Check-In and Check-Out dates before selecting a room.',
                    confirmButtonColor: '#b19453'
                });
                return;
            }

            const gridContainer = document.getElementById('gridContainer');
            gridContainer.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:20px;">Loading room availability...</div>';

            document.getElementById('roomSelectionModal').classList.add('active');

            // Fetch availability grid data
            fetch(`{{ route('admin.rooms.availability-grid') }}?check_in=${checkIn}&check_out=${checkOut}`)
                .then(res => res.json())
                .then(data => {
                    gridContainer.innerHTML = '';
                    if (data.length === 0) {
                        gridContainer.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:20px;">No physical rooms registered.</div>';
                        return;
                    }

                    data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = `grid-room-card ${item.status}`;
                        
                        let cardContent = `
                            <div>
                                <div class="room-number-title">${item.room.room_number}</div>
                                <div class="room-type-lbl">${item.room.room_type_name}</div>
                            </div>
                        `;

                        if (item.status === 'available') {
                            cardContent += `<div class="room-status-lbl available">Available</div>`;
                            card.onclick = () => selectRoomFromGrid(item.room.id, item.room.room_number, item.room.room_type_name);
                        } else if (item.status === 'booked') {
                            const dateRangeStr = `${formatDisplayDate(item.check_in)} - ${formatDisplayDate(item.check_out)}`;
                            cardContent += `
                                <div>
                                    <div class="room-status-lbl booked">Booked</div>
                                    <div class="room-detail-info" style="font-size:0.65rem;">${dateRangeStr}</div>
                                </div>
                            `;
                        } else if (item.status === 'blocked') {
                            const dateRangeStr = `${formatDisplayDate(item.check_in)} - ${formatDisplayDate(item.check_out)}`;
                            cardContent += `
                                <div>
                                    <div class="room-status-lbl blocked">Blocked</div>
                                    <div class="room-detail-info">Reason: <em>${item.notes}</em></div>
                                    <div class="room-detail-info" style="font-size:0.65rem;">${dateRangeStr}</div>
                                </div>
                            `;
                        }

                        card.innerHTML = cardContent;
                        gridContainer.appendChild(card);
                    });
                })
                .catch(err => {
                    gridContainer.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:20px; color:#c62828;">Failed to load room availability.</div>';
                    console.error(err);
                });
        }

        function formatDisplayDate(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            return `${parts[2]}/${parts[1]}/${parts[0].substring(2)}`;
        }

        function selectRoomFromGrid(roomId, roomNumber, roomTypeName) {
            document.getElementById('selected_room_id').value = roomId;
            document.getElementById('selected_room_display').value = `${roomNumber} (${roomTypeName})`;
            closeRoomSelectionModal();
        }

        function closeRoomSelectionModal() {
            document.getElementById('roomSelectionModal').classList.remove('active');
        }

        // 7. Desktop Sidebar Toggle
        function toggleSidebarDesktop() {
            document.querySelector('.admin-wrapper').classList.toggle('sidebar-collapsed');
        }

        // Mobile Sidebar Toggle
        function toggleAdminSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
        }

        // 8. Client-side booking list filter
        let filterPicker = null;
        const filterPickerEl = document.getElementById('bookingListFilterPicker');
        if (filterPickerEl) {
            filterPicker = new CustomDateRangePicker('bookingListFilterPicker', {
                xDaysLimit: 365,
                onSelect: (start, end) => {
                    filterBookingsList(start, end);
                }
            });
        }

        function filterBookingsList(filterStart, filterEnd) {
            const rows = document.querySelectorAll('.booking-row');
            const fs = new Date(filterStart);
            const fe = new Date(filterEnd);
            fs.setHours(0,0,0,0);
            fe.setHours(0,0,0,0);

            rows.forEach(row => {
                const rs = new Date(row.dataset.checkIn);
                const re = new Date(row.dataset.checkOut);
                rs.setHours(0,0,0,0);
                re.setHours(0,0,0,0);

                // Overlap condition
                if (rs < fe && re > fs) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function clearBookingFilter() {
            if (filterPicker) {
                filterPicker.selectedStart = null;
                filterPicker.selectedEnd = null;
                filterPicker.renderCalendar();
                filterPicker.updateInputDisplay();
            }
            const rows = document.querySelectorAll('.booking-row');
            rows.forEach(row => {
                row.style.display = '';
            });
        }
    </script>

</body>
</html>
