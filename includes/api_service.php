<?php
// Core API Service Layer for Unified Cultural Calendar
require_once __DIR__ . '/../config.php';

// Define provider priority values (lower number = higher priority)
define('SOURCE_PRIORITY', [
    'association'     => 1,
    'vedika'          => 2,
    'google_calendar' => 3,
    'calendarific'    => 4,
    'festivo'         => 5
]);

/**
 * Seed initial common normalization rules
 */
function seed_normalization_rules($pdo) {
    $rules = [
        ['original_name' => 'Deepavali', 'normalized_name' => 'Diwali / Deepavali', 'normalized_category' => 'Festivals'],
        ['original_name' => 'Diwali Festival', 'normalized_name' => 'Diwali / Deepavali', 'normalized_category' => 'Festivals'],
        ['original_name' => 'Dipawali', 'normalized_name' => 'Diwali / Deepavali', 'normalized_category' => 'Festivals'],
        ['original_name' => 'Maha Shivaratri', 'normalized_name' => 'Maha Shivaratri', 'normalized_category' => 'Hindu Festivals'],
        ['original_name' => 'Mahashivratri', 'normalized_name' => 'Maha Shivaratri', 'normalized_category' => 'Hindu Festivals'],
        ['original_name' => 'Independence Day', 'normalized_name' => 'Independence Day', 'normalized_category' => 'National Holidays'],
        ['original_name' => 'Republic Day', 'normalized_name' => 'Republic Day', 'normalized_category' => 'National Holidays'],
        ['original_name' => 'Gandhi Jayanti', 'normalized_name' => 'Gandhi Jayanti', 'normalized_category' => 'National Holidays']
    ];

    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO `event_normalization` (`original_name`, `normalized_name`, `normalized_category`) VALUES (?, ?, ?)");
        foreach ($rules as $rule) {
            $stmt->execute([$rule['original_name'], $rule['normalized_name'], $rule['normalized_category']]);
        }
    } catch (PDOException $e) {
        error_log("Failed seeding normalization rules: " . $e->getMessage());
    }
}

/**
 * Helper to fetch a normalized event name if defined
 */
function get_normalized_event($pdo, $title, $default_category) {
    try {
        $stmt = $pdo->prepare("SELECT `normalized_name`, `normalized_category` FROM `event_normalization` WHERE LOWER(`original_name`) = LOWER(?)");
        $stmt->execute([trim($title)]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            return [
                'title' => $res['normalized_name'],
                'category' => $res['normalized_category']
            ];
        }
    } catch (PDOException $e) {
        // Fallback below
    }
    return [
        'title' => trim($title),
        'category' => $default_category
    ];
}

/**
 * Perform cURL request helper
 */
