// js/auto_refresh.js
let autoRefreshInterval = null;

function startAutoRefresh(callback, ms = 10000) {
  if (autoRefreshInterval) clearInterval(autoRefreshInterval);
  callback();
  autoRefreshInterval = setInterval(callback, ms);
}

function stopAutoRefresh() {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
    autoRefreshInterval = null;
  }
}

// when included, start fetching pending automatically
document.addEventListener('DOMContentLoaded', function(){
  // expects a global function fetchPending() to exist in page
  if (typeof fetchPending === 'function') {
    startAutoRefresh(fetchPending, 10000);
  }
});
