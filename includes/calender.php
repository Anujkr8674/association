<?php
// Unified Cultural Calendar component
require_once __DIR__ . '/api_service.php';

// If AJAX request, return JSON data and exit
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header('Content-Type: application/json');
    $cal_year = isset($_GET['cal_year']) ? intval($_GET['cal_year']) : 2026;
    $cal_month = isset($_GET['cal_month']) ? intval($_GET['cal_month']) : 8;
    $calendar_events = fetch_unified_events($pdo, $cal_year, $cal_month);
    echo json_encode($calendar_events);
    exit;
}

// Active year and month selection (default to August 2026 or URL params)
$cal_year = isset($_GET['cal_year']) ? intval($_GET['cal_year']) : 2026;
$cal_month = isset($_GET['cal_month']) ? intval($_GET['cal_month']) : 8;

// Safety bounds check
if ($cal_month < 1 || $cal_month > 12) {
    $cal_month = 8;
}
if ($cal_year < 2000 || $cal_year > 2100) {
    $cal_year = 2026;
}

// Fetch unified, deduplicated, and customized events from database
$calendar_events = fetch_unified_events($pdo, $cal_year, $cal_month);

// Determine default selected date for this calendar view
$today_str = date('Y-m-d');
$today_parts = explode('-', $today_str);
$is_current_view = ($cal_year == $today_parts[0] && $cal_month == $today_parts[1]);

if ($is_current_view) {
    $default_active_date = $today_str;
} else {
    $default_active_date = sprintf("%04d-%02d-01", $cal_year, $cal_month);
}

// Helper for category indicator symbols
function get_category_symbol($category) {
    switch ($category) {
        case 'Festivals':
        case 'Puja':
        case 'Hindu Festivals':
        case 'Bengali Festivals':
            return '🪔';
        case 'National Holidays':
        case 'Regional Holidays':
            return '🇮🇳';
        case 'Cultural Events':
            return '🎭';
        case 'Association Events':
        case 'Meeting':
            return '👥';
        default:
            return '📅';
    }
}
?>