function perform_curl_request($url, $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt_array($ch, [
        CURLOPT_TIMEOUT => 8,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("API request returned HTTP code " . $httpCode);
    }
    return $response;
}

/**
 * 1. Vedika Festival Calendar API Integration
 */
function fetch_vedika_api($year, $month) {
    $apiKey = getenv('VEDIKA_API_KEY');
    if (!$apiKey || strpos($apiKey, 'your_') === 0) {
        // Safe high-fidelity Simulation Mode if keys are blank
        return fetch_vedika_simulation($year, $month);
    }

    $url = "https://api.vedika.org/v1/festivals?key=" . urlencode($apiKey) . "&year=" . intval($year) . "&month=" . intval($month) . "&country=IN";
    try {
        $res = perform_curl_request($url);
        $data = json_decode($res, true);
        $results = [];
        if (isset($data['festivals']) && is_array($data['festivals'])) {
            foreach ($data['festivals'] as $item) {
                $results[] = [
                    'title' => $item['name'],
                    'date' => $item['date'],
                    'time' => 'All day',
                    'location' => 'Home / Temples',
                    'category' => 'Bengali Festivals',
                    'description' => isset($item['description']) ? $item['description'] : 'Traditional Bengali festival celebration.',
                    'motif' => 'lotus',
                    'external_event_id' => 'vedika_' . $item['id']
                ];
            }
        }
        return $results;
    } catch (Exception $e) {
        error_log("Vedika API Failure: " . $e->getMessage() . ". Falling back to simulation.");
        return fetch_vedika_simulation($year, $month);
    }
}

function fetch_vedika_simulation($year, $month) {
    $simulated = [
        '08-16' => [
            [
                'title' => 'Srila Raghunandana Thakur Disappearance',
                'category' => 'Disappearance Day',
                'description' => 'Srila Raghunandana Thakur Disappearance is observed with special kirtans, prayers, and distribution of special prasadam.',
                'motif' => 'lotus'
            ],
            [
                'title' => 'Srila Vamsidas Babaji Disappearance',
                'category' => 'Disappearance Day',
                'description' => 'Srila Vamsidas Babaji Disappearance is observed with special kirtans and lectures detailing his renounced life and intense devotion to Lord Krishna.',
                'motif' => 'lotus'
            ]
        ],
        '08-24' => [
            [
                'title' => 'Srila Rupa Goswami Disappearance',
                'category' => 'Disappearance Day',
                'description' => 'Disappearance festival of Srila Rupa Goswami, the chief of the Six Goswamis of Vrindavan. Special readings from his literary contributions like Bhakti-rasamrta-sindhu.',
                'motif' => 'lotus'
            ]
        ],
        '09-04' => [
            [
                'title' => 'Maha Janmashtami Celebration',
                'category' => 'Festivals',
                'description' => 'Grand celebration of the appearance day of Lord Sri Krishna. The festival includes Abhishek, dramatic plays, traditional dances, kirtan, and midnight Aarati.',
                'motif' => 'conch'
            ]
        ],
        '10-17' => [
            [
                'title' => 'Durga Puja - Maha Saptami',
                'category' => 'Festivals',
                'description' => 'Durga Puja celebrations kick off with Maha Saptami morning rituals, pran pratishtha, Pushpanjali, community lunch (Bhog), and evening cultural recitals.',
                'motif' => 'conch'
            ]
        ],
        '10-18' => [
            [
                'title' => 'Durga Puja - Maha Ashtami',
                'category' => 'Festivals',
                'description' => 'Maha Ashtami morning Pushpanjali, Kumari Puja, and the highly auspicious Sandhi Puja. Followed by a massive community bhog distribution and theatrical programs.',
                'motif' => 'conch'
            ]
        ],
        '11-08' => [
            [
                'title' => 'Kali Puja & Diwali Festival',
                'category' => 'Festivals',
                'description' => 'Traditional Kali Puja performed at midnight. The entire ground is lit up with thousands of clay lamps (diyas), accompanied by cultural events and snacks stalls.',
                'motif' => 'diya'
            ]
        ]
    ];

    $month_str = str_pad($month, 2, '0', STR_PAD_LEFT);
    $results = [];
    $id_counter = 1;
    foreach ($simulated as $day_key => $evs) {
        if (strpos($day_key, $month_str . '-') === 0) {
            foreach ($evs as $ev) {
                $results[] = [
                    'title' => $ev['title'],
                    'date' => $year . '-' . $day_key,
                    'time' => 'All day',
                    'location' => 'Noida Sectors',
                    'category' => $ev['category'],
                    'description' => $ev['description'],
                    'motif' => $ev['motif'],
                    'external_event_id' => 'vedika_sim_' . $year . '_' . $month_str . '_' . ($id_counter++)
                ];
            }
        }
    }
    return $results;
}

/**
 * 2. Calendarific API Integration
 */
function fetch_calendarific_api($year, $month) {
    $apiKey = getenv('CALENDARIFIC_API_KEY');
    if (!$apiKey || strpos($apiKey, 'your_') === 0) {
        return fetch_calendarific_simulation($year, $month);
    }

    $url = "https://calendarific.com/api/v2/holidays?api_key=" . urlencode($apiKey) . "&country=IN&year=" . intval($year) . "&month=" . intval($month);
    try {
        $res = perform_curl_request($url);
        $data = json_decode($res, true);
        $results = [];
        if (isset($data['response']['holidays']) && is_array($data['response']['holidays'])) {
            foreach ($data['response']['holidays'] as $item) {
                $results[] = [
                    'title' => $item['name'],
                    'date' => isset($item['date']['iso']) ? substr($item['date']['iso'], 0, 10) : '',
                    'time' => 'All day',
                    'location' => 'National',
                    'category' => 'National Holidays',
                    'description' => isset($item['description']) ? $item['description'] : 'Public national holiday observation.',
                    'motif' => 'flag',
                    'external_event_id' => 'calendarific_' . $item['name'] . '_' . $year
                ];
            }
        }
        return $results;
    } catch (Exception $e) {
        error_log("Calendarific API Failure: " . $e->getMessage() . ". Falling back to simulation.");
        return fetch_calendarific_simulation($year, $month);
    }
}

function fetch_calendarific_simulation($year, $month) {
    $results = [];
    if ($month == 8) {
        $results[] = [
            'title' => 'Independence Day',
            'date' => $year . '-08-15',
            'time' => 'All day',
            'location' => 'India',
            'category' => 'National Holidays',
            'description' => 'Flag hoisting celebrations nationwide on India\'s Independence Day.',
            'motif' => 'flag',
            'external_event_id' => 'calendarific_sim_ind_day_' . $year
        ];
    } elseif ($month == 10) {
        $results[] = [
            'title' => 'Mahatma Gandhi Jayanti',
            'date' => $year . '-10-02',
            'time' => 'All day',
            'location' => 'India',
            'category' => 'National Holidays',
            'description' => 'Birth anniversary of Mahatma Gandhi, Father of the Nation.',
            'motif' => 'flag',
            'external_event_id' => 'calendarific_sim_gandhi_' . $year
        ];
    }
    return $results;
}

/**
 * 3. Festivo Holidays API Integration
 */
function fetch_festivo_api($year, $month) {
    $apiKey = getenv('FESTIVO_API_KEY');
    if (!$apiKey || strpos($apiKey, 'your_') === 0) {
        return fetch_festivo_simulation($year, $month);
    }

    $url = "https://api.getfestivo.com/v2/holidays?api_key=" . urlencode($apiKey) . "&country=IN&year=" . intval($year) . "&month=" . intval($month);
    try {
        $res = perform_curl_request($url);
        $data = json_decode($res, true);
        $results = [];
        if (isset($data['holidays']) && is_array($data['holidays'])) {
            foreach ($data['holidays'] as $item) {
                $results[] = [
                    'title' => $item['name'],
                    'date' => $item['date'],
                    'time' => 'All day',
                    'location' => 'Regional',
                    'category' => 'Regional Holidays',
                    'description' => 'Observance day holiday.',
                    'motif' => 'flag',
                    'external_event_id' => 'festivo_' . $item['id']
                ];
            }
        }
        return $results;
    } catch (Exception $e) {
        error_log("Festivo API Failure: " . $e->getMessage() . ". Falling back to simulation.");
        return fetch_festivo_simulation($year, $month);
    }
}

function fetch_festivo_simulation($year, $month) {
    $results = [];
    if ($month == 8) {
        $results[] = [
            'title' => 'Raksha Bandhan',
            'date' => $year . '-08-28',
            'time' => 'All day',
            'location' => 'Regional',
            'category' => 'Regional Holidays',
            'description' => 'Sibling bonding festival and public holiday.',
            'motif' => 'lotus',
            'external_event_id' => 'festivo_sim_rakhi_' . $year
        ];
    }
    return $results;
}

/**
 * 4. Google Calendar API Integration with Client OAuth Refresh
 */
function fetch_google_calendar_api($year, $month) {
    $clientId = getenv('GOOGLE_CLIENT_ID');
    $clientSecret = getenv('GOOGLE_CLIENT_SECRET');
    $refreshToken = getenv('GOOGLE_REFRESH_TOKEN');
    $calendarId = getenv('GOOGLE_CALENDAR_ID') ?: 'primary';

    if (!$clientId || !$clientSecret || !$refreshToken || strpos($clientId, 'your_') === 0) {
        return fetch_google_calendar_simulation($year, $month);
    }

    try {
        // First get access token using refresh token
        $tokenUrl = "https://oauth2.googleapis.com/token";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ]));
        $tokenRes = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($tokenRes, true);
        if (!isset($tokenData['access_token'])) {
            throw new Exception("Could not fetch Google Calendar access token.");
        }

        $accessToken = $tokenData['access_token'];

        // Build start/end datetime boundary in ISO format
        $startStr = sprintf("%04d-%02d-01T00:00:00Z", $year, $month);
        $endDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $endStr = sprintf("%04d-%02d-%02dT23:59:59Z", $year, $month, $endDay);

        $eventsUrl = "https://www.googleapis.com/calendar/v3/calendars/" . urlencode($calendarId) . "/events" .
            "?timeMin=" . urlencode($startStr) . "&timeMax=" . urlencode($endStr) . "&singleEvents=true&orderBy=startTime";

        $res = perform_curl_request($eventsUrl, [
            "Authorization: Bearer " . $accessToken,
            "Accept: application/json"
        ]);

        $data = json_decode($res, true);
        $results = [];

        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                // Parse date or datetime
                $dateVal = '';
                $allDay = 1;
                $startTimeStr = 'All day';
                if (isset($item['start']['date'])) {
                    $dateVal = $item['start']['date'];
                } elseif (isset($item['start']['dateTime'])) {
                    $dateVal = substr($item['start']['dateTime'], 0, 10);
                    $allDay = 0;
                    $startTimeStr = date('H:i:s', strtotime($item['start']['dateTime']));
                }

                $results[] = [
                    'title' => $item['summary'],
                    'date' => $dateVal,
                    'time' => $startTimeStr,
                    'location' => isset($item['location']) ? $item['location'] : 'Google Calendar Event',
                    'category' => 'Association Events',
                    'description' => isset($item['description']) ? $item['description'] : 'Imported Google Calendar item.',
                    'motif' => 'flag',
                    'all_day' => $allDay,
                    'external_event_id' => 'gcal_' . $item['id']
                ];
            }
        }
        return $results;
    } catch (Exception $e) {
        error_log("Google Calendar Sync Error: " . $e->getMessage() . ". Falling back to simulation.");
        return fetch_google_calendar_simulation($year, $month);
    }
}

