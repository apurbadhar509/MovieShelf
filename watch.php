<?php
/* ===========================
   watch.php
   Video watching page with access control.

   Logic:
   1. If NOT logged in → redirect to login.html
   2. If logged in but NOT subscribed → show "subscribe" message
   3. If logged in AND subscribed → show video player
   =========================== */

// Start session to read user data
session_start();

// --- ACCESS CONTROL: Step 1 ---
// Check if user is logged in by looking for user_id in session
if (!isset($_SESSION['user_id'])) {
    // Not logged in — send them to the login page
    // We also pass the current URL so we can redirect back after login (optional)
    header('Location: login.html?error=login_required');
    exit;
}

// --- User is logged in, get their data ---
$user_name     = htmlspecialchars($_SESSION['user_name']);
$is_subscribed = $_SESSION['is_subscribed'];  // 0 = not subscribed, 1 = subscribed

// Get movie ID from URL parameter (e.g., watch.php?id=1)
// intval() converts it to an integer for safety
$movie_id = intval($_GET['id'] ?? 1);

// Sample movie data — in a real project this would come from the database
// Array key = movie id, value = movie info array
$movies = [
    1 => ['title' => 'Dark Horizon',   'year' => '2024', 'genre' => 'Sci-Fi',    'duration' => '2h 18m'],
    2 => ['title' => 'Echo of Shadows','year' => '2024', 'genre' => 'Thriller',  'duration' => '1h 52m'],
    3 => ['title' => 'Nebula Rising',  'year' => '2025', 'genre' => 'Adventure', 'duration' => '2h 34m'],
    4 => ['title' => 'The Last Signal','year' => '2024', 'genre' => 'Drama',     'duration' => '1h 44m'],
    5 => ['title' => 'Iron Wolves',    'year' => '2023', 'genre' => 'Action',    'duration' => '2h 05m'],
];

// Use movie data if id exists, otherwise show a default
$movie = $movies[$movie_id] ?? ['title' => 'Unknown Movie', 'year' => '2024', 'genre' => 'Unknown', 'duration' => 'N/A'];
?>
<!DOCTYPE html>
<!--
  ===== watch.php =====
  Protected video player page.
  Requires login + subscription to view content.
-->
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $movie['title'] ?> — CineStream</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/movie.css" />
  <style>
    /* Inline styles specific to the watch page */
    .player-controls {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
      padding: 30px 20px 16px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      opacity: 1;
      transition: opacity 0.3s ease;
    }

    .progress-bar-wrap {
      width: 100%;
      height: 4px;
      background: rgba(255,255,255,0.25);
      border-radius: 2px;
      cursor: pointer;
      transition: height 0.2s;
    }

    .progress-bar-wrap:hover { height: 7px; }

    #progress {
      height: 100%;
      background: var(--red);
      border-radius: 2px;
      width: 0%;
      transition: width 0.5s linear;
    }

    .controls-row {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .ctrl-btn {
      background: none;
      border: none;
      color: var(--white);
      font-size: 1.2rem;
      cursor: pointer;
      padding: 4px;
      opacity: 0.85;
      transition: opacity 0.2s;
    }

    .ctrl-btn:hover { opacity: 1; }

    #timeDisplay {
      font-size: 0.8rem;
      color: rgba(255,255,255,0.7);
      margin-left: 4px;
    }

    #volumeSlider {
      width: 80px;
      accent-color: var(--red);
      cursor: pointer;
    }

    .ctrl-btn.fullscreen { margin-left: auto; }

    .movie-title-bar {
      padding: 20px 20px 0;
      color: var(--white);
    }

    .movie-title-bar h2 {
      font-family: var(--font-display);
      font-size: 1.6rem;
      letter-spacing: 1px;
    }

    .movie-title-bar .meta {
      font-size: 0.82rem;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--text-muted);
      font-size: 0.85rem;
      margin-bottom: 16px;
      transition: color var(--transition);
    }

    .back-btn:hover { color: var(--white); }

    /* Demo video placeholder (shown when no real video file exists) */
    .demo-screen {
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, #0d0d1a, #1a0d1a);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: rgba(255,255,255,0.5);
      font-family: var(--font-display);
      font-size: 1rem;
      letter-spacing: 2px;
      gap: 12px;
    }

    .demo-screen .play-icon { font-size: 4rem; opacity: 0.3; }
  </style>
</head>
<body class="watch-page">

