/* ===========================
   js/player.js — Video Player
   =========================== */

document.addEventListener('DOMContentLoaded', function () {

  const video = document.querySelector('#mainVideo');
  if (!video) return; // Exit if no video element on page

  const playBtn     = document.querySelector('#playBtn');
  const muteBtn     = document.querySelector('#muteBtn');
  const fullBtn     = document.querySelector('#fullBtn');
  const progressBar = document.querySelector('#progressBar');
  const progress    = document.querySelector('#progress');
  const timeDisplay = document.querySelector('#timeDisplay');
  const controls    = document.querySelector('.player-controls');
  const videoWrap   = document.querySelector('.video-wrapper');

  /* ---- Helpers ---- */
  function formatTime(secs) {
    const m = Math.floor(secs / 60);
    const s = Math.floor(secs % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
  }

  /* ---- Play / Pause ---- */
  function togglePlay() {
    if (video.paused) {
      video.play();
      if (playBtn) playBtn.textContent = '⏸';
    } else {
      video.pause();
      if (playBtn) playBtn.textContent = '▶';
    }
  }

  if (playBtn) playBtn.addEventListener('click', togglePlay);

  // Click on video to play/pause
  video.addEventListener('click', togglePlay);

  /* ---- Mute / Unmute ---- */
  if (muteBtn) {
    muteBtn.addEventListener('click', function () {
      video.muted = !video.muted;
      muteBtn.textContent = video.muted ? '🔇' : '🔊';
    });
  }

  /* ---- Progress bar ---- */
  video.addEventListener('timeupdate', function () {
    if (!video.duration) return;
    const pct = (video.currentTime / video.duration) * 100;
    if (progress) progress.style.width = pct + '%';
    if (timeDisplay) {
      timeDisplay.textContent = formatTime(video.currentTime) + ' / ' + formatTime(video.duration);
    }
  });

  if (progressBar) {
    progressBar.addEventListener('click', function (e) {
      const rect = progressBar.getBoundingClientRect();
      const pct  = (e.clientX - rect.left) / rect.width;
      video.currentTime = pct * video.duration;
    });
  }

  /* ---- Fullscreen ---- */
  if (fullBtn) {
    fullBtn.addEventListener('click', function () {
      if (!document.fullscreenElement) {
        (videoWrap || video).requestFullscreen();
        fullBtn.textContent = '⛶';
      } else {
        document.exitFullscreen();
        fullBtn.textContent = '⛶';
      }
    });
  }

  /* ---- Auto-hide controls ---- */
  let hideTimer;

  function showControls() {
    if (controls) controls.style.opacity = '1';
    clearTimeout(hideTimer);
    hideTimer = setTimeout(function () {
      if (!video.paused && controls) controls.style.opacity = '0';
    }, 3000);
  }

  if (videoWrap) {
    videoWrap.addEventListener('mousemove', showControls);
    videoWrap.addEventListener('mouseleave', function () {
      if (!video.paused && controls) controls.style.opacity = '0';
    });
  }

  /* ---- Keyboard shortcuts ---- */
  document.addEventListener('keydown', function (e) {
    switch (e.key) {
      case ' ':
      case 'k':
        e.preventDefault();
        togglePlay();
        break;
      case 'm':
        video.muted = !video.muted;
        if (muteBtn) muteBtn.textContent = video.muted ? '🔇' : '🔊';
        break;
      case 'ArrowRight':
        video.currentTime = Math.min(video.currentTime + 10, video.duration);
        break;
      case 'ArrowLeft':
        video.currentTime = Math.max(video.currentTime - 10, 0);
        break;
    }
  });

  /* ---- Volume control ---- */
  const volumeSlider = document.querySelector('#volumeSlider');
  if (volumeSlider) {
    volumeSlider.addEventListener('input', function () {
      video.volume = volumeSlider.value;
      video.muted  = (volumeSlider.value == 0);
      if (muteBtn) muteBtn.textContent = video.muted ? '🔇' : '🔊';
    });
  }

}); // end DOMContentLoaded
