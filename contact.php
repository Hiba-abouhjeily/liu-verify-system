<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | LIU Verify</title>
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

    <main class="container contact-page">
    <section class="contact-wrapper">

        <!-- Left Side Text -->
        <div class="contact-info-text">
            <span class="section-badge">
                <i class="fas fa-headset"></i>
                Contact Support
            </span>

            <h1>Contact Our Team</h1>

            <p>
                Have questions regarding the blockchain verification process or need technical support with your digital certificates?
            </p>

            <p>
                Our administration is available Monday through Friday to assist you with any inquiries.
            </p>

            <div class="contact-highlights">
                <div class="contact-highlight">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email Support</strong>
                        <span>info@liu.edu.lb</span>
                    </div>
                </div>

                <div class="contact-highlight">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>Working Hours</strong>
                        <span>Monday - Friday</span>
                    </div>
                </div>

                <div class="contact-highlight">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Secure Verification</strong>
                        <span>Blockchain-based certificate support</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Contact Card -->
        <section class="contact-form-card">
            <div class="contact-form-header">
                <i class="fas fa-paper-plane"></i>
                <h2>Send a Message</h2>
                <p>Fill out the form below and our team will get back to you.</p>
            </div>

            <form action="#" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i> Your Name
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        placeholder="Enter your full name" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Your Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Enter your email address" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="message">
                        <i class="fas fa-comment-dots"></i> How can we help you?
                    </label>
                    <textarea 
                        id="message" 
                        name="message" 
                        rows="5" 
                        placeholder="Type your message here..." 
                        required
                    ></textarea>
                </div>

                <button type="submit" class="btn-full">
                    Submit Inquiry
                </button>
            </form>
        </section>

    </section>
</main>

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