function fetch_google_calendar_simulation($year, $month) {
    $results = [];
    if ($month == 8) {
        $results[] = [
            'title' => 'Annual Cultural Night Rehearsal',
            'date' => $year . '-08-20',
            'time' => '18:00:00',
            'location' => 'Association Office Hall',
            'category' => 'Cultural Events',
            'description' => 'General rehearsals for stage artists, dancers, and musicians.',
            'motif' => 'flag',
            'all_day' => 0,
            'external_event_id' => 'gcal_sim_rehearsal_' . $year
        ];
    }
    return $results;
}

/**
 * Core Cache synchronizer method
 */
function sync_monthly_events($pdo, $year, $month) {
    // Seed normalization rules once
    seed_normalization_rules($pdo);

    $sources = ['vedika', 'calendarific', 'festivo', 'google_calendar'];

    foreach ($sources as $source) {
        // Query sync logs to verify cache status
        $stmt = $pdo->prepare("SELECT * FROM `sync_logs` WHERE `year` = ? AND `month` = ? AND `source` = ?");
        $stmt->execute([$year, $month, $source]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);

        // API calls cached for 24 hours
        if ($log && (time() - strtotime($log['last_synced_at']) < 86400) && $log['status'] === 'success') {
            continue; // Synced recently, skip network load
        }

        try {
            $events = [];
            if ($source === 'vedika') {
                $events = fetch_vedika_api($year, $month);
            } elseif ($source === 'calendarific') {
                $events = fetch_calendarific_api($year, $month);
            } elseif ($source === 'festivo') {
                $events = fetch_festivo_api($year, $month);
            } elseif ($source === 'google_calendar') {
                $events = fetch_google_calendar_api($year, $month);
            }

            // Save records to database
            save_external_events($pdo, $events, $source, $year, $month);

            // Log successful execution
            if ($log) {
                $up = $pdo->prepare("UPDATE `sync_logs` SET `status` = 'success', `last_synced_at` = CURRENT_TIMESTAMP WHERE `id` = ?");
                $up->execute([$log['id']]);
            } else {
                $ins = $pdo->prepare("INSERT INTO `sync_logs` (`year`, `month`, `source`, `status`) VALUES (?, ?, ?, 'success')");
                $ins->execute([$year, $month, $source]);
            }
        } catch (Exception $e) {
            error_log("Failed syncing provider $source: " . $e->getMessage());

            if ($log) {
                $up = $pdo->prepare("UPDATE `sync_logs` SET `status` = 'failed' WHERE `id` = ?");
                $up->execute([$log['id']]);
            } else {
                $ins = $pdo->prepare("INSERT INTO `sync_logs` (`year`, `month`, `source`, `status`) VALUES (?, ?, ?, 'failed')");
                $ins->execute([$year, $month, $source]);
            }
        }
    }

    // Resolve matching name duplicates for the given month
    resolve_monthly_duplicates($pdo, $year, $month);
}

