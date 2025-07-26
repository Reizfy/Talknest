// Modal post detail with comments
function commentHtml(comment) {
    const userImgSrc = comment.user_image
        ? `/storage/profiles/images/${comment.user_image}`
        : "/images/avatars/02.png";
    let repliesHtml = "";
    if (Array.isArray(comment.replies) && comment.replies.length > 0) {
        repliesHtml = `<ul class=\"list-unstyled\">${comment.replies.map(commentHtml).join("")}</ul>`;
    }
    // Indent <li> if this is a reply (has parent_id)
    const liIndentClass = comment.parent_id ? "ms-5" : "";
    return `
        <li class="mb-2 ${liIndentClass}" data-comment-id="${comment.id}">
            <div class="d-flex">
                <img src="${userImgSrc}" alt="userimg" class="avatar-50 p-1 pt-2 bg-soft-primary rounded-pill img-fluid">
                <div class="ms-3">
                    <h6 class="mb-1">${comment.username ?? "Unknown"}</h6>
                    <p class="mb-1">${comment.content ?? ""}</p>
                    <div class="d-flex flex-wrap align-items-center mb-1" style="gap:8px;">
                        <span>${timeAgo(comment.created_at)}</span>
                        <button class="btn btn-sm btn-link reply-btn" type="button">Reply</button>
                    </div>
                    <div class="reply-form-container" style="display:none;"></div>
                </div>
            </div>
            ${repliesHtml}
        </li>
    `;
}

function renderPostDetail(post, comments) {
    const userImgSrc = post.user_image
        ? `/storage/profiles/images/${post.user_image}`
        : "/images/avatars/02.png";
    // Voting color logic for modal
    const upvoteColor = post.current_user_vote === 1 ? "text-primary" : "text-body";
    const downvoteColor = post.current_user_vote === -1 ? "text-danger" : "text-body";
    return `
    <div class="modal fade" id="postDetailModal" tabindex="-1" aria-labelledby="postDetailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <div class="d-flex align-items-center">
              <img class="rounded-pill img-fluid avatar-60 bg-soft-danger p-1 ps-2" src="${userImgSrc}" alt="">
              <h5 class="ms-3 mb-0">u/${post.username ?? "Unknown"}</h5>
            </div>
            <button type="button" class="btn-close mx-2" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h4>${post.title ?? ""}</h4>
            ${
                post.media && post.media.trim() !== ""
                    ? `<div class="user-post mt-2" style="width:100%;height:400px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#222;border-radius:8px;">${(() => {
                          const ext = post.media.split(".").pop().toLowerCase();
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
                      })()}</div>`
                    : ""
            }
            <p class="mt-3">${post.content ?? ""}</p>
            <div class="d-flex align-items-center bg-light" style="gap:12px;padding:8px 16px;border-radius:10px;width:max-content;margin-bottom:16px;">
                <button class="btn d-flex align-items-center vote-btn" data-vote="1" data-post="${post.id}" style="background:none;border:none;padding:0;">
                    <svg class="icon-32 ${upvoteColor}" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.334 2.75H7.665C4.645 2.75 2.75 4.889 2.75 7.916V16.084C2.75 19.111 4.635 21.25 7.665 21.25H16.334C19.364 21.25 21.25 19.111 21.25 16.084V7.916C21.25 4.889 19.364 2.75 16.334 2.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 7.91394L12 16.0859" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M8.25205 11.6777L12 7.91373L15.748 11.6777" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
                <span style="font-weight:600;" id="votes-count-modal">${post.votes_count}</span>
                <button class="btn d-flex align-items-center vote-btn" data-vote="-1" data-post="${post.id}" style="background:none;border:none;padding:0;">
                    <svg class="icon-32 ${downvoteColor}" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.666 21.25H16.335C19.355 21.25 21.25 19.111 21.25 16.084V7.916C21.25 4.889 19.365 2.75 16.335 2.75H7.666C4.636 2.75 2.75 4.889 2.75 7.916V16.084C2.75 19.111 4.636 21.25 7.666 21.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M12 16.0861V7.91406" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M15.748 12.3223L12 16.0863L8.25195 12.3223" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
            <hr>
            <ul class="list-unstyled" id="comments-list">
              ${comments.map(commentHtml).join("")}
            </ul>
            <form id="comment-form-modal" class="mb-3" autocomplete="off">
              <div class="input-group">
                <input type="text" class="form-control" name="content" placeholder="Add a comment..." required autocomplete="off">
                <button class="btn btn-primary" type="button" id="comment-submit-btn">Comment</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    `;
}

