const cookie = () => {
    const daysToKeep = 365; 
  
    const banner = document.getElementById('cookie-banner');
    const acceptBtn = document.getElementById('cookie-accept');
    const moreBtn = document.getElementById('cookie-more');
  
    function setCookie(name, value, days) {
      let expires = "";
      if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
  
    function getCookie(name) {
      const nameEQ = name + "=";
      const ca = document.cookie.split(';');
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
      }
      return null;
    }
  
    const isAccepted = getCookie('cookieAccepted') === 'true';
  
    if (!isAccepted) {
      banner.hidden = false;
      banner.classList.add('active');
    }
  
    acceptBtn.addEventListener('click', function () {
      setCookie('cookieAccepted', 'true', daysToKeep);
      banner.style.transition = 'opacity 200ms ease, transform 200ms';
      banner.style.opacity = '0';
      banner.style.transform = 'translateY(10px)';
      setTimeout(() => banner.hidden = true, 220);
    });
  
    moreBtn.addEventListener('click', function () {
      banner.style.transition = 'opacity 200ms ease, transform 200ms';
      banner.style.opacity = '0';
      banner.style.transform = 'translateY(10px)';
      setTimeout(() => banner.hidden = true, 220);
    });
  }
  
  export default cookie;
  