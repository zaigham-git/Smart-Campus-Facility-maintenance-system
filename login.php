<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus - Login</title>
    <!-- Google Font: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- External CSS Link -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="login-container">
        <div class="brand-header">
            <div class="brand-badge">SC</div>
            <h1>Smart Campus</h1>
            <p>Facility & Maintenance Management Portal</p>
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Campus Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" class="form-control" placeholder="name@tuf.edu.pk" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-submit">Sign In to Portal</button>
        </form>

        <div class="footer-text">
            Role-based access for <span>Students, Staff & Admin</span>
        </div>
    </div>

</body>
</html>