<!-- Calendar and Events Section CSS -->
<style>
    /* Styling tokens */
    :root {
        --cal-primary: #D43F3A; /* BCA Red */
        --cal-accent: #E5A93B;  /* BCA Gold */
        --cal-dark: #211A17;
        --cal-cream: #FBF4E6;
        --cal-sand: #FBF4E6;
        --cal-white: #FFFFFF;
        --cal-gray: #7A726E;
        --cal-light-gray: #EFECE6;
        --cal-radius: 14px;
        --cal-transition: all 0.3s ease;
    }

    .cal-section {
        background-color: var(--cal-cream);
        padding: 5.5rem 0;
        border-top: 1px solid rgba(33, 26, 23, 0.05);
        border-bottom: 1px solid rgba(33, 26, 23, 0.05);
    }

    .cal-main-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2.5rem;
        margin-top: 2.5rem;
    }

    /* Top Controls Row */
    .cal-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
        background-color: var(--cal-white);
        padding: 1.5rem 2rem;
        border-radius: var(--cal-radius);
        box-shadow: 0 4px 15px rgba(33, 26, 23, 0.03);
        border: 1px solid var(--cal-light-gray);
    }

    .cal-view-toggles {
        display: flex;
        background-color: var(--cal-sand);
        padding: 0.3rem;
        border-radius: 30px;
        border: 1px solid var(--cal-light-gray);
    }

    .cal-toggle-btn {
        background: none;
        border: none;
        padding: 0.5rem 1.2rem;
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--cal-gray);
        border-radius: 20px;
        cursor: pointer;
        transition: var(--cal-transition);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .cal-toggle-btn.active {
        background-color: var(--cal-primary);
        color: var(--cal-white);
        box-shadow: 0 3px 8px rgba(212, 63, 58, 0.2);
    }

    .cal-month-scroller {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .cal-scroll-arrow {
        background: none;
        border: 1px solid var(--cal-light-gray);
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--cal-dark);
        cursor: pointer;
        transition: var(--cal-transition);
    }

    .cal-scroll-arrow:hover {
        background-color: var(--cal-primary);
        color: var(--cal-white);
        border-color: var(--cal-primary);
        box-shadow: 0 4px 10px rgba(212, 63, 58, 0.15);
    }

    .cal-selectors-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cal-select-input {
        background-color: var(--cal-white);
        border: 1px solid var(--cal-light-gray);
        border-radius: 8px;
        padding: 0.45rem 0.85rem;
        font-size: 0.95rem;
        font-weight: 600;
        font-family: 'Outfit', sans-serif;
        color: var(--cal-dark);
        cursor: pointer;
    }

    .cal-select-input:focus {
        outline: none;
        border-color: var(--cal-accent);
    }

    .cal-today-btn {
        background-color: var(--cal-sand);
        border: 1px solid var(--cal-light-gray);
        color: var(--cal-dark);
        border-radius: 30px;
        padding: 0.5rem 1.1rem;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--cal-transition);
    }

    .cal-today-btn:hover {
        background-color: var(--cal-primary);
        color: var(--cal-white);
        border-color: var(--cal-primary);
    }

    /* Filter Tabs Category */
    .cal-filter-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        justify-content: center;
    }

    .cal-filter-tab {
        background-color: var(--cal-white);
        border: 1px solid var(--cal-light-gray);
        border-radius: 30px;
        padding: 0.55rem 1.15rem;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--cal-gray);
        cursor: pointer;
        transition: var(--cal-transition);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .cal-filter-tab:hover,
    .cal-filter-tab.active {
        background-color: var(--cal-primary);
        color: var(--cal-white);
        border-color: var(--cal-primary);
        box-shadow: 0 4px 10px rgba(212, 63, 58, 0.15);
    }

    .cal-filter-tab.active i {
        color: var(--cal-accent);
    }

    /* Grid Layout Container split: calendar grid on left, selected day list on right */
    .cal-layout-grid-view {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }

    /* Calendar Grid Widget */
    .cal-widget-panel {
        background-color: var(--cal-white);
        border-radius: var(--cal-radius);
        box-shadow: 0 4px 20px rgba(33, 26, 23, 0.03);
        border: 1px solid var(--cal-light-gray);
        padding: 2rem;
    }

    .cal-grid-cols-7 {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.5rem;
        text-align: center;
    }

    .cal-weekday-hdr {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--cal-accent);
        letter-spacing: 0.5px;
        margin-bottom: 0.8rem;
    }

    .cal-grid-day {
        aspect-ratio: 1.1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--cal-dark);
        border-radius: 8px;
        border: 1px solid rgba(33, 26, 23, 0.02);
        cursor: pointer;
        position: relative;
        transition: var(--cal-transition);
        background-color: var(--cal-cream);
    }

    .cal-grid-day:hover:not(.other-month):not(.active) {
        background-color: var(--cal-sand);
    }

    .cal-grid-day.other-month {
        color: rgba(33, 26, 23, 0.25);
        background-color: transparent;
        cursor: default;
    }

    .cal-grid-day.active {
        background-color: var(--cal-primary);
        color: var(--cal-white);
        border-color: var(--cal-primary);
    }

    .cal-grid-day.today {
        border: 2px solid var(--cal-accent) !important;
        box-shadow: inset 0 0 0 1px var(--cal-accent);
    }

    .cal-grid-day.today::after {
        content: 'TODAY';
        position: absolute;
        top: 4px;
        right: 6px;
        font-size: 0.55rem;
        color: var(--cal-accent);
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    /* Days indicators count dots or symbols */
    .cal-day-indicator-box {
        display: flex;
        gap: 3px;
        justify-content: center;
        width: 100%;
        margin-bottom: 2px;
        height: 16px;
    }

    .cal-mini-badge {
        font-size: 0.75rem;
        line-height: 1;
    }

    /* Selected Date List Sidebar */
    .cal-sidebar-panel {
        background-color: var(--cal-white);
        border-radius: var(--cal-radius);
        box-shadow: 0 4px 20px rgba(33, 26, 23, 0.03);
        border: 1px solid var(--cal-light-gray);
        padding: 1.8rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        height: fit-content;
    }

    .cal-sidebar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--cal-dark);
        border-bottom: 2px solid var(--cal-sand);
        padding-bottom: 0.5rem;
    }

    .cal-sidebar-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .cal-sidebar-row {
        background-color: var(--cal-sand);
        border-radius: 8px;
        padding: 0.85rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        cursor: pointer;
        transition: var(--cal-transition);
        border-left: 4px solid var(--cal-accent);
    }

    .cal-sidebar-row:hover {
        background-color: #F3EBDD;
        transform: translateX(3px);
    }

    .cal-sidebar-row-hdr {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cal-sidebar-symbol {
        font-size: 1.1rem;
    }

    .cal-sidebar-row-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--cal-dark);
        line-height: 1.3;
    }

    .cal-sidebar-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        font-size: 0.75rem;
        color: var(--cal-gray);
    }

    /* List/Upcoming view styles */
    .cal-list-view {
        background-color: var(--cal-white);
        border-radius: var(--cal-radius);
        box-shadow: 0 4px 20px rgba(33, 26, 23, 0.03);
        border: 1px solid var(--cal-light-gray);
        padding: 2.2rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .cal-list-item {
        display: grid;
        grid-template-columns: 140px 1fr auto;
        gap: 2rem;
        align-items: center;
        padding-bottom: 1.5rem;
        border-bottom: 1px dashed var(--cal-light-gray);
        transition: var(--cal-transition);
    }

    .cal-list-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .cal-list-date-box {
        background-color: var(--cal-sand);
        border-left: 4px solid var(--cal-primary);
        padding: 0.75rem 1rem;
        border-radius: 6px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .cal-list-day-num {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--cal-primary);
        line-height: 1.1;
    }

    .cal-list-month-lbl {
        font-size: 0.72rem;
        text-transform: uppercase;
        font-weight: 800;
        color: var(--cal-gray);
        letter-spacing: 0.5px;
        margin-top: 2px;
    }

    .cal-list-info {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .cal-list-title-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .cal-list-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--cal-dark);
    }

    .cal-list-desc {
        font-size: 0.92rem;
        color: var(--cal-gray);
        line-height: 1.5;
    }

    .cal-list-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.2rem;
        font-size: 0.82rem;
        color: var(--cal-gray);
        margin-top: 0.2rem;
    }

    .cal-list-meta-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .cal-list-meta-item i {
        color: var(--cal-accent);
    }

    .btn-details {
        background: none;
        border: 1px solid var(--cal-accent);
        color: var(--cal-primary);
        padding: 0.5rem 1.1rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: var(--cal-transition);
    }

    .btn-details:hover {
        background-color: var(--cal-primary);
        color: var(--cal-white);
        border-color: var(--cal-primary);
        box-shadow: 0 4px 10px rgba(212, 63, 58, 0.15);
    }

    /* Modal Styling Details */
    .cal-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(33, 26, 23, 0.55);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: var(--cal-transition);
        padding: 1.5rem;
    }

    .cal-modal.active {
        opacity: 1;
        visibility: visible;
    }

    .cal-modal-box {
        background-color: var(--cal-white);
        width: 100%;
        max-width: 680px;
        max-height: 90vh;
        overflow-y: auto;
        border-radius: var(--cal-radius);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        position: relative;
        transform: translateY(25px);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.2);
    }

    .cal-modal.active .cal-modal-box {
        transform: translateY(0);
    }

    .cal-modal-close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--cal-dark);
        cursor: pointer;
        font-size: 1rem;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: var(--cal-transition);
    }

    .cal-modal-close-btn:hover {
        background-color: var(--cal-primary);
        color: var(--cal-white);
    }

    .cal-modal-img-hdr {
        height: 240px;
        position: relative;
        overflow: hidden;
    }

    .cal-modal-fallback-grad {
        height: 100%;
        background: linear-gradient(135deg, #E5A93B 0%, #D43F3A 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cal-modal-fallback-svg {
        width: 72px;
        height: 72px;
        fill: var(--cal-white);
        opacity: 0.9;
    }

    .cal-modal-body {
        padding: 2.2rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .cal-modal-cat-tag {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 800;
        color: var(--cal-primary);
        letter-spacing: 1px;
    }

    .cal-modal-title {
        font-size: 1.65rem;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: var(--cal-dark);
        line-height: 1.3;
    }

    .cal-modal-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        background-color: var(--cal-sand);
        padding: 1.2rem;
        border-radius: 8px;
        font-size: 0.88rem;
        color: var(--cal-gray);
    }

    .cal-modal-meta-cell {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .cal-modal-meta-cell i {
        color: var(--cal-primary);
        font-size: 0.95rem;
    }

    .cal-modal-desc {
        font-size: 0.96rem;
        color: var(--cal-gray);
        line-height: 1.7;
    }

    /* Sub-schedules details in modal */
    .cal-modal-schedules-section {
        border-top: 1px solid var(--cal-light-gray);
        padding-top: 1.5rem;
        margin-top: 0.5rem;
    }

    .cal-modal-schedules-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--cal-dark);
        margin-bottom: 0.8rem;
    }

    .cal-modal-schedule-list {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .cal-modal-schedule-row {
        background-color: var(--cal-cream);
        border: 1px solid var(--cal-light-gray);
        padding: 0.65rem 1rem;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        font-size: 0.88rem;
        gap: 1rem;
    }

    .cal-modal-source-lbl {
        font-size: 0.78rem;
        color: var(--cal-gray);
        font-style: italic;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        border-top: 1px solid var(--cal-light-gray);
        padding-top: 1rem;
    }

    .btn-gcal {
        background-color: #4285F4;
        color: var(--cal-white);
        border: none;
        border-radius: 6px;
        padding: 0.65rem 1.2rem;
        font-size: 0.88rem;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--cal-transition);
        width: fit-content;
        margin-top: 0.5rem;
        text-decoration: none;
    }

    .btn-gcal:hover {
        background-color: #357AE8;
        box-shadow: 0 4px 12px rgba(66, 133, 244, 0.25);
    }

    /* Media responsive adjustments */
    @media (max-width: 991px) {
        .cal-layout-grid-view {
            grid-template-columns: 1fr;
        }
        .cal-list-item {
            grid-template-columns: 100px 1fr;
        }
        .cal-list-item .btn-details {
            grid-column: span 2;
            width: 100%;
            text-align: center;
        }
    }

    #cal-grid-view-container, #cal-list-view-container {
        transition: opacity 0.2s ease-in-out;
    }

    @media (max-width: 680px) {
        /* Hide switcher */
        .cal-grid-switcher {
            display: none !important;
        }

        /* Show grid layout container in block/stacked mode */
        .cal-layout-grid-view {
            display: block !important;
        }

        /* Show the selected day events sidebar list below the grid on mobile */
        .cal-sidebar-panel {
            display: flex !important;
            width: 100% !important;
            margin-top: 1.5rem !important;
            box-shadow: 0 4px 15px rgba(33, 26, 23, 0.02) !important;
            padding: 1.2rem !important;
        }

        /* Keep the monthly grid panel visible and tighten its layout */
        .cal-widget-panel {
            display: block !important;
            padding: 1rem 0.8rem !important;
        }

        #cal-monthly-days-grid .cal-grid-day {
            padding: 0.2rem 0.1rem;
            font-size: 0.78rem;
            aspect-ratio: 1;
        }

        #cal-monthly-days-grid .cal-day-indicator-box {
            gap: 1px;
            max-width: 100%;
            flex-wrap: wrap;
            overflow: hidden;
            height: auto;
            max-height: 10px;
            margin-top: 2px;
        }

        #cal-monthly-days-grid .cal-mini-badge {
            font-size: 0.55rem;
        }

        /* Hide the chronological list view on mobile main page (only visible in full calendar modal) */
        #cal-list-view-container {
            display: none !important;
        }

        /* Force scroller and button into a single row on mobile without wrapping */
        .cal-top-bar {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 0.3rem !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .cal-top-bar > div {
            padding: 0.25rem !important;
            margin: 0 !important;
            flex-shrink: 1 !important;
        }

        .cal-top-bar span#cal-month-year-title {
            font-size: 0.95rem !important;
            min-width: 100px !important;
            padding: 0 0.3rem !important;
            white-space: nowrap !important;
        }

        .cal-top-bar button.cal-scroll-arrow {
            width: 28px !important;
            height: 28px !important;
        }

        #cal-view-full-btn {
            padding: 0.45rem 0.65rem !important;
            font-size: 0.78rem !important;
            border-radius: 20px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }
    }

    /* Full Calendar Modal Styling */
    .cal-full-modal-box {
        background-color: var(--cal-white);
        width: 100%;
        max-width: 920px;
        border-radius: var(--cal-radius);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        position: relative;
        transform: translateY(25px);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.2);
        border-top: 5px solid var(--cal-accent);
    }
    
    .cal-modal.active .cal-full-modal-box {
        transform: translateY(0);
    }
    
    .cal-full-modal-body {
        padding: 2.5rem;
        display: grid;
        grid-template-columns: 1.25fr 1.75fr;
        gap: 2.5rem;
    }
    
    /* Left column mini grid cells */
    #cal-modal-days-grid .cal-grid-day {
        aspect-ratio: 1;
        padding: 0.25rem;
        font-size: 0.85rem;
        border-radius: 6px;
        background-color: var(--cal-cream);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: var(--cal-transition);
        position: relative;
    }
    
    #cal-modal-days-grid .cal-grid-day.other-month {
        background-color: transparent;
        color: rgba(33, 26, 23, 0.25);
        cursor: default;
    }
    
    #cal-modal-days-grid .cal-grid-day.active {
        background-color: var(--cal-primary);
        color: var(--cal-white);
    }
    
    #cal-modal-days-grid .cal-grid-day.today {
        border: 2px solid var(--cal-accent) !important;
        box-shadow: inset 0 0 0 1px var(--cal-accent);
    }
    
    /* Day dots indicator in modal */
    .cal-modal-day-dots {
        display: flex;
        gap: 2px;
        justify-content: center;
        height: 6px;
        align-items: center;
    }
    
    .cal-modal-day-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background-color: var(--cal-accent);
    }
    
    #cal-modal-days-grid .cal-grid-day.active .cal-modal-day-dot {
        background-color: var(--cal-white);
    }
    
    /* Right column scroll list styling */
    .cal-modal-scroll-item {
        background-color: var(--cal-sand);
        border-radius: 8px;
        padding: 1rem;
        display: grid;
        grid-template-columns: 56px 1fr auto;
        gap: 1rem;
        align-items: center;
        transition: var(--cal-transition);
        border-left: 4px solid var(--cal-accent);
    }
    
    .cal-modal-scroll-item:hover {
        background-color: #F3EBDD;
        transform: translateX(3px);
    }
    
    .cal-modal-item-datebox {
        background-color: var(--cal-white);
        border: 1px solid var(--cal-light-gray);
        border-radius: 6px;
        padding: 0.35rem 0.15rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-shadow: var(--shadow-sm);
        height: 52px;
    }
    
    .cal-modal-item-daynum {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--cal-primary);
        line-height: 1.1;
    }
    
    .cal-modal-item-monthlbl {
        font-size: 0.62rem;
        text-transform: uppercase;
        font-weight: 800;
        color: var(--cal-gray);
        letter-spacing: 0.5px;
    }
    
    .cal-modal-item-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--cal-dark);
        line-height: 1.35;
    }
    
    .cal-modal-item-meta {
        font-size: 0.78rem;
        color: var(--cal-gray);
        margin-top: 0.2rem;
        display: flex;
        gap: 0.8rem;
    }
    
    .cal-modal-item-link {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--cal-primary);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.2rem;
        white-space: nowrap;
        text-decoration: none;
    }
    
    .cal-modal-item-link:hover {
        color: var(--cal-accent);
    }
    
    /* Scrollbar customization for the right list */
    #cal-modal-events-scroll-list::-webkit-scrollbar {
        width: 6px;
    }
    
    #cal-modal-events-scroll-list::-webkit-scrollbar-track {
        background: var(--cal-cream);
        border-radius: 10px;
    }
    
    #cal-modal-events-scroll-list::-webkit-scrollbar-thumb {
        background: var(--cal-accent);
        border-radius: 10px;
    }
    
    /* Mobile responsive for full modal */
    @media (max-width: 768px) {
        .cal-modal {
            padding: 0.75rem;
        }
        .cal-full-modal-box {
            min-width: 0;
            width: 100%;
        }
        .cal-full-modal-body {
            grid-template-columns: 1fr;
            padding: 1.25rem 1.05rem;
            gap: 1.5rem;
        }
        .cal-full-modal-left {
            min-width: 0;
            width: 100%;
        }
        #cal-modal-days-grid .cal-grid-day {
            padding: 0.2rem 0.1rem;
            font-size: 0.78rem;
            aspect-ratio: 1;
        }
        .cal-modal-day-dots {
            gap: 1px;
            max-width: 100%;
            flex-wrap: wrap;
            overflow: hidden;
            height: auto;
            max-height: 10px;
            margin-top: 2px;
        }
        .cal-modal-day-dot {
            width: 4px;
            height: 4px;
        }
    }
