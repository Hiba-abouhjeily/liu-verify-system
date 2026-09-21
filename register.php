<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | LIU Verify</title>
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
                    <li><a href="about.html">about</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
                <a href="#" class="help-link"><i class="fas fa-question-circle"></i> Help</a>
            </div>
        </nav>
    
    </nav>

    <main class="container login-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>Create Account</h2>
            <p>Join the secure academic network.</p>
        </div>

        <form action="#" method="POST" class="auth-form">

            <!-- Full Name + Email same row -->
            <div class="form-row">
                <div class="form-group">
                    <label for="fullname">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input 
                        type="text" 
                        id="fullname" 
                        name="fullname" 
                        placeholder="Your full name" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> LIU Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="name@student.liu.edu.lb" 
                        required
                    >
                </div>
            </div>

            <!-- Password + Confirm Password same row -->
            <div class="form-row">
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Min. 8 characters" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="confirm-password">
                        <i class="fas fa-check-double"></i> Confirm Password
                    </label>
                    <input 
                        type="password" 
                        id="confirm-password" 
                        name="confirm_password" 
                        placeholder="Repeat password" 
                        required
                    >
                </div>
            </div>

            <!-- Account type alone -->
            <div class="form-row single-row">
                <div class="form-group">
                    <label for="account-type">
                        <i class="fas fa-id-badge"></i> Account Type
                    </label>
                    <select id="account-type" name="account_type" required>
                        <option value="" disabled selected>Select account type</option>
                        <option value="student">Student</option>
                        <option value="employer">Employer</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-full">
                Create Account
            </button>

        </form>

        <div class="auth-footer">
            <p>
                Already have an account?
                <a href="login.html">Login here</a>
            </p>
        </div>

    </div>
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