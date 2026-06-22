<?php
// staff.php - Our Staff Page (Improved to display staff from database)
session_start();
$page_title = "Our Staff";

// Include database connection
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.85), rgba(0,0,0,0.7)), url('assets/images/staff-bg.jpg'); background-size: cover; background-position: center; background-attachment: fixed; padding: 80px 0;">
    <div class="container text-center text-white">
        <h1 class="display-4 fw-bold">Our Staff</h1>
        <p class="lead">Dedicated Educators Committed to Excellence</p>
    </div>
</section>

<!-- Staff Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <?php
        // Fetch active staff from database
        try {
            $staff_query = $conn->query("SELECT * FROM staff WHERE is_active = 1 ORDER BY FIELD(category, 'administration', 'department_head', 'teaching', 'support'), display_order, name");
            $all_staff = $staff_query->fetchAll(PDO::FETCH_ASSOC);
            
            // Group by category
            $grouped_staff = [
                'administration' => [],
                'department_head' => [],
                'teaching' => [],
                'support' => []
            ];
            
            foreach($all_staff as $s) {
                $grouped_staff[$s['category']][] = $s;
            }
            
            $category_names = [
                'administration' => 'Administration',
                'department_head' => 'Department Heads',
                'teaching' => 'Teaching Staff',
                'support' => 'Support Staff'
            ];
            
            $category_icons = [
                'administration' => 'fa-user-tie',
                'department_head' => 'fa-chalkboard-teacher',
                'teaching' => 'fa-users',
                'support' => 'fa-handshake'
            ];
            
            $has_staff = false;
            
            foreach($grouped_staff as $cat_key => $staff_members):
                if(count($staff_members) > 0):
                    $has_staff = true;
        ?>
            <div class="category-section mb-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="category-icon me-3" style="background: #1a4d8c; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas <?= $category_icons[$cat_key] ?> fa-lg"></i>
                    </div>
                    <h2 class="category-title" style="color: #1a4d8c; margin: 0; font-weight: 700;">
                        <?= $category_names[$cat_key] ?>
                    </h2>
                    <span class="badge bg-primary ms-3"><?= count($staff_members) ?></span>
                </div>
                
                <div class="row g-4">
                    <?php foreach($staff_members as $staff): ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="staff-card h-100" style="text-align: center; padding: 25px 20px; background: white; border-radius: 15px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); transition: all 0.3s; border-bottom: 4px solid transparent;">
                                <?php 
                                $img_path = 'assets/uploads/staff/' . $staff['image'];
                                if(!file_exists($img_path) || !$staff['image']) {
                                    $img_path = 'assets/uploads/staff/default.jpg';
                                }
                                ?>
                                <div class="staff-image-wrapper" style="position: relative; display: inline-block;">
                                    <img src="<?= $img_path ?>" alt="<?= htmlspecialchars($staff['name']) ?>" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #1a4d8c; padding: 3px; transition: all 0.3s;">
                                    <?php if($cat_key == 'administration'): ?>
                                        <span style="position: absolute; bottom: 5px; right: 5px; background: #e67e22; color: white; font-size: 10px; padding: 2px 10px; border-radius: 20px; font-weight: bold;">Admin</span>
                                    <?php endif; ?>
                                </div>
                                
                                <h3 style="color: #1a4d8c; margin: 15px 0 5px; font-size: 1.2rem;"><?= htmlspecialchars($staff['name']) ?></h3>
                                
                                <p style="color: #2e7d32; font-weight: 600; font-size: 0.95rem; margin-bottom: 8px;">
                                    <i class="fas fa-briefcase" style="font-size: 12px;"></i> <?= htmlspecialchars($staff['title']) ?>
                                </p>
                                
                                <?php if($staff['position'] && $staff['position'] != $staff['title']): ?>
                                    <p style="color: #555; font-size: 0.85rem; margin-bottom: 10px;">
                                        <i class="fas fa-chevron-right" style="font-size: 10px; color: #1a4d8c;"></i> <?= htmlspecialchars($staff['position']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="staff-contact" style="margin: 10px 0;">
                                    <?php if($staff['email']): ?>
                                        <p style="margin: 3px 0; font-size: 0.85rem; color: #555;">
                                            <i class="fas fa-envelope" style="color: #1a4d8c; width: 20px;"></i> 
                                            <a href="mailto:<?= htmlspecialchars($staff['email']) ?>" style="color: #555; text-decoration: none;"><?= htmlspecialchars($staff['email']) ?></a>
                                        </p>
                                    <?php endif; ?>
                                    <?php if($staff['phone']): ?>
                                        <p style="margin: 3px 0; font-size: 0.85rem; color: #555;">
                                            <i class="fas fa-phone" style="color: #1a4d8c; width: 20px;"></i> 
                                            <a href="tel:<?= htmlspecialchars($staff['phone']) ?>" style="color: #555; text-decoration: none;"><?= htmlspecialchars($staff['phone']) ?></a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($staff['bio']): ?>
                                    <div class="staff-bio" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #ecf0f1;">
                                        <p style="color: #666; font-size: 0.85rem; margin: 0; line-height: 1.5;">
                                            <?= htmlspecialchars(substr($staff['bio'], 0, 80)) ?>
                                            <?php if(strlen($staff['bio']) > 80): ?>...<?php endif; ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php 
                endif;
            endforeach;
            
            if(!$has_staff):
        ?>
            <div class="text-center py-5" style="background: #f8f9fa; border-radius: 15px;">
                <i class="fas fa-users" style="font-size: 4rem; color: #dee2e6;"></i>
                <p class="mt-3" style="font-size: 1.2rem; color: #7f8c8d;">No staff members available yet.</p>
                <p style="color: #95a5a6;">Please check back later or contact the school administration.</p>
            </div>
        <?php 
            endif;
        } catch(PDOException $e) {
            echo '<div class="alert alert-danger">Error loading staff: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>
</section>

<!-- Staff Stats Section -->
<section style="background: #1a4d8c; padding: 40px 0; color: white;">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="fas fa-user-tie" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    <h3 style="font-size: 2rem; margin: 10px 0 0;">
                        <?= count($grouped_staff['administration'] ?? []) ?>
                    </h3>
                    <p style="opacity: 0.8;">Administration</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="fas fa-chalkboard-teacher" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    <h3 style="font-size: 2rem; margin: 10px 0 0;">
                        <?= count($grouped_staff['department_head'] ?? []) ?>
                    </h3>
                    <p style="opacity: 0.8;">Department Heads</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="fas fa-users" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    <h3 style="font-size: 2rem; margin: 10px 0 0;">
                        <?= count($grouped_staff['teaching'] ?? []) ?>
                    </h3>
                    <p style="opacity: 0.8;">Teaching Staff</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <i class="fas fa-handshake" style="font-size: 2.5rem; opacity: 0.8;"></i>
                    <h3 style="font-size: 2rem; margin: 10px 0 0;">
                        <?= count($grouped_staff['support'] ?? []) ?>
                    </h3>
                    <p style="opacity: 0.8;">Support Staff</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>