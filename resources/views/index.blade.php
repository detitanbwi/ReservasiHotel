<?php
use App\Helpers\Icons;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['hotel_name'] ?? 'The Bronze Oasis Resort' }} - Direct Booking</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}?v={{ time() }}">
</head>
<body>

    <!-- Header / Navbar -->
    <header>
        <div class="nav-container">
            <a href="{{ route('index') }}" class="logo-wrapper">
                {!! Icons::getLogo('#ffffff', 36) !!}
                <span class="logo-text">{{ $settings['hotel_name'] ?? 'The Bronze Oasis Resort' }}</span>
            </a>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#rooms">Rooms</a></li>
                <li><a href="{{ route('admin') }}" class="btn-nav-admin">Admin Dashboard</a></li>
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            {!! Icons::getLogo('#ffffff', 100) !!}
            <h1 class="hero-title">{{ $settings['hotel_name'] ?? 'The Bronze Oasis Resort' }}</h1>
            <p class="hero-subtitle">Luxurious Simplicity & Comfort</p>
        </div>
    </section>

    <!-- Search Form Widget -->
    <div class="search-widget-container">
        <div class="search-widget">
            <form action="{{ route('index') }}#rooms" method="GET" class="search-form" id="searchForm">
                <div class="form-group">
                    <label>
                        {!! Icons::getCalendar('#241d01', 14) !!} Check-In
                    </label>
                    <input type="date" name="check_in" id="check_in" class="form-control" required value="{{ $checkIn }}">
                </div>
                <div class="form-group">
                    <label>
                        {!! Icons::getCalendar('#241d01', 14) !!} Check-Out
                    </label>
                    <input type="date" name="check_out" id="check_out" class="form-control" required value="{{ $checkOut }}">
                </div>
                <div class="form-group">
                    <label>
                        {!! Icons::getPax('#241d01', 14) !!} Guests
                    </label>
                    <select name="guests" id="guests" class="form-control">
                        <option value="1" {{ $guests === 1 ? 'selected' : '' }}>1 Guest</option>
                        <option value="2" {{ $guests === 2 ? 'selected' : '' }}>2 Guests</option>
                        <option value="3" {{ $guests === 3 ? 'selected' : '' }}>3 Guests</option>
                        <option value="4" {{ $guests === 4 ? 'selected' : '' }}>4 Guests</option>
                        <option value="5" {{ $guests === 5 ? 'selected' : '' }}>5 Guests</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">
                    Check Availability
                </button>
            </form>
        </div>
    </div>

    <!-- Company Profile Section -->
    <section id="about" class="section">
        <div class="section-title-wrapper">
            <span class="section-subtitle">Discover</span>
            <h2 class="section-title">Our Profile</h2>
        </div>
        <div class="profile-grid">
            <div class="profile-text">
                <h3>A Place of Serenity</h3>
                <p>{!! nl2br(e($settings['hotel_about'] ?? '')) !!}</p>
                <p>Located at the beautiful area of <strong>{{ $settings['hotel_address'] ?? '' }}</strong>, our resort caters to travelers looking for true quality of space, modern service, and peace of mind.</p>
                
                <div class="vision-mission">
                    <div class="card-box">
                        <h4>{!! Icons::getShieldCheck('#b19453', 20) !!} Our Vision</h4>
                        <p style="font-size: 0.9rem; color: var(--color-text-muted);">{{ $settings['hotel_vision'] ?? '' }}</p>
                    </div>
                    <div class="card-box">
                        <h4>{!! Icons::getClock('#b19453', 20) !!} Our Mission</h4>
                        <p style="font-size: 0.9rem; color: var(--color-text-muted);">{{ $settings['hotel_mission'] ?? '' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="profile-images">
                <div class="profile-img-item" style="grid-column: span 2; height: 300px;">
                    <img src="{{ asset('hero.jpg') }}" alt="Luxury Room">
                </div>
                <div class="profile-img-item">
                    <div style="width:100%; height:100%; background: #241d01; display:flex; align-items:center; justify-content:center;">
                        {!! Icons::getLogo('#ffffff', 80) !!}
                    </div>
                </div>
                <div class="profile-img-item" style="display:flex; flex-direction:column; justify-content:center; padding: 20px; background-color: var(--color-bg-alt); border: 1px solid var(--color-bronze-light);">
                    <h5 style="font-size: 1.1rem; margin-bottom: 8px;">X-Day Booking Lock</h5>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted);">To maintain scheduling precision, date selections are limited to a rolling window of <strong>{{ $settings['x_days_limit'] ?? 30 }} days</strong> in advance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Catalog Section -->
    <section id="rooms" class="section" style="background-color: var(--color-bg-alt); max-width: 100%; padding-left: calc((100% - 1200px)/2 + 20px); padding-right: calc((100% - 1200px)/2 + 20px);">
        <div class="section-title-wrapper">
            <span class="section-subtitle">Exquisite Stays</span>
            <h2 class="section-title">Available Accommodations</h2>
            @if ($searched)
                <p style="margin-top: 10px; color: var(--color-text-muted);">Showing rates for: <strong>{{ date('d M Y', strtotime($checkIn)) }}</strong> to <strong>{{ date('d M Y', strtotime($checkOut)) }}</strong> ({{ (new DateTime($checkIn))->diff(new DateTime($checkOut))->days }} nights, {{ $guests }} guests)</p>
            @else
                <p style="margin-top: 10px; color: var(--color-text-muted);">Enter your check-in and check-out dates above to view real-time availability and dynamic pricing.</p>
            @endif
        </div>

        <div class="catalog-grid">
            @if ($searched)
                @if (empty($catalog))
                    <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: white; border: 1px solid var(--color-bronze-light); border-radius: var(--border-radius);">
                        <p style="font-size: 1.1rem; color: var(--color-text-muted);">No rooms match your search criteria or are available for these dates.</p>
                    </div>
                @else
                    @foreach ($catalog as $item)
                        <div class="room-card">
                            <div class="room-img-placeholder">
                                {!! Icons::getBed('#ffffff', 50) !!}
                            </div>
                            <div class="room-card-content">
                                <h3 class="room-name">{{ $item['room_type']['name'] }}</h3>
                                <p class="room-desc">{{ $item['room_type']['description'] }}</p>
                                
                                <div class="room-details">
                                    <span class="room-badge">
                                        {!! Icons::getPax('#241d01', 12) !!} Max {{ $item['room_type']['capacity'] }} guests
                                    </span>
                                    <span class="room-badge">
                                        {!! Icons::getShieldCheck('#241d01', 12) !!} {{ $item['available_count'] }} Room(s) available
                                    </span>
                                </div>

                                <div class="room-footer">
                                    <div class="room-price-info">
                                        <span class="price-label">Total for {{ $item['price_info']['nights'] }} nights</span>
                                        <span class="room-price-val">Rp {{ number_format($item['price_info']['total_price'], 0, ',', '.') }}</span>
                                    </div>
                                    @if ($item['available_count'] > 0)
                                        <button class="btn-primary" onclick="openBookingModal('{{ $item['room_type']['name'] }}', {{ $item['price_info']['total_price'] }}, {{ $item['price_info']['nights'] }})">
                                            Book Now
                                        </button>
                                    @else
                                        <button class="btn-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">
                                            Fully Booked
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            @else
                @foreach ($catalog as $item)
                    <div class="room-card">
                        <div class="room-img-placeholder">
                            {!! Icons::getBed('#ffffff', 50) !!}
                        </div>
                        <div class="room-card-content">
                            <h3 class="room-name">{{ $item['room_type']['name'] }}</h3>
                            <p class="room-desc">{{ $item['room_type']['description'] }}</p>
                            
                            <div class="room-details">
                                <span class="room-badge">
                                    {!! Icons::getPax('#241d01', 12) !!} Max {{ $item['room_type']['capacity'] }} guests
                                </span>
                            </div>

                            <div class="room-footer">
                                <div class="room-price-info">
                                    <span class="price-label">Starting from</span>
                                    <span class="room-price-val">Rp {{ number_format($item['room_type']['base_price'], 0, ',', '.') }}<span>/night</span></span>
                                </div>
                                <a href="#home" class="btn-secondary" onclick="document.getElementById('check_in').focus();">
                                    Select Dates
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <div class="footer-logo">
                    {!! Icons::getLogo('#ffffff', 32) !!}
                    <span class="logo-text">{{ $settings['hotel_name'] ?? 'The Bronze Oasis' }}</span>
                </div>
                <p class="footer-desc">{{ $settings['hotel_about'] ?? '' }}</p>
            </div>
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#rooms">Rooms</a></li>
                    <li><a href="{{ route('admin') }}">Admin Dashboard</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <p style="color: var(--color-bronze-light); font-size: 0.95rem; margin-bottom: 10px;">
                    {{ $settings['hotel_address'] ?? '' }}
                </p>
                <p style="color: var(--color-bronze-light); font-size: 0.95rem;">
                    WhatsApp: {{ $settings['admin_phone'] ?? '' }}
                </p>
            </div>
        </div>
        <div class="footer-bottom" style="display: flex; flex-direction: column; align-items: center; gap: 10px; justify-content: center;">
            <div>&copy; {{ date('Y') }} {{ $settings['hotel_name'] ?? 'The Bronze Oasis' }}. All rights reserved.</div>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; opacity: 0.8;">
                <span>Powered by</span>
                {!! Icons::getWirodevLogo(18) !!}
                <strong style="color: #ffffff; letter-spacing: 0.05em;">wirodev</strong>
            </div>
        </div>
    </footer>

    <!-- Booking Modal -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeBookingModal()">&times;</button>
            <h3 class="modal-title">Complete Reservation</h3>
            <p class="modal-desc">Fill in your information to generate the WhatsApp direct booking request.</p>
            
            <div class="summary-box">
                <div class="summary-title">Reservation Summary</div>
                <div class="summary-row">
                    <span>Room Type:</span>
                    <span id="sumRoomType">-</span>
                </div>
                <div class="summary-row">
                    <span>Check-In:</span>
                    <span>{{ $checkIn ? date('d M Y', strtotime($checkIn)) : '-' }}</span>
                </div>
                <div class="summary-row">
                    <span>Check-Out:</span>
                    <span>{{ $checkOut ? date('d M Y', strtotime($checkOut)) : '-' }}</span>
                </div>
                <div class="summary-row">
                    <span>Duration:</span>
                    <span id="sumDuration">-</span>
                </div>
                <div class="summary-row">
                    <span>Guests:</span>
                    <span>{{ $guests }} Persons</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Estimated Total:</span>
                    <span id="sumTotal">Rp 0</span>
                </div>
            </div>

            <form id="bookingForm" onsubmit="handleBookingSubmit(event)">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="guest_name">Full Name</label>
                    <input type="text" id="guest_name" class="form-control" placeholder="John Doe" required>
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="guest_phone">WhatsApp Number</label>
                    <input type="tel" id="guest_phone" class="form-control" placeholder="e.g. 08123456789" required>
                </div>
                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="guest_notes">Additional Notes</label>
                    <textarea id="guest_notes" class="form-control" rows="3" placeholder="Special requests, bed preference, etc."></textarea>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">
                    {!! Icons::getChatCheck('#ffffff', 18) !!} Send Message to Admin
                </button>
            </form>
        </div>
    </div>

    <!-- Scripting for date limiters, modal triggers, and WhatsApp API redirect -->
    <script>
        const xDaysLimit = {{ $settings['x_days_limit'] ?? 30 }};
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        const formatDate = (date) => {
            let d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        };

        const maxDate = new Date();
        maxDate.setDate(today.getDate() + xDaysLimit);

        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');

        checkInInput.min = formatDate(today);
        checkInInput.max = formatDate(maxDate);

        checkInInput.addEventListener('change', () => {
            const selectedCI = new Date(checkInInput.value);
            const minCO = new Date(selectedCI);
            minCO.setDate(selectedCI.getDate() + 1);
            
            checkOutInput.min = formatDate(minCO);
            
            const maxCO = new Date(selectedCI);
            maxCO.setDate(selectedCI.getDate() + xDaysLimit);
            checkOutInput.max = formatDate(maxCO);
            
            if (checkOutInput.value && new Date(checkOutInput.value) <= selectedCI) {
                checkOutInput.value = formatDate(minCO);
            }
        });

        let selectedRoomTypeName = '';
        let selectedTotalPriceFormatted = '';
        let selectedDuration = 0;

        function openBookingModal(roomTypeName, totalPrice, nights) {
            selectedRoomTypeName = roomTypeName;
            selectedDuration = nights;
            selectedTotalPriceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(totalPrice);
            
            document.getElementById('sumRoomType').innerText = roomTypeName;
            document.getElementById('sumDuration').innerText = nights + ' Night(s)';
            document.getElementById('sumTotal').innerText = selectedTotalPriceFormatted;
            
            document.getElementById('bookingModal').classList.add('active');
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.remove('active');
        }

        function handleBookingSubmit(e) {
            e.preventDefault();
            
            const name = document.getElementById('guest_name').value;
            const phone = document.getElementById('guest_phone').value;
            const notes = document.getElementById('guest_notes').value || '-';
            
            const checkInVal = '{{ $checkIn }}';
            const checkOutVal = '{{ $checkOut }}';
            const guestsVal = '{{ $guests }}';
            const adminPhone = '{{ $settings['admin_phone'] ?? '6281234567890' }}';

            const message = `Halo Admin, saya ingin booking kamar:

📋 DATA DIRI PEMESAN
• Nama: ${name}
• No. WA: ${phone}

🛏️ DETAIL RESERVASI
• Tipe Kamar: ${selectedRoomTypeName}
• Check-in: ${checkInVal}
• Check-out: ${checkOutVal}
• Jumlah Kamar: 1 Kamar
• Jumlah Pax: ${guestsVal} Orang
• Catatan: ${notes}

💰 ESTIMASI TOTAL HARGA
• Total: ${selectedTotalPriceFormatted}

Mohon info ketersediaan slot dan rekening pembayarannya. Terima kasih!`;

            let destinationPhone = phone.replace(/\D/g, '');
            if (destinationPhone.startsWith('0')) {
                destinationPhone = '62' + destinationPhone.substring(1);
            }

            const waLink = `https://api.whatsapp.com/send?phone=${destinationPhone}&text=${encodeURIComponent(message)}`;
            
            window.open(waLink, '_blank');
            closeBookingModal();
        }
    </script>
</body>
</html>
