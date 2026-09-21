<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify | LIU Verify</title>
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
                    <li><a href="home.php">Home</a></li>
                    <li><a href="login.php">LogIn</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <a href="help.php" class="help-link"><i class="fas fa-question-circle"></i> Help</a>
            </div>
        </nav>
    
    </nav>

    <div class="container">
        <section class="verify-section">
            <div class="verify-box">
                <i class="fas fa-qrcode fa-4x"></i>
                <h3>Scan QR Code</h3>
                <p>Verify instantly using your camera.</p>
                <br>
                <button class="btn">Start Scanner</button>
            </div>

            <div class="divider"><span>OR</span></div>

            <div class="verify-box">
                <i class="fas fa-fingerprint fa-4x"></i>
                <h3>Manual Verification</h3>
                <p>Enter the SHA-256 hash here.</p>
                <input type="text" class="hash-input" placeholder="Paste hash code...">
                <button class="btn">Verify Hash</button>
            </div>
        </section>
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