</style>

<!-- Unified Cultural Calendar HTML View -->
<section class="cal-section" id="events-calendar">
    <div class="container">
        
        <!-- Header -->
        <div class="section-header">
            <span class="welcome-subtitle" style="text-align: center;">Unified Cultural Calendar</span>
            <h2 class="welcome-title" style="text-align: center; color: var(--cal-dark);">Explore festivals, holidays, cultural programs and association events.</h2>
            <div class="alpona-divider" style="margin-bottom: 2rem;">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>
        </div>

      

        <!-- Filter tabs categories -->
        <!-- <div class="cal-filter-wrap" style="margin-top: 1.5rem; margin-bottom: 2.2rem;">
            <button class="cal-filter-tab active" data-category="All"><i class="fa-solid fa-globe"></i> All</button>
            <button class="cal-filter-tab" data-category="Festivals"><i class="fa-solid fa-dharmachakra"></i> Festivals</button>
            <button class="cal-filter-tab" data-category="Puja"><i class="fa-solid fa-bell"></i> Puja</button>
            <button class="cal-filter-tab" data-category="Bengali Festivals"><i class="fa-solid fa-sun"></i> Bengali Festivals</button>
            <button class="cal-filter-tab" data-category="Hindu Festivals"><i class="fa-solid fa-om"></i> Hindu Festivals</button>
            <button class="cal-filter-tab" data-category="National Holidays"><i class="fa-solid fa-flag"></i> National Holidays</button>
            <button class="cal-filter-tab" data-category="Regional Holidays"><i class="fa-solid fa-map-pin"></i> Regional Holidays</button>
            <button class="cal-filter-tab" data-category="Cultural Events"><i class="fa-solid fa-theater-masks"></i> Cultural Events</button>
            <button class="cal-filter-tab" data-category="Association Events"><i class="fa-solid fa-users"></i> Association Events</button>
        </div> -->

          <!-- Top Controls Bar styled as per second image -->
        <div class="cal-top-bar" style="border: none; background: transparent; padding: 0; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: none;">
            <!-- Left group: Month Navigation (arrows and month name) -->
            <div style="display: flex; align-items: center; border: 1px solid var(--cal-light-gray); padding: 0.4rem; border-radius: 8px; background-color: var(--cal-white); box-shadow: var(--shadow-sm);">
                <button type="button" class="cal-scroll-arrow" id="cal-prev-month" style="border: none; background: none; width: 34px; height: 34px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-chevron-left"></i></button>
                <span id="cal-month-year-title" style="font-family: 'Outfit', var(--font-body); font-size: 1.3rem; font-weight: 700; color: var(--cal-dark); padding: 0 1.5rem; min-width: 180px; text-align: center; display: inline-block;">
                    <?php 
                        $monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                        echo $monthNames[$cal_month - 1] . ' ' . $cal_year;
                    ?>
                </span>
                <button type="button" class="cal-scroll-arrow" id="cal-next-month" style="border: none; background: none; width: 34px; height: 34px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-chevron-right"></i></button>
            </div>

            <!-- Right group: View Full Calendar Button -->
            <button type="button" id="cal-view-full-btn" class="btn" style="border: 2px solid var(--cal-accent); background-color: transparent; color: var(--red); padding: 0.6rem 1.4rem; border-radius: 30px; font-size: 0.95rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.6rem; transition: var(--transition); box-shadow: none;">
                <i class="fa-regular fa-calendar-days" style="color: var(--cal-accent); font-size: 1.15rem;"></i>
                <span>View Full Calendar</span>
            </button>
        </div>

        <!-- 1. MONTHLY GRID VIEW (Default) -->
        <div class="cal-layout-grid-view" id="cal-grid-view-container">
            <!-- Left Side calendar Grid -->
            <div class="cal-widget-panel">
                <div class="cal-grid-cols-7">
                    <div class="cal-weekday-hdr">Sun</div>
                    <div class="cal-weekday-hdr">Mon</div>
                    <div class="cal-weekday-hdr">Tue</div>
                    <div class="cal-weekday-hdr">Wed</div>
                    <div class="cal-weekday-hdr">Thu</div>
                    <div class="cal-weekday-hdr">Fri</div>
                    <div class="cal-weekday-hdr">Sat</div>
                </div>

                <div class="cal-grid-cols-7" id="cal-monthly-days-grid">
                    <?php
                    // Compute days structure
                    $first_day_of_week = date('w', strtotime("$cal_year-$cal_month-01"));
                    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $cal_month, $cal_year);
                    $prev_month_days = date('t', strtotime("-1 month", strtotime("$cal_year-$cal_month-01")));

                    // 1. Render previous month trailing days
                    for ($i = $first_day_of_week - 1; $i >= 0; $i--) {
                        $dayNum = $prev_month_days - $i;
                        echo "<div class='cal-grid-day other-month'>$dayNum</div>";
                    }

                    // 2. Render current month days
                    for ($dayNum = 1; $dayNum <= $days_in_month; $dayNum++) {
                        $m_str = str_pad($cal_month, 2, '0', STR_PAD_LEFT);
                        $d_str = str_pad($dayNum, 2, '0', STR_PAD_LEFT);
                        $dateStr = "$cal_year-$m_str-$d_str";

                        // Get events on this date
                        $day_events = [];
                        foreach ($calendar_events as $ev) {
                            if ($ev['display_start_date'] === $dateStr || 
                                (!empty($ev['display_end_date']) && $dateStr >= $ev['display_start_date'] && $dateStr <= $ev['display_end_date'])) {
                                $day_events[] = $ev;
                            }
                        }

                        $activeClass = ($dateStr === $default_active_date) ? 'active' : '';
                        $todayClass = ($dateStr === $today_str) ? 'today' : '';
                        echo "<div class='cal-grid-day $activeClass $todayClass' data-date='$dateStr'>";
                        echo "<span>$dayNum</span>";
                        
                        // Mini indicators box
                        echo "<div class='cal-day-indicator-box'>";
                        foreach ($day_events as $ev) {
                            $symbol = get_category_symbol($ev['display_category']);
                            echo "<span class='cal-mini-badge' title='" . htmlspecialchars($ev['display_title']) . "' data-cat='" . htmlspecialchars($ev['display_category']) . "'>$symbol</span>";
                        }
                        echo "</div>";
                        
                        echo "</div>";
                    }

                    // 3. Render next month trailing days
                    $cells_filled = $first_day_of_week + $days_in_month;
                    $trailing_cells = 42 - $cells_filled;
                    for ($dayNum = 1; $dayNum <= $trailing_cells; $dayNum++) {
                        echo "<div class='cal-grid-day other-month'>$dayNum</div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Right Side Selected Day List Sidebar -->
            <div class="cal-sidebar-panel">
                <div class="cal-sidebar-title" id="cal-selected-day-title">Events on <?php echo date('F d, Y', strtotime($default_active_date)); ?></div>
                <div class="cal-sidebar-list" id="cal-selected-day-list-container">
                    <!-- Loaded dynamically via JavaScript -->
                </div>
            </div>
        </div>

        <!-- 2. CHRONOLOGICAL LIST VIEW (Hidden by default, shown on toggle or on mobile) -->
        <div class="cal-list-view" id="cal-list-view-container" style="display: none;">
            <?php if (empty($calendar_events)): ?>
                <div style="text-align: center; color: var(--cal-gray); padding: 3rem 0; font-style: italic;">
                    No events or holidays scheduled for this month.
                </div>
            <?php else: ?>
                <?php foreach ($calendar_events as $ev): ?>
                    <?php 
                        $evDay = date('d', strtotime($ev['display_start_date']));
                        $evMonthName = date('M', strtotime($ev['display_start_date']));
                        $symbol = get_category_symbol($ev['display_category']);
                    ?>
                    <div class="cal-list-item" data-id="<?php echo $ev['id']; ?>" data-cat="<?php echo htmlspecialchars($ev['display_category']); ?>">
                        <div class="cal-list-date-box">
                            <span class="cal-list-day-num"><?php echo $evDay; ?></span>
                            <span class="cal-list-month-lbl"><?php echo $evMonthName; ?></span>
                        </div>
                        <div class="cal-list-info">
                            <div class="cal-list-title-row">
                                <span class="cal-sidebar-symbol"><?php echo $symbol; ?></span>
                                <h3 class="cal-list-title"><?php echo htmlspecialchars($ev['display_title']); ?></h3>
                            </div>
                            <p class="cal-list-desc">
                                <?php 
                                    if (!empty($ev['display_short_description'])) {
                                        echo htmlspecialchars($ev['display_short_description']);
                                    } else {
                                        echo htmlspecialchars(substr($ev['display_description'], 0, 140)) . '...';
                                    }
                                ?>
                            </p>
                            <div class="cal-list-meta">
                                <div class="cal-list-meta-item">
                                    <i class="fa-solid fa-clock"></i>
                                    <span><?php echo htmlspecialchars($ev['display_time']); ?></span>
                                </div>
                                <div class="cal-list-meta-item">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span><?php echo htmlspecialchars($ev['display_location']); ?></span>
                                </div>
                            </div>
                        </div>
                        <button class="btn-details view-event-btn" data-id="<?php echo $ev['id']; ?>">View Details</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- Unified Event Detailed Modal Dialog -->
<div class="cal-modal" id="cal-unified-event-modal">
    <div class="cal-modal-box">
        <button type="button" class="cal-modal-close-btn" id="cal-close-modal-btn" aria-label="Close dialog"><i class="fa-solid fa-xmark"></i></button>
        <div id="cal-modal-header-container">
            <!-- Header Image / Fallback Gradient -->
        </div>
        <div class="cal-modal-body">
            <span class="cal-modal-cat-tag" id="cal-md-category">FESTIVAL</span>
            <h2 class="cal-modal-title" id="cal-md-title">Event Display Title</h2>
            
            <div class="cal-modal-meta-grid">
                <div class="cal-modal-meta-cell">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span id="cal-md-date">Date here</span>
                </div>
                <div class="cal-modal-meta-cell">
                    <i class="fa-solid fa-clock"></i>
                    <span id="cal-md-time">Time here</span>
                </div>
                <div class="cal-modal-meta-cell" style="grid-column: span 2;">
                    <i class="fa-solid fa-location-dot"></i>
                    <span id="cal-md-location">Location Address</span>
                </div>
            </div>

            <p class="cal-modal-desc" id="cal-md-description">
                Description goes here...
            </p>

            <!-- Sub Schedules Activities -->
            <div class="cal-modal-schedules-section" id="cal-md-schedules-box" style="display: none;">
                <h4 class="cal-modal-schedules-title">Program Activities & Schedules</h4>
                <div class="cal-modal-schedule-list" id="cal-md-schedules-list">
                    <!-- Rows injected here -->
                </div>
            </div>

            <span class="cal-modal-source-lbl">
                <i class="fa-solid fa-globe"></i>
                <span id="cal-md-source">Source: Association</span>
            </span>

            <a href="#" target="_blank" class="btn-gcal" id="cal-md-add-gcal-btn">
                <i class="fa-brands fa-google"></i> Add to Google Calendar
            </a>
        </div>
    </div>
</div>

<!-- Unified Full Calendar Modal Dialog -->
<div class="cal-modal" id="cal-full-calendar-modal">
    <div class="cal-full-modal-box">
        <button type="button" class="cal-modal-close-btn" id="cal-close-full-modal-btn" aria-label="Close dialog"><i class="fa-solid fa-xmark"></i></button>
        <div class="cal-full-modal-body">
            <!-- Left Column: Mini Grid -->
            <div class="cal-full-modal-left">
                <div class="cal-modal-scroller" style="display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                    <button type="button" class="cal-scroll-arrow" id="cal-modal-prev-month" style="border: none; background: none; width: 34px; height: 34px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-chevron-left"></i></button>
                    <span id="cal-modal-month-year-title" style="font-family: 'Outfit', var(--font-body); font-size: 1.25rem; font-weight: 700; color: var(--cal-dark); padding: 0 1rem; min-width: 150px; text-align: center;">August 2026</span>
                    <button type="button" class="cal-scroll-arrow" id="cal-modal-next-month" style="border: none; background: none; width: 34px; height: 34px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                
                <div class="cal-grid-cols-7" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--cal-accent); text-align: center; margin-bottom: 0.8rem;">
                    <div>Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                </div>
                
                <div class="cal-grid-cols-7" id="cal-modal-days-grid" style="gap: 0.35rem;">
                    <!-- Rendered dynamically -->
                </div>
            </div>
            
            <!-- Right Column: Events List -->
            <div class="cal-full-modal-right">
                <div id="cal-modal-event-count" style="font-weight: bold; color: var(--cal-accent); margin-bottom: 1.2rem; font-family: 'Outfit', var(--font-body); font-size: 1rem;">0 events this month</div>
                <div id="cal-modal-events-scroll-list" style="overflow-y: auto; max-height: 380px; display: flex; flex-direction: column; gap: 1rem; padding-right: 0.5rem;">
                    <!-- Rendered dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SVG motifs definitions library -->