/**
 * Parse and insert imported external events, checking normalization mapping
 */
function save_external_events($pdo, $events, $source, $year, $month) {
    if (empty($events)) return;

    $stmtCheck = $pdo->prepare("SELECT `id`, `is_custom` FROM `events` WHERE `source` = ? AND `external_event_id` = ?");
    $stmtInsert = $pdo->prepare("INSERT INTO `events` 
        (`title`, `slug`, `short_description`, `date`, `time`, `start_time`, `end_time`, `all_day`, `location`, `category`, `event_type`, `image`, `description`, `motif`, `source`, `external_event_id`, `is_custom`, `is_external`, `is_active`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, 1)");

    foreach ($events as $ev) {
        if (empty($ev['date'])) continue;

        // Apply normalization layer mapping
        $normalized = get_normalized_event($pdo, $ev['title'], $ev['category']);
        $display_title = $normalized['title'];
        $display_category = $normalized['category'];

        // Auto-slugify
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $display_title));

        // Format times
        $start_time = null;
        $end_time = null;
        $all_day = isset($ev['all_day']) ? intval($ev['all_day']) : 1;
        if (!$all_day && !empty($ev['time']) && $ev['time'] !== 'All day') {
            $start_time = $ev['time'];
        }

        // Check if already in database
        $stmtCheck->execute([$source, $ev['external_event_id']]);
        $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$exists) {
            $stmtInsert->execute([
                $display_title,
                $slug,
                substr($ev['description'], 0, 250),
                $ev['date'],
                $ev['time'],
                $start_time,
                $end_time,
                $all_day,
                $ev['location'],
                $display_category,
                'external',
                isset($ev['image']) ? $ev['image'] : '',
                $ev['description'],
                $ev['motif'],
                $source,
                $ev['external_event_id']
            ]);
        }
    }
}

