<script>
  // Real-time clock
  function updateClock() {
    const now = new Date();
    const options = { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    };
    const clockElement = document.getElementById('live-clock');
    if (clockElement) {
      clockElement.textContent = now.toLocaleDateString('id-ID', options);
    }
  }
  
  // Update clock every second
  setInterval(updateClock, 1000);
  
  // Initialize on page load
  document.addEventListener('DOMContentLoaded', updateClock);
  
  // Toast notification system
  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const colors = {
      success: 'bg-green-500',
      error: 'bg-red-500',
      warning: 'bg-yellow-500',
      info: 'bg-blue-500'
    };
    
    toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-xl shadow-xl z-50 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
      <div class="flex items-center space-x-3">
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
        <span>${message}</span>
      </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
      toast.classList.remove('translate-x-full');
      toast.classList.add('translate-x-0');
    }, 10);
    
    // Remove after 5 seconds
    setTimeout(() => {
      toast.classList.remove('translate-x-0');
      toast.classList.add('translate-x-full');
      setTimeout(() => toast.remove(), 300);
    }, 5000);
  }
  
  // Check for success/error messages in URL
  function checkURLMessages() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    
    if (success) {
      showToast(decodeURIComponent(success), 'success');
    }
    if (error) {
      showToast(decodeURIComponent(error), 'error');
    }
  }
  
  // Check messages on page load
  checkURLMessages();
  
  // Auto-hide alerts after 5 seconds
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });
</script>
</body>
</html>