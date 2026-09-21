<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Certificate | LIU Verify</title>
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

    <div class="container">
        <div class="auth-card">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class="fas fa-cloud-upload-alt fa-3x" style="color: #0f4c92;"></i>
                <h2 style="margin-top: 10px;">Issue New Certificate</h2>
                <p>Upload student credentials to the decentralized ledger.</p>
            </div>
            
            <form class="auth-form">
                <div class="form-group">
                    <label><i class="fas fa-user-graduate"></i> Student Full Name</label>
                    <input type="text" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> Student ID</label>
                    <input type="text" placeholder="e.g., 202100555" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-graduation-cap"></i> Degree Type</label>
                    <select required>
                        <option value="" disabled selected>Select degree</option>
                        <option value="cs">BS in Computer Science</option>
                        <option value="ce">BE in Computer Engineering</option>
                        <option value="mis">BS in Management Information Systems</option>
                        <option value="bba">Bachelor of Business Administration</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-file-pdf"></i> Certificate File (PDF)</label>
                    <input type="file" accept="application/pdf" required>
                </div>

                <button type="submit" class="btn" style="width: 100%;">Generate Hash & Issue</button>
            </form>
        </div>
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