/**
 * Helper to determine if two event titles are duplicates on the same day
 */
function are_events_duplicate($title1, $title2) {
    // Clean titles to base lowercase alphanumeric words
    $t1 = strtolower(trim(preg_replace('/[^A-Za-z0-9\s]+/', '', $title1)));
    $t2 = strtolower(trim(preg_replace('/[^A-Za-z0-9\s]+/', '', $title2)));
    
    if ($t1 === $t2) {
        return true;
    }
    
    // Direct substring match (e.g. "Independence Day Ceremony" vs "Independence Day")
    if (strpos($t1, $t2) !== false || strpos($t2, $t1) !== false) {
        return true;
    }
    
    // Keyword similarity check
    $noise_words = ['of', 'the', 'in', 'and', 'a', 'celebration', 'ceremony', 'festival', 'day', 'observance', 'holiday'];
    $words1 = array_filter(explode(' ', $t1), function($w) use ($noise_words) {
        return strlen($w) > 2 && !in_array($w, $noise_words);
    });
    $words2 = array_filter(explode(' ', $t2), function($w) use ($noise_words) {
        return strlen($w) > 2 && !in_array($w, $noise_words);
    });
    
    if (empty($words1) || empty($words2)) {
        return false;
    }
    
    $intersection = array_intersect($words1, $words2);
    if (!empty($intersection)) {
        return true;
    }
    
    return false;
}

/**
 * Group events on the same date and resolve priorities, settings duplicates inactive
 */
