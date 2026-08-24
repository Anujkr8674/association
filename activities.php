<?php
// Include the shared header
include 'includes/header.php';
require_once 'config.php';

// Fetch all activities from database
$recent_activities = [];
try {
    if (isset($pdo)) {
        $stmt_act = $pdo->query("SELECT * FROM `recent_activities` ORDER BY `created_at` DESC");
        $recent_activities = $stmt_act->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Fail silently
}

if (empty($recent_activities)) {
    $recent_activities = [
        ['id' => 1, 'title' => 'Morning Programme 2018', 'description' => 'Our beautiful community gathering for morning rituals and prayers during Durga Puja 2018.', 'image' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?q=80&w=600'],
        ['id' => 2, 'title' => 'Durga Puja Invitation Card 2021', 'description' => 'The official creative design and release of our Durga Puja invitation cards for 2021.', 'image' => 'https://images.unsplash.com/photo-1502134249126-9f3755a50d78?q=80&w=600'],
        ['id' => 3, 'title' => 'Evening Programme 2018', 'description' => 'Dance dramas, classical songs and folk performances by our community members in 2018.', 'image' => 'https://images.unsplash.com/photo-1536304997881-a372c179924b?q=80&w=600'],
        ['id' => 4, 'title' => 'Dandiya Night 2018', 'description' => 'Vibrant Garba and Dandiya dance events under decorative lighting with delicious foods.', 'image' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?q=80&w=600']
    ];
}
?>

<style>
    .activities-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .activities-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 20% 50%, rgba(201, 154, 46, 0.15) 0%, transparent 50%),
                          radial-gradient(circle at 80% 50%, rgba(200, 59, 45, 0.15) 0%, transparent 50%);
        z-index: 1;
    }

    .activities-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .activities-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .activities-sec {
        padding: 6.5rem 0;
        background-color: var(--primary-bg);
    }

    .activities-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 2.5rem 2rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .activity-card {
        text-decoration: none;
        display: flex;
        flex-direction: column;
        background-color: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(33, 26, 23, 0.06);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0, 0, 0, 0.03);
        width: calc(25% - 1.5rem);
        min-width: 260px;
        max-width: 280px;
        box-sizing: border-box;
    }

    .activity-image-wrapper {
        width: 100%;
        height: 200px;
        overflow: hidden;
        position: relative;
        background-color: var(--secondary-bg);
    }

    .activity-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .activity-content-box {
        padding: 1.5rem 1.25rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        justify-content: center;
        align-items: center;
        text-align: center;
        border-top: 1px solid rgba(0, 0, 0, 0.02);
    }

    .activity-title {
        font-family: var(--font-headings);
        font-size: 1.12rem;
        color: var(--dark);
        font-weight: 700;
        margin: 0;
        line-height: 1.4;
        transition: color 0.3s ease;
    }

    /* Hover effect */
    .activity-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 36px rgba(139, 30, 30, 0.12);
        border-color: var(--gold);
        background-color: var(--red);
    }

    .activity-card:hover .activity-image-wrapper img {
        transform: scale(1.06);
    }

    .activity-card:hover .activity-title {
        color: var(--white) !important;
    }

    /* Responsive adjustments */
    @media (max-width: 991px) {
        .activity-card {
            width: calc(33.333% - 1.5rem);
        }
    }

    @media (max-width: 768px) {
        .activity-card {
            width: calc(50% - 1.5rem);
        }
    }

    @media (max-width: 480px) {
        .activities-grid {
            max-width: 320px;
            margin-left: auto;
            margin-right: auto;
        }
        .activity-card {
            width: 100%;
        }
    }
</style>

<!-- Banner Header -->
<section class="activities-banner">
    <div class="container">
        <h1 class="activities-banner-title">Our Activities</h1>
        <span class="activities-banner-subtitle">A collection of events, programs, and community memories</span>
    </div>
</section>

<!-- Activities Grid -->
<section class="activities-sec">
    <div class="container">
        <div class="activities-grid">
            <?php foreach ($recent_activities as $act): ?>
                <?php 
                $act_img = htmlspecialchars($act['image']);
                if (strpos($act['image'], 'http') !== 0) {
                    $act_img = $act_img; // absolute path to file in project root
                }
                ?>
                <a href="activity-details.php?id=<?php echo $act['id']; ?>" class="activity-card">
                    <div class="activity-image-wrapper">
                        <img src="<?php echo $act_img; ?>" alt="<?php echo htmlspecialchars($act['title']); ?>" loading="lazy">
                    </div>
                    <div class="activity-content-box">
                        <h3 class="activity-title"><?php echo htmlspecialchars($act['title']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
