<?php
// ============================
// index.php - Home Page
// ============================
// This is the main landing page of the website.
// It shows a hero section and featured movies from the database.

include 'db.php'; // Include database connection

// Start a session so we can check if user is logged in
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineStream - Watch & Download Movies</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- ====== NAVBAR ====== -->
    <nav class="navbar">
        <div class="logo">Cine<span>Stream</span></div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="movies.php">Movies</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- If logged in, show username and logout link -->
                <li><a href="#">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <!-- If not logged in, show Login and Register -->
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- ====== HERO SECTION ====== -->
    <section class="hero">
        <h1>Watch & Download <span>Movies Free</span></h1>
        <p>Thousands of movies at your fingertips. No subscription needed.</p>
        <a href="movies.php" class="btn btn-primary">Browse Movies</a>
        &nbsp;
        <a href="register.php" class="btn btn-outline">Join Free</a>
    </section>

    <!-- ====== FEATURED MOVIES ====== -->
    <h2 class="section-title">🎬 Featured Movies</h2>

    <div class="movie-grid">
        <?php
        // Fetch only 8 movies for the home page (LIMIT 8)
        $query  = "SELECT * FROM movies ORDER BY id DESC LIMIT 8";
        $result = mysqli_query($conn, $query);

        // Check if any movies were found
        if (mysqli_num_rows($result) > 0):
            // Loop through each movie and display a card
            while ($movie = mysqli_fetch_assoc($result)):
        ?>
                <div class="movie-card">
                    <!-- Movie Poster -->
                    <?php if (!empty($movie['poster'])): ?>
                        <img src="<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <?php else: ?>
                        <!-- Show emoji if no poster image -->
                        <div class="poster-placeholder">🎬</div>
                    <?php endif; ?>

                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p><?php echo htmlspecialchars($movie['genre']); ?> &bull; <?php echo htmlspecialchars($movie['year']); ?></p>
                        <!-- Link to movie details page, passing the movie ID in URL -->
                        <a href="movie_details.php?id=<?php echo $movie['id']; ?>" class="btn btn-primary">▶ View</a>
                    </div>
                </div>
        <?php
            endwhile;
        else:
            // If no movies in database yet, show a message
            echo '<p style="color: var(--muted); padding: 20px;">No movies found. <a href="add_movie.php" style="color:var(--accent)">Add some!</a></p>';
        endif;
        ?>
    </div>

    <!-- ====== FOOTER ====== -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> CineStream &mdash; </p>
    </footer>

    <script src="script.js"></script>
</body>

</html>