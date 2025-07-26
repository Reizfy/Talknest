// Helper to format date as 'x ago'
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
let page = 1;
let loading = false;
let hasMore = true;
let currentSort = "best";

function renderPost(post) {
    const userImgSrc = post.user_image
        ? `/storage/profiles/images/${post.user_image}`
        : "/images/avatars/default.jpg";
    // Voting color logic (persist using backend value)
    const upvoteColor =
        post.current_user_vote === 1 ? "text-primary" : "text-body";
    const downvoteColor =
        post.current_user_vote === -1 ? "text-danger" : "text-body";
    return `
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between pb-4">
            <div class="header-title">
                <div class="d-flex flex-wrap">
                    <div class="media-support-user-img me-3">
                        <img class="rounded-pill img-fluid avatar-60 bg-soft-danger " src="${userImgSrc}" alt="">
                    </div>
                    <div class="media-support-info mt-3">
                        <h5 class="mb-0">u/${post.username ?? "Unknown"}</h5>
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
                        const ext = post.media.split(".").pop().toLowerCase();
                        const videoExts = ["mp4", "webm", "ogg", "mov", "mkv"];
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
                    <span style="font-weight:600;">${post.votes_count}</span>
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
                    <button class="btn d-flex align-items-center comment-btn" style="background:none;border:none;padding:0;">
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

function fetchPosts(initial = false) {
    if (loading || !hasMore) return;
    loading = true;
    const loadingDiv = document.getElementById("loading");
    if (loadingDiv) loadingDiv.style.display = "block";

    fetch(`/nests/${window.nestName}/posts?page=${page}&sort=${currentSort}`)
        .then((res) => res.json())
        .then((data) => {
            if (initial)
                document.getElementById("posts-container").innerHTML = "";
            data.data.forEach((post) => {
                document
                    .getElementById("posts-container")
                    .insertAdjacentHTML("beforeend", renderPost(post));
            });
            hasMore = !!data.next_page_url;
            if (hasMore) page++;
            if (loadingDiv) loadingDiv.style.display = "none";
            loading = false;
        });
}

document.addEventListener("DOMContentLoaded", () => {
    fetchPosts(true);
    const sortDropdown = document.getElementById("sort-dropdown");
    if (sortDropdown) {
        sortDropdown.addEventListener("change", function () {
            currentSort = this.value;
            page = 1;
            hasMore = true;
            fetchPosts(true);
        });
    }

    // Delegate vote button clicks only
    document.getElementById("posts-container").addEventListener("click", function (e) {
        const voteBtn = e.target.closest(".vote-btn");
        if (voteBtn) {
            const postId = voteBtn.getAttribute("data-post");
            const voteValue = parseInt(voteBtn.getAttribute("data-vote"));
            if (!postId || isNaN(voteValue)) return;
            // Find current vote from UI
            const card = voteBtn.closest(".card");
            let currentVote = 0;
            const upvoteBtn = card.querySelector('.vote-btn[data-vote="1"] .icon-32');
            const downvoteBtn = card.querySelector('.vote-btn[data-vote="-1"] .icon-32');
            if (upvoteBtn && upvoteBtn.classList.contains("text-primary")) currentVote = 1;
            if (downvoteBtn && downvoteBtn.classList.contains("text-danger")) currentVote = -1;
            // If already voted, undo vote
            const sendValue = currentVote === voteValue ? 0 : voteValue;
            fetch(`/posts/${postId}/vote`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: JSON.stringify({ value: sendValue }),
            })
            .then((res) => res.json())
            .then((data) => {
                // Update vote count and colors in UI
                if (!card) return;
                // Update vote count
                const voteCountSpan = card.querySelector('span[style*="font-weight:600;"]');
                if (voteCountSpan) voteCountSpan.textContent = data.votes_count;
                // Update upvote/downvote colors using backend value
                if (upvoteBtn) {
                    upvoteBtn.classList.remove("text-primary", "text-body");
                    upvoteBtn.classList.add(data.current_user_vote === 1 ? "text-primary" : "text-body");
                }
                if (downvoteBtn) {
                    downvoteBtn.classList.remove("text-danger", "text-body");
                    downvoteBtn.classList.add(data.current_user_vote === -1 ? "text-danger" : "text-body");
                }
            });
            return;
        }
        // Comment button click: open detail modal
        const commentBtn = e.target.closest(".comment-btn");
        if (commentBtn) {
            const card = commentBtn.closest(".card");
            const postId = card.querySelector('.vote-btn[data-post]')?.getAttribute('data-post');
            if (!postId) return;
            // Simulate card click to open detail
            fetch(`/nests/${window.nestName}/comments/${postId}`)
                .then((res) => res.json())
                .then((data) => {
                    const oldModal = document.getElementById("postDetailModal");
                    if (oldModal) oldModal.remove();
                    document.body.insertAdjacentHTML("beforeend", window.renderPostDetail ? window.renderPostDetail(data.post, data.comments) : renderPostDetail(data.post, data.comments));
                    const modalEl = document.getElementById("postDetailModal");
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                });
            return;
        }
    });
});

const style = document.createElement("style");
style.innerHTML = `
    .card.mb-3:hover {
        background-color: #f5f5f5 !important;
        cursor: pointer !important;
    }
    .card.mb-3:hover .card-header,
    .card.mb-3:hover .card-body {
        background-color: #f5f5f5 !important;
    }
`;
document.head.appendChild(style);
