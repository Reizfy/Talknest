// profile-feed.js
// User profile feed: matches home-feed.js and home-detail.js, but only shows posts by the user

document.addEventListener("DOMContentLoaded", function () {
    // Add hover style for cards
    const style = document.createElement("style");
    style.innerHTML = `
.media-support-info a {
    color: #222 !important;
    text-decoration: none;
    transition: color 0.2s;
}
.media-support-info a:hover {
    color: #222 !important;
    text-decoration: underline !important;
}
.card.home-feed-card, .card.profile-feed-card {
    transition: background 0.2s;
}
.card.home-feed-card:hover, .card.profile-feed-card:hover {
    background-color: #f5f5f5 !important;
    cursor: pointer !important;
}
.card.home-feed-card:hover .card-header,
.card.home-feed-card:hover .card-body,
.card.profile-feed-card:hover .card-header,
.card.profile-feed-card:hover .card-body {
    background-color: #f5f5f5 !important;
}
`;
    document.head.appendChild(style);

    const feedContainer = document.getElementById("profile-feed");
    let page = 1;
    let loading = false;
    let hasMore = true;

    function timeAgo(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        if (diff < 60) return `${diff}s ago`;
        if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        return date.toLocaleDateString();
    }

    function renderPost(post) {
        const nestImgSrc = post.nest_image
            ? `/storage/nests/profiles/${post.nest_image}`
            : "/images/avatars/default.jpg";
        const upvoteColor =
            post.current_user_vote === 1 ? "text-primary" : "text-body";
        const downvoteColor =
            post.current_user_vote === -1 ? "text-danger" : "text-body";
        return `
        <div class="card mb-3 home-feed-card">
            <div class="card-header d-flex align-items-center justify-content-between pb-4">
                <div class="header-title">
                    <div class="d-flex flex-wrap">
                        <div class="media-support-user-img me-3">
                            <img class="rounded-pill img-fluid avatar-60 bg-soft-danger " src="${nestImgSrc}" alt="Nest profile">
                        </div>
                        <div class="media-support-info mt-3">
                            <h5 class="mb-0">
                                <a href="/nests/${
                                    post.nest_name
                                }" style="text-decoration:none;">
                                    n/${post.nest_name ?? "Unknown"}
                                </a>
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <span class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                        ${timeAgo(post.created_at)}
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="px-4 pb-2">
                    <h4>${post.title ?? ""}</h4>
                </div>
                ${
                    post.media && post.media.trim() !== ""
                        ? `
                    <div class="user-post mt-2" style="width:100%;height:600px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#222;">
                        ${(() => {
                            const ext = post.media
                                .split(".")
                                .pop()
                                .toLowerCase();
                            const videoExts = [
                                "mp4",
                                "webm",
                                "ogg",
                                "mov",
                                "mkv",
                            ];
                            if (videoExts.includes(ext)) {
                                return `<video src="/storage/posts/${post.media}" controls style="width:100%;height:100%;object-fit:contain;background:#000;"></video>`;
                            } else {
                                return `<img src="/storage/posts/${post.media}" alt="post-image" style="width:100%;height:100%;object-fit:contain;">`;
                            }
                        })()}
                    </div>
                `
                        : ""
                }
                <div class="px-4">
                    ${
                        !post.media || post.media.trim() === ""
                            ? `<p style='color:#222;font-size:1.2em;width:100%;margin:24px 0;'>${
                                  post.content ?? ""
                              }</p>`
                            : ""
                    }
                </div>
                <div class="comment-area d-flex p-3" style="gap:12px;">
                    <div class="d-flex align-items-center bg-light" style="gap:12px;padding:8px 16px;border-radius:10px;width:max-content;">
                        <button class="btn d-flex align-items-center vote-btn" data-vote="1" data-post="${
                            post.id
                        }" style="background:none;border:none;padding:0;">
                            <svg class="icon-32 ${upvoteColor}" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.334 2.75H7.665C4.645 2.75 2.75 4.889 2.75 7.916V16.084C2.75 19.111 4.635 21.25 7.665 21.25H16.334C19.364 21.25 21.25 19.111 21.25 16.084V7.916C21.25 4.889 19.364 2.75 16.334 2.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M12 7.91394L12 16.0859" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M8.25205 11.6777L12 7.91373L15.748 11.6777" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                        <span style="font-weight:600;">${
                            post.votes_count
                        }</span>
                        <button class="btn d-flex align-items-center vote-btn" data-vote="-1" data-post="${
                            post.id
                        }" style="background:none;border:none;padding:0;">
                            <svg class="icon-32 ${downvoteColor}" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.666 21.25H16.335C19.355 21.25 21.25 19.111 21.25 16.084V7.916C21.25 4.889 19.365 2.75 16.335 2.75H7.666C4.636 2.75 2.75 4.889 2.75 7.916V16.084C2.75 19.111 4.636 21.25 7.666 21.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M12 16.0861V7.91406" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M15.748 12.3223L12 16.0863L8.25195 12.3223" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="d-flex align-items-center bg-light" style="gap:12px;padding:8px 16px;border-radius:10px;width:max-content;">
                        <button class="btn d-flex align-items-center comment-btn" style="background:none;border:none;padding:0;" data-post="${
                            post.id
                        }">
                            <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.0714 19.0699C16.0152 22.1263 11.4898 22.7867 7.78642 21.074C7.23971 20.8539 6.79148 20.676 6.36537 20.676C5.17849 20.683 3.70117 21.8339 2.93336 21.067C2.16555 20.2991 3.31726 18.8206 3.31726 17.6266C3.31726 17.2004 3.14642 16.7602 2.92632 16.2124C1.21283 12.5096 1.87411 7.98269 4.93026 4.92721C8.8316 1.02443 15.17 1.02443 19.0714 4.9262C22.9797 8.83501 22.9727 15.1681 19.0714 19.0699Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M15.9393 12.4131H15.9483" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M11.9306 12.4131H11.9396" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                <path d="M7.92128 12.4131H7.93028" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                        <span style="font-weight:600;">${
                            post.comments_count ?? 0
                        }</span>
                    </div>
                </div>
            </div>
        </div>
        `;
    }

    // Remove old modal if exists, always use #postDetailModal for consistency
    const oldModal = document.getElementById("postDetailModal");
    if (oldModal) oldModal.remove();

    // Use profile-detail.js modal for post detail
    function openDetailModal(postId) {
        if (typeof window.showProfilePostDetail === "function") {
            window.showProfilePostDetail(postId);
        } else {
            alert("Profile detail modal not loaded.");
        }
    }

    // Fetch and render user posts
    function fetchUserPosts(initial = false) {
        if (loading || !hasMore) return;
        loading = true;
        if (initial)
            feedContainer.innerHTML =
                "<div class='text-muted'>Loading posts...</div>";
        fetch(`/user-feed-posts?page=${page}`)
            .then((res) => res.json())
            .then((data) => {
                const posts = Array.isArray(data.data) ? data.data : [];
                if (initial) feedContainer.innerHTML = "";
                if (posts.length > 0) {
                    posts.forEach((post) => {
                        feedContainer.insertAdjacentHTML(
                            "beforeend",
                            renderPost(post)
                        );
                    });
                }
                hasMore = posts.length >= 10;
                if (hasMore) page++;
                loading = false;
            });
    }

    // Initial fetch
    fetchUserPosts(true);

    // Infinite scroll
    window.addEventListener("scroll", function () {
        if (!hasMore || loading) return;
        const scrollY = window.scrollY || window.pageYOffset;
        const viewport = window.innerHeight;
        const fullHeight = document.body.offsetHeight;
        if (scrollY + viewport + 200 >= fullHeight) {
            fetchUserPosts();
        }
    });

    // Event delegation for vote, comment, and card click
    feedContainer.addEventListener("click", function (e) {
        // Vote button logic
        const voteBtn = e.target.closest(".vote-btn");
        if (voteBtn) {
            const postId = voteBtn.getAttribute("data-post");
            const voteValue = parseInt(voteBtn.getAttribute("data-vote"));
            if (!postId || isNaN(voteValue)) return;
            const card = voteBtn.closest(".card");
            let currentVote = 0;
            const upvoteBtn = card.querySelector(
                '.vote-btn[data-vote="1"] .icon-32'
            );
            const downvoteBtn = card.querySelector(
                '.vote-btn[data-vote="-1"] .icon-32'
            );
            if (upvoteBtn && upvoteBtn.classList.contains("text-primary"))
                currentVote = 1;
            if (downvoteBtn && downvoteBtn.classList.contains("text-danger"))
                currentVote = -1;
            const sendValue = currentVote === voteValue ? 0 : voteValue;
            fetch(`/posts/${postId}/vote`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({ value: sendValue }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (!card) return;
                    const voteCountSpan = card.querySelector(
                        'span[style*="font-weight:600;"]'
                    );
                    if (voteCountSpan)
                        voteCountSpan.textContent = data.votes_count;
                    if (upvoteBtn) {
                        upvoteBtn.classList.remove("text-primary", "text-body");
                        upvoteBtn.classList.add(
                            data.current_user_vote === 1
                                ? "text-primary"
                                : "text-body"
                        );
                    }
                    if (downvoteBtn) {
                        downvoteBtn.classList.remove(
                            "text-danger",
                            "text-body"
                        );
                        downvoteBtn.classList.add(
                            data.current_user_vote === -1
                                ? "text-danger"
                                : "text-body"
                        );
                    }
                });
            return; // Prevent card/modal logic on vote click
        }

        // Comment button logic OR card click logic
        let postId = null;
        const commentBtn = e.target.closest(".comment-btn");
        if (commentBtn) {
            postId = commentBtn.getAttribute("data-post");
        } else {
            const card = e.target.closest(".card");
            if (card) {
                // Find post id from card
                // If not present, try to find from vote/comment btn inside card
                postId = card.getAttribute("data-post-id");
                if (!postId) {
                    // Try to find from comment button inside card
                    const commentBtnInCard = card.querySelector(".comment-btn");
                    if (commentBtnInCard) {
                        postId = commentBtnInCard.getAttribute("data-post");
                    }
                }
            }
        }
        if (postId) openDetailModal(postId);
    });
});