function resolve_monthly_duplicates($pdo, $year, $month) {
    try {
        // Fetch all events scheduled in this month range
        $start_date = sprintf("%04d-%02d-01", $year, $month);
        $end_date = sprintf("%04d-%02d-%02d", $year, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year));

        $stmt = $pdo->prepare("SELECT `id`, `title`, `date`, `source`, `is_custom` FROM `events` 
            WHERE `date` >= ? AND `date` <= ?");
        $stmt->execute([$start_date, $end_date]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by Date
        $by_date = [];
        foreach ($events as $ev) {
            $by_date[$ev['date']][] = $ev;
        }

        $stmtUpdate = $pdo->prepare("UPDATE `events` SET `is_active` = ?, `status` = ? WHERE `id` = ?");

        foreach ($by_date as $date => $day_events) {
            if (count($day_events) <= 1) {
                if (!empty($day_events)) {
                    $stmtUpdate->execute([1, 'active', $day_events[0]['id']]);
                }
                continue;
            }

            // Cluster duplicates on this day
            $groups = [];
            foreach ($day_events as $ev) {
                $added = false;
                foreach ($groups as &$group) {
                    if (are_events_duplicate($ev['title'], $group[0]['title'])) {
                        $group[] = $ev;
                        $added = true;
                        break;
                    }
                }
                if (!$added) {
                    $groups[] = [$ev];
                }
            }

            // Resolve priority within each duplicate cluster
            foreach ($groups as $group) {
                if (count($group) == 1) {
                    $stmtUpdate->execute([1, 'active', $group[0]['id']]);
                    continue;
                }

                $best_ev = null;
                $best_prio = 999;

                foreach ($group as $ev) {
                    $src = $ev['source'] ?: 'association';
                    $prio = isset(SOURCE_PRIORITY[$src]) ? SOURCE_PRIORITY[$src] : 99;
                    if ($prio < $best_prio) {
                        $best_prio = $prio;
                        $best_ev = $ev;
                    }
                }

                // Activate the highest priority source, deactivate duplicates
                foreach ($group as $ev) {
                    if ($ev['id'] === $best_ev['id']) {
                        $stmtUpdate->execute([1, 'active', $ev['id']]);
                    } else {
                        $stmtUpdate->execute([0, 'duplicate', $ev['id']]);
                    }
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Failed duplicate resolution: " . $e->getMessage());
    }
}

/**
 * Fetch unified view including overrides
 */
function fetch_unified_events($pdo, $year, $month, $filter_category = 'All') {
    // Sync cache before loading data only if synchronization is enabled
    if (!defined('ENABLE_API_SYNC') || ENABLE_API_SYNC) {
        sync_monthly_events($pdo, $year, $month);
    }

    $start_date = sprintf("%04d-%02d-01", $year, $month);
    $end_date = sprintf("%04d-%02d-%02d", $year, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year));

    $sql = "SELECT 
        e.`id`,
        e.`time`,
        COALESCE(eo.`title`, e.`title`) as `display_title`,
        COALESCE(eo.`short_description`, e.`short_description`) as `display_short_description`,
        COALESCE(eo.`description`, e.`description`) as `display_description`,
        COALESCE(eo.`location`, e.`location`) as `display_location`,
        COALESCE(eo.`category`, e.`category`) as `display_category`,
        COALESCE(eo.`start_date`, e.`date`) as `display_start_date`,
        COALESCE(eo.`end_date`, e.`end_date`) as `display_end_date`,
        COALESCE(eo.`start_time`, e.`start_time`) as `display_start_time`,
        COALESCE(eo.`end_time`, e.`end_time`) as `display_end_time`,
        COALESCE(eo.`all_day`, e.`all_day`) as `display_all_day`,
        COALESCE(eo.`image`, e.`image`) as `display_image`,
        e.`motif`,
        e.`source`,
        e.`is_custom`,
        e.`external_event_id`,
        eo.`id` as `is_overridden`
    FROM `events` e
    LEFT JOIN `event_overrides` eo ON e.`id` = eo.`event_id`
    WHERE e.`is_active` = 1 
      AND (
        (e.`date` >= :start AND e.`date` <= :end)
        OR (e.`end_date` IS NOT NULL AND e.`date` <= :end AND e.`end_date` >= :start)
      )";

    if ($filter_category !== 'All') {
        $sql .= " AND COALESCE(eo.`category`, e.`category`) = :category";
    }

    $sql .= " ORDER BY e.`date` ASC, e.`id` ASC";

    try {
        $stmt = $pdo->prepare($sql);
        $params = ['start' => $start_date, 'end' => $end_date];
        if ($filter_category !== 'All') {
            $params['category'] = $filter_category;
        }
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch sub-schedules for each event
        foreach ($results as &$ev) {
            // Determine display_time dynamically
            if ($ev['display_all_day']) {
                $ev['display_time'] = 'All day';
            } else {
                $t_str = '';
                if (!empty($ev['display_start_time'])) {
                    $t_str = date('g:i A', strtotime($ev['display_start_time']));
                    if (!empty($ev['display_end_time'])) {
                        $t_str .= ' - ' . date('g:i A', strtotime($ev['display_end_time']));
                    }
                } else {
                    $t_str = !empty($ev['time']) ? $ev['time'] : 'All day';
                }
                $ev['display_time'] = $t_str;
            }

            $stmtSch = $pdo->prepare("SELECT `id`, `date`, `time`, `title`, `description` 
                FROM `event_schedules` WHERE `event_id` = ? ORDER BY `date` ASC, `time` ASC");
            $stmtSch->execute([$ev['id']]);
            $ev['schedules'] = $stmtSch->fetchAll(PDO::FETCH_ASSOC);
        }
        return $results;
    } catch (PDOException $e) {
        error_log("Failed fetching unified calendar: " . $e->getMessage());
        return [];
    }
}
?>
