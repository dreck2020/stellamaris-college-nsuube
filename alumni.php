<?php
// alumni.php - Alumni (Old Girls) Page with Fee Cards
session_start();
require_once 'config/database.php';
$db = new Database();
$conn = $db->getConnection();

$message = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $graduation_year = $_POST['graduation_year'];
    $marital_status = $_POST['marital_status'];
    $profession = $_POST['profession'];
    $employment_status = $_POST['employment_status'];
    
    $stmt = $conn->prepare("INSERT INTO alumni (full_name, email, phone, graduation_year, marital_status, profession, employment_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$full_name, $email, $phone, $graduation_year, $marital_status, $profession, $employment_status])) {
        $message = '<div class="alert alert-success">✅ Registration successful! Welcome to our alumni community.</div>';
    } else {
        $message = '<div class="alert alert-danger">❌ Registration failed. Please try again.</div>';
    }
}
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
/* Page Header */
.page-header {
    background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/alumni-bg.jpg');
    background-size: cover;
    background-position: center;
    padding: 60px 0;
    color: white;
    text-align: center;
}

/* Fee Cards Section */
.fee-cards-section {
    padding: 60px 0;
    background: #f8f9fa;
}
.section-title {
    text-align: center;
    margin-bottom: 40px;
}
.section-title h2 {
    font-size: 32px;
    color: #1a4d8c;
    margin-bottom: 10px;
}
.section-title p {
    color: #666;
    font-size: 16px;
}
.fee-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}
.fee-card {
    background: white;
    border-radius: 20px;
    padding: 30px 20px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: 1px solid #eef2f6;
}
.fee-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.12);
}
.fee-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #1a4d8c, #2e7d32);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.fee-icon i {
    font-size: 40px;
    color: white;
}
.fee-card h3 {
    font-size: 22px;
    color: #1a4d8c;
    margin-bottom: 15px;
}
.fee-amount {
    font-size: 32px;
    font-weight: 700;
    color: #2e7d32;
    margin-bottom: 10px;
}
.fee-period {
    color: #888;
    font-size: 14px;
    margin-bottom: 15px;
}
.fee-description {
    color: #555;
    font-size: 14px;
    line-height: 1.6;
}
/* Alumni Info & Form (existing styles enhanced) */
.alumni-info {
    background: #f5f5f5;
    padding: 30px;
    border-radius: 15px;
    height: 100%;
}
.alumni-info h2 {
    color: #1a4d8c;
}
.alumni-info h3 {
    color: #2e7d32;
    margin-top: 20px;
}
.alumni-info ul {
    list-style: none;
    padding: 0;
}
.alumni-info li {
    padding: 8px 0;
}
.alumni-info li i {
    color: #2e7d32;
    margin-right: 10px;
}
.registration-form {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    height: 100%;
}
.registration-form h3 {
    color: #1a4d8c;
    margin-bottom: 20px;
}
.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
}
.btn-primary {
    background: #1a4d8c;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    width: 100%;
    font-weight: 600;
    transition: background 0.3s;
}
.btn-primary:hover {
    background: #2e7d32;
}
@media (max-width: 768px) {
    .fee-grid {
        grid-template-columns: 1fr;
    }
    .section-title h2 {
        font-size: 28px;
    }
}
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Old Girls Association</h1>
        <p>Stella Maris College Alumni Network</p>
    </div>
</section>

<!-- NEW: Fee Cards Section -->
<section class="fee-cards-section">
    <div class="container">
        <div class="section-title">
            <h2>Join the Stella Maris Old Girls Association</h2>
            <p>Become a member and enjoy exclusive benefits while supporting your alma mater.</p>
        </div>
        <div class="fee-grid">
            <!-- Membership Fee Card -->
            <div class="fee-card">
                <div class="fee-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>Membership Fee</h3>
                <div class="fee-amount">UGX 30,000</div>
                <div class="fee-period">One-time (Lifetime)</div>
                <div class="fee-description">
                    Lifetime membership fee. Grants you full access to alumni network, events, and voting rights.
                </div>
            </div>
            <!-- Subscription Fee Card -->
            <div class="fee-card">
                <div class="fee-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Annual Subscription</h3>
                <div class="fee-amount">UGX 20,000</div>
                <div class="fee-period">Per Year</div>
                <div class="fee-description">
                    Covers newsletters, event invitations, and administrative costs of the association.
                </div>
            </div>
            <!-- Welfare Fee Card -->
            <div class="fee-card">
                <div class="fee-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3>Annual Welfare Contribution</h3>
                <div class="fee-amount">UGX 60,000</div>
                <div class="fee-period">Voluntary / Year</div>
                <div class="fee-description">
                    Supports needy students, bereaved members, and emergency assistance for old girls.
                </div>
            </div>
        </div>
        <p class="text-center mt-4" style="color: #666;">
            <i class="fas fa-info-circle"></i> Payments can be made via mobile money, bank deposit, or at the school bursar’s office.
        </p>
    </div>
</section>

<!-- Alumni Content (Info + Registration Form) -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="alumni-info">
                    <h2>Welcome Alumni!</h2>
                    <p>Stay connected with your alma mater and fellow Old girls. Join our growing community of over 5,000 alumni worldwide.</p>
                    <h3>Benefits of Registration:</h3>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Network with fellow alumni</li>
                        <li><i class="fas fa-check-circle"></i> Get updates about school events</li>
                        <li><i class="fas fa-check-circle"></i> Access job opportunities</li>
                        <li><i class="fas fa-check-circle"></i> Mentor current students</li>
                        <li><i class="fas fa-check-circle"></i> Receive newsletters and updates</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="registration-form">
                    <h3>Alumni Registration</h3>
                    <?php echo $message; ?>
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label>Year of Completion *</label>
                            <select name="graduation_year" class="form-control" required>
                                <option value="">Select Year</option>
                                <?php for($year = 2000; $year <= date('Y'); $year++): ?>
                                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Marital Status</label>
                            <select name="marital_status" class="form-control">
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Profession</label>
                            <input type="text" name="profession" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label>Employment Status</label>
                            <select name="employment_status" class="form-control">
                                <option value="employed">Employed</option>
                                <option value="unemployed">Unemployed</option>
                                <option value="self-employed">Self Employed</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Register Now</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>