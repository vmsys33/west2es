<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-top: 20px;
        }
        .logo {
            width: 70px;
            height: 70px;
            margin-bottom: 10px;
            border-radius: 50%;
            background-color: gray;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .school-info {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            margin-bottom: 10px;
        }
        .user-info {
            margin-left: 20px;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with logo and school name/address -->
        <div class="header">
            <div class="logo"></div>
            <div class="school-info">
                West II Elementary School<br>
                Cadiz City, Negros Occidental
            </div>
        </div>

        <!-- User Information Section -->
        <div class="section">
            <h2>User Information</h2>
            <div class="two-columns">
                <div class="user-info">
                    <p><strong>Name:</strong> John Doe</p>
                    <p><strong>Email:</strong> john.doe@example.com</p>
                    <p><strong>Phone:</strong> +1234567890</p>
                    <p><strong>Address:</strong> 1234 Street, City, Country</p>
                </div>
                <div class="user-info">
                    <p><strong>Occupation:</strong> Teacher</p>
                    <p><strong>Subject:</strong> Mathematics</p>
                    <p><strong>School:</strong> Springfield High School</p>
                    <p><strong>Years of Experience:</strong> 10</p>
                    <p><strong>Grade Level Taught:</strong> 5th Grade</p>
                    <p><strong>Languages Spoken:</strong> English, Filipino</p>
                    <p><strong>Certifications:</strong> Teaching Certificate, Master’s in Education</p>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>&copy; 2025 Company Name. All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>
