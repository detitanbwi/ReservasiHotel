<?php

namespace App\Helpers;

class Icons {
    // 1. Logo Properti (Menara Hotel + Bulan & Bintang)
    public static function getLogo($color = 'currentColor', $width = 80) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 160 160" width="' . $width . '" height="' . $width . '" fill="none">
            <!-- Ground Base Line -->
            <line x1="20" y1="130" x2="140" y2="130" stroke="' . $color . '" stroke-width="4" stroke-linecap="round"/>
            
            <!-- Left Tower Side -->
            <path d="M45,130 L45,95 L60,80 L60,130" stroke="' . $color . '" stroke-width="4" stroke-linejoin="round"/>
            
            <!-- Central Tall Tower -->
            <path d="M68,130 L68,52 L88,38 L88,130" stroke="' . $color . '" stroke-width="4" stroke-linejoin="round"/>
            <line x1="78" y1="58" x2="78" y2="130" stroke="' . $color . '" stroke-width="3" stroke-linecap="round"/>
            
            <!-- Right Tower Side -->
            <path d="M96,130 L96,95 L111,80 L111,130" stroke="' . $color . '" stroke-width="4" stroke-linejoin="round"/>
            
            <!-- Crescent Moon -->
            <path d="M100,28 C108,28 116,33 119,40 C110,41 104,49 104,58 C104,67 110,75 119,76 C116,83 108,88 100,88 C88,88 78,78 78,66 C78,54 88,44 100,28 Z" 
                  stroke="' . $color . '" stroke-width="4" stroke-linejoin="round"/>
                  
            <!-- Star -->
            <path d="M124,35 L126,40 L131,41 L127,45 L128,50 L124,47 L120,50 L121,45 L117,41 L122,40 Z" 
                  fill="' . $color . '"/>
        </svg>';
    }

    // 2. Check-in/Out dates (Calendar with X)
    public static function getCalendar($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
            <line x1="9" y1="14" x2="15" y2="20"/>
            <line x1="15" y1="14" x2="9" y2="20"/>
        </svg>';
    }

    // 3. Pax/Guests (Hotel Bell)
    public static function getPax($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-4 8-4 8h20s-4-1-4-8z"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            <path d="M12 2v2"/>
        </svg>';
    }

    // 4. Availability X days (Clock)
    public static function getClock($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
        </svg>';
    }

    // 5. Room Type (Bed)
    public static function getBed($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 4v16M2 8h20M2 14h20M22 4v16"/>
            <path d="M6 8v3a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V8"/>
        </svg>';
    }

    // 6. Dynamic Pricing Rules (Chart with Arrow going up + "WEEK")
    public static function getPricingChart($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
            <polyline points="16 7 22 7 22 13"/>
            <line x1="2" y1="20" x2="22" y2="20" stroke-dasharray="2 2"/>
            <text x="3" y="6" font-family="sans-serif" font-size="5" font-weight="bold" fill="' . $color . '" stroke="none">WEEK</text>
        </svg>';
    }

    // 7. Reservation Management (Shield with Checkmark)
    public static function getShieldCheck($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 11 11 13 15 9"/>
        </svg>';
    }

    // 8. Admin Management (User with Gear)
    public static function getUserGear($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
            <circle cx="20" cy="12" r="3" stroke-dasharray="2 2"/>
        </svg>';
    }

    // 9. WA Notifier / Chat Bubble with Text Lines
    public static function getChatLines($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            <line x1="8" y1="7" x2="16" y2="7"/>
            <line x1="8" y1="11" x2="16" y2="11"/>
        </svg>';
    }

    // 10. WA Notifier Confirmation / Check
    public static function getChatCheck($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            <polyline points="9 10 11 12 15 8"/>
        </svg>';
    }

    // 11. Credit Card (Room Reservation)
    public static function getCard($color = 'currentColor', $width = 24) {
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="' . $width . '" height="' . $width . '" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
            <line x1="1" y1="10" x2="23" y2="10"/>
        </svg>';
    }

    // 12. Wirodev Logo
    public static function getWirodevLogo($width = 24) {
        $height = round($width * (316 / 500));
        return '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 316" width="' . $width . '" height="' . $height . '" fill="none">
            <!-- Green Circle -->
            <circle cx="250" cy="55" r="55" fill="#72c043"/>
            <path d="M250,0 C219.6,0 195,24.6 195,55 C195,65.3 197.8,75 202.7,83.3 C197.8,75 195,65.3 195,55 C195,24.6 219.6,0 250,0 Z" fill="#a6d980" opacity="0.6"/>
            
            <!-- Left Wing -->
            <path d="M0,0 L98,0 L200,165 L272,135 L200,240 C175,275 140,290 100,290 L0,0 Z" fill="#184997"/>
            <path d="M0,0 C30,90 60,180 100,290 C125,290 145,280 160,265 C110,180 60,90 0,0 Z" fill="#1b75bc" opacity="0.9"/>
            
            <!-- Right Wing -->
            <path d="M500,0 L402,0 L300,165 L228,135 L300,240 C325,275 360,290 400,290 L500,0 Z" fill="#184997"/>
            <path d="M500,0 C470,90 440,180 400,290 C375,290 355,280 340,265 C390,180 440,90 500,0 Z" fill="#1b75bc" opacity="0.9"/>
        </svg>';
    }
}
