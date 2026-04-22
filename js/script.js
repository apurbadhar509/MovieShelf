/* ===========================
   js/script.js — Main Scripts
   =========================== */

/* ---- Navbar: add 'scrolled' class on scroll ---- */
window.addEventListener('scroll', function () {
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }
});

/* ---- Movie Row Scroll Buttons ---- */
// Each .movie-row-wrapper has left/right scroll buttons
document.addEventListener('DOMContentLoaded', function () {

  // Setup all scroll buttons
  const wrappers = document.querySelectorAll('.movie-row-wrapper');

  wrappers.forEach(function (wrapper) {
    const row = wrapper.querySelector('.movie-row');
    const btnLeft = wrapper.querySelector('.scroll-btn.left');
    const btnRight = wrapper.querySelector('.scroll-btn.right');

    if (!row) return;

    // Scroll right
    if (btnRight) {
      btnRight.addEventListener('click', function () {
        row.scrollBy({ left: 600, behavior: 'smooth' });
      });
    }

    // Scroll left
    if (btnLeft) {
      btnLeft.addEventListener('click', function () {
        row.scrollBy({ left: -600, behavior: 'smooth' });
      });
    }
  });

  /* ---- Hero Auto-rotate (for index page) ---- */
  // Cycles through hero data every 7 seconds
  const heroData = [
    {
      title: 'DARK HORIZON',
      badge: '🔥 Trending Now',
      desc: 'In a fractured future, one detective must unravel a conspiracy that stretches across time and memory.',
      year: '2024',
      duration: '2h 18m',
      genre: 'Sci-Fi',
      rating: '98% Match',
      color: '#1a1a2e'
    },
    {
      title: 'ECHO OF SHADOWS',
      badge: '⭐ Top Pick',
      desc: 'A gripping psychological thriller about identity, loss, and the secrets that bind a small mountain town.',
      year: '2024',
      duration: '1h 52m',
      genre: 'Thriller',
      rating: '95% Match',
      color: '#1a0a0a'
    },
    {
      title: 'NEBULA RISING',
      badge: '🚀 New Release',
      desc: 'When humanity reaches the edge of the known galaxy, they find something waiting — and it has been waiting a very long time.',
      year: '2025',
      duration: '2h 34m',
      genre: 'Adventure',
      rating: '97% Match',
      color: '#0a1a0a'
    }
  ];

  let heroIndex = 0;
  const heroBg = document.querySelector('.hero-bg');
  const heroTitle = document.querySelector('.hero-title');
  const heroBadge = document.querySelector('.hero-badge');
  const heroDesc = document.querySelector('.hero-desc');
  const heroRating = document.querySelector('.hero-meta .rating');
  const heroYear = document.querySelector('.hero-meta .year');
  const heroDuration = document.querySelector('.hero-meta .duration');
  const heroGenre = document.querySelector('.hero-meta .genre');

  function updateHero() {
    const d = heroData[heroIndex];
    if (heroBg) heroBg.style.backgroundColor = d.color;
    if (heroTitle) fadeUpdate(heroTitle, d.title);
    if (heroBadge) fadeUpdate(heroBadge, d.badge);
    if (heroDesc) fadeUpdate(heroDesc, d.desc);
    if (heroRating) heroRating.textContent = d.rating;
    if (heroYear) heroYear.textContent = d.year;
    if (heroDuration) heroDuration.textContent = d.duration;
    if (heroGenre) heroGenre.textContent = d.genre;
    heroIndex = (heroIndex + 1) % heroData.length;
  }

  function fadeUpdate(el, newText) {
    el.style.transition = 'opacity 0.5s';
    el.style.opacity = '0';
    setTimeout(function () {
      el.textContent = newText;
      el.style.opacity = '1';
    }, 400);
  }

  // Only auto-rotate if hero elements exist
  if (heroTitle) {
    setInterval(updateHero, 7000);
  }

  /* ---- Active nav link highlight ---- */
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  const navLinks = document.querySelectorAll('.navbar-links a');
  navLinks.forEach(function (link) {
    const href = link.getAttribute('href');
    if (href && href.includes(currentPage)) {
      link.classList.add('active');
    }
  });

  /* ---- Category filter buttons ---- */
  const filterBtns = document.querySelectorAll('.filter-btn');
  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');

      const genre = btn.dataset.genre;
      const cards = document.querySelectorAll('.movie-card[data-genre]');
      cards.forEach(function (card) {
        if (genre === 'all' || card.dataset.genre === genre) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  /* ---- Password visibility toggle ---- */
  const toggleBtns = document.querySelectorAll('.toggle-pass');
  toggleBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const input = btn.previousElementSibling;
      if (input && input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
      } else if (input) {
        input.type = 'password';
        btn.textContent = '👁';
      }
    });
  });

  /* ---- Simple form client-side validation ---- */
  const registerForm = document.querySelector('#registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
      const pass = document.querySelector('#password').value;
      const confirm = document.querySelector('#confirm_password').value;
      const alertBox = document.querySelector('.alert');

      if (pass !== confirm) {
        e.preventDefault();
        if (alertBox) {
          alertBox.className = 'alert alert-error';
          alertBox.textContent = 'Passwords do not match!';
          alertBox.style.display = 'block';
        }
      }
    });
  }

}); // end DOMContentLoaded
