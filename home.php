<?php
require_once 'includes/config.php';
session_start();
if(!isset($_SESSION["user_id"])){
    header("Location:login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chapter & Verse - Premium Bookstore</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --cream: #F8F6F3;
            --charcoal: #2C2C2C;
            --orange: #E8A87C;
            --light-gray: #E5E5E5;
            --dark-orange: #D4956B;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--cream);
            color: var(--charcoal);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background: var(--cream);
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--charcoal);
            text-decoration: none;
            gap:100px;
        }

        .nav-menu {
    display: flex;
    list-style: none;
    gap:1.8rem;
    align-items: center;
    margin: 0;
    padding: 0;
    justify-content: space-between;
}

.nav-menu li {
    display: inline-block;
}

.nav-menu a, .search-cart a  {
    text-decoration: none;
    color: var(--text-color, black);
    font-weight: 500;
    position: relative; 
    transition: color 0.3s ease; 
}

.nav-menu a::after , .search-cart a::after{
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--orange, #f97316);
    transition: width 0.3s ease; 
}

.nav-menu a:hover ,.search-cart a:hover{
    color: var(--orange, #f97316);
}

.nav-menu a:hover::after , .search-cart a:hover::after {
    width: 100%; 
}


.login-btn, .logout-btn, .inventory-btn, .add-book-btn, .msg-btn, .exchange-btn {
    background: var(--orange, #f97316);
    color: white !important;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-block;
    white-space:nowrap;
    min-width:max-content;
}

.login-btn:hover, .logout-btn:hover, .inventory-btn:hover, .add-book-btn:hover ,.msg-btn:hover,.exchange-btn:hover{
    background: #ea580c; 
}

        .search-cart {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .icon-btn {
            background: none;
            border: none;
            color: var(--charcoal);
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0.5rem;
            transition: color 0.3s ease;
            margin-left:50px;
        }

        .icon-btn:hover {
            color: var(--orange);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--cream) 0%, #F0ECE8 100%);
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            min-height: 500px;
        }

        .hero-text {
            z-index: 2;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.3s forwards;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: var(--charcoal);
            opacity: 0.8;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.6s forwards;
        }

        .cta-btn {
            background: var(--charcoal);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.9s forwards;
        }

        .cta-btn:hover {
            background: var(--orange);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .hero-book {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .featured-book {
            width: 300px;
            height: 450px;
            background: linear-gradient(135deg, #8B4513 0%, #A0522D 50%, #CD853F 100%);
            border-radius: 10px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transform: perspective(1000px) rotateY(-15deg);
            transition: transform 0.6s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .featured-book:hover {
            transform: perspective(1000px) rotateY(0deg) scale(1.05);
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            padding: 0 1rem;
        }

        .book-author {
            font-size: 1rem;
            opacity: 0.9;
            text-align: center;
        }

        /* Sections */
        .section {
            padding: 4rem 0;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--orange);
        }

        /* Featured Books Carousel */
        .featured-books {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .books-carousel {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .book-card {
            text-align: center;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .book-card:hover {
            transform: translateY(-10px);
        }

        .book-cover {
            width: 150px;
            height: 225px;
            background: linear-gradient(135deg, var(--orange) 0%, var(--dark-orange) 100%);
            border-radius: 8px;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: box-shadow 0.3s ease;
        }

        .book-cover:hover {
            box-shadow: 0 15px 35px rgba(0,0,0,0.25);
        }

        .book-info h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .book-info p {
            color: var(--charcoal);
            opacity: 0.7;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .book-price {
            font-weight: 600;
            color: var(--orange);
            font-size: 1.1rem;
        }

        /* Genre Cards */
        .genres-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .genre-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .genre-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .genre-icon {
            width: 60px;
            height: 60px;
            background: var(--orange);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .genre-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        /* Staff Picks */
        .staff-picks {
            background: linear-gradient(135deg, #F5F3F0 0%, var(--cream) 100%);
        }

        .picks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .pick-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-left: 4px solid var(--orange);
        }

        .pick-quote {
            font-style: italic;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--charcoal);
            line-height: 1.7;
        }

        .pick-author {
            font-weight: 600;
            color: var(--orange);
            margin-bottom: 0.5rem;
        }

        .pick-staff {
            font-size: 0.9rem;
            color: var(--charcoal);
            opacity: 0.7;
        }

        /* New Arrivals Section */
        .new-arrivals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .arrival-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .arrival-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .arrival-image {
            position: relative;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .book-cover-new {
            width: 120px;
            height: 180px;
            background: linear-gradient(135deg, var(--orange) 0%, var(--dark-orange) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .new-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #4CAF50;
            color: white;
            padding: 0.3rem 0.7rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .arrival-info {
            padding: 1.5rem;
        }

        .arrival-info h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--charcoal);
        }

        .arrival-info p {
            color: var(--charcoal);
            opacity: 0.7;
            margin-bottom: 0.5rem;
        }

        .arrival-description {
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 1rem 0;
            color: var(--charcoal);
            opacity: 0.8;
        }

        .quick-add-btn {
            background: var(--orange);
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .quick-add-btn:hover {
            background: var(--dark-orange);
            transform: translateY(-1px);
        }

        /* About Section */
        .about-section {
            background: linear-gradient(135deg, #F5F3F0 0%, var(--cream) 100%);
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
            margin-top: 2rem;
        }

        .about-text h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--charcoal);
            margin-bottom: 1rem;
        }

        .about-story {
            margin-bottom: 3rem;
        }

        .about-story p {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 1rem;
            color: var(--charcoal);
        }

        .values-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .value-item {
            text-align: center;
            padding: 1rem;
        }

        .value-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .value-item h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: var(--charcoal);
            margin-bottom: 0.5rem;
        }

        .value-item p {
            font-size: 0.9rem;
            color: var(--charcoal);
            opacity: 0.8;
            line-height: 1.5;
        }

        .about-image {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .bookstore-image {
            background: white;
            border-radius: 15px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .image-placeholder {
            text-align: center;
            color: var(--orange);
        }

        .bookshelf-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .image-placeholder p {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--charcoal);
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .stat-item {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--orange);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--charcoal);
            opacity: 0.8;
        }

        /* Newsletter */
        .newsletter {
            background: var(--charcoal);
            color: white;
            text-align: center;
            padding: 4rem 2rem;
        }

        .newsletter-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .newsletter-form {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .newsletter-input {
            padding: 1rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            min-width: 300px;
            outline: none;
        }

        .newsletter-btn {
            background: var(--orange);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .newsletter-btn:hover {
            background: var(--dark-orange);
        }

        /* Footer */
        footer {
            background: var(--charcoal);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--orange);
        }

        .footer-section a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: var(--orange);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 1rem;
            text-align: center;
            opacity: 0.7;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .out-of-stock {
            color: #f44336;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .book-count {
            color: var(--orange);
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 0.5rem;
            display: block;
        }

        /* Book Modal Styles */
        .book-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            max-width: 800px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            padding: 2rem;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 2rem;
            cursor: pointer;
            color: var(--charcoal);
            transition: color 0.3s ease;
        }

        .modal-close:hover {
            color: var(--orange);
        }

        .modal-book {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            align-items: start;
        }

        .modal-book-cover {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal-book-info h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .modal-author {
            font-size: 1.2rem;
            color: var(--orange);
            margin-bottom: 1rem;
        }

        .modal-price {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--charcoal);
            margin-bottom: 1rem;
        }

        .modal-description {
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
        }

        .add-to-cart-btn, .view-details-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .add-to-cart-btn {
            background: var(--orange);
            color: white;
        }

        .add-to-cart-btn:hover {
            background: var(--dark-orange);
        }

        .view-details-btn {
            background: transparent;
            color: var(--charcoal);
            border: 2px solid var(--charcoal);
        }

        .view-details-btn:hover {
            background: var(--charcoal);
            color: white;
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 1001;
            animation: slideIn 0.3s ease-out;
        }

        .notification.success {
            background: #4CAF50;
        }

        .notification.error {
            background: #f44336;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .cart-count {
            background: var(--orange);
            color: white;
            border-radius: 50%;
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }

            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .search-bar input {
                width: 150px;
            }

            .newsletter-form {
                flex-direction: column;
                align-items: center;
            }

            .newsletter-input {
                min-width: 250px;
            }

            .new-arrivals-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .values-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .about-stats {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        /* Scroll animations */
        .scroll-fade {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-out;
        }

        .scroll-fade.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                <a href="#" class="logo">Chapter & Verse</a>
                </div>
<nav> 
    <ul class="nav-menu"> 
        <li><a href="#featured">Popular Books</a></li> 
        <li><a href="#genres">Book Types</a></li> 
        <li><a href="#staff-picks">Our Picks</a></li> 
        <li><a href="#new-arrivals">New Books</a></li> 
        <li><a href="#about">About Us</a></li> 
        <li><a href="inventory.php" class="inventory-btn">Inventory</a></li> 
        <li><a href="add_book.php" class="add-book-btn">Add Book</a></li> 
        <li><a href="messages.php" class="msg-btn">Messages</a></li>
        <li><a href="exchanges.php" class="exchange-btn">Exchanges</a></li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <!-- Show when logged in -->
            <li><a href="logout.php" class="logout-btn">Sign Out</a></li> 
        <?php else: ?>
            <!-- Show when not logged in -->
            <li><a href="register.php" class="login-btn">Sign In</a></li> 
        <?php endif; ?>
    </ul> 
</nav>
                <div class="search-cart">
                    <button class="icon-btn">👤 <a href="profile.php">Profile</a></button>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <div class="hero-text">
                        <h1 class="hero-title">Find Your Next Great Book</h1>
                        <p class="hero-subtitle">We have the best books from famous writers and new authors. Find something perfect for you.</p>
                        <a href="#featured" class="cta-btn">See Our Books</a>
                    </div>
                    <div class="hero-book">
                        <div class="featured-book">
                            <h3 class="book-title">The Art of Storytelling</h3>
                            <p class="book-author">by Margaret Atwood</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="featured" class="section">
            <div class="container">
                <div class="featured-books scroll-fade">
                    <h2 class="section-title">Popular Books</h2>
                    <div class="books-carousel">
                        <div class="book-card">
                            <div class="book-cover">Fiction</div>
                            <div class="book-info">
                                <h3>Ocean Tides</h3>
                                <p>Sarah Mitchell</p>
                                <div class="book-price">$24.99</div>
                            </div>
                        </div>
                        <div class="book-card">
                            <div class="book-cover">Mystery</div>
                            <div class="book-info">
                                <h3>The Last Chapter</h3>
                                <p>David Chen</p>
                                <div class="book-price">$19.99</div>
                            </div>
                        </div>
                        <div class="book-card">
                            <div class="book-cover">Romance</div>
                            <div class="book-info">
                                <h3>Summer Nights</h3>
                                <p>Elena Rodriguez</p>
                                <div class="book-price">$22.50</div>
                            </div>
                        </div>
                        <div class="book-card">
                            <div class="book-cover">Biography</div>
                            <div class="book-info">
                                <h3>A Life Remembered</h3>
                                <p>James Thompson</p>
                                <div class="book-price">$28.99</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="genres" class="section">
            <div class="container">
                <h2 class="section-title scroll-fade">Book Types</h2>
                <div class="genres-grid">
                    <div class="genre-card scroll-fade">
                        <div class="genre-icon">📚</div>
                        <h3>Fiction</h3>
                        <p>Read exciting stories and great tales from today's writers and classic authors.</p>
                    </div>
                    <div class="genre-card scroll-fade">
                        <div class="genre-icon">🔍</div>
                        <h3>Mystery & Thriller</h3>
                        <p>Exciting books that will surprise you. Try to guess what happens before the end!</p>
                    </div>
                    <div class="genre-card scroll-fade">
                        <div class="genre-icon">🚀</div>
                        <h3>Science Fiction</h3>
                        <p>Travel to other worlds and see what the future might look like with new technology.</p>
                    </div>
                    <div class="genre-card scroll-fade">
                        <div class="genre-icon">📖</div>
                        <h3>Non-Fiction</h3>
                        <p>Learn new things with true stories, life stories, and books about real topics.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="staff-picks" class="section staff-picks">
            <div class="container">
                <h2 class="section-title scroll-fade">Our Favorites</h2>
                <div class="picks-grid">
                    <div class="pick-card scroll-fade">
                        <div class="pick-quote">"An amazing story about people that you will remember for a long time."</div>
                        <div class="pick-author">The Invisible Bridge</div>
                        <div class="pick-staff">— Emma loves fiction books</div>
                    </div>
                    <div class="pick-card scroll-fade">
                        <div class="pick-quote">"A wonderful trip through time that mixes history with imagination."</div>
                        <div class="pick-author">Chronicles of Tomorrow</div>
                        <div class="pick-staff">— Marcus knows science fiction</div>
                    </div>
                    <div class="pick-card scroll-fade">
                        <div class="pick-quote">"A beautiful true story that shows us how to stay strong and hopeful."</div>
                        <div class="pick-author">Finding Light</div>
                        <div class="pick-staff">— Sofia picks biography books</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="new-arrivals" class="section">
            <div class="container">
                <h2 class="section-title scroll-fade">New Books This Week</h2>
                <div class="new-arrivals-grid">
                    <div class="arrival-card scroll-fade">
                        <div class="arrival-image">
                            <div class="book-cover-new">Historical</div>
                            <span class="new-badge">NEW</span>
                        </div>
                        <div class="arrival-info">
                            <h3>The Lost Kingdom</h3>
                            <p>by Rachel Green</p>
                            <div class="book-price">$26.99</div>
                            <p class="arrival-description">A wonderful story about a hidden kingdom and brave people who want to save it.</p>
                            <button class="quick-add-btn">Add to Cart</button>
                        </div>
                    </div>
                    <div class="arrival-card scroll-fade">
                        <div class="arrival-image">
                            <div class="book-cover-new">Cooking</div>
                            <span class="new-badge">NEW</span>
                        </div>
                        <div class="arrival-info">
                            <h3>Simple Meals</h3>
                            <p>by Chef Marco</p>
                            <div class="book-price">$22.99</div>
                            <p class="arrival-description">Easy recipes for busy people. Make great food with simple ingredients.</p>
                            <button class="quick-add-btn">Add to Cart</button>
                        </div>
                    </div>
                    <div class="arrival-card scroll-fade">
                        <div class="arrival-image">
                            <div class="book-cover-new">Self-Help</div>
                            <span class="new-badge">NEW</span>
                        </div>
                        <div class="arrival-info">
                            <h3>Be Your Best Self</h3>
                            <p>by Dr. Lisa Wong</p>
                            <div class="book-price">$19.99</div>
                            <p class="arrival-description">Learn how to be happy and successful. Simple tips for a better life.</p>
                            <button class="quick-add-btn">Add to Cart</button>
                        </div>
                    </div>
                    <div class="arrival-card scroll-fade">
                        <div class="arrival-image">
                            <div class="book-cover-new">Technology</div>
                            <span class="new-badge">NEW</span>
                        </div>
                        <div class="arrival-info">
                            <h3>Future Tech Today</h3>
                            <p>by Alex Kim</p>
                            <div class="book-price">$29.99</div>
                            <p class="arrival-description">See how new technology will change our world. Easy to understand for everyone.</p>
                            <button class="quick-add-btn">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="section about-section">
            <div class="container">
                <h2 class="section-title scroll-fade">About Chapter & Verse</h2>
                <div class="about-content">
                    <div class="about-text scroll-fade">
                        <div class="about-story">
                            <h3>Our Story</h3>
                            <p>We started Chapter & Verse because we love books. For over 15 years, we have helped people find great books to read. We believe every person can find a book they will love.</p>
                            <p>Our team reads many books every month. We choose the best ones for our store. We want to help you discover new writers and enjoy reading more.</p>
                        </div>
                        <div class="about-values">
                            <h3>What We Do</h3>
                            <div class="values-grid">
                                <div class="value-item">
                                    <div class="value-icon">📚</div>
                                    <h4>Choose Great Books</h4>
                                    <p>We pick only the best books for our customers.</p>
                                </div>
                                <div class="value-item">
                                    <div class="value-icon">💡</div>
                                    <h4>Help You Choose</h4>
                                    <p>Our team helps you find books you will enjoy.</p>
                                </div>
                                <div class="value-item">
                                    <div class="value-icon">🤝</div>
                                    <h4>Build Community</h4>
                                    <p>We bring book lovers together through events.</p>
                                </div>
                                <div class="value-item">
                                    <div class="value-icon">🎯</div>
                                    <h4>Make Reading Fun</h4>
                                    <p>We make it easy and fun to discover new books.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="about-image scroll-fade">
                        <div class="bookstore-image">
                            <div class="image-placeholder">
                                <div class="bookshelf-icon">📖</div>
                                <p>Our Cozy Bookstore</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="newsletter">
            <div class="container">
                <h2 class="newsletter-title">Join Our Book Community</h2>
                <p>Get book ideas, meet authors, and learn about book events near you.</p>
                <form class="newsletter-form">
                    <input type="email" class="newsletter-input" placeholder="Your email address" required>
                    <button type="submit" class="newsletter-btn">Join Us</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Shop</h3>
                    <a href="#">New Books</a>
                    <a href="#">Best Sellers</a>
                    <a href="#">Gift Cards</a>
                    <a href="#">Special Offers</a>
                </div>
                <div class="footer-section">
                    <h3>Services</h3>
                    <a href="#">Book Help</a>
                    <a href="#">Author Events</a>
                    <a href="#">Book Clubs</a>
                    <a href="#">Personal Shopping</a>
                </div>
                <div class="footer-section">
                    <h3>Help</h3>
                    <a href="#">Contact Us</a>
                    <a href="#">Delivery Info</a>
                    <a href="#">Returns</a>
                    <a href="#">FAQ</a>
                </div>
                <div class="footer-section">
                    <h3>Connect</h3>
                    <a href="#">Newsletter</a>
                    <a href="#">Social Media</a>
                    <a href="#">Author Blog</a>
                    <a href="#">Book Reviews</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Literary Haven. All rights reserved. | Made for book lovers, by book lovers.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {

                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.scroll-fade').forEach(el => {
            observer.observe(el);
        });

        // Search functionality
        document.querySelector('.search-bar input').addEventListener('keypress', async function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value;
                if (searchTerm) {
                    this.style.background = '#f0f0f0';
                    setTimeout(() => {
                        this.style.background = 'transparent';
                    }, 200);
                    await performSearch(searchTerm);
                }
            }
        });

        // Newsletter form with database integration
        document.querySelector('.newsletter-form').addEventListener('submit', async function(e) {
            const email = this.querySelector('.newsletter-input').value;
            if (email) {
                const btn = this.querySelector('.newsletter-btn');
                const originalText = btn.textContent;
                btn.textContent = 'Joining...';
                btn.disabled = true;
                
                const result = await subscribeNewsletter(email);
                
                if (result.success) {
                    btn.textContent = 'Joined!';
                    btn.style.background = '#4CAF50';
                    this.reset();
                    showNotification('Welcome to our book community!');
                } else {
                    btn.textContent = 'Error - Try Again';
                    btn.style.background = '#f44336';
                    showNotification('Could not join. Please try again.', 'error');
                }
                
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 2000);
            }
        });

        // Book card hover effects
        document.querySelectorAll('.book-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(-10px) scale(1)';
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            if (hero) {
                const rate = scrolled * -0.3;
                hero.style.transform = `translateY(${rate}px)`;
            }
        });

        // API Configuration
        const API_BASE_URL = '/api';

        // Data fetching utilities
        async function fetchData(endpoint) {
            try {
                const response = await fetch(`${API_BASE_URL}${endpoint}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                console.error('Error fetching data:', error);
                return null;
            }
        }

        // Create book card element
        function createBookCard(book) {
            return `
                <div class="book-card" data-book-id="${book.id}">
                    <div class="book-cover" style="background-image: url('${book.book_image || ''}')">
                        ${book.book_image ? '' : book.genre || 'Book'}
                    </div>
                    <div class="book-info">
                        <h3>${book.title}</h3>
                        <p>${book.author}</p>
                        <div class="book-price">${book.price}</div>
                        ${book.stock > 0 ? '' : '<span class="out-of-stock">Out of Stock</span>'}
                    </div>
                </div>
            `;
        }

        // Create staff pick card element
        function createStaffPickCard(pick) {
            return `
                <div class="pick-card scroll-fade" data-book-id="${pick.book_id}">
                    <div class="pick-quote">"${pick.review_text}"</div>
                    <div class="pick-author">${pick.book_title}</div>
                    <div class="pick-staff">— ${pick.staff_name} loves ${pick.staff_title}</div>
                </div>
            `;
        }

        // Create genre card element
        function createGenreCard(genre) {
            return `
                <div class="genre-card scroll-fade" data-genre-id="${genre.id}">
                    <div class="genre-icon">${genre.icon}</div>
                    <h3>${genre.name}</h3>
                    <p>${genre.description}</p>
                    <span class="book-count">${genre.book_count} books available</span>
                </div>
            `;
        }

        // Load featured books from database
        async function loadFeaturedBooks() {
            const books = await fetchData('/books/featured');
            if (books && books.length > 0) {
                const carousel = document.querySelector('.books-carousel');
                carousel.innerHTML = books.map(book => createBookCard(book)).join('');
                
                carousel.querySelectorAll('.book-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const bookId = card.dataset.bookId;
                        openBookModal(bookId);
                    });
                });
            }
        }

        // Load staff picks from database
        async function loadStaffPicks() {
            const picks = await fetchData('/books/staff-picks');
            if (picks && picks.length > 0) {
                const picksGrid = document.querySelector('.picks-grid');
                picksGrid.innerHTML = picks.map(pick => createStaffPickCard(pick)).join('');
            }
        }

        // Load genres from database
        async function loadGenres() {
            const genres = await fetchData('/genres');
            if (genres && genres.length > 0) {
                const genresGrid = document.querySelector('.genres-grid');
                genresGrid.innerHTML = genres.map(genre => createGenreCard(genre)).join('');
                
                genresGrid.querySelectorAll('.genre-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const genreId = card.dataset.genreId;
                        window.location.href = `/browse?genre=${genreId}`;
                    });
                });
            }
        }

        // Load hero featured book
        async function loadHeroBook() {
            const heroBook = await fetchData('/books/hero-featured');
            if (heroBook) {
                const featuredBookEl = document.querySelector('.featured-book');
                featuredBookEl.innerHTML = `
                    <h3 class="book-title">${heroBook.title}</h3>
                    <p class="book-author">by ${heroBook.author}</p>
                `;
                
                if (heroBook.book_image) {
                    featuredBookEl.style.backgroundImage = `url('${heroBook.book_image}')`;
                    featuredBookEl.style.backgroundSize = 'cover';
                    featuredBookEl.style.backgroundPosition = 'center';
                }
            }
        }

        // Search functionality with database integration
        async function performSearch(query) {
            if (!query.trim()) return;
            
            try {
                const results = await fetchData(`/search?q=${encodeURIComponent(query)}`);
                if (results) {
                    window.location.href = `/search?q=${encodeURIComponent(query)}`;
                }
            } catch (error) {
                console.error('Search error:', error);
            }
        }

        // Book modal for quick view
        async function openBookModal(bookId) {
            const book = await fetchData(`/books/${bookId}`);
            if (book) {
                const modal = document.createElement('div');
                modal.className = 'book-modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <span class="modal-close">&times;</span>
                        <div class="modal-book">
                            <img src="${book.book_image || ''}" alt="${book.title}" class="modal-book-cover">
                            <div class="modal-book-info">
                                <h2>${book.title}</h2>
                                <p class="modal-author">by ${book.author}</p>
                                <p class="modal-price">${book.price}</p>
                                <p class="modal-description">${book.description}</p>
                                <div class="modal-actions">
                                    <button class="add-to-cart-btn" data-book-id="${book.id}">Add to Cart</button>
                                    <button class="view-details-btn" onclick="window.location.href='/books/${book.id}'">See More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                modal.style.display = 'flex';
                
                modal.querySelector('.modal-close').onclick = () => {
                    modal.remove();
                };
                
                modal.onclick = (e) => {
                    if (e.target === modal) {
                        modal.remove();
                    }
                };
            }
        }

        // Add to cart functionality
        async function addToCart(bookId, quantity = 1) {
            try {
                const response = await fetch(`${API_BASE_URL}/cart/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        book_id: bookId,
                        quantity: quantity
                    })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    updateCartCount(result.cart_count);
                    showNotification('Book added to cart!');
                } else {
                    throw new Error('Failed to add to cart');
                }
            } catch (error) {
                console.error('Add to cart error:', error);
                showNotification('Error adding book to cart', 'error');
            }
        }

        // Update cart count in header
        function updateCartCount(count) {
            const cartBtn = document.querySelector('.icon-btn[data-cart]');
            if (cartBtn) {
                cartBtn.innerHTML = `🛒 ${count > 0 ? `<span class="cart-count">${count}</span>` : ''}`;
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Newsletter subscription with database
        async function subscribeNewsletter(email) {
            try {
                const response = await fetch(`${API_BASE_URL}/newsletter/subscribe`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email })
                });
                
                if (response.ok) {
                    return { success: true };
                } else {
                    throw new Error('Subscription failed');
                }
            } catch (error) {
                console.error('Newsletter subscription error:', error);
                return { success: false, error: error.message };
            }
        }

        // Authentication state management
        function updateAuthState() {
            const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
            const loginBtn = document.querySelector('.login-btn');
            const logoutBtn = document.querySelector('.logout-btn');
            
            if (isLoggedIn) {
                loginBtn.style.display = 'none';
                logoutBtn.style.display = 'block';
            } else {
                loginBtn.style.display = 'block';
                logoutBtn.style.display = 'none';
            }
        }

        // Logout functionality
        async function handleLogout() {
            try {
                const response = await fetch(`${API_BASE_URL}/logout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                if (response.ok) {
                    localStorage.setItem('isLoggedIn', 'false');
                    updateAuthState();
                    showNotification('Successfully logged out!');
                    window.location.href = '/';
                } else {
                    throw new Error('Logout failed');
                }
            } catch (error) {
                console.error('Logout error:', error);
                showNotification('Error logging out', 'error');
            }
        }

        // Initialize the page with database data
        document.addEventListener('DOMContentLoaded', async function() {
            document.body.style.opacity = '0';
            setTimeout(() => {
                document.body.style.transition = 'opacity 0.5s ease-in';
                document.body.style.opacity = '1';
            }, 100);
            
            try {
                await Promise.all([
                    loadHeroBook(),
                    loadFeaturedBooks(),
                    loadGenres(),
                    loadStaffPicks()
                ]);
                
                const cartData = await fetchData('/cart/count');
                if (cartData) {
                    updateCartCount(cartData.count);
                }
                
                updateAuthState();
                
                document.getElementById('logoutBtn').addEventListener('click', async (e) => {
                    await handleLogout();
                });
                
            } catch (error) {
                console.error('Error loading page data:', error);
                console.log('Falling back to static content');
            }
            
            setTimeout(() => {
                document.querySelectorAll('.scroll-fade').forEach(el => {
                    observer.observe(el);
                });
            }, 500);
        });

        // Event delegation for dynamically added content
        document.body.addEventListener('click', async function(e) {
            if (e.target.classList.contains('add-to-cart-btn')) {
                const bookId = e.target.dataset.bookId;
                await addToCart(bookId);
            }
            
            if (e.target.closest('.genre-card')) {
                const genreCard = e.target.closest('.genre-card');
                const genreId = genreCard.dataset.genreId;
                if (genreId) {
                    window.location.href = `/browse?genre=${genreId}`;
                }
            }
        });
    </script>
</body>
</html>

