<?php
// faq.php - Frequently Asked Questions Page
session_start();
$page_title = "FAQ";
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="page-header" style="background: linear-gradient(rgba(26,77,140,0.8), rgba(0,0,0,0.6)), url('assets/images/faq-bg.jpg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1>Frequently Asked Questions</h1>
        <p>Find answers to common questions about our school</p>
    </div>
</section>

<!-- FAQ Content -->
<section style="padding: 60px 0;">
    <div class="container">
        <div class="faq-item" style="margin-bottom: 20px;">
            <div class="faq-question" style="background: #f5f5f5; padding: 18px; border-radius: 10px; cursor: pointer; transition: all 0.3s;">
                <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-question-circle" style="color: #1a4d8c; margin-right: 10px;"></i> How do I apply for admission?</h3>
            </div>
            <div class="faq-answer" style="display: none; padding: 20px; background: white; border-radius: 10px; margin-top: 10px; border-left: 3px solid #2e7d32;">
                <p>You can apply by filling out the admission inquiry form on our Admission page. The process includes submitting an application, taking an entrance examination, and an interview with our admissions team.</p>
            </div>
        </div>
        
        <div class="faq-item" style="margin-bottom: 20px;">
            <div class="faq-question" style="background: #f5f5f5; padding: 18px; border-radius: 10px; cursor: pointer;">
                <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-question-circle" style="color: #1a4d8c; margin-right: 10px;"></i> What are the school fees?</h3>
            </div>
            <div class="faq-answer" style="display: none; padding: 20px; background: white; border-radius: 10px; margin-top: 10px; border-left: 3px solid #2e7d32;">
                <p>School fees vary by grade level. Please contact our admissions office or download the fee structure from our Downloads page for detailed information.</p>
            </div>
        </div>
        
        <div class="faq-item" style="margin-bottom: 20px;">
            <div class="faq-question" style="background: #f5f5f5; padding: 18px; border-radius: 10px; cursor: pointer;">
                <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-question-circle" style="color: #1a4d8c; margin-right: 10px;"></i> Does the school offer boarding facilities?</h3>
            </div>
            <div class="faq-answer" style="display: none; padding: 20px; background: white; border-radius: 10px; margin-top: 10px; border-left: 3px solid #2e7d32;">
                <p>Yes, Stella Maris College offers both day and boarding options. Our boarding facilities are modern, secure, and well-maintained with dedicated housemothers.</p>
            </div>
        </div>
        
        <div class="faq-item" style="margin-bottom: 20px;">
            <div class="faq-question" style="background: #f5f5f5; padding: 18px; border-radius: 10px; cursor: pointer;">
                <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-question-circle" style="color: #1a4d8c; margin-right: 10px;"></i> What extracurricular activities are available?</h3>
            </div>
            <div class="faq-answer" style="display: none; padding: 20px; background: white; border-radius: 10px; margin-top: 10px; border-left: 3px solid #2e7d32;">
                <p>We offer a wide range of activities including sports (football, basketball, volleyball, athletics), clubs (debate, choir, drama, environment), and spiritual activities.</p>
            </div>
        </div>
        
        <div class="faq-item" style="margin-bottom: 20px;">
            <div class="faq-question" style="background: #f5f5f5; padding: 18px; border-radius: 10px; cursor: pointer;">
                <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-question-circle" style="color: #1a4d8c; margin-right: 10px;"></i> How can alumni get involved?</h3>
            </div>
            <div class="faq-answer" style="display: none; padding: 20px; background: white; border-radius: 10px; margin-top: 10px; border-left: 3px solid #2e7d32;">
                <p>Alumni can register on our Alumni page, join our networking events, mentor current students, and contribute to school development projects.</p>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('.faq-question').click(function() {
        $(this).next('.faq-answer').slideToggle();
        $(this).css('background', '#e8f0fe');
    });
});
</script>

<?php include 'includes/footer.php'; ?>