<!-- Simple back nav -->
<nav style="position:fixed;top:0;left:0;right:0;z-index:100;padding:16px 4%;background:rgba(0,0,0,0.8);">
  <div style="display:flex;align-items:center;justify-content:space-between;">
    <a href="index.html" class="navbar-logo" style="font-size:1.6rem;">CINESTREAM</a>
    <div style="display:flex;align-items:center;gap:16px;">
      <!-- Show username from session -->
      <span style="font-size:0.85rem;color:var(--text-muted);">
        Signed in as <strong style="color:var(--white);"><?= $user_name ?></strong>
      </span>
      <a href="php/logout.php" style="font-size:0.82rem;color:var(--text-muted);">Sign Out</a>
    </div>
  </div>
</nav>

<div class="video-container" style="margin-top: 80px;">

  <!-- Back button -->
  <a href="movie.html?id=<?= $movie_id ?>" class="back-btn">← Back to <?= $movie['title'] ?></a>

  <?php
  // --- ACCESS CONTROL: Step 2 ---
  // Check if user has an active subscription
  if ($is_subscribed == 0):
  ?>

    <!-- NOT SUBSCRIBED: Show subscription wall -->
    <div class="subscribe-wall">
      <span class="lock-icon">🔒</span>
      <h2>Subscription Required</h2>
      <p>
        You need an active subscription to watch
        <strong style="color:var(--white);"><?= $movie['title'] ?></strong>.
        Choose a plan to start watching unlimited movies and shows.
      </p>
      <a href="subscription.html" class="btn-primary" style="display:inline-block; text-decoration:none;">
        View Subscription Plans
      </a>
      <p style="margin-top: 16px; font-size:0.8rem; color: var(--text-muted);">
        Already subscribed? Try <a href="php/logout.php" style="color:var(--text-muted);">signing out</a> and back in.
      </p>
    </div>

  <?php else: ?>

    <!-- SUBSCRIBED: Show the video player -->
    <div class="movie-title-bar">
      <h2><?= $movie['title'] ?></h2>
      <div class="meta">
        <?= $movie['year'] ?> &nbsp;·&nbsp; <?= $movie['genre'] ?> &nbsp;·&nbsp; <?= $movie['duration'] ?>
      </div>
    </div>

    <div class="video-wrapper" style="margin-top:12px;">

      <!--
        VIDEO ELEMENT:
        Replace 'assets/videos/movie1.mp4' with a real video file path.
        The demo-screen div below is shown as a placeholder when no video is loaded.
      -->
      <video
        id="mainVideo"
        preload="metadata"
        style="position:absolute;inset:0;width:100%;height:100%;background:#000;"
      >
        <!-- Point this src to your actual video file -->
        <source src="assets/videos/sample.mp4" type="video/mp4" />
        Your browser does not support the video element.
      </video>

      <!-- Placeholder shown if no video file is present -->
      <div class="demo-screen" id="demoScreen">
        <span class="play-icon">▶</span>
        <span><?= strtoupper($movie['title']) ?></span>
        <span style="font-family: var(--font-body); font-size:0.75rem; letter-spacing:1px;">
          Add a real .mp4 file to assets/videos/ to enable playback
        </span>
      </div>

      <!-- Custom Video Controls -->
      <div class="player-controls" id="playerControls">

        <!-- Progress bar -->
        <div class="progress-bar-wrap" id="progressBar">
          <div id="progress"></div>
        </div>

        <!-- Control buttons row -->
        <div class="controls-row">
          <button class="ctrl-btn" id="playBtn" title="Play / Pause (Space)">▶</button>

          <button class="ctrl-btn" id="muteBtn" title="Mute (M)">🔊</button>
          <input type="range" id="volumeSlider" min="0" max="1" step="0.05" value="1" title="Volume" />

          <span id="timeDisplay">0:00 / 0:00</span>

          <button class="ctrl-btn fullscreen" id="fullBtn" title="Fullscreen">⛶</button>
        </div>

      </div>
    </div>

    <!-- Keyboard shortcut hints -->
    <div style="margin-top:14px; font-size:0.78rem; color:var(--text-muted); text-align:center;">
      Keyboard: <kbd style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:3px;">Space</kbd> Play/Pause &nbsp;
      <kbd style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:3px;">←</kbd>
      <kbd style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:3px;">→</kbd> Seek 10s &nbsp;
      <kbd style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:3px;">M</kbd> Mute
    </div>

  <?php endif; ?>

</div>

<script src="js/script.js"></script>
<?php if ($is_subscribed): ?>
<script src="js/player.js"></script>
<script>
  // Hide the demo screen placeholder when the video actually loads
  const video = document.getElementById('mainVideo');
  const demo  = document.getElementById('demoScreen');

  if (video && demo) {
    video.addEventListener('loadeddata', function () {
      demo.style.display = 'none';
    });

    // If video fails to load (no file), keep demo screen visible
    video.addEventListener('error', function () {
      demo.style.display = 'flex';
    });
  }
</script>
<?php endif; ?>

</body>
</html>
