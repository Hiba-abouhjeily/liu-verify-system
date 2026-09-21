<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | LIU Verify</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
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

    <div class="container">
        <section class="hero-card">
            <h1>Academic Certificate Verification</h1>
            <p>A secure, decentralized system for issuing and verifying academic credentials using blockchain principles.</p>
            <br>
            <a href="register.html" class="btn">Get Started</a>
        </section>

        <section class="info-grid">
            <div class="info-card">
                <i class="fas fa-shield-halved"></i>
                <h3>Cryptographic Hashing</h3>
                <p>Every certificate is converted into a unique SHA-256 hash, making it impossible to forge or alter.</p>
            </div>
            <div class="info-card">
                <i class="fas fa-cubes"></i>
                <h3>Blockchain Integrity</h3>
                <p>Digital fingerprints are stored in a decentralized ledger, providing a permanent and transparent record.</p>
            </div>
            <div class="info-card">
                <i class="fas fa-qrcode"></i>
                <h3>Instant QR Verify</h3>
                <p>Employers can instantly validate authenticity by scanning a QR code or uploading a document.</p>
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