// Delegate post click to show modal

document
    .getElementById("posts-container")
    .addEventListener("click", function (e) {
        const card = e.target.closest(".card");
        if (!card) return;
        // Only open modal if not clicking vote/comment buttons
        if (e.target.closest(".vote-btn") || e.target.closest(".comment-area"))
            return;
        const postId = card
            .querySelector(".vote-btn[data-post]")
            ?.getAttribute("data-post");
        if (!postId) return;
        fetch(`/nests/${window.nestName}/comments/${postId}`)
            .then((res) => res.json())
            .then((data) => {
                // Remove any existing modal
                const oldModal = document.getElementById("postDetailModal");
                if (oldModal) oldModal.remove();
                // Insert modal HTML
                document.body.insertAdjacentHTML(
                    "beforeend",
                    renderPostDetail(data.post, data.comments)
                );
                // Show modal
                const modalEl = document.getElementById("postDetailModal");
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                // Attach all event listeners after modal is rendered
                attachModalListeners(modalEl, data.post.id);
            });

        // Helper to attach listeners to modal
        function attachModalListeners(modalEl, postId) {
            // Voting
            const upvoteBtn = modalEl.querySelector('.vote-btn[data-vote="1"] .icon-32');
            const downvoteBtn = modalEl.querySelector('.vote-btn[data-vote="-1"] .icon-32');
            modalEl.querySelectorAll('.vote-btn').forEach((btn) => {
                btn.addEventListener('click', function(ev) {
                    ev.stopPropagation();
                    const voteValue = parseInt(btn.getAttribute('data-vote'));
                    let currentVote = 0;
                    if (upvoteBtn && upvoteBtn.classList.contains('text-primary')) currentVote = 1;
                    if (downvoteBtn && downvoteBtn.classList.contains('text-danger')) currentVote = -1;
                    const sendValue = currentVote === voteValue ? 0 : voteValue;
                    fetch(`/posts/${postId}/vote`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ value: sendValue }),
                    })
                    .then(res => res.json())
                    .then(result => {
                        const voteCountSpan = modalEl.querySelector('#votes-count-modal');
                        if (voteCountSpan && result.votes_count !== undefined) voteCountSpan.textContent = result.votes_count;
                        if (upvoteBtn) {
                            upvoteBtn.classList.remove('text-primary', 'text-body');
                            upvoteBtn.classList.add(result.current_user_vote === 1 ? 'text-primary' : 'text-body');
                        }
                        if (downvoteBtn) {
                            downvoteBtn.classList.remove('text-danger', 'text-body');
                            downvoteBtn.classList.add(result.current_user_vote === -1 ? 'text-danger' : 'text-body');
                        }
                        // Update vote count and colors in main post list
                        const mainCard = document.querySelector('.card .vote-btn[data-post="' + postId + '"]').closest('.card');
                        if (mainCard) {
                            const mainUpvoteBtn = mainCard.querySelector('.vote-btn[data-vote="1"] .icon-32');
                            const mainDownvoteBtn = mainCard.querySelector('.vote-btn[data-vote="-1"] .icon-32');
                            const mainVoteCountSpan = mainCard.querySelector('span[style*="font-weight:600;"]');
                            if (mainVoteCountSpan && result.votes_count !== undefined) mainVoteCountSpan.textContent = result.votes_count;
                            if (mainUpvoteBtn) {
                                mainUpvoteBtn.classList.remove('text-primary', 'text-body');
                                mainUpvoteBtn.classList.add(result.current_user_vote === 1 ? 'text-primary' : 'text-body');
                            }
                            if (mainDownvoteBtn) {
                                mainDownvoteBtn.classList.remove('text-danger', 'text-body');
                                mainDownvoteBtn.classList.add(result.current_user_vote === -1 ? 'text-danger' : 'text-body');
                            }
                        }
                    });
                });
            });

            // Comment submit
            const commentForm = modalEl.querySelector('#comment-form-modal');
            if (commentForm) {
                const input = commentForm.elements['content'];
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        submitComment();
                    }
                });
                const commentBtn = commentForm.querySelector('#comment-submit-btn');
                if (commentBtn) {
                    commentBtn.addEventListener('click', function(ev) {
                        ev.preventDefault();
                        submitComment();
                    });
                }
                commentForm.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    submitComment();
                });
                function submitComment() {
                    const content = input.value.trim();
                    if (!content) return;
                    fetch(`/posts/${postId}/comment`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ content }),
                    })
                    .then(async res => {
                        if (res.status === 401) {
                            window.location.href = '/login';
                            return;
                        }
                        if (!res.ok) {
                            const errorText = await res.text();
                            console.error('Comment error:', errorText);
                            return;
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (!data) return;
                        const commentsList = modalEl.querySelector('#comments-list');
                        if (commentsList && data.id) {
                            commentsList.insertAdjacentHTML('beforeend', commentHtml(data));
                        }
                        commentForm.reset();
                    })
                    .catch((err) => {
                        console.error('Comment AJAX error:', err);
                    });
                }
            }

            // Reply button and reply form
            modalEl.querySelector('#comments-list').addEventListener('click', function(ev) {
                const replyBtn = ev.target.closest('.reply-btn');
                if (replyBtn) {
                    const commentLi = replyBtn.closest('li[data-comment-id]');
                    if (!commentLi) return;
                    const replyContainer = commentLi.querySelector('.reply-form-container');
                    if (!replyContainer) return;
                    if (replyContainer.innerHTML.trim() === '') {
                        replyContainer.innerHTML = `
                            <form class="reply-form mt-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="content" placeholder="Reply..." required>
                                    <button class="btn btn-primary btn-sm" type="submit">Reply</button>
                                </div>
                            </form>
                        `;
                        replyContainer.style.display = '';
                    } else {
                        replyContainer.innerHTML = '';
                        replyContainer.style.display = 'none';
                    }
                }
                const replyForm = ev.target.closest('.reply-form');
                if (replyForm) {
                    ev.preventDefault();
                    const commentLi = replyForm.closest('li[data-comment-id]');
                    if (!commentLi) return;
                    const parentId = commentLi.getAttribute('data-comment-id');
                    const content = replyForm.elements['content'].value.trim();
                    if (!content) return;
                    fetch(`/posts/${postId}/comment`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ content, parent_id: parentId }),
                    })
                    .then(async res => {
                        if (res.status === 401) {
                            const data = await res.json().catch(() => ({}));
                            if (data.redirect) {
                                window.location.href = data.redirect;
                                return;
                            }
                        }
                        if (!res.ok) {
                            const errorText = await res.text();
                            console.error('Reply error:', errorText);
                            return;
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (!data) return;
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        replyForm.reset();
                        replyForm.closest('.reply-form-container').innerHTML = '';
                        replyForm.closest('.reply-form-container').style.display = 'none';
                        commentLi.insertAdjacentHTML('beforeend', commentHtml(data));
                    })
                    .catch((err) => {
                        console.error('Reply AJAX error:', err);
                    });
                }
            });
        }
    });
