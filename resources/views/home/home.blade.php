<x-app-layout :assets="$assets ?? []">
    <div class="row d-flex justify-content-center">
        <div class="col-lg-6">
            <div class="profile-content tab-content">
                <div id="profile-feed" class="tab-pane fade active show">
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card sticky-top">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Recent Posts</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div id="about-recent-posts">
                        <div class="text-muted">Loading recent posts...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.components.share-offcanvas')
    <style>
        .card.sticky-top {
            top: 80px !important;
        }
    </style>
</x-app-layout>
<!-- Home Feed and Detail JS -->
<script src="/js/home-feed.js"></script>
<script src="/js/home-detail.js"></script>
<script src="/js/home-sidebar.js"></script>
