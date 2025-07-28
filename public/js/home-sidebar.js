// home-sidebar.js
// Fetch and render recent posts for About card sidebar

document.addEventListener("DOMContentLoaded", function () {
    const recentPostsContainer = document.getElementById("about-recent-posts");
    if (!recentPostsContainer) return;

    function timeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const seconds = Math.floor((now - date) / 1000);
        if (isNaN(seconds)) return dateString;
        if (seconds < 60) return `${seconds}s ago`;
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes}m ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours}h ago`;
        const days = Math.floor(hours / 24);
        if (days < 7) return `${days}d ago`;
        const weeks = Math.floor(days / 7);
        if (weeks < 4) return `${weeks}w ago`;
        const months = Math.floor(days / 30);
        if (months < 12) return `${months}mo ago`;
        const years = Math.floor(days / 365);
        return `${years}y ago`;
    }

    function renderRecentPost(post) {
        const nestName = post.nest_name ? post.nest_name : "Unknown Nest";
        const userName = post.username ? post.username : "Unknown User";
        const nestImgSrc = post.nest_image
            ? `/storage/nests/profiles/${post.nest_image}`
            : "/images/avatars/default.jpg";
        return `
        <div class="d-flex align-items-center mb-3">
            <img src="${nestImgSrc}" alt="nest-img" class="rounded-pill avatar-40 me-2" style="object-fit:cover;">
            <div>
                <div><a href="/nests/${nestName}" class="fw-bold">${nestName}</a></div>
                <div class="text-muted small">by ${userName} • ${timeAgo(post.created_at)}</div>
                <div class="small">${post.title}</div>
            </div>
        </div>
        `;
    }

    function fetchRecentPosts() {
        recentPostsContainer.innerHTML = '<div class="text-muted">Loading recent posts...</div>';
        fetch('/sidebar-recent-posts?limit=5')
            .then((res) => res.json())
            .then((data) => {
                if (!data.data || data.data.length === 0) {
                    recentPostsContainer.innerHTML = '<div class="text-muted">No recent posts found.</div>';
                    return;
                }
                recentPostsContainer.innerHTML = data.data.map(renderRecentPost).join("");
            })
            .catch(() => {
                recentPostsContainer.innerHTML = '<div class="text-danger">Failed to load recent posts.</div>';
            });
    }

    fetchRecentPosts();
});
