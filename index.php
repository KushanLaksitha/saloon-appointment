<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saloon Kavisha - Elegant Beauty</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Saloon Kavisha</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
                    <li class="nav-item"><a class="nav-link" href="#booking">Book Now</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <h1 class="display-4 floating-element">Saloon Kavisha</h1>
            <p class="lead mb-4">Elevate Your Beauty, Embrace Your Elegance</p>
            <div class="d-flex justify-content-center">
                <a href="#booking" class="btn btn-primary btn-lg rounded-pill px-4 py-2 me-3">
                    Book Appointment
                </a>
                <a href="#services" class="btn btn-outline-light btn-lg rounded-pill px-4 py-2">
                    Our Services
                </a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <h2 class="text-center mb-5 display-6 text-white">Our Signature Services</h2>
            <div class="row g-4">
                <?php
                $services = [
                    ['name' => 'Luxury Cut', 'price' => 'LKR 750', 'description' => 'Precision styling that transforms'],
                    ['name' => 'Color Artistry', 'price' => 'LKR 2500', 'description' => 'Personalized color magic'],
                    ['name' => 'Bridal Elegance', 'price' => 'LKR 8000', 'description' => 'Your dream look perfected'],
                    ['name' => 'Wellness Spa', 'price' => 'LKR 10000', 'description' => 'Complete relaxation experience']
                ];

                foreach ($services as $service) {
                    echo '<div class="col-md-3 col-sm-6">';
                    echo '<div class="service-card h-100">';
                    echo '<h5 class="card-title text-white mb-3">' . $service['name'] . '</h5>';
                    echo '<p class="text-light mb-3">' . $service['description'] . '</p>';
                    echo '<span class="badge bg-primary">' . $service['price'] . '</span>';
                    echo '</div></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- New Testimonials Section -->
    <section id="testimonials" class="testimonials-section">
        <div class="container">
            <h2 class="text-center mb-5 display-6 text-white">What Our Clients Say</h2>
            <div class="row">
                <?php
                $testimonials = [
                    ['name' => 'Kushan Laksitha', 'text' => 'Absolutely amazing experience! The stylist understood exactly what I wanted.'],
                    ['name' => 'Sachintha Ravishan', 'text' => 'Best bridal makeup I could have asked for. Felt like a true queen on my wedding day!'],
                    ['name' => 'Kulunu Adithya', 'text' => 'Professional service and incredible attention to detail. Highly recommended!']
                ];

                foreach ($testimonials as $testimonial) {
                    echo '<div class="col-md-4">';
                    echo '<div class="testimonial-card">';
                    echo '<p class="text-light mb-3">' . $testimonial['text'] . '</p>';
                    echo '<h6 class="text-white">- ' . $testimonial['name'] . '</h6>';
                    echo '</div></div>';
                }
                ?>
            </div>
        </div>
    </section>
    <!-- Photo Gallery Section -->
    <section id="gallery" class="photo-gallery-section">
        <div class="container">
            <h2 class="text-center mb-5 display-6 text-white">Our Gallery</h2>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="https://packmo.lk/wp-content/uploads/2019/11/side-fade.jpg" alt="Haircut Style" class="img-fluid">
                </div>
                <div class="gallery-item">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRO-gBPIo9qmny7YD_QjQA4QLjmjksE3Yodtg&s" alt="Color Treatment" class="img-fluid">
                </div>
                <div class="gallery-item">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRI8L6XmtiIi7jMU49UrK5c5c4zKLYJsmCwfw&s" alt="Bridal Makeup" class="img-fluid">
                </div>
                <div class="gallery-item">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTgu1xt5rJNVzuCWeG0tG2ZrSeEvRlf9OAu3Q&s" alt="Styling Session" class="img-fluid">
                </div>
                <div class="gallery-item">
                    <img src="https://m.psecn.photoshelter.com/img-get/I0000nSlrU1hCrBA/s/600/600/asp-wakem-14659.jpg" alt="Spa Treatment" class="img-fluid">
                </div>
                <div class="gallery-item">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRQzSpqWy6aSLMw9sX0g-7skoVIzyc5OURUHA&s" alt="Professional Styling" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section class="booking-container" id="booking">
            <div class="booking-header">
                <h2>Book Your Appointment</h2>
            </div>
            
            <form class="booking-form" id="bookingForm" method="POST" action="auth/booking.php">
                <div class="form-group">
                    <label class="form-label">Select Service</label>
                    <div class="service-select" id="serviceSelect">
                        <div class="service-option" data-service="Haircut">Haircut</div>
                        <div class="service-option" data-service="Color">Color</div>
                        <div class="service-option" data-service="Styling">Styling</div>
                        <div class="service-option" data-service="Bridal">Bridal</div>
                        <div class="service-option" data-service="Spa">Spa</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number" required>
                </div>

                <div class="form-group">
                    <label for="date" class="form-label">Preferred Date</label>
                    <input type="datetime-local" class="form-control" id="date" required>
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Additional Notes</label>
                    <textarea class="form-control" id="message" rows="3" placeholder="Any special requests?"></textarea>
                </div>

                <button type="submit" class="btn-submit w-100">
                    Schedule Appointment
                </button>
            </form>
       </section>
        <footer class="creative-footer">
        <div class="footer-background"></div>
        <div class="footer-content">
            <div class="footer-section footer-logo">
                <img src="image/logo.png" alt="Saloon Kavisha Logo">
                <h3>Saloon Kavisha</h3>
            </div>

            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#testimonials">Testimonials</a></li>
                    <li><a href="#booking">Book Appointment</a></li>
                </ul>
            </div>

            <div class="footer-section footer-contact">
                <h4>Contact Us</h4>
                <p>📍 Bemmullegedara, Narammala</p>
                <p>📞 +94 787472289</p>
                <p>✉️ hello@saloonkavisha.com</p>
            </div>

            <div class="footer-section">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a href="#" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" aria-label="Facebook">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" aria-label="Pinterest">
                        <i class="fab fa-pinterest"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 Saloon Kavisha. All Rights Reserved. | Designed with ❤️ by Kushan Kumarasiri</p>
        </div>

        <!-- Font Awesome for Social Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/index.js"></script>
</body>
</html>