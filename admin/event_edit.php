<?php
session_start();
require_once __DIR__ . '/../config.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'short_description' => '',
    'date' => '',
    'end_date' => '',
    'time' => '',
    'start_time' => '',
    'end_time' => '',
    'all_day' => 1,
    'location' => '',
    'address' => '',
    'city' => '',
    'state' => '',
    'country' => 'India',
    'latitude' => '',
    'longitude' => '',
    'map_url' => '',
    'category' => 'Festivals',
    'image' => '',
    'motif' => 'lotus',
    'description' => '',
    'is_custom' => 1,
    'source' => 'association'
];
$schedules = [];

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `events` WHERE `id` = ?");
        $stmt->execute([$id]);
        $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched) {
            $item = $fetched;
            
            // Check for override if external
            if ($item['is_custom'] == 0) {
                $stmtOverride = $pdo->prepare("SELECT * FROM `event_overrides` WHERE `event_id` = ?");
                $stmtOverride->execute([$id]);
                $override = $stmtOverride->fetch(PDO::FETCH_ASSOC);
                if ($override) {
                    foreach ($override as $key => $val) {
                        if ($key !== 'id' && $key !== 'event_id' && $val !== null && $val !== '') {
                            if ($key === 'start_date') {
                                $item['date'] = $val;
                            } else {
                                $item[$key] = $val;
                            }
                        }
                    }
                }
            }

            // Load schedules
            $stmtSch = $pdo->prepare("SELECT * FROM `event_schedules` WHERE `event_id` = ? ORDER BY `date` ASC, `time` ASC");
            $stmtSch->execute([$id]);
            $schedules = $stmtSch->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        die("Error fetching event: " . $e->getMessage());
    }
}
?>
<?php
$page_title = $id > 0 ? ($item['is_custom'] == 0 ? 'Customize Event' : 'Modify Custom Event') : 'Create New Event';
require_once __DIR__ . '/includes/sidebar.php';
?>
            <div class="form-card">
        <div class="form-header">
            <h1 class="form-title">
                <?php 
                    if ($id > 0) {
                        echo $item['is_custom'] == 0 ? 'Customize Synced API Event' : 'Modify Custom Event';
                    } else {
                        echo 'Create New Event';
                    }
                ?>
            </h1>
            <div class="form-subtitle">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Configure details for the unified cultural calendar.</span>
            </div>
        </div>

        <div class="form-body">
            <!-- Override Info Notice banner -->
            <?php if ($item['is_custom'] == 0): ?>
                <div style="background-color: #FFF9E6; border: 1px solid #FFE0B2; padding: 1.2rem; border-radius: 8px; margin-bottom: 2rem; color: #B78103; font-size: 0.92rem; display: flex; align-items: flex-start; gap: 0.8rem;">
                    <i class="fa-solid fa-circle-info" style="font-size: 1.2rem; margin-top: 2px; color: var(--gold);"></i>
                    <div>
                        <strong>Customizing Synced Event (API Override)</strong>: You are overriding fields for a holiday pulled from an external calendar API (Source: <strong><?php echo htmlspecialchars($item['source']); ?></strong>). The original provider metadata remains untouched. Your customizations take priority on the user-facing calendar list.
                    </div>
                </div>
            <?php endif; ?>

            <form action="action.php?act=save_event" method="POST">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">

                <div class="form-grid">
                    <!-- Title -->
                    <div class="form-group full-width">
                        <label class="form-label" for="title">Event Title</label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Durga Puja - Sandhi Puja" value="<?php echo htmlspecialchars($item['title']); ?>" required>
                    </div>

                    <!-- Category & Fallback Motif -->
                    <div class="form-group">
                        <label class="form-label" for="category">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="Festivals" <?php echo $item['category'] === 'Festivals' ? 'selected' : ''; ?>>Festivals</option>
                            <option value="Puja" <?php echo $item['category'] === 'Puja' ? 'selected' : ''; ?>>Puja</option>
                            <option value="Hindu Festivals" <?php echo $item['category'] === 'Hindu Festivals' ? 'selected' : ''; ?>>Hindu Festivals</option>
                            <option value="Bengali Festivals" <?php echo $item['category'] === 'Bengali Festivals' ? 'selected' : ''; ?>>Bengali Festivals</option>
                            <option value="National Holidays" <?php echo $item['category'] === 'National Holidays' ? 'selected' : ''; ?>>National Holidays</option>
                            <option value="Regional Holidays" <?php echo $item['category'] === 'Regional Holidays' ? 'selected' : ''; ?>>Regional Holidays</option>
                            <option value="Cultural Events" <?php echo $item['category'] === 'Cultural Events' ? 'selected' : ''; ?>>Cultural Events</option>
                            <option value="Association Events" <?php echo $item['category'] === 'Association Events' ? 'selected' : ''; ?>>Association Events</option>
                            <option value="Meeting" <?php echo $item['category'] === 'Meeting' ? 'selected' : ''; ?>>Meeting</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="motif">Decorative Motif Icon</label>
                        <select id="motif" name="motif" class="form-control" required>
                            <option value="lotus" <?php echo $item['motif'] === 'lotus' ? 'selected' : ''; ?>>Lotus Flower (Spiritual/Normal)</option>
                            <option value="conch" <?php echo $item['motif'] === 'conch' ? 'selected' : ''; ?>>Conch Shell (Pujas/Celebration)</option>
                            <option value="flag" <?php echo $item['motif'] === 'flag' ? 'selected' : ''; ?>>Flag (National/General holidays)</option>
                            <option value="diya" <?php echo $item['motif'] === 'diya' ? 'selected' : ''; ?>>Diya Lamp (Diwali/Kali Puja)</option>
                        </select>
                    </div>

                    <!-- Date & Ranges -->
                    <div class="section-divider-title">Date & Duration Boundaries</div>

                    <div class="form-group">
                        <label class="form-label" for="date">Start Date</label>
                        <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($item['date']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="end_date">End Date (Optional, for multi-day events)</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($item['end_date']); ?>">
                    </div>

                    <!-- Time Constraints -->
                    <div class="section-divider-title">Timings & Hours</div>

                    <div class="form-group" style="display: flex; align-items: center;">
                        <label class="form-checkbox-label">
                            <input type="checkbox" id="all_day" name="all_day" value="1" class="checkbox-input" <?php echo $item['all_day'] == 1 ? 'checked' : ''; ?>>
                            <span>This is an All-Day Event</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="time">Timings Display Label</label>
                        <input type="text" id="time" name="time" class="form-control" placeholder="e.g. 06:00 PM - 10:00 PM or All Day" value="<?php echo htmlspecialchars($item['time']); ?>" required>
                    </div>

                    <div class="form-group full-width" id="time-fields-group" style="display: <?php echo $item['all_day'] == 1 ? 'none' : 'grid'; ?>; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <label class="form-label" for="start_time">Start Time</label>
                            <input type="time" id="start_time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($item['start_time']); ?>">
                        </div>
                        <div>
                            <label class="form-label" for="end_time">End Time</label>
                            <input type="time" id="end_time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($item['end_time']); ?>">
                        </div>
                    </div>

                    <!-- Location details -->
                    <div class="section-divider-title">Location & Geographic Details</div>

                    <div class="form-group">
                        <label class="form-label" for="location">Venue Name</label>
                        <input type="text" id="location" name="location" class="form-control" placeholder="e.g. Sector 62 Main Ground" value="<?php echo htmlspecialchars($item['location']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Street Address</label>
                        <input type="text" id="address" name="address" class="form-control" placeholder="e.g. B-Block Community Centre" value="<?php echo htmlspecialchars($item['address']); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($item['city']) ?: 'Noida'; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="state">State</label>
                        <input type="text" id="state" name="state" class="form-control" value="<?php echo htmlspecialchars($item['state']) ?: 'Uttar Pradesh'; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="latitude">Latitude (Optional)</label>
                        <input type="number" step="0.000001" id="latitude" name="latitude" class="form-control" placeholder="e.g. 28.6291" value="<?php echo htmlspecialchars($item['latitude']); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="longitude">Longitude (Optional)</label>
                        <input type="number" step="0.000001" id="longitude" name="longitude" class="form-control" placeholder="e.g. 77.3719" value="<?php echo htmlspecialchars($item['longitude']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="map_url">Google Maps URL Embed Link</label>
                        <input type="url" id="map_url" name="map_url" class="form-control" placeholder="https://maps.google.com/..." value="<?php echo htmlspecialchars($item['map_url']); ?>">
                    </div>

                    <!-- Descriptions -->
                    <div class="section-divider-title">Content & Media</div>

                    <div class="form-group full-width">
                        <label class="form-label" for="image">Cover Image URL</label>
                        <input type="url" id="image" name="image" class="form-control" placeholder="Unsplash image URL address" value="<?php echo htmlspecialchars($item['image']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="short_description">Short Description (Preview)</label>
                        <input type="text" id="short_description" name="short_description" class="form-control" placeholder="Provide a brief one-sentence preview..." value="<?php echo htmlspecialchars($item['short_description']); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="description">Full Description</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Provide full details, programs schedule list, etc..." required><?php echo htmlspecialchars($item['description']); ?></textarea>
                    </div>

                    <!-- Schedules activities list -->
                    <div class="section-divider-title">Detailed Program Sub-Schedules</div>
                    
                    <div class="form-group full-width">
                        <p style="font-size: 0.88rem; color: var(--gray); margin-bottom: 1rem;">Add daily sub-schedules, rituals timing list, or individual performances within this event (e.g. Pushpanjali at 08:00 AM on Oct 17th).</p>
                        <div id="schedule-rows-container">
                            <?php foreach ($schedules as $index => $sch): ?>
                                <div class="schedule-row">
                                    <div>
                                        <input type="date" name="sch_date[]" class="form-control" value="<?php echo $sch['date']; ?>" required>
                                    </div>
                                    <div>
                                        <input type="text" name="sch_time[]" class="form-control" placeholder="e.g. 8:00 AM" value="<?php echo htmlspecialchars($sch['time']); ?>">
                                    </div>
                                    <div>
                                        <input type="text" name="sch_title[]" class="form-control" placeholder="e.g. Pushpanjali" value="<?php echo htmlspecialchars($sch['title']); ?>" required>
                                    </div>
                                    <div>
                                        <input type="text" name="sch_desc[]" class="form-control" placeholder="Brief info (optional)" value="<?php echo htmlspecialchars($sch['description']); ?>">
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-cancel delete-sch-row-btn" style="padding: 0.55rem 0.8rem; margin: 0; min-height: unset; height: auto;"><i class="fa-solid fa-trash-can"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-cancel" id="add-schedule-btn" style="padding: 0.55rem 1.2rem; font-size: 0.85rem; margin-top: 0.5rem;">
                            <i class="fa-solid fa-plus"></i> Add Activity Row
                        </button>
                    </div>
                </div>

                <div class="btn-row">
                    <a href="events.php" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-submit">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Save Event configurations</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toggle timed fields JS & Mobile Nav Drawer toggles -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const allDayCheckbox = document.getElementById('all_day');
        const timeFields = document.getElementById('time-fields-group');
        
        if (allDayCheckbox && timeFields) {
            allDayCheckbox.addEventListener('change', function() {
                timeFields.style.display = this.checked ? 'none' : 'grid';
            });
        }

        // Add dynamically schedules rows
        const container = document.getElementById('schedule-rows-container');
        const addBtn = document.getElementById('add-schedule-btn');

        if (addBtn && container) {
            addBtn.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'schedule-row';
                
                // Auto default date to parent event date value if present
                const evDate = document.getElementById('date').value;

                row.innerHTML = `
                    <div>
                        <input type="date" name="sch_date[]" class="form-control" value="${evDate}" required>
                    </div>
                    <div>
                        <input type="text" name="sch_time[]" class="form-control" placeholder="e.g. 8:00 AM">
                    </div>
                    <div>
                        <input type="text" name="sch_title[]" class="form-control" placeholder="e.g. Pushpanjali" required>
                    </div>
                    <div>
                        <input type="text" name="sch_desc[]" class="form-control" placeholder="Brief info (optional)">
                    </div>
                    <div>
                        <button type="button" class="btn btn-cancel delete-sch-row-btn" style="padding: 0.55rem 0.8rem; margin: 0; min-height: unset; height: auto;"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                `;

                row.querySelector('.delete-sch-row-btn').addEventListener('click', function() {
                    row.remove();
                });

                container.appendChild(row);
            });
        }

        // Add click listener on existing delete buttons
        document.querySelectorAll('.delete-sch-row-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.schedule-row').remove();
            });
        });
    });
    </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
