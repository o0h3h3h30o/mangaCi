/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************!*\
  !*** ./resources/js/read.js ***!
  \******************************/
document.addEventListener("DOMContentLoaded", function () {
  // Mobile chapter navigation
  var mobileNav = document.getElementById('mobile-chapter-nav');

  // Navigation with keyboard
  var nextEle = document.getElementById('btn-next');
  var prevEle = document.getElementById('btn-prev');
  document.addEventListener('keydown', function (event) {
    if (event.keyCode === 37) {
      prevEle.click();
    } else if (event.keyCode === 39) {
      nextEle.click();
    }
  });

  // Set timeout for updating views
  setTimeout(function () {
    updateViews();
  }, 5000);

  // Track reading time and exp
  initializeReadingTracker();
});
function updateViews() {
  // Update views
  axios.post('/action/views', {
    chapter_id: chapter_id,
    _token: csrf_token
  }).then(function (resp) {
    var data = resp.data.data;

    // Handle exp awards
    if (data.exp_awarded && data.exp_awarded > 0) {
      showExpNotification(data.exp_awarded, data.current_level);
    }

    // Existing points notification (currently commented out)
    // if (data.s === 2) {
    //     window.customToast({
    //         icon: 'success',
    //         title: `Bạn được cộng ${data.p} điểm shop và ${data.a} điểm danh hiệu!`,
    //         duration: 3000
    //     });
    // }
  })["catch"](function (error) {
    console.error("Lỗi khi cập nhật views:", error);
  });
}

/**
 * Initialize reading time tracker
 * Existing updateViews() called every 5s already tracks backend state
 * No additional tracking needed - backend handles 10-min cooldown
 * Reading time tracking works silently without UI display
 */
function initializeReadingTracker() {
  // Reading time tracking is handled silently by backend
  // No UI display needed - functionality runs in background
}

/**
 * Show exp award notification using pure Tailwind CSS
 * Creates a toast element with slide-in animation, auto-dismisses after duration
 */
function showExpNotification(expAwarded, currentLevel) {
  // Create toast container
  var toast = document.createElement('div');
  toast.className = [
  // Positioning: fixed bottom-right corner
  'fixed', 'bottom-6', 'left-6', 'z-50',
  // Layout
  'flex', 'items-center', 'gap-3',
  // Styling
  'bg-green-600', 'text-white', 'px-5', 'py-3', 'rounded-lg', 'shadow-lg',
  // Initial state for animation: invisible + shifted down
  'opacity-0', 'translate-y-4',
  // Smooth transition for opacity and transform
  'transition-all', 'duration-300', 'ease-out'].join(' ');

  // Icon (checkmark circle) + message text
  toast.innerHTML = "\n        <svg class=\"w-6 h-6 flex-shrink-0\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\"\n                  d=\"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\"/>\n        </svg>\n        <span class=\"text-sm font-medium\">+".concat(expAwarded, " EXP! (Level ").concat(currentLevel, ")</span>\n    ");
  document.body.appendChild(toast);

  // Trigger slide-in animation on next frame
  // requestAnimationFrame ensures the browser has rendered the initial state first
  requestAnimationFrame(function () {
    toast.classList.remove('opacity-0', 'translate-y-4');
    toast.classList.add('opacity-100', 'translate-y-0');
  });

  // Auto-dismiss after 3 seconds
  setTimeout(function () {
    // Slide-out animation: fade + move down
    toast.classList.remove('opacity-100', 'translate-y-0');
    toast.classList.add('opacity-0', 'translate-y-4');
    // Remove from DOM after transition completes (300ms matches duration-300)
    setTimeout(function () {
      return toast.remove();
    }, 300);
  }, 3000);
}
/******/ })()
;