<svg style="display: none;">
    <!-- Lotus motif for Vaishnava / Puja festivals -->
    <g id="motif-lotus">
        <path d="M12,2C11.5,4,9.5,7,7.5,9C8.5,8.8,10.2,8.5,12,8.5C13.8,8.5,15.5,8.8,16.5,9C14.5,7,12.5,4,12,2Z" />
        <path d="M12,8.5C10.2,8.5,7,9.5,4.5,12C7,12,10.2,11.5,12,11.5C13.8,11.5,17,12,19.5,12C17,9.5,13.8,8.5,12,8.5Z" />
        <path d="M12,11.5C9.8,11.5,5.5,13,3,16.5C6,16,10.2,15,12,15C13.8,15,18,16,21,16.5C18.5,13,14.2,11.5,12,11.5Z" />
        <path d="M12,15C9.5,15,6.5,16.5,4,20C8,19,10.5,18.5,12,18.5C13.5,18.5,16,19,20,20C17.5,16.5,14.5,15,12,15Z" />
        <circle cx="12" cy="21" r="1.5" />
    </g>
    <!-- Conch motif for main festival celebrations -->
    <g id="motif-conch">
        <path d="M17.3,7.4C16.8,5.1,14.4,3.3,12,3.3C9.6,3.3,7.2,5.1,6.7,7.4C6.1,10.2,8,13.2,10.5,14.8C9.5,15.7,8.2,16.9,7.6,18.3C7,19.7,7.2,20.7,8.2,20.7C9.3,20.7,11.2,19.1,12.5,17.9C13.8,19.1,15.7,20.7,16.8,20.7C17.8,20.7,18,19.7,17.4,18.3C16.8,16.9,15.5,15.7,14.5,14.8C17,13.2,18.9,10.2,17.3,7.4ZM12,15.5C10,14.3,8.5,11.3,8.5,9C8.5,6.7,10,5.2,12,5.2C14,5.2,15.5,6.7,15.5,9C15.5,11.3,14,14.3,12,15.5Z" />
    </g>
    <!-- Flag motif for national and general holidays -->
    <g id="motif-flag">
        <path d="M5,3H6V21H5V3ZM6,5L19,9.5L6,14V5Z" />
    </g>
    <!-- Diya lamp motif for Diwali and Kali Puja -->
    <g id="motif-diya">
        <path d="M12,2C12,2 10,5 10,7C10,8.1 10.9,9 12,9C13.1,9 14,8.1 14,7C14,5 12,2 12,2ZM19,13C19,16.9 15.9,20 12,20C8.1,20 5,16.9 5,13C5,11.9 5.3,10.9 5.7,10H18.3C18.7,10.9 19,11.9 19,13Z" />
    </g>
