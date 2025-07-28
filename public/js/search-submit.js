document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.querySelector('.search-input input[type="search"]');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var q = searchInput.value.trim();
                if (q.length > 0) {
                    window.location.href = '/search?q=' + encodeURIComponent(q);
                }
            }
        });
    }
});
