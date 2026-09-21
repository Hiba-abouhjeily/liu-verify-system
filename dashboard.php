<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | LIU Verify</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <nav>
            <div class="nav-container">
                <div class="logo">
                    <img src="Assests/logoimage.png" alt="LIU Logo">
                    <span>Verify</span>
                </div>
                <ul class="nav-links">
                    <li><a href="home.html">Home</a></li>
                    <li><a href="login.html">LogIn</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
                <a href="#" class="help-link"><i class="fas fa-question-circle"></i> Help</a>
            </div>
        </nav>
    
    </nav>

    <div class="container dashboard-container">
        <aside class="sidebar">
            <div class="user-profile">
                <i class="fas fa-user-circle"></i>
                <h3>User Name</h3>
                <p>Student</p>
            </div>
            <ul class="sidebar-links">
                <li><a href="#" class="active"><i class="fas fa-th-large"></i> Overview</a></li>
                <li><a href="verify.html"><i class="fas fa-check-double"></i> Verify</a></li>
                <li><a href="#"><i class="fas fa-file-invoice"></i> My Certificates</a></li>
                <li><a href="login.html" style="color:red;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main style="flex:1;">
            <div class="hero" style="text-align:left; padding:40px;">
                <h2>Welcome back, Student!</h2>
                <p>Manage your decentralized academic records from this portal.</p>
            </div>
            <div class="info-grid">
                <div class="info-card">
                    <i class="fas fa-file-export"></i>
                    <h4>04</h4>
                    <p>Issued Certificates</p>
                </div>
                <div class="info-card">
                    <i class="fas fa-eye"></i>
                    <h4>12</h4>
                    <p>Verification Views</p>
                </div>
            </div>
        </main>
    </div>
    <footer>
        <div class="footer-content">
            <div class="footer-section about">
                <h3>LIU Verify</h3>
                <p>Securing academic integrity through decentralized blockchain technology for future graduates.</p>
            </div>
    
            <div class="footer-section contact">
                <h3>Contact Us</h3>
                <p><i class="fas fa-envelope"></i> <a href="mailto:info@liu.edu.lb" class="footer-link">info@liu.edu.lb</a></p>
                <p><i class="fas fa-phone"></i> <a href="tel:+9611706881" class="footer-link">+961 1 706 881</a></p>
            </div>
    
            <div class="footer-section social">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 Lebanese International University - Senior Project
        </div>
    </footer>
</body>
</html>