</svg>

<!-- Dynamic Calendar JavaScript controller -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Hydrate event records parsed from PHP
    const events = <?php echo json_encode($calendar_events); ?>;

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    
    // Default selected date
    let selectedDateStr = '<?php echo $default_active_date; ?>';
    let currentCategory = 'All';
    let displayYear = <?php echo $cal_year; ?>;
    let displayMonth = <?php echo $cal_month; ?>;

    // 2. Elements Map
    const prevMonthBtn = document.getElementById('cal-prev-month');
    const nextMonthBtn = document.getElementById('cal-next-month');
    const todayJumperBtn = document.getElementById('cal-view-full-btn');

    const filterTabs = document.querySelectorAll('.cal-filter-tab');
    
    const selectedDayTitle = document.getElementById('cal-selected-day-title');
    const selectedDayList = document.getElementById('cal-selected-day-list-container');

    const modal = document.getElementById('cal-unified-event-modal');
    const closeModalBtn = document.getElementById('cal-close-modal-btn');

    // 3. Helper format date strings without timezone shifts
    function formatDateDisplay(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        return `${day} ${monthNames[month].substring(0, 3)} ${year}`;
    }

    function getCategorySymbol(cat) {
        switch (cat) {
            case 'Festivals':
            case 'Puja':
            case 'Hindu Festivals':
            case 'Bengali Festivals': return '🪔';
            case 'National Holidays':
            case 'Regional Holidays': return '🇮🇳';
            case 'Cultural Events': return '🎭';
            case 'Association Events':
            case 'Meeting': return '👥';
            default: return '📅';
        }
    }

    // 4. GOOGLE CALENDAR REDIRECT URL GENERATOR
    function getGoogleCalendarUrl(ev) {
        const title = encodeURIComponent(ev.display_title);
        const desc = encodeURIComponent((ev.display_short_description || ev.display_description) + "\n\nSource: " + ev.source);
        const loc = encodeURIComponent(ev.display_location);
        
        let startStr = ev.display_start_date.replace(/-/g, '');
        let endStr = '';
        
        if (ev.display_end_date) {
            const d = new Date(ev.display_end_date);
            d.setDate(d.getDate() + 1);
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            endStr = `${y}${m}${day}`;
        } else {
            const d = new Date(ev.display_start_date);
            d.setDate(d.getDate() + 1);
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            endStr = `${y}${m}${day}`;
        }
        
        let datesParam = `${startStr}/${endStr}`;
        
        if (parseInt(ev.display_all_day) === 0 && ev.display_start_time) {
            const startHHMM = ev.display_start_time.replace(/:/g, '').substring(0, 6);
            const endHHMM = ev.display_end_time ? ev.display_end_time.replace(/:/g, '').substring(0, 6) : '235959';
            datesParam = `${startStr}T${startHHMM}/${endStr}T${endHHMM}`;
        }
        
        return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&dates=${datesParam}&details=${desc}&location=${loc}&ctz=Asia/Kolkata`;
    }

    // 5. RENDER DAILY EVENTS SIDEBAR LIST
    function renderSelectedDayEvents() {
        const parts = selectedDateStr.split('-');
        const day = parseInt(parts[2], 10);
        const monthIndex = parseInt(parts[1], 10) - 1;
        const year = parseInt(parts[0], 10);

        selectedDayTitle.textContent = `Events on ${monthNames[monthIndex]} ${day}, ${year}`;
        selectedDayList.innerHTML = '';

        // Filter events for selected date
        let dayEvents = events.filter(ev => {
            if (ev.display_start_date === selectedDateStr) return true;
            if (ev.display_end_date && selectedDateStr >= ev.display_start_date && selectedDateStr <= ev.display_end_date) return true;
            return false;
        });

        // Filter by current active category tab
        if (currentCategory !== 'All') {
            dayEvents = dayEvents.filter(ev => ev.display_category === currentCategory);
        }

        if (dayEvents.length === 0) {
            selectedDayList.innerHTML = `<div class="cal-no-events-msg">No scheduled activities for this date.</div>`;
            return;
        }

        dayEvents.forEach(ev => {
            const row = document.createElement('div');
            row.className = 'cal-sidebar-row';
            row.setAttribute('data-id', ev.id);

            const symbol = getCategorySymbol(ev.display_category);

            row.innerHTML = `
                <div class="cal-sidebar-row-hdr">
                    <span class="cal-sidebar-symbol">${symbol}</span>
                    <span class="cal-sidebar-row-title">${ev.display_title}</span>
                </div>
                <div class="cal-sidebar-meta">
                    <span><i class="fa-solid fa-clock"></i> ${ev.display_time}</span>
                    <span><i class="fa-solid fa-location-dot"></i> ${ev.display_location}</span>
                </div>
            `;

            row.addEventListener('click', () => openEventDetailsModal(ev.id));
            selectedDayList.appendChild(row);
        });
    }

    // 6. EVENT MODAL LOADER
    function openEventDetailsModal(eventId) {
        const ev = events.find(e => parseInt(e.id) === parseInt(eventId));
        if (!ev) return;

        document.getElementById('cal-md-category').textContent = ev.display_category;
        document.getElementById('cal-md-title').textContent = ev.display_title;
        
        let dateString = formatDateDisplay(ev.display_start_date);
        if (ev.display_end_date && ev.display_end_date !== ev.display_start_date) {
            dateString += ' — ' + formatDateDisplay(ev.display_end_date);
        }
        document.getElementById('cal-md-date').textContent = dateString;
        document.getElementById('cal-md-time').textContent = ev.display_time;
        document.getElementById('cal-md-location').textContent = ev.display_location;
        document.getElementById('cal-md-description').textContent = ev.display_description;

        // Render Cover Image or fallback motif gradient
        const header = document.getElementById('cal-modal-header-container');
        if (ev.display_image && ev.display_image.trim() !== '') {
            header.innerHTML = `
                <div class="cal-modal-img-hdr">
                    <img src="${ev.display_image}" alt="${ev.display_title}" style="width:100%; height:100%; object-fit:cover;">
                </div>
            `;
        } else {
            header.innerHTML = `
                <div class="cal-modal-img-hdr">
                    <div class="cal-modal-fallback-grad">
                        <svg viewBox="0 0 24 24" class="cal-modal-fallback-svg"><use href="#motif-${ev.motif || 'lotus'}"></use></svg>
                    </div>
                </div>
            `;
        }

        // Render detailed schedules if present
        const schedulesBox = document.getElementById('cal-md-schedules-box');
        const schedulesList = document.getElementById('cal-md-schedules-list');
        schedulesList.innerHTML = '';
        if (ev.schedules && ev.schedules.length > 0) {
            ev.schedules.forEach(sch => {
                const schRow = document.createElement('div');
                schRow.className = 'cal-modal-schedule-row';
                schRow.innerHTML = `
                    <strong>${formatDateDisplay(sch.date)} ${sch.time ? '('+sch.time+')' : ''}</strong>
                    <span>${sch.title} ${sch.description ? ' - ' + sch.description : ''}</span>
                `;
                schedulesList.appendChild(schRow);
            });
            schedulesBox.style.display = 'block';
        } else {
            schedulesBox.style.display = 'none';
        }

        // Source label
        document.getElementById('cal-md-source').textContent = `Source: ${ev.source.toUpperCase()}`;

        // Add to Google Calendar url
        const addGcalBtn = document.getElementById('cal-md-add-gcal-btn');
        addGcalBtn.setAttribute('href', getGoogleCalendarUrl(ev));

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closeModalBtn.addEventListener('click', () => {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // 7. DYNAMIC CALENDAR CELLS RENDERER (AJAX updates)
    function renderCalendarGrid(year, month, eventsList) {
        const daysGrid = document.getElementById('cal-monthly-days-grid');
        daysGrid.innerHTML = '';

        const firstDayOfWeek = new Date(year, month - 1, 1).getDay();
        const daysInMonth = new Date(year, month, 0).getDate();
        const prevMonthDays = new Date(year, month - 1, 0).getDate();

        // 1. Render previous month trailing days
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const dayNum = prevMonthDays - i;
            const cell = document.createElement('div');
            cell.className = 'cal-grid-day other-month';
            cell.textContent = dayNum;
            daysGrid.appendChild(cell);
        }

        // 2. Render current month days
        const todayStr = '<?php echo date('Y-m-d'); ?>';
        for (let dayNum = 1; dayNum <= daysInMonth; dayNum++) {
            const m_str = String(month).padStart(2, '0');
            const d_str = String(dayNum).padStart(2, '0');
            const dateStr = `${year}-${m_str}-${d_str}`;

            const dayEvents = eventsList.filter(ev => {
                if (ev.display_start_date === dateStr) return true;
                if (ev.display_end_date && dateStr >= ev.display_start_date && dateStr <= ev.display_end_date) return true;
                return false;
            });

            const cell = document.createElement('div');
            let activeClass = (dateStr === selectedDateStr) ? 'active' : '';
            let todayClass = (dateStr === todayStr) ? 'today' : '';
            cell.className = `cal-grid-day ${activeClass} ${todayClass}`;
            cell.setAttribute('data-date', dateStr);

            const span = document.createElement('span');
            span.textContent = dayNum;
            cell.appendChild(span);

            const indicatorBox = document.createElement('div');
            indicatorBox.className = 'cal-day-indicator-box';
            dayEvents.forEach(ev => {
                const symbol = getCategorySymbol(ev.display_category);
                const badge = document.createElement('span');
                badge.className = 'cal-mini-badge';
                badge.title = ev.display_title;
                badge.setAttribute('data-cat', ev.display_category);
                badge.textContent = symbol;
                if (currentCategory !== 'All' && ev.display_category !== currentCategory) {
                    badge.style.display = 'none';
                }
                indicatorBox.appendChild(badge);
            });
            cell.appendChild(indicatorBox);

            cell.addEventListener('click', function() {
                document.querySelectorAll('#cal-monthly-days-grid .cal-grid-day:not(.other-month)').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                selectedDateStr = this.getAttribute('data-date');
                renderSelectedDayEvents();
            });

            daysGrid.appendChild(cell);
        }

        // 3. Render next month trailing days
        const cellsFilled = firstDayOfWeek + daysInMonth;
        const trailingCells = 42 - cellsFilled;
        for (let dayNum = 1; dayNum <= trailingCells; dayNum++) {
            const cell = document.createElement('div');
            cell.className = 'cal-grid-day other-month';
            cell.textContent = dayNum;
            daysGrid.appendChild(cell);
        }
    }

    // 8. DYNAMIC CALENDAR LIST VIEW RENDERER (AJAX updates)
    function renderCalendarList(eventsList) {
        const listContainer = document.getElementById('cal-list-view-container');
        listContainer.innerHTML = '';

        let filteredEvents = eventsList;
        if (currentCategory !== 'All') {
            filteredEvents = eventsList.filter(ev => ev.display_category === currentCategory);
        }

        // Add "X events this month" count header styled as per second image
        const countHeader = document.createElement('div');
        countHeader.className = 'cal-list-count-header';
        countHeader.style.cssText = 'font-weight: 700; color: var(--cal-accent); margin: 0 0 1.5rem 0; font-family: "Outfit", sans-serif; font-size: 1.25rem; text-align: left; width: 100%;';
        countHeader.textContent = `${filteredEvents.length} events this month`;
        listContainer.appendChild(countHeader);

        if (filteredEvents.length === 0) {
            const noEventsDiv = document.createElement('div');
            noEventsDiv.style.cssText = 'text-align: center; color: var(--cal-gray); padding: 3rem 0; font-style: italic; width: 100%;';
            noEventsDiv.textContent = 'No scheduled events or holidays for this month.';
            listContainer.appendChild(noEventsDiv);
            return;
        }

        filteredEvents.forEach(ev => {
            const dateParts = ev.display_start_date.split('-');
            const year = parseInt(dateParts[0], 10);
            const month = parseInt(dateParts[1], 10) - 1;
            const day = parseInt(dateParts[2], 10);

            const dateObj = new Date(year, month, day);
            const evDay = String(day).padStart(2, '0');
            const evMonthName = dateObj.toLocaleString('default', { month: 'short' });
            const symbol = getCategorySymbol(ev.display_category);

            const item = document.createElement('div');
            item.className = 'cal-list-item';
            item.setAttribute('data-id', ev.id);
            item.setAttribute('data-cat', ev.display_category);

            const descText = ev.display_short_description || (ev.display_description.substring(0, 140) + '...');

            item.innerHTML = `
                <div class="cal-list-date-box">
                    <span class="cal-list-day-num">${evDay}</span>
                    <span class="cal-list-month-lbl">${evMonthName}</span>
                </div>
                <div class="cal-list-info">
                    <div class="cal-list-title-row">
                        <span class="cal-sidebar-symbol">${symbol}</span>
                        <h3 class="cal-list-title">${ev.display_title}</h3>
                    </div>
                    <p class="cal-list-desc">${descText}</p>
                    <div class="cal-list-meta">
                        <div class="cal-list-meta-item">
                            <i class="fa-solid fa-clock"></i>
                            <span>${ev.display_time}</span>
                        </div>
                        <div class="cal-list-meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>${ev.display_location}</span>
                        </div>
                    </div>
                </div>
                <button class="btn-details view-event-btn" data-id="${ev.id}">View Details</button>
            `;

            item.querySelector('.view-event-btn').addEventListener('click', function(e) {
                e.stopPropagation();
                openEventDetailsModal(ev.id);
            });

            listContainer.appendChild(item);
        });
    }

    // 9. AJAX FETCH AND UPDATE CONTROLLER
    function fetchAndRenderMonth(year, month) {
        const gridView = document.getElementById('cal-grid-view-container');
        const listView = document.getElementById('cal-list-view-container');
        
        gridView.style.opacity = '0.5';
        listView.style.opacity = '0.5';

        fetch(`includes/calender.php?ajax=1&cal_year=${year}&cal_month=${month}`)
            .then(res => res.json())
            .then(data => {
                events.length = 0;
                events.push(...data);

                // Update month scroller title text
                const titleSpan = document.getElementById('cal-month-year-title');
                titleSpan.textContent = `${monthNames[month - 1]} ${year}`;

                // Calculate default selected date
                const todayStr = '<?php echo date('Y-m-d'); ?>';
                const todayParts = todayStr.split('-');
                const isCurrentView = (year === parseInt(todayParts[0]) && month === parseInt(todayParts[1]));

                if (isCurrentView) {
                    selectedDateStr = todayStr;
                } else {
                    selectedDateStr = `${year}-${String(month).padStart(2, '0')}-01`;
                }

                // Render dynamic views
                renderCalendarGrid(year, month, events);
                renderCalendarList(events);
                renderSelectedDayEvents();

                gridView.style.opacity = '1';
                listView.style.opacity = '1';
            })
            .catch(err => {
                console.error("AJAX calendar load error: ", err);
                gridView.style.opacity = '1';
                listView.style.opacity = '1';
            });
    }

    // 10. LIVE CATEGORY FRONTEND FILTERING
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.getAttribute('data-category');

            const indicators = document.querySelectorAll('.cal-mini-badge');
            indicators.forEach(ind => {
                if (currentCategory === 'All') {
                    ind.style.display = 'inline';
                } else {
                    ind.style.display = (ind.getAttribute('data-cat') === currentCategory) ? 'inline' : 'none';
                }
            });

            // Re-render list view to update items and count header
            renderCalendarList(events);
            renderSelectedDayEvents();
        });
    });

    // 11. ATTACH SCROLLER NAVIGATION EVENT HANDLERS
    prevMonthBtn.addEventListener('click', () => {
        displayMonth--;
        if (displayMonth < 1) {
            displayMonth = 12;
            displayYear--;
        }
        fetchAndRenderMonth(displayYear, displayMonth);
    });

    nextMonthBtn.addEventListener('click', () => {
        displayMonth++;
        if (displayMonth > 12) {
            displayMonth = 1;
            displayYear++;
        }
        fetchAndRenderMonth(displayYear, displayMonth);
    });

    // Modal scroller and state
    let modalYear = displayYear;
    let modalMonth = displayMonth;
    const fullModal = document.getElementById('cal-full-calendar-modal');
    const closeFullModalBtn = document.getElementById('cal-close-full-modal-btn');
    const modalPrevMonthBtn = document.getElementById('cal-modal-prev-month');
    const modalNextMonthBtn = document.getElementById('cal-modal-next-month');

    function renderModalCalendar(year, month, eventsList) {
        const daysGrid = document.getElementById('cal-modal-days-grid');
        daysGrid.innerHTML = '';

        const firstDayOfWeek = new Date(year, month - 1, 1).getDay();
        const daysInMonth = new Date(year, month, 0).getDate();
        const prevMonthDays = new Date(year, month - 1, 0).getDate();

        // 1. Render previous month trailing days
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const dayNum = prevMonthDays - i;
            const cell = document.createElement('div');
            cell.className = 'cal-grid-day other-month';
            cell.textContent = dayNum;
            daysGrid.appendChild(cell);
        }

        // 2. Render current month days
        const todayStr = '<?php echo date('Y-m-d'); ?>';
        for (let dayNum = 1; dayNum <= daysInMonth; dayNum++) {
            const m_str = String(month).padStart(2, '0');
            const d_str = String(dayNum).padStart(2, '0');
            const dateStr = `${year}-${m_str}-${d_str}`;

            const dayEvents = eventsList.filter(ev => {
                if (ev.display_start_date === dateStr) return true;
                if (ev.display_end_date && dateStr >= ev.display_start_date && dateStr <= ev.display_end_date) return true;
                return false;
            });

            const cell = document.createElement('div');
            let activeClass = (dateStr === selectedDateStr) ? 'active' : '';
            let todayClass = (dateStr === todayStr) ? 'today' : '';
            cell.className = `cal-grid-day ${activeClass} ${todayClass}`;
            cell.setAttribute('data-date', dateStr);

            const span = document.createElement('span');
            span.textContent = dayNum;
            cell.appendChild(span);

            // Mini dots box
            const dotsBox = document.createElement('div');
            dotsBox.className = 'cal-modal-day-dots';
            dayEvents.forEach(() => {
                const dot = document.createElement('span');
                dot.className = 'cal-modal-day-dot';
                dotsBox.appendChild(dot);
            });
            cell.appendChild(dotsBox);

            cell.addEventListener('click', function() {
                document.querySelectorAll('#cal-modal-days-grid .cal-grid-day:not(.other-month)').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                selectedDateStr = this.getAttribute('data-date');
                
                // Highlight corresponding cell in main grid too
                const mainCell = document.querySelector(`#cal-monthly-days-grid .cal-grid-day[data-date="${selectedDateStr}"]`);
                if (mainCell) {
                    document.querySelectorAll('#cal-monthly-days-grid .cal-grid-day:not(.other-month)').forEach(c => c.classList.remove('active'));
                    mainCell.classList.add('active');
                }
                
                renderSelectedDayEvents();
                
                // Scroll to corresponding scroll list event
                const firstEvId = dayEvents.length > 0 ? dayEvents[0].id : null;
                if (firstEvId) {
                    const scrollItem = document.querySelector(`.cal-modal-scroll-item[data-id="${firstEvId}"]`);
                    if (scrollItem) {
                        scrollItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        scrollItem.style.backgroundColor = '#F3EBDD';
                        setTimeout(() => {
                            scrollItem.style.backgroundColor = '';
                        }, 1500);
                    }
                }
            });

            daysGrid.appendChild(cell);
        }

        // 3. Render next month trailing days
        const cellsFilled = firstDayOfWeek + daysInMonth;
        const trailingCells = 42 - cellsFilled;
        for (let dayNum = 1; dayNum <= trailingCells; dayNum++) {
            const cell = document.createElement('div');
            cell.className = 'cal-grid-day other-month';
            cell.textContent = dayNum;
            daysGrid.appendChild(cell);
        }
    }

    function renderModalScrollList(eventsList) {
        const scrollList = document.getElementById('cal-modal-events-scroll-list');
        const countHeader = document.getElementById('cal-modal-event-count');
        
        scrollList.innerHTML = '';
        let filteredEvents = eventsList;
        if (currentCategory !== 'All') {
            filteredEvents = eventsList.filter(ev => ev.display_category === currentCategory);
        }
        
        countHeader.textContent = `${filteredEvents.length} events this month`;
        
        if (filteredEvents.length === 0) {
            scrollList.innerHTML = `
                <div style="text-align: center; color: var(--cal-gray); padding: 3rem 0; font-style: italic; width: 100%;">
                    No events or festivals scheduled for this month.
                </div>
            `;
            return;
        }
        
        filteredEvents.forEach(ev => {
            const dateParts = ev.display_start_date.split('-');
            const year = parseInt(dateParts[0], 10);
            const month = parseInt(dateParts[1], 10) - 1;
            const day = parseInt(dateParts[2], 10);

            const dateObj = new Date(year, month, day);
            const evDay = String(day).padStart(2, '0');
            const evMonthName = dateObj.toLocaleString('default', { month: 'short' });

            const item = document.createElement('div');
            item.className = 'cal-modal-scroll-item';
            item.setAttribute('data-id', ev.id);
            
            item.innerHTML = `
                <div class="cal-modal-item-datebox">
                    <span class="cal-modal-item-daynum">${evDay}</span>
                    <span class="cal-modal-item-monthlbl">${evMonthName}</span>
                </div>
                <div>
                    <h4 class="cal-modal-item-title">${ev.display_title}</h4>
                    <div class="cal-modal-item-meta">
                        <span><i class="fa-solid fa-clock"></i> ${ev.display_time}</span>
                        <span><i class="fa-solid fa-location-dot"></i> ${ev.display_location}</span>
                    </div>
                </div>
                <a href="#" class="cal-modal-item-link view-details-link">
                    <span>View Details</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            `;
            
            item.querySelector('.view-details-link').addEventListener('click', function(e) {
                e.preventDefault();
                fullModal.classList.remove('active');
                openEventDetailsModal(ev.id);
            });
            
            scrollList.appendChild(item);
        });
    }

    function fetchAndRenderModalMonth(year, month) {
        const leftCol = document.querySelector('.cal-full-modal-left');
        const rightCol = document.querySelector('.cal-full-modal-right');
        
        leftCol.style.opacity = '0.5';
        rightCol.style.opacity = '0.5';

        fetch(`includes/calender.php?ajax=1&cal_year=${year}&cal_month=${month}`)
            .then(res => res.json())
            .then(data => {
                const titleSpan = document.getElementById('cal-modal-month-year-title');
                titleSpan.textContent = `${monthNames[month - 1]} ${year}`;

                renderModalCalendar(year, month, data);
                renderModalScrollList(data);

                leftCol.style.opacity = '1';
                rightCol.style.opacity = '1';
            })
            .catch(err => {
                console.error("AJAX modal calendar load error: ", err);
                leftCol.style.opacity = '1';
                rightCol.style.opacity = '1';
            });
    }

    // View Full Calendar button opens the Full Calendar Modal
    if (todayJumperBtn) {
        todayJumperBtn.addEventListener('click', () => {
            modalYear = displayYear;
            modalMonth = displayMonth;
            fetchAndRenderMonth(displayYear, displayMonth); // Sync background to today if navigated
            fetchAndRenderModalMonth(modalYear, modalMonth);
            
            fullModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (closeFullModalBtn) {
        closeFullModalBtn.addEventListener('click', () => {
            fullModal.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    fullModal.addEventListener('click', (e) => {
        if (e.target === fullModal) {
            fullModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    modalPrevMonthBtn.addEventListener('click', () => {
        modalMonth--;
        if (modalMonth < 1) {
            modalMonth = 12;
            modalYear--;
        }
        fetchAndRenderModalMonth(modalYear, modalMonth);
    });

    modalNextMonthBtn.addEventListener('click', () => {
        modalMonth++;
        if (modalMonth > 12) {
            modalMonth = 1;
            modalYear++;
        }
        fetchAndRenderModalMonth(modalYear, modalMonth);
    });

    // Attach click handlers to PHP-rendered day cells
    const dayCells = document.querySelectorAll('.cal-grid-day:not(.other-month)');
    dayCells.forEach(cell => {
        cell.addEventListener('click', function() {
            dayCells.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            selectedDateStr = this.getAttribute('data-date');
            renderSelectedDayEvents();
        });
    });

    // Setup details buttons inside the list items
    document.querySelectorAll('.view-event-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.getAttribute('data-id');
            openEventDetailsModal(id);
        });
    });

    // Initialize sidebar and list view count headers
    renderCalendarList(events);
    renderSelectedDayEvents();
});
</script>
