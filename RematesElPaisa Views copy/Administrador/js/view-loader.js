// Simple view loader: fetches HTML files from `html/` and injects their <main class="main-content"> into #app-content
(function(){
  function fetchView(path){
    return fetch(path, {cache: 'no-store'})
      .then(function(res){ if(!res.ok) throw new Error('Fetch failed: '+res.status); return res.text(); })
      .then(function(html){
        // Try to extract <main class="main-content"> from the fetched HTML
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        var main = doc.querySelector('main.main-content') || doc.querySelector('body') || doc.documentElement;
        return main.innerHTML;
      });
  }

  function setActiveLink(link){
    document.querySelectorAll('[data-view].active').forEach(function(a){ a.classList.remove('active'); });
    if(link) link.classList.add('active');
  }

  function loadDefault(){
    // Load dashboard content or index if a local fragment is desired
    // If there is a local fragment in the current document's <main>, keep it.
    var currentMain = document.querySelector('#app-content');
    if(currentMain && currentMain.dataset.initialized) return;
    // If there's a link with data-view="index" use it as default
    var defaultLink = document.querySelector('[data-view="index"]');
    if(defaultLink){ setActiveLink(defaultLink); }
    if(currentMain) currentMain.dataset.initialized = 'true';
  }

  function attachHandlers(){
    document.body.addEventListener('click', function(e){
      // Prefer explicit data-view attributes
      var a = e.target.closest('[data-view]');
      // Otherwise intercept local .html links (relative) to prevent full reload
      if(!a){
        var candidate = e.target.closest('a[href$=".html"]');
        if(candidate && !/^(https?:)?\/\//.test(candidate.getAttribute('href'))){
          a = candidate;
        }
      }
  if(!a) return;
  // If this anchor explicitly opts out of client-side routing, allow default navigation
  if(a.hasAttribute && a.hasAttribute('data-external')) return;
      e.preventDefault();
      var view = a.getAttribute('data-view') || a.getAttribute('href');
      if(!view) return;
      // Normalize href starting with ./ or leading slash
      setActiveLink(a);
      if(view === 'index') return;
      var container = document.getElementById('app-content');
      if(!container) return;
      container.classList.add('loading');
      fetchView(view).then(function(content){
        container.innerHTML = content;
        container.classList.remove('loading');
        window.scrollTo(0,0);
      }).catch(function(err){
        container.classList.remove('loading');
        container.innerHTML = '<div class="error">No se pudo cargar la vista: '+(err.message||err)+'</div>';
      });
    });
  }

  // Initialize
  document.addEventListener('DOMContentLoaded', function(){
    attachHandlers();
    loadDefault();
  });
})();
